<?php
/**
 * Configuration de la base de données
 * 
 * Copiez ce fichier dans config/db.php et adaptez les paramètres
 */

return [
    'class' => 'yii\db\Connection',
    
    // MySQL
    'dsn' => 'mysql:host=localhost;dbname=checklist_accessibilite',
    'username' => 'root',
    'password' => '',
    'charset' => 'utf8mb4',
    
    // PostgreSQL (alternative)
    // 'dsn' => 'pgsql:host=localhost;port=5432;dbname=checklist_accessibilite',
    // 'username' => 'postgres',
    // 'password' => '',
    // 'charset' => 'utf8',
    
    // Options de connexion
    'enableSchemaCache' => true,
    'schemaCacheDuration' => 3600,
    'schemaCache' => 'cache',
    
    // Attributs PDO
    'attributes' => [
        PDO::ATTR_EMULATE_PREPARES => false,
        PDO::ATTR_STRINGIFY_FETCHES => false,
    ],
];
