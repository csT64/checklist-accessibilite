# NOTE TECHNIQUE : Système de Checklist d'Accessibilité RGAA

**Projet** : checklist-accessibilite  
**Framework** : Yii2 PHP  
**Base de données** : MySQL  
**Environnement** : Debian + Apache  
**Repository** : https://github.com/csT64/checklist-accessibilite  
**Domaine local** : http://checklist-accessibilite.local/

---

## 1. OBJECTIF DU PROJET

Application web permettant aux rédacteurs et éditeurs de contenus CMS de **vérifier systématiquement l'accessibilité** de leurs publications selon le **référentiel RGAA** (Référentiel Général d'Amélioration de l'Accessibilité).

### Problématique
La majorité des non-conformités RGAA proviennent des **contenus publiés** (textes mal structurés, images sans alternative, liens ambigus) et non du socle technique. Cette application permet de prévenir ces erreurs **avant publication**.

### Public cible
- **Rédacteurs** : Création du contenu éditorial
- **Éditeurs** : Intégration et mise en forme dans le CMS
- **Responsables accessibilité** : Suivi de la conformité

---

## 2. ARCHITECTURE TECHNIQUE

### 2.1 Structure MVC (Yii2)

```
/srv/checklist-accessibilite/
├── config/
│   ├── db.php              # Configuration BDD
│   └── web.php             # Configuration application
├── controllers/
│   ├── ContenuController.php       # CRUD des contenus
│   └── VerificationController.php  # Gestion de la checklist
├── models/
│   ├── Contenu.php         # Modèle des contenus à vérifier
│   ├── Categorie.php       # 9 catégories RGAA (5.1 à 5.9)
│   ├── Critere.php         # 50+ critères d'accessibilité
│   ├── Verification.php    # Statuts de vérification par critère
│   └── User.php            # Utilisateurs authentifiés
├── views/
│   ├── contenu/
│   │   ├── index.php       # Liste des contenus
│   │   ├── view.php        # Détail + statistiques
│   │   └── create.php      # Création de contenu
│   └── verification/
│       ├── checklist.php   # Interface principale de vérification
│       └── _critere-item.php  # Carte de critère (composant)
├── web/
│   ├── index.php           # Point d'entrée
│   ├── css/
│   │   └── accessible.css  # Styles accessibles
│   └── js/
│       └── checklist.js    # Interactions AJAX
├── migrations/
│   ├── m260126_100001_create_categorie_table.php
│   ├── m260126_100002_create_critere_table.php
│   ├── m260126_100003_create_contenu_table.php
│   └── m260126_100004_create_verification_table.php
├── data/
│   └── criteres-rgaa.md    # Source des critères (Markdown)
└── commands/
    └── CriteresController.php  # Import des critères
```

---

## 3. MODÈLE DE DONNÉES

### 3.1 Schéma relationnel

```
┌─────────────────┐
│   categorie     │  (9 enregistrements : catégories 5.1 à 5.9)
├─────────────────┤
│ id (PK)         │
│ code            │  Ex: "5.1", "5.2", "5.3"
│ nom             │  Ex: "Texte et rédaction", "Structure et hiérarchie"
│ ordre           │
└─────────────────┘
        │
        │ 1:N
        ▼
┌─────────────────┐
│    critere      │  (50+ enregistrements : critères d'accessibilité)
├─────────────────┤
│ id (PK)         │
│ categorie_id (FK)│ → categorie.id
│ code            │  Ex: "C1", "C2" (identifiant interne)
│ titre           │  Ex: "Langage clair et compréhensible"
│ priorite        │  ENUM('critique', 'importante', 'recommandee')
│ wcag            │  Ex: "3.1.3, 3.1.5"
│ rgaa            │  Ex: "10.1, 10.4"
│ raweb           │  Ex: "8.7"
│ description     │  TEXT - Explication du critère
│ a_verifier      │  TEXT - Points à contrôler
│ exemples_valides│  TEXT - Bonnes pratiques
│ exemples_invalides│ TEXT - Erreurs à éviter
│ outils_recommandes│ TEXT - Outils de test
│ ordre           │
└─────────────────┘
        │
        │ N:1
        ▼
┌─────────────────┐
│     contenu     │  (Contenus à vérifier)
├─────────────────┤
│ id (PK)         │
│ titre           │  Ex: "Page d'accueil", "Article blog X"
│ type            │  Ex: "page", "article", "formulaire"
│ url             │  URL du contenu
│ description     │  TEXT
│ utilisateur_id (FK)│ → user.id (créateur)
│ created_at      │
│ updated_at      │
└─────────────────┘
        │
        │ 1:N
        ▼
┌─────────────────┐
│  verification   │  (Statuts de vérification par critère)
├─────────────────┤
│ id (PK)         │
│ contenu_id (FK) │ → contenu.id
│ critere_id (FK) │ → critere.id
│ statut          │  ENUM('conforme', 'non_conforme', 'non_applicable', 'a_verifier')
│ commentaire     │  TEXT - Observations
│ verificateur_id (FK)│ → user.id (qui a vérifié)
│ created_at      │
│ updated_at      │
└─────────────────┘
        │
        │ UNIQUE KEY (contenu_id, critere_id)
        │ 1 seule vérification par critère et par contenu

┌─────────────────┐
│      user       │  (Utilisateurs)
├─────────────────┤
│ id (PK)         │
│ username        │  UNIQUE
│ password_hash   │
│ auth_key        │
│ email           │
│ created_at      │
│ updated_at      │
└─────────────────┘
```

