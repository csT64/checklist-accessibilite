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

<a href="#main-content" class="skip-link">Aller au contenu principal</a>

<div class="verification-checklist">
    
    <!-- En-tête accessible -->
    <header class="checklist-header">
        <h1><?= Html::encode($this->title) ?></h1>
        
        <?php if ($contenu->url): ?>
        <p class="contenu-url">
            <strong class="label">URL du contenu :</strong>
            <?= Html::a(Html::encode($contenu->url), $contenu->url, [
                'target' => '_blank',
                'rel' => 'noopener noreferrer',
                'title' => 'Ouvrir le contenu dans un nouvel onglet'
            ]) ?>
        </p>
        <?php endif; ?>
        
        <!-- Barre de progression accessible -->
        <div class="progression" role="region" aria-label="Progression de la vérification">
            <p id="progression-label">
                <strong>Progression :</strong> 
                <?= $verifies ?> critères vérifiés sur <?= $total ?> 
                (<?= $progression ?>%)
            </p>
            <div class="barre-progression" role="progressbar" 
                 aria-valuenow="<?= $progression ?>" 
                 aria-valuemin="0" 
                 aria-valuemax="100"
                 aria-labelledby="progression-label">
                <div class="barre-remplie" style="width: <?= $progression ?>%">
                    <?= $progression ?>%
                </div>
            </div>
        </div>
    </header>

    <!-- Filtres accessibles -->
    <aside class="filtres" role="search" aria-label="Filtres de la checklist">
        <h2>Filtrer les critères</h2>
        
        <fieldset>
            <legend>Par priorité</legend>
            <div class="filtre-groupe">
                <?= Html::checkbox('filtre-critique', true, [
                    'id' => 'filtre-critique',
                    'class' => 'filtre-checkbox',
                    'aria-label' => 'Afficher les critères critiques'
                ]) ?>
                <?= Html::label('🔴 Critique', 'filtre-critique') ?>
            </div>
            <div class="filtre-groupe">
                <?= Html::checkbox('filtre-importante', true, [
                    'id' => 'filtre-importante',
                    'class' => 'filtre-checkbox',
                    'aria-label' => 'Afficher les critères importants'
                ]) ?>
                <?= Html::label('🟠 Importante', 'filtre-importante') ?>
            </div>
            <div class="filtre-groupe">
                <?= Html::checkbox('filtre-recommandee', true, [
                    'id' => 'filtre-recommandee',
                    'class' => 'filtre-checkbox',
                    'aria-label' => 'Afficher les critères recommandés'
                ]) ?>
                <?= Html::label('🟢 Recommandée', 'filtre-recommandee') ?>
            </div>
        </fieldset>

        <fieldset>
            <legend>Par statut</legend>
            <div class="filtre-groupe">
                <?= Html::checkbox('filtre-a-verifier', true, [
                    'id' => 'filtre-a-verifier',
                    'class' => 'filtre-checkbox',
                    'aria-label' => 'Afficher les critères à vérifier'
                ]) ?>
                <?= Html::label('🔄 À vérifier', 'filtre-a-verifier') ?>
            </div>
            <div class="filtre-groupe">
                <?= Html::checkbox('filtre-non-conforme', true, [
                    'id' => 'filtre-non-conforme',
                    'class' => 'filtre-checkbox',
                    'aria-label' => 'Afficher les critères non conformes'
                ]) ?>
                <?= Html::label('❌ Non conforme', 'filtre-non-conforme') ?>
            </div>
        </fieldset>
    </aside>

    <!-- Liste des critères -->
    <main id="main-content">
        <?php foreach ($categories as $categorie): ?>
        
        <section class="categorie" id="categorie-<?= $categorie->id ?>" 
                 data-categorie-id="<?= $categorie->id ?>">
            
            <h2><?= Html::encode($categorie->code . ' ' . $categorie->nom) ?></h2>
            
            <ul class="liste-criteres" role="list">
                <?php foreach ($categorie->criteres as $critere): 
                    $verification = $verifications[$critere->id] ?? null;
                ?>
                
                <li class="critere-item <?= $critere->getPrioriteClass() ?>" 
                    data-critere-id="<?= $critere->id ?>"
                    data-priorite="<?= $critere->priorite ?>"
                    data-statut="<?= $verification ? $verification->statut : 'a_verifier' ?>">
                    
                    <?= $this->render('_critere-item', [
                        'critere' => $critere,
                        'verification' => $verification,
                        'contenu' => $contenu,
                    ]) ?>
                    
                </li>
                
                <?php endforeach; ?>
            </ul>
            
        </section>
        
        <?php endforeach; ?>
    </main>

    <!-- Actions globales -->
    <footer class="checklist-footer">
        <?= Html::a('📊 Voir le résumé', ['contenu/view', 'id' => $contenu->id], [
            'class' => 'btn btn-primary',
            'aria-label' => 'Voir le résumé de la vérification'
        ]) ?>
        
        <?= Html::a('← Retour aux contenus', ['contenu/index'], [
            'class' => 'btn btn-secondary'
        ]) ?>
    </footer>

</div>

<?php
// JavaScript accessible pour les filtres
$this->registerJs("
// Gestion des filtres
document.querySelectorAll('.filtre-checkbox').forEach(function(checkbox) {
    checkbox.addEventListener('change', function() {
        appliquerFiltres();
    });
});

function appliquerFiltres() {
    const prioritesFiltrees = {
        critique: document.getElementById('filtre-critique').checked,
        importante: document.getElementById('filtre-importante').checked,
        recommandee: document.getElementById('filtre-recommandee').checked
    };
    
    const statutsFiltres = {
        'a_verifier': document.getElementById('filtre-a-verifier').checked,
        'non_conforme': document.getElementById('filtre-non-conforme').checked
    };
    
    document.querySelectorAll('.critere-item').forEach(function(item) {
        const priorite = item.getAttribute('data-priorite');
        const statut = item.getAttribute('data-statut');
        
        const prioriteVisible = prioritesFiltrees[priorite];
        const statutVisible = statutsFiltres[statut] !== undefined ? statutsFiltres[statut] : true;
        
        if (prioriteVisible && statutVisible) {
            item.style.display = '';
            item.removeAttribute('aria-hidden');
        } else {
            item.style.display = 'none';
            item.setAttribute('aria-hidden', 'true');
        }
    });
}
", \yii\web\View::POS_READY);
?>
