<?php

namespace app\controllers;

use Yii;
use app\models\Contenu;
use app\models\Categorie;
use app\models\Critere;
use app\models\Verification;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\AccessControl;
use yii\web\Response;

/**
 * VerificationController gère les vérifications d'accessibilité
 */
class VerificationController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    /**
     * Affiche la checklist de vérification pour un contenu
     *
     * @param int $id ID du contenu
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionChecklist($id)
    {
        $contenu = $this->findContenu($id);
        
        // Récupérer toutes les catégories avec leurs critères
        $categories = Categorie::find()
            ->with('criteres')
            ->orderBy(['ordre' => SORT_ASC])
            ->all();
        
        // Récupérer ou créer les vérifications pour chaque critère
        $verifications = [];
        foreach ($categories as $categorie) {
            foreach ($categorie->criteres as $critere) {
                $verification = Verification::find()
                    ->where([
                        'contenu_id' => $contenu->id,
                        'critere_id' => $critere->id
                    ])
                    ->one();
                
                if (!$verification) {
                    $verification = new Verification([
                        'contenu_id' => $contenu->id,
                        'critere_id' => $critere->id,
                        'statut' => Verification::STATUT_A_VERIFIER,
                    ]);
                    $verification->save();
                }
                
                $verifications[$critere->id] = $verification;
            }
        }
        
        return $this->render('checklist', [
            'contenu' => $contenu,
            'categories' => $categories,
            'verifications' => $verifications,
        ]);
    }

    /**
     * Met à jour une vérification (AJAX ou formulaire classique)
     *
     * @param int|null $id ID de la vérification (null pour création)
     * @return array|Response
     */
    public function actionUpdate($id = null)
    {
        $request = Yii::$app->request;
        
        if ($id) {
            $verification = Verification::findOne($id);
            if (!$verification) {
                if ($request->isAjax) {
                    Yii::$app->response->format = Response::FORMAT_JSON;
                    return [
                        'success' => false,
                        'message' => 'Vérification introuvable.',
                    ];
                }
                throw new NotFoundHttpException('Vérification introuvable.');
            }
        } else {
            $verification = new Verification();
        }
        
        $verification->verificateur_id = Yii::$app->user->id;
        
        if ($verification->load($request->post()) && $verification->save()) {
            
            if ($request->isAjax) {
                Yii::$app->response->format = Response::FORMAT_JSON;
                return [
                    'success' => true,
                    'message' => 'Vérification enregistrée avec succès.',
                    'verification' => [
                        'id' => $verification->id,
                        'statut' => $verification->statut,
                        'statut_label' => $verification->getStatutLabel(),
                        'commentaire' => $verification->commentaire,
                    ],
                ];
            }
            
            Yii::$app->session->setFlash('success', 'Vérification enregistrée avec succès.');
            return $this->redirect(['checklist', 'id' => $verification->contenu_id]);
        }
        
        if ($request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return [
                'success' => false,
                'message' => 'Erreur lors de l\'enregistrement.',
                'errors' => $verification->errors,
            ];
        }
        
        Yii::$app->session->setFlash('error', 'Erreur lors de l\'enregistrement de la vérification.');
        return $this->redirect(['checklist', 'id' => $verification->contenu_id]);
    }

    /**
     * Enregistrement rapide AJAX (sans rechargement de page)
     *
     * @return array
     */
public function actionQuickSave()
{
    // Forcer la réponse en JSON
    Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
    
    $request = Yii::$app->request;
    
    // Accepter les requêtes POST ET AJAX
    if (!$request->isPost) {
        return [
            'success' => false, 
            'message' => 'Seules les requêtes POST sont acceptées.'
        ];
    }
    
    // Récupérer les paramètres
    $contenu_id = $request->post('contenu_id');
    $critere_id = $request->post('critere_id');
    $statut = $request->post('statut');
    $commentaire = $request->post('commentaire', '');
    
    // Validation basique
    if (!$contenu_id || !$critere_id || !$statut) {
        return [
            'success' => false,
            'message' => 'Paramètres manquants (contenu_id, critere_id ou statut).',
            'received' => [
                'contenu_id' => $contenu_id,
                'critere_id' => $critere_id,
                'statut' => $statut,
            ]
        ];
    }
    
    // Trouver ou créer la vérification
    $verification = Verification::find()
        ->where([
            'contenu_id' => $contenu_id,
            'critere_id' => $critere_id
        ])
        ->one();
    
    if (!$verification) {
        $verification = new Verification([
            'contenu_id' => $contenu_id,
            'critere_id' => $critere_id,
        ]);
    }
    
    // Mettre à jour
    $verification->statut = $statut;
    $verification->commentaire = $commentaire;
    $verification->verificateur_id = Yii::$app->user->id;
    
    if ($verification->save()) {
        // Récupérer les stats mises à jour
        $contenu = Contenu::findOne($contenu_id);
        $stats = $contenu ? $contenu->getStatistiques() : null;
        
        return [
            'success' => true,
            'message' => 'Enregistré',
            'verification' => [
                'id' => $verification->id,
                'statut' => $verification->statut,
                'statut_label' => $verification->getStatutLabel(),
            ],
            'stats' => $stats,
        ];
    }
    
    return [
        'success' => false,
        'message' => 'Erreur lors de l\'enregistrement.',
        'errors' => $verification->errors,
    ];
}



    /**
     * Trouve un contenu ou lance une exception
     *
     * @param int $id
     * @return Contenu
     * @throws NotFoundHttpException
     */
    protected function findContenu($id)
    {
        if (($model = Contenu::findOne($id)) !== null) {
            return $model;
        }
        
        throw new NotFoundHttpException('Le contenu demandé n\'existe pas.');
    }
}