### 3.2 Données de référence

**9 catégories RGAA** (code, nom, ordre) :
1. 5.1 - Texte et rédaction
2. 5.2 - Structure et hiérarchie
3. 5.3 - Liens
4. 5.4 - Images et visuels
5. 5.5 - Tableaux
6. 5.6 - Couleurs et mise en forme
7. 5.7 - Médias audio et vidéo
8. 5.8 - Formulaires
9. 5.9 - Documents et contenus intégrés

**50+ critères** répartis dans ces catégories, avec pour chacun :
- Priorité (🔴 Critique, 🟠 Importante, 🟢 Recommandée)
- Références WCAG 2.1/2.2, RGAA, RAWeb
- Points de vérification détaillés
- Exemples conformes et non conformes
- Outils recommandés

---

## 4. FONCTIONNALITÉS PRINCIPALES

### 4.1 Gestion des contenus

**Route** : `/index.php?r=contenu/index`

- **Liste** : Affiche tous les contenus avec score de conformité
- **Créer** : Formulaire de création (titre, type, URL, description)
- **Détail** : Vue avec statistiques détaillées :
  - Conformes : X critères
  - Non conformes : Y critères
  - Non applicables : Z critères
  - À vérifier : W critères
  - Score de conformité : XX%
  - Progression : YY% (critères vérifiés / total)
- **Modifier** : Édition des informations
- **Supprimer** : Avec confirmation

### 4.2 Interface de vérification (Checklist)

**Route** : `/index.php?r=verification/checklist&id=X`

Interface principale permettant de vérifier **critère par critère** l'accessibilité d'un contenu.

#### Composants de l'interface

**A. Barre de progression**
- Affichage visuel de la progression (% de critères vérifiés)
- Mise à jour dynamique en temps réel

**B. Filtres**
- **Par priorité** : 🔴 Critique / 🟠 Importante / 🟢 Recommandée
- **Par statut** : 🔄 À vérifier / ❌ Non conforme

**C. Cartes de critères** (une par critère)

Chaque carte contient :

1. **En-tête**
   - Badge de priorité coloré
   - Code + titre du critère
   - Badge "✓ Vérifié" (si critère déjà vérifié)
   - Badge de statut actuel (Conforme/Non conforme/etc.)

2. **Corps**
   - Description du critère
   - Références WCAG/RGAA/RAWeb
   - Bouton "ℹ️ Voir les détails et exemples"

3. **Panneau d'aide** (dépliable)
   - **À vérifier** : Points de contrôle
   - **✅ Exemples conformes** : Bonnes pratiques
   - **❌ Exemples non conformes** : Erreurs courantes
   - **🛠️ Outils recommandés** : Outils de test

4. **Formulaire de vérification**
   - 4 choix radio :
     * ✅ Conforme
     * ❌ Non conforme
     * ⚪ Non applicable
     * 🔄 À vérifier
   - Champ commentaire (optionnel)
   - Bouton "💾 Enregistrer"
   - Message de confirmation en temps réel

