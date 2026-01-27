# Système de checklist d'accessibilité des contenus CMS  
**Aligné WCAG 2.1 / 2.2 niveau AA – Référentiel RGAA – Référentiel RAWeb 1**

## 1. Contexte et objectifs

La majorité des non-conformités en accessibilité numérique observées lors des audits RGAA ne proviennent pas du socle technique, mais **des contenus publiés dans le CMS** : textes mal structurés, images sans alternative, liens ambigus, vidéos non sous-titrées, formulaires inaccessibles, etc.

Ce document présente un **système de checklist d'accessibilité à destination des créateurs de contenus** (rédacteurs et éditeurs), conçu pour :

- sécuriser la production de contenus accessibles dès la publication ;
- réduire les écarts RGAA récurrents liés aux pratiques éditoriales ;
- responsabiliser les contributeurs sans leur demander d'expertise technique ;
- s'intégrer dans un workflow CMS existant.

> ⚠️ Ce système ne remplace pas un audit RGAA.  
> Il constitue un **outil opérationnel de prévention** des non-conformités.

---

## 2. Référentiels et périmètre

### 2.1 Référentiels utilisés

Le système est basé sur :
- **WCAG 2.1 / 2.2 – niveau AA** (référence internationale)
- **RGAA (version en vigueur)** pour l'alignement réglementaire français
- **RAWeb 1** (Référentiel Accessibilité Web du Luxembourg) pour des recommandations complémentaires

Pour chaque recommandation :
- le ou les critères WCAG sont indiqués à titre informatif ;
- les critères RGAA correspondants sont référencés ;
- les recommandations spécifiques au RAWeb sont clairement identifiées par la mention **(RAWeb)**.

---

### 2.2 Périmètre couvert

Le système concerne **exclusivement les contenus** produits dans le CMS :

- textes et rédaction
- structure des pages
- liens
- images et visuels
- tableaux
- couleurs et mise en forme
- médias audio / vidéo
- **formulaires et champs de saisie**
- documents et contenus intégrés (PDF, iframe, widgets)

Il **n'inclut pas** :
- le développement front-end ;
- l'accessibilité des composants techniques du CMS ;
- les scripts ou fonctionnalités applicatives.

---

## 3. Profil et responsabilités

Cette checklist s'adresse aux **producteurs de contenus** qui créent et publient des contenus dans le CMS, incluant :

- rédaction et structuration des textes
- choix des mots et formulation des liens
- intégration et mise en forme dans le CMS
- ajout d'images et de médias avec leurs alternatives
- création et configuration de formulaires
- publication de documents

> Le producteur de contenus est responsable de l'accessibilité éditoriale sans avoir besoin d'expertise technique en développement.

---

## 4. Système de priorisation

Chaque règle est pondérée selon son impact sur l'accessibilité.

### 🔴 Critique
- Non-respect = non-conformité majeure RGAA
- Impact direct sur l'accès à l'information
- À vérifier systématiquement avant publication

### 🟠 Importante
- Impact significatif sur l'usage
- Peut dégrader fortement l'expérience utilisateur
- À vérifier dès que le type de contenu est concerné

### 🟢 Recommandée
- Amélioration du confort et de la compréhension
- Bonnes pratiques éditoriales

---

## 5. Checklist détaillée par type de contenu

---

## 5.1 Texte et rédaction

### Langage clair et compréhensible
- **Profil** : Producteur de contenus
- **Priorité** : 🔴 Critique
- **WCAG** : 3.1.3, 3.1.5 (info)
- **RGAA** : 10.1, 10.4

**À vérifier :**
- phrases courtes et structurées ;
- vocabulaire simple ;
- acronymes et sigles explicités à la première occurrence ;
- évitement du jargon non expliqué.

**Exemples :**
- ✅ "TVA (Taxe sur la Valeur Ajoutée)"
- ❌ "TVA" sans explication à la première mention

---

