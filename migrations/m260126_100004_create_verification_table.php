<?php

use yii\db\Migration;

/**
 * Migration pour la table verification
 * Contient les vérifications de critères pour chaque contenu
 */
class m260126_100004_create_verification_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%verification}}', [
            'id' => $this->primaryKey(),
            'contenu_id' => $this->integer()->notNull()->comment('Contenu vérifié'),
            'critere_id' => $this->integer()->notNull()->comment('Critère évalué'),
            'statut' => "ENUM('conforme', 'non_conforme', 'non_applicable', 'a_verifier') DEFAULT 'a_verifier' COMMENT 'Résultat de la vérification'",
            'commentaire' => $this->text()->comment('Commentaire ou précision'),
            'verificateur_id' => $this->integer()->comment('Personne qui a vérifié'),
            'created_at' => $this->integer(),
            'updated_at' => $this->integer(),
        ], 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB');

        // Clés étrangères
        $this->addForeignKey(
            'fk-verification-contenu_id',
            '{{%verification}}',
            'contenu_id',
            '{{%contenu}}',
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk-verification-critere_id',
            '{{%verification}}',
            'critere_id',
            '{{%critere}}',
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk-verification-verificateur_id',
            '{{%verification}}',
            'verificateur_id',
            '{{%user}}',
            'id',
            'SET NULL',
            'CASCADE'
        );

        // Index composite unique pour éviter les doublons
        $this->createIndex(
            'idx-verification-contenu-critere',
            '{{%verification}}',
            ['contenu_id', 'critere_id'],
            true // unique
        );

        // Index pour recherches par statut
        $this->createIndex(
            'idx-verification-statut',
            '{{%verification}}',
            'statut'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk-verification-contenu_id', '{{%verification}}');
        $this->dropForeignKey('fk-verification-critere_id', '{{%verification}}');
        $this->dropForeignKey('fk-verification-verificateur_id', '{{%verification}}');
        $this->dropTable('{{%verification}}');
    }
}