### 4.3 Sauvegarde AJAX

**Route** : `/index.php?r=verification/quick-save` (POST)

**Paramètres** :
- `contenu_id` : ID du contenu
- `critere_id` : ID du critère
- `statut` : Statut choisi
- `commentaire` : Texte optionnel
- `_csrf` : Token CSRF

**Réponse JSON** :
```json
{
  "success": true,
  "message": "Enregistré",
  "verification": {
    "id": 123,
    "statut": "conforme",
    "statut_label": "✅ Conforme"
  },
  "stats": {
    "total": 50,
    "conformes": 15,
    "non_conformes": 2,
    "non_applicables": 0,
    "a_verifier": 33,
    "score_conformite": 88.24,
    "progression": 34
  }
}
```

**Comportement** :
- Enregistrement sans rechargement de page
- Mise à jour instantanée de la barre de progression
- Mise à jour des badges visuels
- Persistence en base de données

---

## 5. WORKFLOW UTILISATEUR

### 5.1 Scénario nominal

```
1. Connexion
   └─> /index.php?r=site/login
       (admin / admin par défaut)

2. Création d'un contenu
   └─> /index.php?r=contenu/create
       Titre : "Page d'accueil"
       Type : "Page web"
       URL : https://example.com/
       Description : "Page d'accueil du site"

3. Lancement de la vérification
   └─> /index.php?r=contenu/view&id=1
       Clic sur "🔍 Vérifier l'accessibilité"

4. Vérification critère par critère
   └─> /index.php?r=verification/checklist&id=1
       
       Pour chaque critère :
       a) Lire le titre et la description
       b) Cliquer sur "ℹ️ Voir détails" si besoin
       c) Consulter les points à vérifier
       d) Vérifier le contenu réel
       e) Cocher le statut approprié
       f) Ajouter un commentaire si nécessaire
       g) Cliquer "💾 Enregistrer"
       h) Observer la mise à jour de la progression

5. Consultation des résultats
   └─> Retour à /index.php?r=contenu/view&id=1
       Voir :
       - Score de conformité
       - Statistiques détaillées
       - Liste des non-conformités

6. Corrections et re-vérification
   └─> Corriger le contenu
       Relancer la vérification des critères corrigés
```

---

## 6. ASPECTS TECHNIQUES IMPORTANTS

### 6.1 Authentification

- **Modèle** : `app\models\User` hérite de `yii\db\ActiveRecord`
- **Interface** : Implémente `yii\web\IdentityInterface`
- **Méthodes clés** :
  - `findIdentity()` : Recherche par ID
  - `findByUsername()` : Recherche par username
  - `validatePassword()` : Vérification du mot de passe
  - `setPassword()` : Hash sécurisé avec `password_hash()`

### 6.2 URLs

**Mode standard** (actuel) :
```
/index.php?r=contenu/index
/index.php?r=verification/checklist&id=1
```

**Mode Pretty URLs** (configurable mais non prioritaire) :
```
/contenu/index
/verification/checklist/1
```

Nécessite :
- Configuration `urlManager` dans `config/web.php`
- Fichier `.htaccess` avec `RewriteEngine`
- Apache `AllowOverride All`
- Module `mod_rewrite` activé

### 6.3 JavaScript (checklist.js)

**Événements gérés** :
1. **Panneaux d'aide** : Toggle show/hide avec `hidden` attribute
2. **Formulaires de vérification** : Submit AJAX avec `fetch()`
3. **Filtres** : Change events sur checkboxes
4. **Progression** : Mise à jour dynamique de la barre

**Points critiques** :
- Header `X-Requested-With: XMLHttpRequest` requis pour Yii2
- Token CSRF récupéré depuis `<meta name="csrf-token">`
- Pas de JavaScript inline dans les vues (évite conflits)
- Événements attachés via `addEventListener()` après DOMContentLoaded

### 6.4 CSS (accessible.css)

**Principes d'accessibilité appliqués** :
- Contrastes WCAG AA (4.5:1 minimum)
- Taille de police minimum 16px
- Focus visible sur tous les éléments interactifs
- Pas d'information uniquement par la couleur
- Structure de titres cohérente (H1 → H2 → H3)
- Labels associés aux champs de formulaire
- Attributs ARIA appropriés (role, aria-expanded, aria-live)