### Information non portée uniquement par la forme
- **Profil** : Producteur de contenus
- **Priorité** : 🔴 Critique
- **WCAG** : 1.3.3
- **RGAA** : 10.9

**À vérifier :**
- aucune information transmise uniquement par :
  - la couleur ;
  - le gras ;
  - la position dans la page.

**Exemples :**
- ❌ "Les champs en rouge sont obligatoires"
- ✅ "Les champs marqués d'un astérisque (*) sont obligatoires"

---

### Usage limité des majuscules
- **Profil** : Producteur de contenus
- **Priorité** : 🟠 Importante
- **WCAG** : 1.4.8 (info)

**À vérifier :**
- pas de paragraphes entiers en majuscules ;
- majuscules réservées aux sigles ou titres courts.

---

### Langue des passages en langue étrangère (RAWeb)
- **Profil** : Producteur de contenus
- **Priorité** : 🟢 Recommandée
- **WCAG** : 3.1.2
- **RAWeb** : 8.7

**À vérifier :**
- signaler à l'éditeur les passages de texte en langue étrangère nécessitant l'ajout d'un attribut `lang` ;
- indiquer la langue concernée (ex : anglais, allemand).

**Note :** Cette vérification relève principalement de l'intégration technique, mais le rédacteur doit identifier les passages concernés.

---

## 5.2 Structure et hiérarchie

### Hiérarchie correcte des titres
- **Profil** : Producteur de contenus
- **Priorité** : 🔴 Critique
- **WCAG** : 1.3.1, 2.4.6
- **RGAA** : 9.1, 9.2

**À vérifier dans le CMS :**
- un seul titre de niveau 1 (H1) ;
- ordre logique des niveaux (H1 → H2 → H3…) ;
- titres utilisés pour structurer, pas pour styliser.

**Exemples :**
- ✅ H1 "Article" → H2 "Première partie" → H3 "Sous-section"
- ❌ H1 "Article" → H3 "Première partie" (saute H2)

---

### Listes correctement balisées
- **Profil** : Producteur de contenus
- **Priorité** : 🟠 Importante
- **WCAG** : 1.3.1
- **RGAA** : 9.3

**À vérifier :**
- utilisation des outils de liste du CMS ;
- pas de listes simulées par des tirets ou retours ligne.

**Exemples :**
- ❌ Utiliser "- Point 1" avec des retours à la ligne
- ✅ Utiliser le bouton "liste à puces" du CMS

---

## 5.3 Liens

### Texte de lien explicite
- **Profil** : Producteur de contenus
- **Priorité** : 🔴 Critique
- **WCAG** : 2.4.4
- **RGAA** : 6.1

**À vérifier :**
- le lien est compréhensible soit par son intitulé seul, soit grâce au contexte du lien (titre précédent, paragraphe, liste, cellule de tableau) ;
- éviter les intitulés génériques isolés ("cliquer ici") sans contexte.

**Contexte de lien** : Le RGAA autorise des intitulés comme "Lire la suite" ou "Télécharger" si le contexte permet de comprendre la destination :
- contenu du titre (h1-h6) précédant le lien
- contenu du paragraphe contenant le lien
- contenu de l'item de liste contenant le lien
- contenu d'une cellule de tableau associée

**Exemples valides :**
```html
<article>
  <h2>Guide d'accessibilité numérique</h2>
  <p>Ce guide présente les bonnes pratiques...</p>
  <a href="...">Lire la suite</a>  ✅ (contexte fourni par le titre)
</article>
```

**Exemples non conformes :**
- ❌ "Cliquer ici" isolé sans contexte
- ❌ Liste de plusieurs liens "En savoir plus" sans contexte distinct

**Alternative avec aria-label :**
Si le contexte visuel n'est pas suffisamment structuré dans le code, utiliser un attribut `aria-label` :
```html
<a href="..." aria-label="Télécharger le guide d'accessibilité">Télécharger</a>
```
Note : Signaler ce besoin à l'équipe technique.

---

