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