### 6.5 Import des critères

**Commande** : `php yii criteres/import`

**Source** : `data/criteres-rgaa.md` (format Markdown)

**Parser** :
- Détecte les catégories (`## 5.1 Titre`)
- Extrait les critères (`### Titre du critère`)
- Parse les métadonnées (Priorité, WCAG, RGAA, RAWeb)
- Extrait les sections (Description, À vérifier, Exemples, Outils)
- Insère en base via `Critere::find()->all()`

**Problème connu** : Les codes générés sont temporaires (`C1`, `C2`, etc.) au lieu des vrais codes RGAA. Cela n'affecte pas le fonctionnement mais pourrait être amélioré.

---

## 7. ÉTAT ACTUEL ET PROBLÈMES RÉSOLUS

### 7.1 ✅ Fonctionnel

- Installation complète (migrations, critères, utilisateur admin)
- CRUD des contenus
- Interface de checklist complète
- Sauvegarde AJAX temps réel
- Filtres par priorité et statut
- Panneaux d'aide dépliables
- Calcul automatique des statistiques
- Indicateurs visuels des critères vérifiés

### 7.2 🔧 Corrections récentes

1. **Migrations BDD** : Ordre de création corrigé (user avant contenu)
2. **Import critères** : Parser amélioré pour extraire tout le contenu
3. **JavaScript conflits** : Suppression du JS inline dans `_critere-item.php`
4. **Filtres** : Logique corrigée (ET au lieu de OU)
5. **ActionQuickSave** : Suppression de la vérification `isAjax` trop stricte

### 7.3 ⚠️ Points d'attention

- **Codes critères** : Temporaires (C1, C2...) au lieu des vrais codes RGAA
- **Pretty URLs** : Non configurées (non prioritaire)
- **Tests unitaires** : Absents (à créer si besoin)
- **Documentation utilisateur** : À compléter

---

## 8. PROCHAINES ÉTAPES POSSIBLES

### Améliorations UX
- Export PDF/Excel des résultats
- Historique des vérifications
- Comparaison entre versions
- Notifications par email

### Améliorations techniques
- API REST pour intégration externe
- Tests automatisés (PHPUnit)
- CI/CD avec GitHub Actions
- Docker pour déploiement

### Fonctionnalités métier
- Gestion des rôles (admin, rédacteur, auditeur)
- Rapports d'audit RGAA complets
- Génération de déclaration d'accessibilité
- Intégration avec outils externes (Pa11y, aXe)

---

## 9. COMMANDES UTILES

### Installation/Réinitialisation
```bash
# Installation complète
chmod +x install-complete.sh
./install-complete.sh

# Import des critères
php yii criteres/import

# Créer un utilisateur
php yii user/create [username] [password] [email]
```

### Maintenance BDD
```bash
# Voir les migrations
php yii migrate/history

# Appliquer les migrations
php yii migrate

# Réinitialiser complètement
chmod +x reset-database.sh
./reset-database.sh
```

### Debug
```bash
# Statistiques BDD
php -r "
\$config = require 'config/db.php';
\$pdo = new PDO(\$config['dsn'], \$config['username'], \$config['password']);
foreach(\$pdo->query('SHOW TABLES')->fetchAll(PDO::FETCH_COLUMN) as \$t) {
    \$n = \$pdo->query(\"SELECT COUNT(*) FROM \$t\")->fetchColumn();
    echo \"\$t: \$n\\n\";
}"

# Logs Apache
tail -f /var/log/apache2/error.log

# Logs Yii2
tail -f runtime/logs/app.log
```

---

## 10. CONTACTS ET RESSOURCES

**Projet** : https://github.com/csT64/checklist-accessibilite  
**Framework** : https://www.yiiframework.com/doc/guide/2.0/en  
**RGAA** : https://accessibilite.numerique.gouv.fr/  
**WCAG** : https://www.w3.org/WAI/WCAG21/quickref/

**Utilisateur par défaut** :
- Username : `admin`
- Password : `admin`
- (À changer en production !)

---

**Dernière mise à jour** : 28 janvier 2026  
**Version** : 1.0  
**Statut** : Fonctionnel en développement local