### Indication du type de lien
- **Profil** : Producteur de contenus
- **Priorité** : 🟠 Importante
- **WCAG** : 3.2.2
- **RGAA** : 6.2

**À vérifier :**
- indication des fichiers (PDF, DOC, XLS…) et de leur poids ;
- indication des liens externes ou ouvertures forcées dans un nouvel onglet.

**Exemples :**
- ✅ "Consulter le rapport annuel (PDF, 5 Mo)"
- ✅ "Visiter le site partenaire (nouvelle fenêtre)"

---

### Liens images et intitulés
- **Profil** : Producteur de contenus
- **Priorité** : 🔴 Critique
- **WCAG** : 2.4.4, 1.1.1
- **RGAA** : 6.1

**À vérifier :**
- si un lien contient uniquement une image, l'alternative de l'image doit décrire la fonction du lien ;
- si un lien contient image + texte, vérifier la cohérence.

**Exemples :**
- Image seule dans un lien : alt="Télécharger le formulaire PDF"
- Image + texte : éviter la redondance entre alt et texte

---

## 5.4 Images et visuels

### Texte alternatif pertinent
- **Profil** : Producteur de contenus
- **Priorité** : 🔴 Critique
- **WCAG** : 1.1.1
- **RGAA** : 1.1, 1.2

**À vérifier :**
- image informative → texte alternatif descriptif ;
- image décorative → texte alternatif vide ;
- absence de textes génériques ("image", "photo").

**Exemples :**
- ❌ alt="image"
- ✅ alt="Graphique montrant l'évolution des ventes 2024"
- ✅ alt="" (pour image purement décorative)

---

### Texte présent dans l'image
- **Profil** : Producteur de contenus
- **Priorité** : 🔴 Critique
- **WCAG** : 1.4.5
- **RGAA** : 3.1

**À vérifier :**
- texte de l'image repris dans le contenu ou le texte alternatif ;
- éviter les images contenant des informations essentielles sous forme de texte.

**Exemples :**
- Une infographie avec du texte doit avoir une description détaillée accessible
- Privilégier le texte stylé CSS plutôt qu'une image de texte

---

### Images complexes et description détaillée
- **Profil** : Producteur de contenus
- **Priorité** : 🟠 Importante
- **WCAG** : 1.1.1
- **RGAA** : 1.6, 1.7

**À vérifier :**
- pour graphiques, schémas, infographies : prévoir une description détaillée ;
- description adjacente ou accessible via un lien.

**Exemples :**
- Graphique : fournir un tableau de données équivalent
- Organigramme : décrire la structure et les relations

---

## 5.5 Tableaux

### Usage pertinent des tableaux
- **Profil** : Producteur de contenus
- **Priorité** : 🟠 Importante
- **WCAG** : 1.3.1
- **RGAA** : 5.1

**À vérifier :**
- tableaux utilisés uniquement pour des données tabulaires ;
- jamais pour la mise en page.

---

### En-têtes de tableaux
- **Profil** : Producteur de contenus
- **Priorité** : 🔴 Critique
- **WCAG** : 1.3.1
- **RGAA** : 5.6

**À vérifier :**
- présence d'en-têtes de lignes et/ou colonnes ;
- identification claire dans l'éditeur (utiliser les options "En-tête" du CMS).

---

### Titre de tableau
- **Profil** : Producteur de contenus
- **Priorité** : 🟠 Importante
- **WCAG** : 1.3.1
- **RGAA** : 5.4

**À vérifier :**
- chaque tableau de données possède un titre pertinent ;
- le titre décrit le contenu ou la fonction du tableau.

---

### Tableaux de données complexes (RAWeb)
- **Profil** : Producteur de contenus
- **Priorité** : 🟢 Recommandée
- **WCAG** : 1.3.1
- **RAWeb** : 5.7

**À vérifier :**
- pour les tableaux avec en-têtes sur plusieurs niveaux, signaler la complexité à l'équipe technique ;
- privilégier la simplification du tableau si possible.

