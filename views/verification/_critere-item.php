<?php

use yii\helpers\Html;
use yii\helpers\Url;
use app\models\Verification;

/* @var $critere app\models\Critere */
/* @var $verification app\models\Verification */
/* @var $contenu app\models\Contenu */

$currentStatut = $verification ? $verification->statut : Verification::STATUT_A_VERIFIER;
$critereId = $critere->id;

// Labels des priorités
$prioriteLabels = [
    'critique' => 'Critique',
    'importante' => 'Importante',
    'recommandee' => 'Recommandée',
];
$prioriteLabel = $prioriteLabels[$critere->priorite] ?? $critere->priorite;
?>

<article class="critere" aria-labelledby="critere-title-<?= $critereId ?>">

    <!-- En-tête du critère -->
    <header class="critere-header">
        <span class="badge-priorite badge-priorite-<?= Html::encode($critere->priorite) ?>"
              aria-label="Priorité: <?= Html::encode($prioriteLabel) ?>">
            <?= Html::encode($prioriteLabel) ?>
        </span>

        <h3 id="critere-title-<?= $critereId ?>">
            <span class="critere-code"><?= Html::encode($critere->code) ?></span>
            <?= Html::encode($critere->titre) ?>
        </h3>
    </header>

    <!-- Description -->
    <?php if ($critere->description): ?>
    <div class="critere-description">
        <?= nl2br(Html::encode($critere->description)) ?>
    </div>
    <?php endif; ?>

    <!-- Références -->
    <?php if ($critere->wcag || $critere->rgaa || $critere->raweb): ?>
    <div class="critere-references">
        <?php if ($critere->wcag): ?>
            <span class="reference-tag">WCAG <?= Html::encode($critere->wcag) ?></span>
        <?php endif; ?>
        <?php if ($critere->rgaa): ?>
            <span class="reference-tag">RGAA <?= Html::encode($critere->rgaa) ?></span>
        <?php endif; ?>
        <?php if ($critere->raweb): ?>
            <span class="reference-tag">RAWeb <?= Html::encode($critere->raweb) ?></span>
        <?php endif; ?>
    </div>
    <?php endif; ?>

    <!-- Bouton d'aide (Disclosure Pattern) -->
    <button type="button"
            class="btn-aide"
            aria-expanded="false"
            aria-controls="aide-<?= $critereId ?>">
        <svg class="btn-aide-icon" width="16" height="16" viewBox="0 0 24 24" aria-hidden="true">
            <path fill="currentColor" d="M7.41 8.59L12 13.17l4.59-4.58L18 10l-6 6-6-6 1.41-1.41z"/>
        </svg>
        Voir les détails et exemples
    </button>

    <!-- Panneau d'aide (Disclosure Content) -->
    <div id="aide-<?= $critereId ?>"
         class="critere-aide"
         hidden
         role="region"
         aria-labelledby="critere-title-<?= $critereId ?>"
         tabindex="-1">

        <?php if ($critere->a_verifier): ?>
        <section>
            <h4>À vérifier</h4>
            <div><?= nl2br(Html::encode($critere->a_verifier)) ?></div>
        </section>
        <?php endif; ?>

        <?php if ($critere->exemples_valides): ?>
        <section>
            <h4>Exemples conformes</h4>
            <div><?= nl2br(Html::encode($critere->exemples_valides)) ?></div>
        </section>
        <?php endif; ?>

        <?php if ($critere->exemples_invalides): ?>
        <section>
            <h4>Exemples non conformes</h4>
            <div><?= nl2br(Html::encode($critere->exemples_invalides)) ?></div>
        </section>
        <?php endif; ?>

        <?php if ($critere->outils_recommandes): ?>
        <section>
            <h4>Outils recommandés</h4>
            <div><?= nl2br(Html::encode($critere->outils_recommandes)) ?></div>
        </section>
        <?php endif; ?>
    </div>

    <!-- Formulaire de vérification -->
    <form class="verification-form"
          method="post"
          action="<?= Url::to(['verification/quick-save']) ?>">

        <?= Html::hiddenInput(Yii::$app->request->csrfParam, Yii::$app->request->csrfToken) ?>
        <?= Html::hiddenInput('contenu_id', $contenu->id) ?>
        <?= Html::hiddenInput('critere_id', $critereId) ?>

        <!-- Radio Group (ARIA Pattern) -->
        <div class="statuts-radiogroup"
             role="radiogroup"
             aria-labelledby="statut-label-<?= $critereId ?>">

            <span id="statut-label-<?= $critereId ?>" class="visually-hidden">
                Statut de conformité pour <?= Html::encode($critere->titre) ?>
            </span>

            <?php
            $statuts = [
                'conforme' => 'Conforme',
                'non_conforme' => 'Non conforme',
                'non_applicable' => 'N/A',
                'a_verifier' => 'À vérifier',
            ];

            foreach ($statuts as $value => $label):
                $radioId = "statut-{$critereId}-{$value}";
                $isChecked = $currentStatut === $value;
            ?>
            <div class="statut-radio-wrapper">
                <input type="radio"
                       class="statut-radio"
                       id="<?= $radioId ?>"
                       name="statut"
                       value="<?= $value ?>"
                       <?= $isChecked ? 'checked' : '' ?>>
                <label class="statut-radio-label" for="<?= $radioId ?>">
                    <span class="statut-indicator" aria-hidden="true"></span>
                    <?= Html::encode($label) ?>
                </label>
            </div>
            <?php endforeach; ?>
        </div>

        <!-- Commentaire -->
        <div class="form-groupe">
            <label for="commentaire-<?= $critereId ?>">
                Commentaire <span class="visually-hidden">(optionnel)</span>
            </label>
            <textarea id="commentaire-<?= $critereId ?>"
                      name="commentaire"
                      class="form-control"
                      rows="2"
                      aria-describedby="aide-commentaire-<?= $critereId ?>"><?= Html::encode($verification ? $verification->commentaire : '') ?></textarea>
            <span id="aide-commentaire-<?= $critereId ?>" class="aide-texte">
                Précisez les points non conformes ou vos observations
            </span>
        </div>

        <!-- Actions -->
        <div class="form-actions">
            <button type="submit" class="btn btn-primary btn-sm btn-enregistrer">
                Enregistrer
            </button>
            <span class="statut-sauvegarde" role="status" aria-live="polite"></span>
        </div>
    </form>

</article>
