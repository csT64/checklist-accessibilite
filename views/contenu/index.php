<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use app\models\Contenu;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Mes contenus';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="contenu-index">

    <header>
        <h1><?= Html::encode($this->title) ?></h1>
        
        <p>
            <?= Html::a('➕ Créer un nouveau contenu', ['create'], [
                'class' => 'btn btn-primary',
                'aria-label' => 'Créer un nouveau contenu à vérifier'
            ]) ?>
        </p>
    </header>

    <?php Pjax::begin(['id' => 'contenu-grid']); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'layout' => "{summary}\n{items}\n{pager}",
        'tableOptions' => ['class' => 'table'],
        'summary' => '<p><strong>{begin}-{end}</strong> sur <strong>{totalCount}</strong> contenu(s)</p>',
        'columns' => [
            [
                'attribute' => 'titre',
                'format' => 'html',
                'value' => function ($model) {
                    return Html::a(Html::encode($model->titre), ['view', 'id' => $model->id], [
                        'title' => 'Voir le contenu : ' . $model->titre
                    ]);
                },
            ],
            [
                'attribute' => 'type_contenu',
                'label' => 'Type',
                'value' => function ($model) {
                    return $model->getTypeContenuLabel();
                },
            ],
            [
                'attribute' => 'statut',
                'format' => 'html',
                'value' => function ($model) {
                    $class = '';
                    switch ($model->statut) {
                        case Contenu::STATUT_VALIDE:
                            $class = 'statut-conforme';
                            break;
                        case Contenu::STATUT_VERIFIE:
                            $class = 'statut-importante';
                            break;
                        case Contenu::STATUT_EN_COURS:
                            $class = 'statut-a-verifier';
                            break;
                    }
                    return '<span class="' . $class . '">' . Html::encode($model->getStatutLabel()) . '</span>';
                },
            ],
            [
                'attribute' => 'score_conformite',
                'label' => 'Score',
                'format' => 'html',
                'value' => function ($model) {
                    if ($model->score_conformite === null) {
                        return '<span class="aide-texte">Non calculé</span>';
                    }
                    
                    $class = 'score-bon';
                    if ($model->score_conformite < 50) {
                        $class = 'score-mauvais';
                    } elseif ($model->score_conformite < 80) {
                        $class = 'score-moyen';
                    }
                    
                    return '<strong class="' . $class . '">' . number_format($model->score_conformite, 0) . '%</strong>';
                },
            ],
            [
                'attribute' => 'created_at',
                'label' => 'Créé le',
                'format' => ['date', 'php:d/m/Y'],
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'header' => 'Actions',
                'template' => '{view} {verify} {update} {delete}',
                'buttons' => [
                    'verify' => function ($url, $model) {
                        return Html::a('🔍', ['verification/checklist', 'id' => $model->id], [
                            'title' => 'Vérifier l\'accessibilité',
                            'aria-label' => 'Vérifier l\'accessibilité de ' . $model->titre,
                            'class' => 'btn-action',
                        ]);
                    },
                    'view' => function ($url, $model) {
                        return Html::a('👁️', $url, [
                            'title' => 'Voir',
                            'aria-label' => 'Voir ' . $model->titre,
                            'class' => 'btn-action',
                        ]);
                    },
                    'update' => function ($url, $model) {
                        return Html::a('✏️', $url, [
                            'title' => 'Modifier',
                            'aria-label' => 'Modifier ' . $model->titre,
                            'class' => 'btn-action',
                        ]);
                    },
                    'delete' => function ($url, $model) {
                        return Html::a('🗑️', $url, [
                            'title' => 'Supprimer',
                            'aria-label' => 'Supprimer ' . $model->titre,
                            'class' => 'btn-action',
                            'data' => [
                                'confirm' => 'Êtes-vous sûr de vouloir supprimer ce contenu ?',
                                'method' => 'post',
                            ],
                        ]);
                    },
                ],
            ],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>

<style>
.btn-action {
    display: inline-block;
    padding: 0.25rem 0.5rem;
    margin: 0 0.25rem;
    font-size: 1.2rem;
    text-decoration: none;
}

.btn-action:hover,
.btn-action:focus {
    transform: scale(1.2);
}

.score-bon { color: #2e7d32; }
.score-moyen { color: #e65100; }
.score-mauvais { color: #c62828; }

.table {
    width: 100%;
    border-collapse: collapse;
    margin: 1rem 0;
}

.table th,
.table td {
    padding: 0.75rem;
    text-align: left;
    border: 1px solid #ddd;
}

.table th {
    background-color: #f5f5f5;
    font-weight: 600;
}

.table tbody tr:hover {
    background-color: #fafafa;
}
</style>