**Note :** Les tableaux très complexes nécessitent une implémentation technique avancée (attributs scope, headers). Le rédacteur doit identifier ces cas.

---

## 5.6 Couleurs et mise en forme

### Contraste du texte
- **Profil** : Producteur de contenus
- **Priorité** : 🔴 Critique
- **WCAG** : 1.4.3
- **RGAA** : 3.2

**À vérifier :**
- lisibilité suffisante du texte sur son fond ;
- vigilance sur les textes superposés aux images.

**Seuils minimaux :**
- Texte normal : contraste 4.5:1
- Texte de grande taille : contraste 3:1

**Outils recommandés :** Colour Contrast Analyser, WebAIM Contrast Checker

---

### Information indépendante de la couleur
- **Profil** : Producteur de contenus
- **Priorité** : 🔴 Critique
- **WCAG** : 1.4.1
- **RGAA** : 3.3

**À vérifier :**
- aucune information véhiculée uniquement par la couleur ;
- toujours doubler avec une forme, un texte, un pictogramme.

**Exemples :**
- ❌ "Les liens en rouge sont obligatoires"
- ✅ "Les liens marqués d'un astérisque (*) et en rouge sont obligatoires"

---

### Mise en forme du texte
- **Profil** : Producteur de contenus
- **Priorité** : 🟢 Recommandée
- **WCAG** : 1.4.8

**À vérifier :**
- éviter la justification du texte (aligné à gauche de préférence) ;
- espacement entre les lignes suffisant (1.5 minimum) ;
- largeur de ligne raisonnable (60-80 caractères).

---

## 5.7 Médias audio et vidéo

### Sous-titres pour les vidéos
- **Profil** : Producteur de contenus
- **Priorité** : 🔴 Critique
- **WCAG** : 1.2.2
- **RGAA** : 4.1

**À vérifier :**
- sous-titres synchronisés fournis ;
- pas de sous-titres automatiques non corrigés.

---

### Transcription des audios
- **Profil** : Producteur de contenus
- **Priorité** : 🟠 Importante
- **WCAG** : 1.2.1
- **RGAA** : 4.3

**À vérifier :**
- transcription textuelle disponible pour tout contenu audio seul ;
- transcription accessible sur la même page ou via un lien adjacent.

---

### Audiodescription pour les vidéos
- **Profil** : Producteur de contenus
- **Priorité** : 🟠 Importante
- **WCAG** : 1.2.5
- **RGAA** : 4.5

**À vérifier :**
- pour vidéos où l'image apporte des informations essentielles, prévoir une audiodescription ;
- ou fournir une version alternative avec audiodescription.

---

### Contrôle de lecture
- **Profil** : Producteur de contenus
- **Priorité** : 🔴 Critique
- **WCAG** : 1.4.2
- **RGAA** : 4.10

**À vérifier :**
- pas de lecture automatique de vidéo ou audio avec son ;
- si lecture automatique : possibilité de mettre en pause/arrêter.

---

## 5.8 Formulaires

### Étiquettes (labels) présentes et pertinentes
- **Profil** : Producteur de contenus
- **Priorité** : 🔴 Critique
- **WCAG** : 3.3.2, 1.3.1, 2.4.6
- **RGAA** : 11.1, 11.2

**À vérifier :**
- chaque champ possède une étiquette visible ;
- l'étiquette décrit clairement le type de donnée attendu ;
- l'étiquette est à proximité immédiate du champ ;
- l'étiquette est techniquement associée au champ (vérifier en cliquant sur l'étiquette : le focus doit aller sur le champ).

**Exemples :**
- ✅ "Nom de famille"
- ✅ "Adresse e-mail"
- ❌ Absence d'étiquette, uniquement un placeholder

**Important :** Ne jamais utiliser uniquement le placeholder comme étiquette. Le placeholder disparaît lors de la saisie.

---

### Indication des champs obligatoires
- **Profil** : Producteur de contenus
- **Priorité** : 🔴 Critique
- **WCAG** : 3.3.2
- **RGAA** : 11.10

