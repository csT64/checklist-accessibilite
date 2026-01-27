<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;

/**
 * Modèle pour la table "critere"
 *
 * @property int $id
 * @property int $categorie_id
 * @property string $code
 * @property string $titre
 * @property string $priorite
 * @property string|null $wcag
 * @property string|null $rgaa
 * @property string|null $raweb
 * @property string|null $description
 * @property string|null $a_verifier
 * @property string|null $exemples_valides
 * @property string|null $exemples_invalides
 * @property string|null $outils_recommandes
 * @property int $ordre
 * @property int|null $created_at
 * @property int|null $updated_at
 *
 * @property Categorie $categorie
 * @property Verification[] $verifications
 */
class Critere extends ActiveRecord
{
    const PRIORITE_CRITIQUE = 'critique';
    const PRIORITE_IMPORTANTE = 'importante';
    const PRIORITE_RECOMMANDEE = 'recommandee';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%critere}}';
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
            [['categorie_id', 'code', 'titre', 'priorite', 'ordre'], 'required'],
            [['categorie_id', 'ordre', 'created_at', 'updated_at'], 'integer'],
            [['description', 'a_verifier', 'exemples_valides', 'exemples_invalides', 'outils_recommandes'], 'string'],
            [['code'], 'string', 'max' => 20],
            [['titre'], 'string', 'max' => 255],
            [['wcag', 'rgaa', 'raweb'], 'string', 'max' => 50],
            [['priorite'], 'in', 'range' => [
                self::PRIORITE_CRITIQUE,
                self::PRIORITE_IMPORTANTE,
                self::PRIORITE_RECOMMANDEE
            ]],
            [['categorie_id'], 'exist', 'skipOnError' => true, 'targetClass' => Categorie::class, 'targetAttribute' => ['categorie_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'categorie_id' => 'Catégorie',
            'code' => 'Code',
            'titre' => 'Titre',
            'priorite' => 'Priorité',
            'wcag' => 'WCAG',
            'rgaa' => 'RGAA',
            'raweb' => 'RAWeb',
            'description' => 'Description',
            'a_verifier' => 'À vérifier',
            'exemples_valides' => 'Exemples valides',
            'exemples_invalides' => 'Exemples invalides',
            'outils_recommandes' => 'Outils recommandés',
            'ordre' => 'Ordre',
            'created_at' => 'Créé le',
            'updated_at' => 'Modifié le',
        ];
    }

    /**
     * Relation avec la catégorie
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCategorie()
    {
        return $this->hasOne(Categorie::class, ['id' => 'categorie_id']);
    }

    /**
     * Relation avec les vérifications
     *
     * @return \yii\db\ActiveQuery
     */
    public function getVerifications()
    {
        return $this->hasMany(Verification::class, ['critere_id' => 'id']);
    }

    /**
     * Retourne le label de la priorité avec emoji
     *
     * @return string
     */
    public function getPrioriteLabel()
    {
        $labels = [
            self::PRIORITE_CRITIQUE => '🔴 Critique',
            self::PRIORITE_IMPORTANTE => '🟠 Importante',
            self::PRIORITE_RECOMMANDEE => '🟢 Recommandée',
        ];
        return $labels[$this->priorite] ?? $this->priorite;
    }

    /**
     * Retourne la classe CSS pour la priorité
     *
     * @return string
     */
    public function getPrioriteClass()
    {
        $classes = [
            self::PRIORITE_CRITIQUE => 'priorite-critique',
            self::PRIORITE_IMPORTANTE => 'priorite-importante',
            self::PRIORITE_RECOMMANDEE => 'priorite-recommandee',
        ];
        return $classes[$this->priorite] ?? '';
    }

    /**
     * Retourne les priorités disponibles
     *
     * @return array
     */
    public static function getPriorites()
    {
        return [
            self::PRIORITE_CRITIQUE => '🔴 Critique',
            self::PRIORITE_IMPORTANTE => '🟠 Importante',
            self::PRIORITE_RECOMMANDEE => '🟢 Recommandée',
        ];
    }
}
