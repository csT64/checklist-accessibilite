<?php

use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use app\assets\AppAsset;

/* @var $this \yii\web\View */
/* @var $content string */

AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>" dir="ltr">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?> - Checklist Accessibilité</title>
    <?php $this->head() ?>
    <link rel="stylesheet" href="<?= Yii::getAlias('@web/css/accessible.css') ?>">
</head>
<body>
<?php $this->beginBody() ?>

<!-- Skip link pour navigation clavier -->
<a href="#main-content" class="skip-link">Aller au contenu principal</a>

<div class="wrap">
    <!-- Navigation principale -->
    <nav class="navbar" role="navigation" aria-label="Navigation principale">
        <div class="container">
            <div class="navbar-header">
                <?= Html::a('Checklist Accessibilité', ['/site/index'], [
                    'class' => 'navbar-brand',
                    'aria-label' => 'Retour à l\'accueil'
                ]) ?>
            </div>
            
            <?php if (!Yii::$app->user->isGuest): ?>
            <ul class="nav navbar-nav">
                <li><?= Html::a('📋 Mes contenus', ['/contenu/index']) ?></li>
                <li><?= Html::a('➕ Nouveau contenu', ['/contenu/create']) ?></li>
            </ul>
            
            <ul class="nav navbar-nav navbar-right">
                <li>
                    <span class="navbar-text">Bonjour, <?= Html::encode(Yii::$app->user->identity->username) ?></span>
                </li>
                <li>
                    <?= Html::beginForm(['/site/logout'], 'post') ?>
                    <?= Html::submitButton('Déconnexion', [
                        'class' => 'btn btn-link logout',
                        'aria-label' => 'Se déconnecter'
                    ]) ?>
                    <?= Html::endForm() ?>
                </li>
            </ul>
            <?php endif; ?>
        </div>
    </nav>

    <!-- Fil d'Ariane accessible -->
    <?= Breadcrumbs::widget([
        'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
        'options' => ['aria-label' => 'Fil d\'Ariane'],
        'tag' => 'nav',
    ]) ?>

    <!-- Flash messages accessibles -->
    <?php foreach (Yii::$app->session->getAllFlashes() as $type => $message): ?>
        <?php
        $ariaRole = 'status';
        $icon = 'ℹ️';
        if ($type === 'error') {
            $ariaRole = 'alert';
            $icon = '❌';
        } elseif ($type === 'success') {
            $icon = '✅';
        } elseif ($type === 'warning') {
            $ariaRole = 'alert';
            $icon = '⚠️';
        }
        ?>
        <div class="alert alert-<?= $type ?>" role="<?= $ariaRole ?>">
            <?= $icon ?> <?= Html::encode($message) ?>
        </div>
    <?php endforeach; ?>

    <!-- Contenu principal -->
    <main id="main-content" class="container" role="main">
        <?= $content ?>
    </main>
</div>

<!-- Footer accessible -->
<footer class="footer" role="contentinfo">
    <div class="container">
        <p>
            &copy; Checklist Accessibilité <?= date('Y') ?>
            | Conforme <strong>RGAA 4.1 niveau AA</strong>
        </p>
    </div>
</footer>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