**À vérifier :**
- les champs obligatoires sont clairement identifiés visuellement ;
- la méthode d'identification est explicitée en début de formulaire ;
- ne pas se baser uniquement sur la couleur ou un symbole sans explication.

**Exemples :**
- ✅ Mention en début de formulaire : "Les champs marqués d'un astérisque (*) sont obligatoires"
- ✅ Texte "(obligatoire)" dans l'étiquette
- ❌ Astérisque sans explication
- ❌ Champs en rouge sans autre indication

**Alternative recommandée :** Indiquer plutôt les champs facultatifs (souvent moins nombreux) avec la mention "(facultatif)".

---

### Format de données attendu
- **Profil** : Producteur de contenus
- **Priorité** : 🟠 Importante
- **WCAG** : 3.3.2
- **RGAA** : 11.10

**À vérifier :**
- indication du format attendu pour les champs spécifiques (date, téléphone, code postal) ;
- l'indication est visible et proche du champ (dans l'étiquette ou juste en dessous) ;
- privilégier un exemple concret plutôt qu'un format abstrait.

**Exemples :**
- ✅ "Date de naissance (par exemple : 15/03/1990)"
- ✅ "Téléphone (format : 01 23 45 67 89)"
- ❌ "Date (jj/mm/aaaa)" - vocalisation problématique

---

### Regroupement des champs de même nature
- **Profil** : Producteur de contenus
- **Priorité** : 🔴 Critique
- **WCAG** : 1.3.1
- **RGAA** : 11.5, 11.6

**À vérifier :**
- les groupes de cases à cocher ou boutons radio sont regroupés visuellement ;
- présence d'un titre de groupe (légende) décrivant la nature commune des champs ;
- signaler à l'équipe technique les groupes nécessitant une structuration `<fieldset>` / `<legend>`.

**Exemples :**
- Groupe de boutons radio "Civilité" : Madame / Monsieur / Autre
- Groupe de cases à cocher "Centres d'intérêt" : Sport / Culture / Voyage

**Important :** Ne pas confondre le titre du groupe (légende) avec l'étiquette de chaque champ individuel.

---

### Messages d'erreur explicites et associés
- **Profil** : Producteur de contenus
- **Priorité** : 🔴 Critique
- **WCAG** : 3.3.1, 3.3.3
- **RGAA** : 11.10, 11.11

**À vérifier :**
- présence d'un message d'erreur clair en cas de saisie invalide ;
- le message indique le champ en erreur et la nature de l'erreur ;
- le message suggère une correction si possible ;
- le message est visuellement proche du champ concerné ;
- signaler à l'équipe technique que le message doit être techniquement relié au champ.

**Exemples :**
- ✅ "Le champ 'Adresse e-mail' est invalide. Veuillez saisir une adresse au format nom@exemple.fr"
- ❌ "Erreur" (trop vague)
- ❌ "Champ invalide" (ne précise pas lequel)

**Bonnes pratiques :**
- Éviter le jargon technique ("Erreur 404", "Invalid input")
- Ton constructif et bienveillant
- Ne pas utiliser uniquement la couleur rouge pour signaler l'erreur

---

### Récapitulatif des erreurs
- **Profil** : Producteur de contenus
- **Priorité** : 🟠 Importante
- **WCAG** : 3.3.1
- **RGAA** : 11.10

**À vérifier :**
- en cas d'erreurs multiples à la soumission, prévoir un récapitulatif en haut du formulaire ;
- le récapitulatif liste toutes les erreurs avec des liens vers les champs concernés ;
- signaler cette nécessité à l'équipe technique.

**Exemple de récapitulatif :**
- "3 erreurs ont été détectées dans votre saisie :"
- "Champ 'E-mail' : format invalide"
- "Champ 'Téléphone' : champ obligatoire non renseigné"
- "Champ 'Date de naissance' : date postérieure à aujourd'hui"

---

