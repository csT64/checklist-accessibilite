#!/bin/bash

echo "========================================="
echo "Test et correction de quick-save"
echo "========================================="
echo ""

echo "1️⃣  Vérification de la méthode dans le controller"
echo "---------------------------------------"

if ! grep -q "function actionQuickSave" controllers/VerificationController.php; then
    echo "❌ La méthode actionQuickSave n'existe PAS dans VerificationController.php"
    echo ""
    echo "💡 Solution : Ajoutez la méthode au controller"
    echo "   Le code est dans le fichier actionQuickSave-corrected.php"
    exit 1
else
    echo "✅ La méthode actionQuickSave existe"
fi

echo ""
echo "2️⃣  Test avec curl (simulation AJAX)"
echo "---------------------------------------"

# Test sans AJAX header
echo "Test 1 : Sans header AJAX"
RESPONSE=$(curl -s -X POST \
  -H "Content-Type: application/x-www-form-urlencoded" \
  -d "contenu_id=1&critere_id=1&statut=conforme&commentaire=test" \
  "http://checklist-accessibilite.local/index.php?r=verification/quick-save")

echo "Réponse : $RESPONSE"

if echo "$RESPONSE" | grep -q '"success":true'; then
    echo "✅ La requête fonctionne"
elif echo "$RESPONSE" | grep -q '"success":false'; then
    echo "⚠️  La requête retourne une erreur"
    echo "$RESPONSE" | grep -o '"message":"[^"]*"'
else
    echo "❌ Réponse inattendue"
fi

echo ""
echo "3️⃣  Vérification du code actuel"
echo "---------------------------------------"

# Vérifier si le code vérifie isAjax
if grep -q "isAjax" controllers/VerificationController.php; then
    echo "⚠️  Le controller vérifie \$request->isAjax"
    echo "   Cela peut bloquer les requêtes POST normales"
    echo ""
    echo "💡 Solution : Remplacez"
    echo "   if (!\$request->isAjax || !\$request->isPost)"
    echo "   par"
    echo "   if (!\$request->isPost)"
else
    echo "✅ Le controller ne force pas isAjax"
fi

echo ""
echo "4️⃣  Test direct dans la base de données"
echo "---------------------------------------"

# Vérifier qu'on peut créer une vérification
php << 'PHPCODE'
<?php
$config = require 'config/db.php';
$pdo = new PDO($config['dsn'], $config['username'], $config['password']);

// Vérifier qu'on a un contenu
$contenuId = $pdo->query('SELECT id FROM contenu LIMIT 1')->fetchColumn();
if (!$contenuId) {
    echo "❌ Aucun contenu trouvé. Créez un contenu d'abord.\n";
    exit(1);
}

// Vérifier qu'on a un critère
$critereId = $pdo->query('SELECT id FROM critere LIMIT 1')->fetchColumn();
if (!$critereId) {
    echo "❌ Aucun critère trouvé. Importez les critères d'abord.\n";
    exit(1);
}

echo "✅ Contenu ID: $contenuId\n";
echo "✅ Critère ID: $critereId\n";

// Essayer de créer une vérification directement
try {
    // Supprimer si existe déjà
    $pdo->exec("DELETE FROM verification WHERE contenu_id=$contenuId AND critere_id=$critereId");
    
    // Créer
    $stmt = $pdo->prepare("
        INSERT INTO verification (contenu_id, critere_id, statut, commentaire, created_at, updated_at)
        VALUES (:contenu_id, :critere_id, :statut, :commentaire, :created_at, :updated_at)
    ");
    
    $stmt->execute([
        ':contenu_id' => $contenuId,
        ':critere_id' => $critereId,
        ':statut' => 'conforme',
        ':commentaire' => 'Test depuis script',
        ':created_at' => time(),
        ':updated_at' => time(),
    ]);
    
    echo "✅ Insertion directe dans la BDD fonctionne\n";
    
    // Vérifier
    $count = $pdo->query("SELECT COUNT(*) FROM verification WHERE contenu_id=$contenuId AND critere_id=$critereId")->fetchColumn();
    echo "✅ Vérification créée (total: $count)\n";
    
} catch (PDOException $e) {
    echo "❌ Erreur : " . $e->getMessage() . "\n";
    exit(1);
}
PHPCODE

echo ""
echo "5️⃣  Test avec Yii2"
echo "---------------------------------------"

php << 'PHPCODE'
<?php
require 'vendor/autoload.php';
require 'vendor/yiisoft/yii2/Yii.php';

$config = require 'config/web.php';
$app = new yii\web\Application($config);

require_once 'models/Contenu.php';
require_once 'models/Critere.php';
require_once 'models/Verification.php';

try {
    // Récupérer un contenu et un critère
    $contenu = app\models\Contenu::find()->one();
    $critere = app\models\Critere::find()->one();
    
    if (!$contenu || !$critere) {
        echo "❌ Contenu ou critère manquant\n";
        exit(1);
    }
    
    echo "✅ Contenu trouvé : {$contenu->titre}\n";
    echo "✅ Critère trouvé : {$critere->titre}\n";
    
    // Essayer de créer une vérification
    $verification = new app\models\Verification([
        'contenu_id' => $contenu->id,
        'critere_id' => $critere->id,
        'statut' => 'a_verifier',
        'commentaire' => 'Test Yii2',
        'verificateur_id' => 1,
    ]);
    
    if ($verification->save()) {
        echo "✅ Création de vérification via Yii2 fonctionne\n";
    } else {
        echo "❌ Erreur Yii2 : " . print_r($verification->errors, true) . "\n";
    }
    
} catch (Exception $e) {
    echo "❌ Exception : " . $e->getMessage() . "\n";
    exit(1);
}
PHPCODE

echo ""
echo "========================================="
echo "Solutions"
echo "========================================="
echo ""
echo "Si la requête échoue, vérifiez :"
echo ""
echo "1. Ouvrez controllers/VerificationController.php"
echo "2. Trouvez la méthode actionQuickSave()"
echo "3. Assurez-vous qu'elle ressemble à celle dans actionQuickSave-corrected.php"
echo ""
echo "Changements importants :"
echo "  - Enlever la vérification \$request->isAjax"
echo "  - Garder seulement \$request->isPost"
echo "  - Forcer le format JSON en début de méthode"
echo ""
echo "4. Testez dans le navigateur avec F12 > Network"
echo "   pour voir exactement ce qui est envoyé"
echo ""
