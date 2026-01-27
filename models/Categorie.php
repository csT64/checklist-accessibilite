<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;

/**
 * Modèle pour la table "categorie"
 *
 * @property int $id
 * @property string $nom
 * @property string $code
 * @property string|null $description
 * @property int $ordre
 * @property int|null $created_at
 * @property int|null $updated_at
 *
 * @property Critere[] $criteres
 */
class Categorie extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%categorie}}';
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
            [['nom', 'code', 'ordre'], 'required'],
            [['description'], 'string'],
            [['ordre', 'created_at', 'updated_at'], 'integer'],
            [['nom'], 'string', 'max' => 100],
            [['code'], 'string', 'max' => 10],
            [['code'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'nom' => 'Nom',
            'code' => 'Code',
            'description' => 'Description',
            'ordre' => 'Ordre',
            'created_at' => 'Créé le',
            'updated_at' => 'Modifié le',
        ];
    }

    /**
     * Relation avec les critères
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCriteres()
    {
        return $this->hasMany(Critere::class, ['categorie_id' => 'id'])
            ->orderBy(['ordre' => SORT_ASC]);
    }

    /**
     * Compte le nombre de critères dans cette catégorie
     *
     * @return int
     */
    public function getNombreCriteres()
    {
        return $this->getCriteres()->count();
    }
}
