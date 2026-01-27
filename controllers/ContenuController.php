<?php

namespace app\controllers;

use Yii;
use app\models\Contenu;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;

/**
 * ContenuController gère les opérations CRUD pour les contenus
 */
class ContenuController extends Controller
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
                        'roles' => ['@'], // Utilisateurs connectés uniquement
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Liste tous les contenus
     *
     * @return string
     */
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => Contenu::find()
                ->orderBy(['created_at' => SORT_DESC])
                ->with(['utilisateur']),
            'pagination' => [
                'pageSize' => 20,
            ],
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Affiche un contenu spécifique
     *
     * @param int $id
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);
        $stats = $model->getStatistiques();

        return $this->render('view', [
            'model' => $model,
            'stats' => $stats,
        ]);
    }

    /**
     * Crée un nouveau contenu
     *
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new Contenu();
        $model->utilisateur_id = Yii::$app->user->id;
        $model->statut = Contenu::STATUT_EN_COURS;

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', 'Contenu créé avec succès.');
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Met à jour un contenu existant
     *
     * @param int $id
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', 'Contenu mis à jour avec succès.');
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Supprime un contenu
     *
     * @param int $id
     * @return \yii\web\Response
     * @throws NotFoundHttpException
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        Yii::$app->session->setFlash('success', 'Contenu supprimé avec succès.');
        return $this->redirect(['index']);
    }

    /**
     * Trouve un contenu par son ID
     *
     * @param int $id
     * @return Contenu
     * @throws NotFoundHttpException
     */
    protected function findModel($id)
    {
        if (($model = Contenu::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('Le contenu demandé n\'existe pas.');
    }
}
