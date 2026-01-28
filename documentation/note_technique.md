# Note Technique : Système de Checklist d'Accessibilité RGAA

## Objectif

Application web Yii2 permettant aux créateurs de contenu de vérifier la conformité d'accessibilité numérique selon le **RGAA** (Référentiel Général d'Amélioration de l'Accessibilité français).

> "La plupart des non-conformités proviennent des contenus publiés" plutôt que de l'infrastructure technique.

## Stack Technique

| Composant | Technologie |
|-----------|-------------|
| Framework | Yii2 PHP |
| Base de données | MySQL |
| Environnement | Debian + Apache |

## Modèle de données

4 entités principales :

1. **Catégories RGAA** (9 catégories : 5.1–5.9)
   - Lisibilité du texte
   - Accessibilité des images
   - etc.

2. **Critères d'accessibilité** (50+ critères répartis par catégorie)

3. **Contenus** à vérifier (pages, articles, formulaires)

4. **Vérifications** traçant le statut de conformité par critère

## Fonctionnalités principales

### Gestion de contenu
- Création d'entrées (pages, articles, formulaires)
- Score en temps réel (% de conformité et progression)

### Interface de vérification
- Badges de priorité (critique/important/recommandé)
- Panneaux d'aide dépliables :
  - Points à vérifier
  - Exemples valides
  - Exemples invalides
  - Outils recommandés
- 4 statuts possibles :
  - Conforme
  - Non-conforme
  - Non-applicable
  - À vérifier
- Sauvegarde instantanée via AJAX

### Calculs automatiques
- Scores de conformité mis à jour dynamiquement
- Pourcentages de progression en temps réel

## Limitations actuelles

- Codes de critères temporaires
- Absence de configuration d'URL "propres" (pretty URLs)

---

## Conception

### Environnement de développement local

| Page | URL |
|------|-----|
| Checklist de vérification | http://checklist-accessibilite.local/index.php?r=verification%2Fchecklist&id=1 |

### Routes principales

| Route | Description |
|-------|-------------|
| `verification/checklist` | Interface de vérification des critères pour un contenu |
| `verification/quick-save` | Endpoint AJAX pour sauvegarder une vérification |

---

## Ressources et Documentation Accessibilité

### Patterns ARIA (W3C)

| Ressource | URL | Description |
|-----------|-----|-------------|
| ARIA Authoring Practices Guide | https://www.w3.org/WAI/ARIA/apg/ | Guide complet des bonnes pratiques ARIA |
| ARIA Patterns | https://www.w3.org/WAI/ARIA/apg/patterns/ | Catalogue des patterns d'interface accessibles |

### Composants Inclusifs

| Ressource | URL | Description |
|-----------|-----|-------------|
| Inclusive Components | https://inclusive-components.design/ | Patterns de composants inclusifs par Heydon Pickering |

### Patterns utilisés dans ce projet

| Pattern | Usage | Documentation |
|---------|-------|---------------|
| Accordion | Catégories de critères repliables | [APG Accordion](https://www.w3.org/WAI/ARIA/apg/patterns/accordion/) |
| Disclosure | Panneaux d'aide dépliables | [APG Disclosure](https://www.w3.org/WAI/ARIA/apg/patterns/disclosure/) |
| Radio Group | Sélection du statut de conformité | [APG Radio Group](https://www.w3.org/WAI/ARIA/apg/patterns/radio/) |
| Alert | Messages de sauvegarde | [APG Alert](https://www.w3.org/WAI/ARIA/apg/patterns/alert/) |
| Toolbar | Barre de filtres | [APG Toolbar](https://www.w3.org/WAI/ARIA/apg/patterns/toolbar/) |

### Raccourcis clavier

| Touche | Action |
|--------|--------|
| `j` / `k` | Critère suivant / précédent |
| `1` | Marquer Conforme |
| `2` | Marquer Non conforme |
| `3` | Marquer Non applicable |
| `4` | Marquer À vérifier |
| `h` | Ouvrir/fermer l'aide du critère courant |
| `Échap` | Fermer panneau ouvert |
| `t` | Basculer thème clair/sombre |

---

## Workflow Git : Synchronisation Claude / Local

### Principe

Claude travaille sur la branche : **`claude/access-github-repo-eLj49`**

### Récupérer les modifications de Claude (sur votre poste local)

**Première fois** (récupérer la branche) :
```bash
git fetch origin
git checkout claude/access-github-repo-eLj49
```

**Les fois suivantes** (mettre à jour) :
```bash
git pull origin claude/access-github-repo-eLj49
```

### Envoyer vos modifications vers Claude

Si vous faites des modifications locales et voulez que Claude les voie :
```bash
git add .
git commit -m "Description de vos modifications"
git push origin claude/access-github-repo-eLj49
```

Ensuite, demandez à Claude de faire un `git pull` pour récupérer vos changements.

### Résumé du flux

```
[Vous]                              [Claude]
  |                                    |
  |  <-- git pull ------------------   | (Claude pousse)
  |                                    |
  |  -- git push ------------------>   | (Vous poussez)
  |                                    |
  |  (demandez à Claude de pull)       | git pull
```

---

*Document créé par Claude - Test de synchronisation Git*
