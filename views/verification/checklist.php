<?php

use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $contenu app\models\Contenu */
/* @var $categories app\models\Categorie[] */
/* @var $verifications array */

$this->registerJsFile('@web/js/checklist.js', ['depends' => [\yii\web\JqueryAsset::class]]);

$this->title = 'Vérification : ' . Html::encode($contenu->titre);
$this->params['breadcrumbs'][] = ['label' => 'Contenus', 'url' => ['contenu/index']];
$this->params['breadcrumbs'][] = ['label' => $contenu->titre, 'url' => ['contenu/view', 'id' => $contenu->id]];
$this->params['breadcrumbs'][] = 'Vérification';

// Calcul de la progression
$total = count($verifications);
$verifies = count(array_filter($verifications, function($v) {
    return $v->statut !== 'a_verifier';
}));
$progression = $total > 0 ? round(($verifies / $total) * 100) : 0;
?>

<!-- Skip Link -->
<a href="#main-content" class="skip-link">Aller au contenu principal</a>

<!-- Toggle Thème -->
<button type="button"
        class="theme-toggle"
        aria-label="Basculer le thème sombre/clair"
        title="Basculer le thème (raccourci: t)">
    <span class="icon-sun" aria-hidden="true">&#9728;</span>
    <span class="icon-moon" aria-hidden="true">&#9790;</span>
</button>

