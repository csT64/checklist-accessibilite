<?php

use yii\db\Migration;

/**
 * Migration pour la table critere
 * Contient les critères d'accessibilité détaillés
 */
class m260126_100002_create_critere_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%critere}}', [
            'id' => $this->primaryKey(),
            'categorie_id' => $this->integer()->notNull()->comment('Référence à la catégorie'),
            'code' => $this->string(20)->notNull()->comment('Code du critère (ex: 5.3.1)'),
            'titre' => $this->string(255)->notNull()->comment('Titre du critère'),
            'priorite' => "ENUM('critique', 'importante', 'recommandee') NOT NULL COMMENT 'Niveau de priorité'",
            'wcag' => $this->string(50)->comment('Référence WCAG (ex: 2.4.4)'),
            'rgaa' => $this->string(50)->comment('Référence RGAA (ex: 6.1)'),
            'raweb' => $this->string(50)->comment('Référence RAWeb (ex: 6.1)'),
            'description' => $this->text()->comment('Description du critère'),
            'a_verifier' => $this->text()->comment('Points à vérifier'),
            'exemples_valides' => $this->text()->comment('Exemples de contenus conformes'),
            'exemples_invalides' => $this->text()->comment('Exemples de contenus non conformes'),
            'outils_recommandes' => $this->text()->comment('Outils pour vérifier le critère'),
            'ordre' => $this->integer()->notNull()->comment('Ordre d\'affichage dans la catégorie'),
            'created_at' => $this->integer(),
            'updated_at' => $this->integer(),
        ], 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB');

        // Clé étrangère vers categorie
        $this->addForeignKey(
            'fk-critere-categorie_id',
            '{{%critere}}',
            'categorie_id',
            '{{%categorie}}',
            'id',
            'CASCADE',
            'CASCADE'
        );

        // Index pour recherches
        $this->createIndex(
            'idx-critere-code',
            '{{%critere}}',
            'code'
        );

        $this->createIndex(
            'idx-critere-priorite',
            '{{%critere}}',
            'priorite'
        );

        $this->createIndex(
            'idx-critere-categorie-ordre',
            '{{%critere}}',
            ['categorie_id', 'ordre']
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk-critere-categorie_id', '{{%critere}}');
        $this->dropTable('{{%critere}}');
    }
}
