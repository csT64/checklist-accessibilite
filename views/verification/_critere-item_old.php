<?php

use yii\helpers\Html;
use yii\helpers\Url;
use app\models\Verification;

/* @var $critere app\models\Critere */
/* @var $verification app\models\Verification */
/* @var $contenu app\models\Contenu */

$verificationId = $verification ? $verification->id : null;
$currentStatut = $verification ? $verification->statut : Verification::STATUT_A_VERIFIER;
?>

<article class="critere">
    
    <header class="critere-header">
        <h3 id="critere-<?= $critere->id ?>">
            <span class="critere-priorite" aria-label="Priorité : <?= $critere->priorite ?>">
                <?= $critere->getPrioriteLabel() ?>
            </span>
            <?= Html::encode($critere->code . ' - ' . $critere->titre) ?>
        </h3>
    </header>

    <div class="critere-body">
        
        <!-- Description -->
        <?php if ($critere->description): ?>
        <div class="critere-description">
            <?= nl2br(Html::encode($critere->description)) ?>
        </div>
        <?php endif; ?>

        <!-- Références -->
        <?php if ($critere->wcag || $critere->rgaa || $critere->raweb): ?>
        <div class="critere-references">
            <p>
                <strong>Références :</strong>
                <?php if ($critere->wcag): ?>
                    <span class="reference">WCAG <?= Html::encode($critere->wcag) ?></span>
                <?php endif; ?>
                <?php if ($critere->rgaa): ?>
                    <span class="reference">RGAA <?= Html::encode($critere->rgaa) ?></span>
                <?php endif; ?>
                <?php if ($critere->raweb): ?>
                    <span class="reference">RAWeb <?= Html::encode($critere->raweb) ?></span>
                <?php endif; ?>
            </p>
        </div>
        <?php endif; ?>

        <!-- Bouton d'aide (ouvre un panneau accessible) -->
        <button type="button" 
                class="btn-aide"
                aria-expanded="false"
                aria-controls="aide-<?= $critere->id ?>"
                data-critere-id="<?= $critere->id ?>">
            <span aria-hidden="true">ℹ️</span>
            Voir les détails et exemples
        </button>

        <!-- Panneau d'aide (caché par défaut) -->
        <div id="aide-<?= $critere->id ?>" 
             class="critere-aide" 
             hidden 
             role="region"
             aria-labelledby="critere-<?= $critere->id ?>"
             tabindex="-1">
            
            <?php if ($critere->a_verifier): ?>
            <section>
                <h4>À vérifier :</h4>
                <div><?= nl2br(Html::encode($critere->a_verifier)) ?></div>
            </section>
            <?php endif; ?>

            <?php if ($critere->exemples_valides): ?>
            <section>
                <h4>✅ Exemples conformes :</h4>
                <div><?= nl2br(Html::encode($critere->exemples_valides)) ?></div>
            </section>
            <?php endif; ?>

            <?php if ($critere->exemples_invalides): ?>
            <section>
                <h4>❌ Exemples non conformes :</h4>
                <div><?= nl2br(Html::encode($critere->exemples_invalides)) ?></div>
            </section>
            <?php endif; ?>

            <?php if ($critere->outils_recommandes): ?>
            <section>
                <h4>🛠️ Outils recommandés :</h4>
                <div><?= nl2br(Html::encode($critere->outils_recommandes)) ?></div>
            </section>
            <?php endif; ?>
        </div>

        <!-- Formulaire de vérification -->
        <form class="verification-form" 
              method="post" 
              action="<?= Url::to(['verification/quick-save']) ?>"
              data-critere-id="<?= $critere->id ?>"
              data-ajax="true">
            
            <?= Html::hiddenInput(Yii::$app->request->csrfParam, Yii::$app->request->csrfToken) ?>
            <?= Html::hiddenInput('contenu_id', $contenu->id) ?>
            <?= Html::hiddenInput('critere_id', $critere->id) ?>

            <fieldset>
                <legend>Statut de conformité</legend>
                
                <div class="statuts-radio">
                    <?php 
                    $statuts = Verification::getStatuts();
                    
                    foreach ($statuts as $value => $label):
                    ?>
                    <div class="radio-groupe">
                        <?= Html::radio('statut', $currentStatut === $value, [
                            'value' => $value,
                            'id' => "statut-{$critere->id}-{$value}",
                            'class' => 'statut-radio',
                        ]) ?>
                        <?= Html::label($label, "statut-{$critere->id}-{$value}") ?>
                    </div>
                    <?php endforeach; ?>
                </div>
            </fieldset>

            <div class="form-groupe">
                <?= Html::label('Commentaire (optionnel)', "commentaire-{$critere->id}") ?>
                <?= Html::textarea('commentaire', $verification ? $verification->commentaire : '', [
                    'id' => "commentaire-{$critere->id}",
                    'class' => 'form-control',
                    'rows' => 3,
                    'aria-describedby' => "aide-commentaire-{$critere->id}"
                ]) ?>
                <span id="aide-commentaire-<?= $critere->id ?>" class="aide-texte">
                    Précisez les points non conformes ou vos observations
                </span>
            </div>

            <button type="submit" class="btn btn-primary btn-enregistrer">
                💾 Enregistrer
            </button>
            
            <span class="statut-sauvegarde" aria-live="polite"></span>
        </form>

    </div>

