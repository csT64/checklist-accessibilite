<?php

use yii\helpers\Html;
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

<!-- Header avec navigation principale -->
<header class="site-header">
    <div class="header-container">

        <!-- Logo / Titre du site -->
        <div class="site-brand">
            <?= Html::a('<svg class="brand-icon" width="28" height="28" viewBox="0 0 24 24" aria-hidden="true">
                <path fill="currentColor" d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
            </svg><span>Checklist Accessibilité</span>', ['/site/index'], [
                'class' => 'brand-link',
                'aria-label' => 'Accueil - Checklist Accessibilité'
            ]) ?>
        </div>

        <!-- Navigation principale -->
        <nav class="main-nav" role="navigation" aria-label="Navigation principale">
            <ul class="nav-list">
                <li class="nav-item">
                    <?= Html::a('<svg class="nav-icon" width="18" height="18" viewBox="0 0 24 24" aria-hidden="true">
                        <path fill="currentColor" d="M3 13h8V3H3v10zm0 8h8v-6H3v6zm10 0h8V11h-8v10zm0-18v6h8V3h-8z"/>
                    </svg><span>Tableau de bord</span>', ['/site/index'], [
                        'class' => 'nav-link' . (Yii::$app->controller->id === 'site' && Yii::$app->controller->action->id === 'index' ? ' active' : ''),
                    ]) ?>
                </li>
                <li class="nav-item">
                    <?= Html::a('<svg class="nav-icon" width="18" height="18" viewBox="0 0 24 24" aria-hidden="true">
                        <path fill="currentColor" d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-5 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z"/>
                    </svg><span>Mes contenus</span>', ['/contenu/index'], [
                        'class' => 'nav-link' . (Yii::$app->controller->id === 'contenu' ? ' active' : ''),
                    ]) ?>
                </li>
                <li class="nav-item">
                    <?= Html::a('<svg class="nav-icon" width="18" height="18" viewBox="0 0 24 24" aria-hidden="true">
                        <path fill="currentColor" d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/>
                    </svg><span>Nouveau contenu</span>', ['/contenu/create'], [
                        'class' => 'nav-link' . (Yii::$app->controller->id === 'contenu' && Yii::$app->controller->action->id === 'create' ? ' active' : ''),
                    ]) ?>
                </li>
                <li class="nav-item">
                    <?= Html::a('<svg class="nav-icon" width="18" height="18" viewBox="0 0 24 24" aria-hidden="true">
                        <path fill="currentColor" d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z"/>
                    </svg><span>Aide</span>', ['/site/about'], [
                        'class' => 'nav-link' . (Yii::$app->controller->action->id === 'about' ? ' active' : ''),
                    ]) ?>
                </li>
            </ul>
        </nav>

        <!-- Actions utilisateur -->
        <div class="header-actions">
            <?php if (!Yii::$app->user->isGuest): ?>
                <span class="user-greeting">
                    <svg class="nav-icon" width="18" height="18" viewBox="0 0 24 24" aria-hidden="true">
                        <path fill="currentColor" d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                    </svg>
                    <?= Html::encode(Yii::$app->user->identity->username) ?>
                </span>
                <?= Html::beginForm(['/site/logout'], 'post', ['class' => 'logout-form']) ?>
                <?= Html::submitButton('Déconnexion', [
                    'class' => 'btn btn-sm btn-logout',
                    'aria-label' => 'Se déconnecter'
                ]) ?>
                <?= Html::endForm() ?>
            <?php else: ?>
                <?= Html::a('Connexion', ['/site/login'], ['class' => 'btn btn-sm btn-primary']) ?>
            <?php endif; ?>
        </div>

        <!-- Menu mobile -->
        <button type="button" class="mobile-menu-toggle" aria-expanded="false" aria-controls="mobile-nav" aria-label="Menu">
            <svg width="24" height="24" viewBox="0 0 24 24" aria-hidden="true">
                <path fill="currentColor" d="M3 18h18v-2H3v2zm0-5h18v-2H3v2zm0-7v2h18V6H3z"/>
            </svg>
        </button>

    </div>
</header>

<div class="page-wrapper">

    <!-- Fil d'Ariane accessible -->
    <?php if (!empty($this->params['breadcrumbs'])): ?>
    <nav class="breadcrumb-nav" aria-label="Fil d'Ariane">
        <div class="breadcrumb-container">
            <?= Breadcrumbs::widget([
                'links' => $this->params['breadcrumbs'],
                'homeLink' => [
                    'label' => 'Accueil',
                    'url' => ['/site/index'],
                ],
                'options' => ['class' => 'breadcrumb-list'],
                'itemTemplate' => '<li class="breadcrumb-item">{link}</li>',
                'activeItemTemplate' => '<li class="breadcrumb-item active" aria-current="page">{link}</li>',
            ]) ?>
        </div>
    </nav>
    <?php endif; ?>

    <!-- Flash messages accessibles -->
    <?php foreach (Yii::$app->session->getAllFlashes() as $type => $message): ?>
        <?php
        $ariaRole = 'status';
        $alertClass = 'alert-info';
        if ($type === 'error') {
            $ariaRole = 'alert';
            $alertClass = 'alert-error';
        } elseif ($type === 'success') {
            $alertClass = 'alert-success';
        } elseif ($type === 'warning') {
            $ariaRole = 'alert';
            $alertClass = 'alert-warning';
        }
        ?>
        <div class="alert <?= $alertClass ?>" role="<?= $ariaRole ?>">
            <div class="alert-container">
                <?= Html::encode($message) ?>
                <button type="button" class="alert-close" aria-label="Fermer">&times;</button>
            </div>
        </div>
    <?php endforeach; ?>

    <!-- Contenu principal -->
    <main id="main-content" role="main">
        <?= $content ?>
    </main>

</div>

<!-- Footer accessible -->
<footer class="site-footer" role="contentinfo">
    <div class="footer-container">
        <div class="footer-content">
            <p class="footer-copyright">
                &copy; <?= date('Y') ?> Checklist Accessibilité
            </p>
            <p class="footer-compliance">
                Conforme <strong>RGAA 4.1</strong> niveau AA
            </p>
        </div>
        <nav class="footer-nav" aria-label="Liens du pied de page">
            <ul class="footer-links">
                <li><?= Html::a('Mentions légales', ['/site/mentions']) ?></li>
                <li><?= Html::a('Accessibilité', ['/site/accessibilite']) ?></li>
                <li><?= Html::a('Contact', ['/site/contact']) ?></li>
            </ul>
        </nav>
    </div>
</footer>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
