<?php

use yii\db\Migration;

/**
 * Migration pour la table categorie
 * Contient les catégories de critères RGAA (Texte, Liens, Images, etc.)
 */
class m260126_100001_create_categorie_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%categorie}}', [
            'id' => $this->primaryKey(),
            'nom' => $this->string(100)->notNull()->comment('Nom de la catégorie'),
            'code' => $this->string(10)->notNull()->comment('Code (ex: 5.1)'),
            'description' => $this->text()->comment('Description de la catégorie'),
            'ordre' => $this->integer()->notNull()->comment('Ordre d\'affichage'),
            'created_at' => $this->integer(),
            'updated_at' => $this->integer(),
        ], 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB');

        // Index sur le code pour recherches rapides
        $this->createIndex(
            'idx-categorie-code',
            '{{%categorie}}',
            'code'
        );

        // Insertion des catégories de base
        $this->batchInsert('{{%categorie}}', 
            ['nom', 'code', 'description', 'ordre', 'created_at', 'updated_at'],
            [
                ['Texte et rédaction', '5.1', 'Critères relatifs au contenu textuel', 1, time(), time()],
                ['Structure et hiérarchie', '5.2', 'Structuration des pages avec titres et listes', 2, time(), time()],
                ['Liens', '5.3', 'Accessibilité des liens hypertextes', 3, time(), time()],
                ['Images et visuels', '5.4', 'Alternatives textuelles et images accessibles', 4, time(), time()],
                ['Tableaux', '5.5', 'Tableaux de données accessibles', 5, time(), time()],
                ['Couleurs et mise en forme', '5.6', 'Contraste et information visuelle', 6, time(), time()],
                ['Médias audio et vidéo', '5.7', 'Accessibilité des contenus multimédia', 7, time(), time()],
                ['Formulaires', '5.8', 'Accessibilité des formulaires', 8, time(), time()],
                ['Documents et contenus intégrés', '5.9', 'PDF et contenus embarqués', 9, time(), time()],
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%categorie}}');
    }
}
