<?php

namespace app\commands;

use Yii;
use yii\console\Controller;
use yii\console\ExitCode;
use app\models\Categorie;
use app\models\Critere;

/**
 * Commande pour importer les critères depuis le fichier Markdown
 *
 * Usage: php yii criteres/import
 */
class CriteresController extends Controller
{
    /**
     * Importe les critères depuis data/criteres-rgaa.md
     *
     * @return int Exit code
     */
    public function actionImport()
    {
        $fichier = Yii::getAlias('@app/data/criteres-rgaa.md');
        
        if (!file_exists($fichier)) {
            $this->stderr("❌ Fichier non trouvé : {$fichier}\n");
            return ExitCode::DATAERR;
        }
        
        $this->stdout("📄 Lecture du fichier : {$fichier}\n");
        $contenu = file_get_contents($fichier);
        
        $this->stdout("🔄 Parsing des critères...\n");
        $criteres = $this->parserCriteres($contenu);
        
        $this->stdout("📊 " . count($criteres) . " critères trouvés\n");
        
        // Confirmation
        $this->stdout("\n⚠️  Cette opération va supprimer tous les critères existants et les réimporter.\n");
        $confirmer = $this->prompt("Continuer ? (o/N)", ['default' => 'N']);
        
        if (strtolower($confirmer) !== 'o') {
            $this->stdout("❌ Importation annulée.\n");
            return ExitCode::OK;
        }
        
        // Supprimer les critères existants
        $this->stdout("\n🗑️  Suppression des critères existants...\n");
        Critere::deleteAll();
        
        // Importer les nouveaux critères
        $this->stdout("💾 Import en cours...\n");
        $imported = 0;
        $errors = 0;
        
        foreach ($criteres as $data) {
            $critere = new Critere($data);
            
            if ($critere->save()) {
                $imported++;
                $this->stdout("  ✓ {$critere->code} - {$critere->titre}\n");
            } else {
                $errors++;
                $this->stderr("  ✗ Erreur pour {$data['code']}: " . print_r($critere->errors, true) . "\n");
            }
        }
        
        $this->stdout("\n✅ Import terminé !\n");
        $this->stdout("   - Importés : {$imported}\n");
        $this->stdout("   - Erreurs : {$errors}\n");
        
        return ExitCode::OK;
    }

    /**
     * Parse le fichier Markdown pour extraire les critères
     *
     * @param string $contenu
     * @return array
     */
    protected function parserCriteres($contenu)
    {
        $criteres = [];
        $categorieActuelle = null;
        $ordre = 1;
        
        // Découper en lignes
        $lignes = explode("\n", $contenu);
        
        $critereActuel = null;
        $sectionActuelle = null;
        
        foreach ($lignes as $ligne) {
            $ligne = trim($ligne);
            
            // Détecter les catégories (## 5.1 Texte et rédaction)
            if (preg_match('/^## ([\d\.]+) (.+)$/', $ligne, $matches)) {
                $categorieCode = $matches[1];
                $categorieNom = trim($matches[2]);
                
                // Trouver la catégorie dans la BDD
                $categorieActuelle = Categorie::findOne(['code' => $categorieCode]);
                if (!$categorieActuelle) {
                    $this->stderr("⚠️  Catégorie non trouvée : {$categorieCode}\n");
                }
                $ordre = 1; // Reset l'ordre pour chaque catégorie
                continue;
            }
            
            // Détecter les critères (### Langage clair et compréhensible)
            if (preg_match('/^### (.+)$/', $ligne, $matches)) {
                // Sauvegarder le critère précédent si existe
                if ($critereActuel && $categorieActuelle) {
                    $critereActuel['ordre'] = $ordre++;
                    $criteres[] = $critereActuel;
                }
                
                // Nouveau critère
                $critereActuel = [
                    'categorie_id' => $categorieActuelle ? $categorieActuelle->id : null,
                    'code' => $categorieActuelle ? $categorieActuelle->code . '.' . $ordre : (string)$ordre,
                    'titre' => trim($matches[1]),
                    'priorite' => 'recommandee',
                    'wcag' => null,
                    'rgaa' => null,
                    'raweb' => null,
                    'description' => '',
                    'a_verifier' => '',
                    'exemples_valides' => '',
                    'exemples_invalides' => '',
                    'outils_recommandes' => '',
                ];
                $sectionActuelle = 'description';
                continue;
            }
            
            // Détecter les métadonnées
            if ($critereActuel) {
                if (preg_match('/^\*\*Priorité\*\* : 🔴 Critique/', $ligne)) {
                    $critereActuel['priorite'] = 'critique';
                } elseif (preg_match('/^\*\*Priorité\*\* : 🟠 Importante/', $ligne)) {
                    $critereActuel['priorite'] = 'importante';
                } elseif (preg_match('/^\*\*Priorité\*\* : 🟢 Recommandée/', $ligne)) {
                    $critereActuel['priorite'] = 'recommandee';
                } elseif (preg_match('/^\*\*WCAG\*\* : (.+)$/', $ligne, $matches)) {
                    $critereActuel['wcag'] = trim($matches[1]);
                } elseif (preg_match('/^\*\*RGAA\*\* : (.+)$/', $ligne, $matches)) {
                    $critereActuel['rgaa'] = trim($matches[1]);
                } elseif (preg_match('/^\*\*RAWeb\*\* : (.+)$/', $ligne, $matches)) {
                    $critereActuel['raweb'] = trim($matches[1]);
                } elseif (preg_match('/^\*\*À vérifier/', $ligne)) {
                    $sectionActuelle = 'a_verifier';
                } elseif (preg_match('/^\*\*Exemples/', $ligne)) {
                    // Déterminer si valides ou invalides
                    if (strpos($ligne, 'valides') !== false || strpos($ligne, 'conformes') !== false) {
                        $sectionActuelle = 'exemples_valides';
                    } else {
                        $sectionActuelle = 'exemples_invalides';
                    }
                } elseif (preg_match('/^\*\*Outils/', $ligne)) {
                    $sectionActuelle = 'outils_recommandes';
                } elseif (!empty($ligne) && $ligne[0] !== '#' && $ligne[0] !== '-' && strpos($ligne, '**') !== 0) {
                    // Ajouter le contenu à la section actuelle
                    if ($sectionActuelle && isset($critereActuel[$sectionActuelle])) {
                        $critereActuel[$sectionActuelle] .= $ligne . "\n";
                    }
                }
            }
        }
        
        // Sauvegarder le dernier critère
        if ($critereActuel && $categorieActuelle) {
            $critereActuel['ordre'] = $ordre;
            $criteres[] = $critereActuel;
        }
        
        return $criteres;
    }

    /**
     * Affiche la liste des critères importés
     *
     * @return int
     */
    public function actionList()
    {
        $criteres = Critere::find()
            ->with('categorie')
            ->orderBy(['categorie_id' => SORT_ASC, 'ordre' => SORT_ASC])
            ->all();
        
        $this->stdout("📋 Liste des critères (" . count($criteres) . ")\n\n");
        
        $categorieActuelle = null;
        foreach ($criteres as $critere) {
            if ($categorieActuelle !== $critere->categorie->nom) {
                $categorieActuelle = $critere->categorie->nom;
                $this->stdout("\n📁 {$categorieActuelle}\n");
                $this->stdout(str_repeat('-', 60) . "\n");
            }
            
            $priorite = $critere->getPrioriteLabel();
            $this->stdout("  {$critere->code} - {$priorite} - {$critere->titre}\n");
        }
        
        return ExitCode::OK;
    }
}
