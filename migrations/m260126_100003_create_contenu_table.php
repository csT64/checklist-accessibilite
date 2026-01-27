<?php

use yii\db\Migration;

/**
 * Migration pour la table contenu
 * Contient les contenus à vérifier (pages, articles, formulaires, etc.)
 */
class m260126_100003_create_contenu_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%contenu}}', [
            'id' => $this->primaryKey(),
            'utilisateur_id' => $this->integer()->notNull()->comment('Producteur de contenu'),
            'titre' => $this->string(255)->notNull()->comment('Titre du contenu'),
            'url' => $this->string(500)->comment('URL du contenu (optionnel)'),
            'type_contenu' => "ENUM('page', 'article', 'formulaire', 'document') NOT NULL COMMENT 'Type de contenu'",
            'description' => $this->text()->comment('Description du contenu'),
            'statut' => "ENUM('en_cours', 'verifie', 'valide') DEFAULT 'en_cours' COMMENT 'Statut de la vérification'",
            'score_conformite' => $this->decimal(5, 2)->comment('Score de conformité (%)'),
            'created_at' => $this->integer(),
            'updated_at' => $this->integer(),
        ], 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB');

        // Clé étrangère vers user
        $this->addForeignKey(
            'fk-contenu-utilisateur_id',
            '{{%contenu}}',
            'utilisateur_id',
            '{{%user}}',
            'id',
            'CASCADE',
            'CASCADE'
        );

        // Index pour recherches
        $this->createIndex(
            'idx-contenu-statut',
            '{{%contenu}}',
            'statut'
        );

        $this->createIndex(
            'idx-contenu-utilisateur',
            '{{%contenu}}',
            'utilisateur_id'
        );

        $this->createIndex(
            'idx-contenu-type',
            '{{%contenu}}',
            'type_contenu'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk-contenu-utilisateur_id', '{{%contenu}}');
        $this->dropTable('{{%contenu}}');
    }
}