<div class="checklist-container">

    <!-- En-tête -->
    <header class="checklist-header">
        <h1><?= Html::encode($this->title) ?></h1>

        <?php if ($contenu->url): ?>
        <p class="contenu-url">
            <strong>URL :</strong>
            <?= Html::a(Html::encode($contenu->url), $contenu->url, [
                'target' => '_blank',
                'rel' => 'noopener noreferrer',
            ]) ?>
            <span class="visually-hidden">(s'ouvre dans un nouvel onglet)</span>
        </p>
        <?php endif; ?>

        <!-- Barre de progression -->
        <div class="progression" role="region" aria-label="Progression de la vérification">
            <div class="progression-label">
                <strong>Progression</strong>
                <span class="progression-stats" id="progression-stats">
                    <?= $verifies ?>/<?= $total ?> (<?= $progression ?>%)
                </span>
            </div>
            <div class="barre-progression"
                 role="progressbar"
                 aria-valuenow="<?= $progression ?>"
                 aria-valuemin="0"
                 aria-valuemax="100"
                 aria-label="<?= $progression ?>% complété">
                <div class="barre-remplie" style="width: <?= $progression ?>%"></div>
            </div>
        </div>
    </header>

    <!-- Toolbar Filtres (ARIA Pattern) -->
    <div class="filtres-toolbar" role="toolbar" aria-label="Filtres des critères">

        <!-- Filtres par priorité -->
        <div class="filtres-groupe" role="group" aria-label="Filtrer par priorité">
            <span class="filtres-groupe-label" id="label-priorites">Priorité :</span>

            <label class="filtre-pill filtre-active" data-priorite="critique">
                <?= Html::checkbox('filtre-critique', true, [
                    'id' => 'filtre-critique',
                    'class' => 'filtre-checkbox',
                ]) ?>
                <span>Critique</span>
            </label>

            <label class="filtre-pill filtre-active" data-priorite="importante">
                <?= Html::checkbox('filtre-importante', true, [
                    'id' => 'filtre-importante',
                    'class' => 'filtre-checkbox',
                ]) ?>
                <span>Importante</span>
            </label>

            <label class="filtre-pill filtre-active" data-priorite="recommandee">
                <?= Html::checkbox('filtre-recommandee', true, [
                    'id' => 'filtre-recommandee',
                    'class' => 'filtre-checkbox',
                ]) ?>
                <span>Recommandée</span>
            </label>
        </div>

        <!-- Filtres par statut -->
        <div class="filtres-groupe" role="group" aria-label="Filtrer par statut">
            <span class="filtres-groupe-label" id="label-statuts">Statut :</span>

            <label class="filtre-pill filtre-active">
                <?= Html::checkbox('filtre-a-verifier', true, [
                    'id' => 'filtre-a-verifier',
                    'class' => 'filtre-checkbox',
                ]) ?>
                <span>À vérifier</span>
            </label>

            <label class="filtre-pill filtre-active">
                <?= Html::checkbox('filtre-non-conforme', true, [
                    'id' => 'filtre-non-conforme',
                    'class' => 'filtre-checkbox',
                ]) ?>
                <span>Non conforme</span>
            </label>

            <label class="filtre-pill filtre-active">
                <?= Html::checkbox('filtre-conforme', true, [
                    'id' => 'filtre-conforme',
                    'class' => 'filtre-checkbox',
                ]) ?>
                <span>Conforme</span>
            </label>

            <label class="filtre-pill filtre-active">
                <?= Html::checkbox('filtre-non-applicable', true, [
                    'id' => 'filtre-non-applicable',
                    'class' => 'filtre-checkbox',
                ]) ?>
                <span>N/A</span>
            </label>
        </div>
    </div>

    <!-- Contenu principal -->
    <main id="main-content">

        <!-- Accordion des catégories (ARIA Pattern) -->
        <div class="accordion">
            <?php foreach ($categories as $index => $categorie):
                $panelId = 'panel-categorie-' . $categorie->id;
                $headerId = 'header-categorie-' . $categorie->id;
                $criteresCount = count($categorie->criteres);
            ?>

            <div class="accordion-item" data-categorie-id="<?= $categorie->id ?>">

                <!-- Accordion Header -->
                <h2 class="accordion-header">
                    <button type="button"
                            class="accordion-trigger"
                            id="<?= $headerId ?>"
                            aria-expanded="<?= $index === 0 ? 'true' : 'false' ?>"
                            aria-controls="<?= $panelId ?>">
                        <span class="accordion-title">
                            <?= Html::encode($categorie->code . ' ' . $categorie->nom) ?>
                        </span>
                        <span class="accordion-meta">
                            <span class="accordion-count"><?= $criteresCount ?>/<?= $criteresCount ?> critères</span>
                            <svg class="accordion-icon" viewBox="0 0 24 24" aria-hidden="true">
                                <path fill="currentColor" d="M7.41 8.59L12 13.17l4.59-4.58L18 10l-6 6-6-6 1.41-1.41z"/>
                            </svg>
                        </span>
                    </button>
                </h2>

                <!-- Accordion Panel -->
                <div id="<?= $panelId ?>"
                     class="accordion-panel"
                     role="region"
                     aria-labelledby="<?= $headerId ?>"
                     <?= $index !== 0 ? 'hidden' : '' ?>>

                    <div class="accordion-panel-content">
                        <ul class="liste-criteres" role="list">
                            <?php foreach ($categorie->criteres as $critere):
                                $verification = $verifications[$critere->id] ?? null;
                            ?>

                            <li class="critere-item"
                                data-critere-id="<?= $critere->id ?>"
                                data-priorite="<?= Html::encode($critere->priorite) ?>"
                                data-statut="<?= $verification ? Html::encode($verification->statut) : 'a_verifier' ?>">

                                <?= $this->render('_critere-item', [
                                    'critere' => $critere,
                                    'verification' => $verification,
                                    'contenu' => $contenu,
                                ]) ?>

                            </li>

                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>

            </div>

            <?php endforeach; ?>
        </div>

    </main>

    <!-- Footer -->
    <footer class="checklist-footer">
        <?= Html::a('Voir le résumé', ['contenu/view', 'id' => $contenu->id], [
            'class' => 'btn btn-primary',
        ]) ?>

        <?= Html::a('Retour aux contenus', ['contenu/index'], [
            'class' => 'btn btn-secondary'
        ]) ?>
    </footer>

    <!-- Aide raccourcis clavier -->
    <div class="keyboard-help" aria-hidden="true">
        <kbd>j</kbd>/<kbd>k</kbd> naviguer ·
        <kbd>1</kbd>-<kbd>4</kbd> statut ·
        <kbd>h</kbd> aide ·
        <kbd>?</kbd> tous les raccourcis
    </div>

</div>
