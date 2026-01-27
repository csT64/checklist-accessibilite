<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;

/**
 * Modèle pour la table "verification"
 *
 * @property int $id
 * @property int $contenu_id
 * @property int $critere_id
 * @property string $statut
 * @property string|null $commentaire
 * @property int|null $verificateur_id
 * @property int|null $created_at
 * @property int|null $updated_at
 *
 * @property Contenu $contenu
 * @property Critere $critere
 * @property User $verificateur
 */
class Verification extends ActiveRecord
{
    const STATUT_CONFORME = 'conforme';
    const STATUT_NON_CONFORME = 'non_conforme';
    const STATUT_NON_APPLICABLE = 'non_applicable';
    const STATUT_A_VERIFIER = 'a_verifier';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%verification}}';
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
            [['contenu_id', 'critere_id'], 'required'],
            [['contenu_id', 'critere_id', 'verificateur_id', 'created_at', 'updated_at'], 'integer'],
            [['commentaire'], 'string'],
            [['statut'], 'in', 'range' => [
                self::STATUT_CONFORME,
                self::STATUT_NON_CONFORME,
                self::STATUT_NON_APPLICABLE,
                self::STATUT_A_VERIFIER
            ]],
            [['contenu_id'], 'exist', 'skipOnError' => true, 'targetClass' => Contenu::class, 'targetAttribute' => ['contenu_id' => 'id']],
            [['critere_id'], 'exist', 'skipOnError' => true, 'targetClass' => Critere::class, 'targetAttribute' => ['critere_id' => 'id']],
            [['verificateur_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['verificateur_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'contenu_id' => 'Contenu',
            'critere_id' => 'Critère',
            'statut' => 'Statut',
            'commentaire' => 'Commentaire',
            'verificateur_id' => 'Vérificateur',
            'created_at' => 'Créé le',
            'updated_at' => 'Modifié le',
        ];
    }

    /**
     * Relation avec le contenu
     *
     * @return \yii\db\ActiveQuery
     */
    public function getContenu()
    {
        return $this->hasOne(Contenu::class, ['id' => 'contenu_id']);
    }

    /**
     * Relation avec le critère
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCritere()
    {
        return $this->hasOne(Critere::class, ['id' => 'critere_id']);
    }

    /**
     * Relation avec le vérificateur
     *
     * @return \yii\db\ActiveQuery
     */
    public function getVerificateur()
    {
        return $this->hasOne(User::class, ['id' => 'verificateur_id']);
    }

    /**
     * Retourne les statuts disponibles
     *
     * @return array
     */
    public static function getStatuts()
    {
        return [
            self::STATUT_CONFORME => '✅ Conforme',
            self::STATUT_NON_CONFORME => '❌ Non conforme',
            self::STATUT_NON_APPLICABLE => '⚪ Non applicable',
            self::STATUT_A_VERIFIER => '🔄 À vérifier',
        ];
    }

    /**
     * Retourne le label du statut avec emoji
     *
     * @return string
     */
    public function getStatutLabel()
    {
        $statuts = self::getStatuts();
        return $statuts[$this->statut] ?? $this->statut;
    }

    /**
     * Retourne la classe CSS pour le statut
     *
     * @return string
     */
    public function getStatutClass()
    {
        $classes = [
            self::STATUT_CONFORME => 'statut-conforme',
            self::STATUT_NON_CONFORME => 'statut-non-conforme',
            self::STATUT_NON_APPLICABLE => 'statut-non-applicable',
            self::STATUT_A_VERIFIER => 'statut-a-verifier',
        ];
        return $classes[$this->statut] ?? '';
    }

    /**
     * Après sauvegarde, recalculer le score du contenu
     *
     * @param bool $insert
     * @param array $changedAttributes
     */
    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);
        
        if ($this->contenu) {
            $this->contenu->calculerScore();
        }
    }
}