</article>

<?php
// JavaScript pour le panneau d'aide et l'enregistrement AJAX
$critereIdJs = $critere->id;
$this->registerJs("
// Bouton d'aide
(function() {
    const btn = document.querySelector('.btn-aide[data-critere-id=\"{$critereIdJs}\"]');
    if (!btn) return;
    
    btn.addEventListener('click', function() {
        const aidePanel = document.getElementById('aide-{$critereIdJs}');
        const isExpanded = this.getAttribute('aria-expanded') === 'true';
        
        if (isExpanded) {
            aidePanel.hidden = true;
            this.setAttribute('aria-expanded', 'false');
        } else {
            aidePanel.hidden = false;
            this.setAttribute('aria-expanded', 'true');
            // Déplacer le focus dans le panneau
            aidePanel.focus();
        }
    });
})();

// Sauvegarde AJAX
(function() {
    const form = document.querySelector('.verification-form[data-critere-id=\"{$critereIdJs}\"]');
    if (!form) return;
    
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(form);
        const statusSpan = form.querySelector('.statut-sauvegarde');
        const submitBtn = form.querySelector('.btn-enregistrer');
        
        // Désactiver le bouton pendant l'envoi
        submitBtn.disabled = true;
        statusSpan.textContent = '⏳ Enregistrement...';
        statusSpan.className = 'statut-sauvegarde';
        
        fetch(form.action, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                statusSpan.textContent = '✅ Enregistré';
                statusSpan.className = 'statut-sauvegarde statut-succes';
                
                // Mettre à jour le data-statut de l'item
                const item = form.closest('.critere-item');
                if (item && data.verification) {
                    item.setAttribute('data-statut', data.verification.statut);
                }
                
                // Mettre à jour la progression si disponible
                if (data.stats) {
                    const progressBar = document.querySelector('.barre-remplie');
                    const progressLabel = document.getElementById('progression-label');
                    if (progressBar && progressLabel) {
                        const verifies = data.stats.total - data.stats.a_verifier;
                        const progression = Math.round((verifies / data.stats.total) * 100);
                        progressBar.style.width = progression + '%';
                        progressBar.textContent = progression + '%';
                        progressBar.setAttribute('aria-valuenow', progression);
                        progressLabel.innerHTML = '<strong>Progression :</strong> ' + verifies + ' critères vérifiés sur ' + data.stats.total + ' (' + progression + '%)';
                    }
                }
                
                // Effacer le message après 3 secondes
                setTimeout(() => {
                    statusSpan.textContent = '';
                }, 3000);
            } else {
                statusSpan.textContent = '❌ Erreur : ' + (data.message || 'Erreur inconnue');
                statusSpan.className = 'statut-sauvegarde statut-erreur';
            }
        })
        .catch(error => {
            statusSpan.textContent = '❌ Erreur de connexion';
            statusSpan.className = 'statut-sauvegarde statut-erreur';
            console.error('Erreur:', error);
        })
        .finally(() => {
            submitBtn.disabled = false;
        });
    });
})();
", \yii\web\View::POS_READY);
?>
