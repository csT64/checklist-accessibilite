<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\models\Contenu;

/* @var $this yii\web\View */
/* @var $model app\models\Contenu */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="contenu-form">

    <?php $form = ActiveForm::begin([
        'id' => 'contenu-form',
        'options' => ['class' => 'form-accessible'],
    ]); ?>

    <fieldset>
        <legend>Informations du contenu</legend>

        <?= $form->field($model, 'titre')->textInput([
            'maxlength' => true,
            'aria-required' => 'true',
            'placeholder' => 'Exemple : Page d\'accueil du site'
        ])->label('Titre du contenu <span aria-label="obligatoire">*</span>', ['encode' => false]) ?>

        <?= $form->field($model, 'type_contenu')->dropDownList(
            Contenu::getTypesContenu(),
            ['prompt' => '-- Sélectionner un type --', 'aria-required' => 'true']
        )->label('Type de contenu <span aria-label="obligatoire">*</span>', ['encode' => false]) ?>

        <?= $form->field($model, 'url')->textInput([
            'maxlength' => true,
            'type' => 'url',
            'placeholder' => 'https://exemple.com/ma-page'
        ])->hint('URL du contenu à vérifier (optionnel)') ?>

        <?= $form->field($model, 'description')->textarea([
            'rows' => 4,
            'placeholder' => 'Description du contenu, contexte, remarques...'
        ])->hint('Description optionnelle du contenu') ?>

    </fieldset>

    <?php if (!$model->isNewRecord): ?>
    <fieldset>
        <legend>Statut de vérification</legend>

        <?= $form->field($model, 'statut')->dropDownList(
            Contenu::getStatuts()
        ) ?>

    </fieldset>
    <?php endif; ?>

    <div class="form-groupe form-actions">
        <?= Html::submitButton($model->isNewRecord ? '➕ Créer le contenu' : '💾 Enregistrer', [
            'class' => 'btn btn-primary',
            'aria-label' => $model->isNewRecord ? 'Créer le nouveau contenu' : 'Enregistrer les modifications'
        ]) ?>

        <?= Html::a('Annuler', $model->isNewRecord ? ['index'] : ['view', 'id' => $model->id], [
            'class' => 'btn btn-secondary'
        ]) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<style>
.form-accessible fieldset {
    border: 2px solid #ddd;
    padding: 1.5rem;
    margin-bottom: 1.5rem;
    border-radius: 4px;
}

.form-accessible legend {
    padding: 0 0.5rem;
    font-size: 1.125rem;
    font-weight: 600;
}

.form-actions {
    margin-top: 2rem;
    padding-top: 1rem;
    border-top: 2px solid #ddd;
}

.form-actions .btn {
    margin-right: 1rem;
}

span[aria-label="obligatoire"] {
    color: #c62828;
    font-weight: 700;
}
</style>