### Confirmation pour actions importantes
- **Profil** : Producteur de contenus
- **Priorité** : 🔴 Critique
- **WCAG** : 3.3.4, 3.3.6
- **RGAA** : 11.13

**À vérifier :**
- pour actions engageantes (suppression, transaction financière, modification de données importantes), prévoir :
  - soit une possibilité d'annuler/corriger avant validation définitive ;
  - soit une étape de confirmation explicite ;
  - soit une possibilité de récupération après validation.

**Exemples :**
- Page de récapitulatif avant validation d'une commande
- Bouton "Modifier" sur page de confirmation
- Message "Êtes-vous sûr de vouloir supprimer ?" avec choix Oui/Non

---

### Aide à la saisie et autocomplétion (RAWeb)
- **Profil** : Producteur de contenus
- **Priorité** : 🟢 Recommandée
- **WCAG** : 1.3.5
- **RGAA** : 11.13

**À vérifier :**
- pour les champs standard (nom, prénom, e-mail, téléphone, adresse), suggérer l'activation de l'autocomplétion ;
- signaler à l'équipe technique les champs concernés.

**Champs concernés :**
- Informations personnelles : nom, prénom, civilité
- Coordonnées : e-mail, téléphone, adresse
- Paiement : numéro de carte (si applicable)

**Avantage :** Facilite la saisie pour tous, particulièrement pour les personnes en situation de handicap moteur ou cognitif.

---

### Intitulés de boutons explicites
- **Profil** : Producteur de contenus
- **Priorité** : 🟠 Importante
- **WCAG** : 2.4.6
- **RGAA** : 11.9

**À vérifier :**
- intitulé du bouton de soumission clair et descriptif ;
- éviter les intitulés génériques ambigus.

**Exemples :**
- ✅ "Envoyer ma candidature"
- ✅ "S'inscrire à la newsletter"
- ✅ "Valider ma commande"
- ❌ "Envoyer"
- ❌ "OK"
- ❌ "Valider" (trop vague)

---

### Placeholder : usage et limites
- **Profil** : Producteur de contenus
- **Priorité** : 🟠 Importante
- **WCAG** : 3.3.2

**À vérifier :**
- le placeholder ne remplace JAMAIS l'étiquette ;
- le placeholder peut servir d'exemple de format, mais ne doit pas contenir d'information essentielle ;
- si le placeholder est utilisé, vérifier son contraste (souvent trop faible par défaut).

**Exemples d'usage correct :**
- Étiquette : "Ville" + Placeholder : "Paris"
- Étiquette : "Téléphone" + Placeholder : "01 23 45 67 89"

**Problèmes du placeholder seul :**
- Disparaît lors de la saisie (perte de contexte)
- Contraste souvent insuffisant
- Mal supporté par certaines technologies d'assistance

---

### Navigation clavier dans le formulaire
- **Profil** : Producteur de contenus
- **Priorité** : 🔴 Critique
- **WCAG** : 2.1.1, 2.4.7
- **RGAA** : 11.1

**À vérifier :**
- l'ordre de tabulation est logique et suit l'ordre visuel ;
- tous les champs sont accessibles au clavier ;
- la prise de focus est visuellement identifiable.

**Test simple :** Naviguer dans le formulaire avec la touche Tab uniquement. L'ordre doit être cohérent.

---

## 5.9 Documents et contenus intégrés

### Documents accessibles
- **Profil** : Producteur de contenus
- **Priorité** : 🔴 Critique
- **WCAG** : 1.1.1, 1.3.1
- **RGAA** : 13.3

**À vérifier :**
- PDF balisés et accessibles (structure, alternatives des images) ;
- à défaut, alternative HTML disponible et équivalente ;
- mention du format, du poids et de la langue du document dans le lien de téléchargement.

**Exemples :**
- ✅ "Télécharger le rapport annuel 2024 (PDF accessible, 3 Mo)"
- Si PDF non accessible : ✅ "Consulter le rapport annuel 2024 (version HTML)"

---

