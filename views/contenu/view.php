<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Contenu */
/* @var $stats array */

$this->title = $model->titre;
$this->params['breadcrumbs'][] = ['label' => 'Contenus', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="contenu-view">

    <header>
        <h1><?= Html::encode($this->title) ?></h1>

        <p class="actions">
            <?= Html::a('🔍 Vérifier l\'accessibilité', ['verification/checklist', 'id' => $model->id], [
                'class' => 'btn btn-primary',
                'aria-label' => 'Lancer la vérification d\'accessibilité'
            ]) ?>
            
            <?= Html::a('✏️ Modifier', ['update', 'id' => $model->id], [
                'class' => 'btn btn-secondary'
            ]) ?>
            
            <?= Html::a('🗑️ Supprimer', ['delete', 'id' => $model->id], [
                'class' => 'btn btn-secondary',
                'data' => [
                    'confirm' => 'Êtes-vous sûr de vouloir supprimer ce contenu ? Cette action est irréversible.',
                    'method' => 'post',
                ],
            ]) ?>
        </p>
    </header>

    <section aria-labelledby="details-titre">
        <h2 id="details-titre">Détails du contenu</h2>

        <?= DetailView::widget([
            'model' => $model,
            'options' => ['class' => 'detail-view'],
            'attributes' => [
                'id',
                'titre',
                [
                    'attribute' => 'type_contenu',
                    'value' => $model->getTypeContenuLabel(),
                ],
                [
                    'attribute' => 'url',
                    'format' => 'html',
                    'value' => $model->url ? Html::a(Html::encode($model->url), $model->url, [
                        'target' => '_blank',
                        'rel' => 'noopener noreferrer',
                        'title' => 'Ouvrir dans un nouvel onglet'
                    ]) : '<em>Non renseigné</em>',
                ],
                'description:ntext',
                [
                    'attribute' => 'statut',
                    'value' => $model->getStatutLabel(),
                ],
                [
                    'attribute' => 'score_conformite',
                    'format' => 'html',
                    'value' => $model->score_conformite !== null 
                        ? '<strong>' . number_format($model->score_conformite, 2) . ' %</strong>' 
                        : '<em>Non calculé</em>',
                ],
                [
                    'attribute' => 'utilisateur_id',
                    'label' => 'Créé par',
                    'value' => $model->utilisateur->username ?? 'Inconnu',
                ],
                [
                    'attribute' => 'created_at',
                    'format' => ['date', 'php:d/m/Y à H:i'],
                ],
                [
                    'attribute' => 'updated_at',
                    'format' => ['date', 'php:d/m/Y à H:i'],
                ],
            ],
        ]) ?>
    </section>

    <section aria-labelledby="stats-titre" class="statistiques-section">
        <h2 id="stats-titre">Statistiques de vérification</h2>

        <?php if ($stats['total'] > 0): ?>
        
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-valeur"><?= $stats['total'] ?></div>
                <div class="stat-label">Critères total</div>
            </div>

            <div class="stat-card stat-conforme">
                <div class="stat-valeur"><?= $stats['conforme'] ?></div>
                <div class="stat-label">✅ Conformes</div>
            </div>

            <div class="stat-card stat-non-conforme">
                <div class="stat-valeur"><?= $stats['non_conforme'] ?></div>
                <div class="stat-label">❌ Non conformes</div>
            </div>

            <div class="stat-card">
                <div class="stat-valeur"><?= $stats['non_applicable'] ?></div>
                <div class="stat-label">⚪ Non applicables</div>
            </div>

            <div class="stat-card">
                <div class="stat-valeur"><?= $stats['a_verifier'] ?></div>
                <div class="stat-label">🔄 À vérifier</div>
            </div>

            <div class="stat-card stat-progression">
                <div class="stat-valeur"><?= $stats['progression'] ?> %</div>
                <div class="stat-label">Progression</div>
            </div>
        </div>

        <div class="barre-progression" role="progressbar" 
             aria-valuenow="<?= $stats['progression'] ?>" 
             aria-valuemin="0" 
             aria-valuemax="100"
             aria-label="Progression de la vérification">
            <div class="barre-remplie" style="width: <?= $stats['progression'] ?>%">
                <?= $stats['progression'] ?> %
            </div>
        </div>

        <?php else: ?>
        
        <p class="info-message">
            ℹ️ Aucune vérification n'a encore été effectuée pour ce contenu.
            <?= Html::a('Lancer la vérification', ['verification/checklist', 'id' => $model->id]) ?>
        </p>

        <?php endif; ?>
    </section>

</div>

<style>
.actions {
    margin: 1.5rem 0;
}

.actions .btn {
    margin-right: 0.5rem;
    margin-bottom: 0.5rem;
}

.detail-view {
    width: 100%;
    border-collapse: collapse;
    margin: 1rem 0;
}

.detail-view th,
.detail-view td {
    padding: 0.75rem;
    border: 1px solid #ddd;
    text-align: left;
}

.detail-view th {
    background-color: #f5f5f5;
    font-weight: 600;
    width: 30%;
}

.statistiques-section {
    margin-top: 2rem;
    padding: 1.5rem;
    background-color: #f9f9f9;
    border-radius: 8px;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
    gap: 1rem;
    margin-bottom: 1.5rem;
}

.stat-card {
    padding: 1rem;
    background-color: #fff;
    border: 2px solid #ddd;
    border-radius: 8px;
    text-align: center;
}

.stat-valeur {
    font-size: 2rem;
    font-weight: 700;
    color: #1a1a1a;
}

.stat-label {
    margin-top: 0.5rem;
    font-size: 0.875rem;
    color: #666;
}

.stat-conforme {
    border-color: #2e7d32;
}

.stat-conforme .stat-valeur {
    color: #2e7d32;
}

.stat-non-conforme {
    border-color: #c62828;
}

.stat-non-conforme .stat-valeur {
    color: #c62828;
}

.stat-progression {
    border-color: #0066cc;
}

.stat-progression .stat-valeur {
    color: #0066cc;
}

.info-message {
    padding: 1rem;
    background-color: #e3f2fd;
    border-left: 4px solid #0066cc;
    border-radius: 4px;
}
</style>
