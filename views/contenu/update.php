<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Contenu */

$this->title = 'Modifier : ' . $model->titre;
$this->params['breadcrumbs'][] = ['label' => 'Contenus', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->titre, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Modifier';
?>

<div class="contenu-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
