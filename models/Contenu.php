<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;

/**
 * Modèle pour la table "contenu"
 *
 * @property int $id
 * @property int $utilisateur_id
 * @property string $titre
 * @property string|null $url
 * @property string $type_contenu
 * @property string|null $description
 * @property string $statut
 * @property float|null $score_conformite
 * @property int|null $created_at
 * @property int|null $updated_at
 *
 * @property User $utilisateur
 * @property Verification[] $verifications
 */
class Contenu extends ActiveRecord
{
    const TYPE_PAGE = 'page';
    const TYPE_ARTICLE = 'article';
    const TYPE_FORMULAIRE = 'formulaire';
    const TYPE_DOCUMENT = 'document';

    const STATUT_EN_COURS = 'en_cours';
    const STATUT_VERIFIE = 'verifie';
    const STATUT_VALIDE = 'valide';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%contenu}}';
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::class,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['utilisateur_id', 'titre', 'type_contenu'], 'required'],
            [['utilisateur_id', 'created_at', 'updated_at'], 'integer'],
            [['description'], 'string'],
            [['score_conformite'], 'number'],
            [['titre'], 'string', 'max' => 255],
            [['url'], 'string', 'max' => 500],
            [['url'], 'url'],
            [['type_contenu'], 'in', 'range' => [
                self::TYPE_PAGE,
                self::TYPE_ARTICLE,
                self::TYPE_FORMULAIRE,
                self::TYPE_DOCUMENT
            ]],
            [['statut'], 'in', 'range' => [
                self::STATUT_EN_COURS,
                self::STATUT_VERIFIE,
                self::STATUT_VALIDE
            ]],
            [['utilisateur_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['utilisateur_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'utilisateur_id' => 'Producteur de contenu',
            'titre' => 'Titre du contenu',
            'url' => 'URL',
            'type_contenu' => 'Type de contenu',
            'description' => 'Description',
            'statut' => 'Statut',
            'score_conformite' => 'Score de conformité (%)',
            'created_at' => 'Créé le',
            'updated_at' => 'Modifié le',
        ];
    }

    /**
     * Relation avec l'utilisateur
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUtilisateur()
    {
        return $this->hasOne(User::class, ['id' => 'utilisateur_id']);
    }

    /**
     * Relation avec les vérifications
     *
     * @return \yii\db\ActiveQuery
     */
    public function getVerifications()
    {
        return $this->hasMany(Verification::class, ['contenu_id' => 'id']);
    }

    /**
     * Retourne les types de contenu disponibles
     *
     * @return array
     */
    public static function getTypesContenu()
    {
        return [
            self::TYPE_PAGE => 'Page web',
            self::TYPE_ARTICLE => 'Article',
            self::TYPE_FORMULAIRE => 'Formulaire',
            self::TYPE_DOCUMENT => 'Document',
        ];
    }

    /**
     * Retourne les statuts disponibles
     *
     * @return array
     */
    public static function getStatuts()
    {
        return [
            self::STATUT_EN_COURS => 'En cours',
            self::STATUT_VERIFIE => 'Vérifié',
            self::STATUT_VALIDE => 'Validé',
        ];
    }

    /**
     * Retourne le label du type de contenu
     *
     * @return string
     */
    public function getTypeContenuLabel()
    {
        $types = self::getTypesContenu();
        return $types[$this->type_contenu] ?? $this->type_contenu;
    }

    /**
     * Retourne le label du statut
     *
     * @return string
     */
    public function getStatutLabel()
    {
        $statuts = self::getStatuts();
        return $statuts[$this->statut] ?? $this->statut;
    }

    /**
     * Calcule et met à jour le score de conformité
     *
     * @return bool
     */
    public function calculerScore()
    {
        $verifications = $this->getVerifications()->all();
        
        if (empty($verifications)) {
            $this->score_conformite = 0;
            return $this->save(false, ['score_conformite']);
        }

        $total = count($verifications);
        $conformes = 0;
        $nonApplicables = 0;

        foreach ($verifications as $verif) {
            if ($verif->statut === Verification::STATUT_CONFORME) {
                $conformes++;
            } elseif ($verif->statut === Verification::STATUT_NON_APPLICABLE) {
                $nonApplicables++;
            }
        }

        // Score = conformes / (total - non applicables) * 100
        $applicable = $total - $nonApplicables;
        if ($applicable > 0) {
            $this->score_conformite = round(($conformes / $applicable) * 100, 2);
        } else {
            $this->score_conformite = 0;
        }

        return $this->save(false, ['score_conformite']);
    }

    /**
     * Retourne les statistiques de vérification
     *
     * @return array
     */
    public function getStatistiques()
    {
        $verifications = $this->getVerifications()->all();
        
        $stats = [
            'total' => count($verifications),
            'conforme' => 0,
            'non_conforme' => 0,
            'non_applicable' => 0,
            'a_verifier' => 0,
            'progression' => 0,
        ];

        foreach ($verifications as $verif) {
            switch ($verif->statut) {
                case Verification::STATUT_CONFORME:
                    $stats['conforme']++;
                    break;
                case Verification::STATUT_NON_CONFORME:
                    $stats['non_conforme']++;
                    break;
                case Verification::STATUT_NON_APPLICABLE:
                    $stats['non_applicable']++;
                    break;
                case Verification::STATUT_A_VERIFIER:
                    $stats['a_verifier']++;
                    break;
            }
        }

        // Calcul de la progression (éléments vérifiés)
        $verifies = $stats['total'] - $stats['a_verifier'];
        if ($stats['total'] > 0) {
            $stats['progression'] = round(($verifies / $stats['total']) * 100);
        }

        return $stats;
    }
}