### Contenus intégrés (iframe, widgets)
- **Profil** : Producteur de contenus
- **Priorité** : 🟠 Importante
- **WCAG** : 2.4.1, 4.1.2
- **RGAA** : 2.1, 2.2

**À vérifier :**
- présence d'un titre décrivant le contenu de l'iframe ;
- vérifier la pertinence du titre généré automatiquement par le CMS ;
- s'assurer que le contenu intégré est lui-même accessible (ex : carte interactive, vidéo).

---

### Contenus en téléchargement alternatifs (RAWeb)
- **Profil** : Producteur de contenus
- **Priorité** : 🟢 Recommandée
- **RAWeb** : 13.7

**À vérifier :**
- pour tout document téléchargeable, vérifier l'existence d'une version accessible ou d'une alternative HTML ;
- privilégier les formats ouverts et accessibles.

---

## 6. Usage recommandé du système

### 6.1 Intégration dans le workflow

- Utilisation **avant chaque publication**
- Intégration dans :
  - une charte éditoriale ;
  - un guide contributeur CMS ;
  - une formation interne ;
- Réutilisation comme base de contrôle lors des audits RGAA.

### 6.2 Méthode de vérification

**Checklist pour le producteur de contenus :**

1. **Contenu textuel :**
   - Relire avec les critères de clarté et de compréhension
   - Vérifier l'explicité des liens (intitulé ou contexte)
   - S'assurer qu'aucune information ne repose uniquement sur la couleur ou la forme

2. **Structure :**
   - Vérifier la hiérarchie des titres (H1, H2, H3...)
   - Contrôler le balisage des listes et tableaux

3. **Images et médias :**
   - Vérifier les alternatives textuelles
   - Contrôler la présence de sous-titres pour les vidéos

4. **Formulaires :**
   - Vérifier la présence et la clarté des étiquettes
   - Contrôler l'indication des champs obligatoires
   - Tester la navigation au clavier

5. **Contraste et mise en forme :**
   - Vérifier les contrastes avec un outil dédié
   - S'assurer de la lisibilité du texte

### 6.3 Sensibilisation continue

- Sessions de formation régulières
- Partage des bonnes pratiques entre contributeurs
- Retours d'expérience après audits
- Mise à jour de la checklist selon l'évolution des référentiels

---

## 7. Outils recommandés

### 7.1 Vérification du contraste
- **Colour Contrast Analyser** (gratuit, Windows/Mac)
- **WebAIM Contrast Checker** (en ligne)
- Extension navigateur **WAVE** ou **axe DevTools**

### 7.2 Validation de la structure
- Extension navigateur **HeadingsMap** (pour vérifier la hiérarchie des titres)
- **Web Developer Toolbar** (pour visualiser les balises)

### 7.3 Test de navigation clavier
- Utilisation de la touche **Tab** pour naviguer
- **Shift + Tab** pour revenir en arrière
- Vérification que le focus est toujours visible

### 7.4 Lecteur d'écran (test avancé)
- **NVDA** (gratuit, Windows)
- **JAWS** (payant, Windows)
- **VoiceOver** (intégré, Mac/iOS)

---

## 8. Points de vigilance spécifiques

### 8.1 Contenus dynamiques

**Contenus générés automatiquement :**
- Vérifier que les flux RSS, actualités automatiques conservent une structure accessible
- S'assurer que les dates et métadonnées sont explicites

**Contenus mis à jour fréquemment :**
- Maintenir la cohérence de la structuration
- Ne pas négliger l'accessibilité sous prétexte de rapidité de publication

### 8.2 Contenus multilingues

- Indiquer clairement les changements de langue
- Signaler à l'équipe technique les passages nécessitant un attribut `lang`
- Vérifier la pertinence des traductions d'interface (formulaires notamment)

### 8.3 Contenus riches et interactifs

**Carrousels et diaporamas :**
- Prévoir des alternatives textuelles pour les images
- S'assurer de la présence de contrôles accessibles (lecture/pause, navigation)

**Accordéons et onglets :**
- Vérifier que les intitulés sont explicites
- Tester l'accessibilité au clavier

**Note :** Ces éléments nécessitent souvent une intervention technique, le rédacteur/éditeur doit signaler les besoins.

---

## 9. Bonnes pratiques essentielles pour le producteur de contenus

**Priorités absolues :**
1. Rédiger des liens explicites par leur intitulé ou leur contexte
2. Utiliser un langage clair et simple
3. Expliciter les acronymes à la première occurrence
4. Ne jamais baser l'information uniquement sur la couleur
5. Structurer avec les bons niveaux de titres (H1, H2, H3...)
6. Fournir des alternatives textuelles pertinentes aux images
7. Associer les étiquettes aux champs de formulaires
8. Rédiger des messages d'erreur clairs et constructifs

**Réflexes à adopter :**
- Se poser la question : "Cette information est-elle compréhensible par tous ?"
- Pour les liens : vérifier si l'intitulé seul ou le contexte permet de comprendre la destination
- Utiliser les fonctionnalités d'accessibilité du CMS
- Ne jamais laisser un champ alt vide pour une image informative
- Tester régulièrement la navigation au clavier (touche Tab)
- Vérifier le contraste des textes avec un outil dédié
- Signaler les besoins techniques (attributs aria, langues étrangères) à l'équipe de développement

---

## 10. Évolutions possibles

Ce système pourra être décliné en :
- **Checklist ultra-synthèse** (1 page recto-verso) pour affichage en salle
- **Version spécifique par CMS** (WordPress, Drupal, Typo3…) avec captures d'écran
- **Checklist automatisée** via plugin CMS avec alertes contextuelles
- **Support de formation** : module e-learning, atelier pratique
- **Guide de prise en main** avec tutoriels vidéo
- **Tableau de bord** de suivi des conformités par contributeur
- **Version simplifiée** pour contributeurs occasionnels
- **Version experte** avec critères AAA optionnels

---

## 11. Annexes

### 11.1 Glossaire des termes clés

**Alternative textuelle** : Texte de remplacement pour une image, transmettant la même information aux personnes ne pouvant voir l'image.

**Contraste** : Différence de luminosité entre un texte et son arrière-plan, exprimée sous forme de ratio (ex : 4.5:1).

**Description détaillée** : Description longue d'une image complexe (graphique, schéma), complémentaire à l'alternative textuelle courte.

**Étiquette (label)** : Texte décrivant la nature d'un champ de formulaire.

**Technologies d'assistance** : Outils utilisés par les personnes en situation de handicap (lecteurs d'écran, loupes logicielles, claviers adaptés).

### 11.2 Correspondances référentiels

| Type de contenu | WCAG 2.1 | RGAA | RAWeb |
|---|---|---|---|
| Images alternatives | 1.1.1 | 1.1-1.2 | 1.1-1.2 |
| Structure titres | 1.3.1, 2.4.6 | 9.1-9.2 | 9.1-9.2 |
| Contraste | 1.4.3 | 3.2 | 3.2 |
| Formulaires labels | 3.3.2 | 11.1-11.2 | 11.1-11.2 |
| Liens explicites | 2.4.4 | 6.1 | 6.1 |

### 11.3 Ressources complémentaires

**Référentiels officiels :**
- RGAA : https://accessibilite.numerique.gouv.fr/
- WCAG 2.1 : https://www.w3.org/TR/WCAG21/
- RAWeb : https://accessibilite.public.lu/fr/raweb1/

**Guides et tutoriels :**
- AcceDe Web : https://www.accede-web.com/
- WebAIM : https://webaim.org/
- MDN Web Docs Accessibility : https://developer.mozilla.org/fr/docs/Web/Accessibility

**Outils :**
- Liste des outils d'évaluation W3C : https://www.w3.org/WAI/ER/tools/

---

**Version du document :** 2.0 (enrichie avec formulaires et RAWeb)  
**Date de mise à jour :** Janvier 2026  
**Fin du document**
