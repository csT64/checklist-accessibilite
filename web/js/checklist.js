/**
 * JavaScript pour la checklist d'accessibilité
 * Gestion de la sauvegarde AJAX des vérifications
 */

(function() {
    'use strict';
    
    // Récupérer le token CSRF depuis les meta tags
    function getCsrfToken() {
        const meta = document.querySelector('meta[name="csrf-token"]');
        return meta ? meta.getAttribute('content') : '';
    }
    
    function getCsrfParam() {
        const meta = document.querySelector('meta[name="csrf-param"]');
        return meta ? meta.getAttribute('content') : '_csrf';
    }
    
    // Gérer la soumission de tous les formulaires de vérification
    document.querySelectorAll('.verification-form').forEach(function(form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(form);
            const statusSpan = form.querySelector('.statut-sauvegarde');
            const submitBtn = form.querySelector('.btn-enregistrer');
            const critereId = form.getAttribute('data-critere-id');
            
            // Désactiver le bouton pendant l'envoi
            submitBtn.disabled = true;
            statusSpan.textContent = '⏳ Enregistrement...';
            statusSpan.className = 'statut-sauvegarde';
            
            // Ajouter le token CSRF si pas déjà présent
            const csrfParam = getCsrfParam();
            if (!formData.has(csrfParam)) {
                formData.append(csrfParam, getCsrfToken());
            }
            
            // Préparer les données en format URL-encoded
            const params = new URLSearchParams();
            for (let [key, value] of formData.entries()) {
                params.append(key, value);
            }
            
            // Envoyer la requête
            fetch(form.action, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-Requested-With': 'XMLHttpRequest', // Important pour que Yii2 détecte l'AJAX
                },
                body: params.toString(),
                credentials: 'same-origin'
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Erreur HTTP ' + response.status);
                }
                return response.json();
            })
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
                        updateProgression(data.stats);
                    }
                    
                    // Effacer le message après 3 secondes
                    setTimeout(function() {
                        statusSpan.textContent = '';
                    }, 3000);
                } else {
                    statusSpan.textContent = '❌ Erreur : ' + (data.message || 'Erreur inconnue');
                    statusSpan.className = 'statut-sauvegarde statut-erreur';
                    console.error('Erreur serveur:', data);
                }
            })
            .catch(error => {
                statusSpan.textContent = '❌ Erreur de connexion';
                statusSpan.className = 'statut-sauvegarde statut-erreur';
                console.error('Erreur:', error);
            })
            .finally(function() {
                submitBtn.disabled = false;
            });
        });
    });
    
    function appliquerFiltres() {
    // Récupérer l'état des filtres de priorité
    const prioritesFiltrees = {
        critique: document.getElementById('filtre-critique') ? document.getElementById('filtre-critique').checked : true,
        importante: document.getElementById('filtre-importante') ? document.getElementById('filtre-importante').checked : true,
        recommandee: document.getElementById('filtre-recommandee') ? document.getElementById('filtre-recommandee').checked : true
    };
    
    // Récupérer l'état des filtres de statut
    const statutsFiltres = {
        'a_verifier': document.getElementById('filtre-a-verifier') ? document.getElementById('filtre-a-verifier').checked : false,
        'non_conforme': document.getElementById('filtre-non-conforme') ? document.getElementById('filtre-non-conforme').checked : false
    };
    
    // Vérifier si au moins un filtre de statut est coché
    const auMoinsUnStatutCoche = statutsFiltres['a_verifier'] || statutsFiltres['non_conforme'];
    
    console.log('Filtres appliqués:', {
        priorites: prioritesFiltrees,
        statuts: statutsFiltres,
        auMoinsUnStatutCoche: auMoinsUnStatutCoche
    });
    
    let visibleCount = 0;
    let hiddenCount = 0;
    
    // Appliquer les filtres à chaque critère
    document.querySelectorAll('.critere-item').forEach(function(item) {
        const priorite = item.getAttribute('data-priorite');
        const statut = item.getAttribute('data-statut') || 'a_verifier';
        
        // La priorité doit correspondre
        const prioriteVisible = prioritesFiltrees[priorite];
        
        // Le statut doit correspondre SI des filtres de statut sont actifs
        let statutVisible = true;
        if (auMoinsUnStatutCoche) {
            // Si des filtres de statut sont cochés, le critère doit correspondre à au moins un
            statutVisible = statutsFiltres[statut] === true;
        }
        // Si aucun filtre de statut n'est coché, tous les statuts sont visibles
        
        // Le critère est visible SI priorité ET statut correspondent
        const estVisible = prioriteVisible && statutVisible;
        
        if (estVisible) {
            item.style.display = '';
            item.removeAttribute('aria-hidden');
            visibleCount++;
        } else {
            item.style.display = 'none';
            item.setAttribute('aria-hidden', 'true');
            hiddenCount++;
        }
    });
    
    console.log(`Critères: ${visibleCount} visibles, ${hiddenCount} masqués`);
    
    // Mettre à jour un compteur si présent
    const compteur = document.getElementById('compteur-filtres');
    if (compteur) {
        compteur.textContent = `${visibleCount} critère(s) affiché(s)`;
    }
}

/**
 * Alternative : Filtrage avec logique OU pour les statuts
 * (affiche si critère correspond à AU MOINS UN statut coché)
 */
function appliquerFiltresAlternatif() {
    const prioritesFiltrees = {
        critique: document.getElementById('filtre-critique')?.checked ?? true,
        importante: document.getElementById('filtre-importante')?.checked ?? true,
        recommandee: document.getElementById('filtre-recommandee')?.checked ?? true
    };
    
    const statutsFiltres = {
        'a_verifier': document.getElementById('filtre-a-verifier')?.checked ?? false,
        'non_conforme': document.getElementById('filtre-non-conforme')?.checked ?? false
    };
    
    // Si aucun statut n'est coché, on affiche tous les statuts
    const aucunStatutCoche = !statutsFiltres['a_verifier'] && !statutsFiltres['non_conforme'];
    
    document.querySelectorAll('.critere-item').forEach(function(item) {
        const priorite = item.getAttribute('data-priorite');
        const statut = item.getAttribute('data-statut') || 'a_verifier';
        
        const prioriteOk = prioritesFiltrees[priorite];
        const statutOk = aucunStatutCoche || statutsFiltres[statut];
        
        if (prioriteOk && statutOk) {
            item.style.display = '';
            item.removeAttribute('aria-hidden');
        } else {
            item.style.display = 'none';
            item.setAttribute('aria-hidden', 'true');
        }
    });
}

/**
 * Initialisation des filtres
 * À appeler au chargement de la page
 */
function initFiltres() {
    console.log('Initialisation des filtres...');
    
    // Vérifier que les éléments existent
    const filtres = [
        'filtre-critique',
        'filtre-importante', 
        'filtre-recommandee',
        'filtre-a-verifier',
        'filtre-non-conforme'
    ];
    
    filtres.forEach(id => {
        const element = document.getElementById(id);
        if (element) {
            console.log(`✅ Filtre ${id} trouvé`);
            
            // Attacher l'événement
            element.addEventListener('change', function() {
                console.log(`Filtre ${id} modifié: ${this.checked}`);
                appliquerFiltres();
            });
        } else {
            console.warn(`⚠️  Filtre ${id} non trouvé`);
        }
    });
    
    
    
    // Fonction pour mettre à jour la barre de progression
    function updateProgression(stats) {
        const progressBar = document.querySelector('.barre-remplie');
        const progressLabel = document.getElementById('progression-label');
        
        if (progressBar && progressLabel && stats) {
            const verifies = stats.total - stats.a_verifier;
            const progression = Math.round((verifies / stats.total) * 100);
            
            progressBar.style.width = progression + '%';
            progressBar.textContent = progression + '%';
            progressBar.setAttribute('aria-valuenow', progression);
            
            progressLabel.innerHTML = '<strong>Progression :</strong> ' + 
                verifies + ' critères vérifiés sur ' + stats.total + 
                ' (' + progression + '%)';
        }
    }
    
    // Gestion des panneaux d'aide
    document.querySelectorAll('.btn-aide').forEach(function(btn) {
        btn.addEventListener('click', function() {
            const critereId = this.getAttribute('data-critere-id');
            const aidePanel = document.getElementById('aide-' + critereId);
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
    });
    
    // Gestion des filtres
    document.querySelectorAll('.filtre-checkbox').forEach(function(checkbox) {
        checkbox.addEventListener('change', function() {
            appliquerFiltres();
        });
    });
    
   
    
    /**
 * Mise à jour de checklist.js pour gérer les indicateurs visuels
 * Ajoutez cette fonction à la fin de votre checklist.js existant
 */

/**
 * Met à jour les indicateurs visuels après sauvegarde
 */
function updateVisualIndicators(critereId, statut) {
    const item = document.querySelector(`.critere-item[data-critere-id="${critereId}"]`) ||
                 document.querySelector(`article.critere`).closest(`[data-critere-id="${critereId}"]`);
    
    if (!item) return;
    
    // Mettre à jour l'attribut data-statut
    item.setAttribute('data-statut', statut);
    
    // Marquer comme vérifié si ce n'est pas "a_verifier"
    if (statut !== 'a_verifier') {
        item.setAttribute('data-verifie', 'true');
        
        // Ajouter le badge "Vérifié" s'il n'existe pas
        const header = item.querySelector('.critere-header h3');
        if (header && !header.querySelector('.badge-verifie')) {
            const badge = document.createElement('span');
            badge.className = 'badge-verifie';
            badge.setAttribute('aria-label', 'Critère vérifié');
            badge.textContent = '✓ Vérifié';
            header.appendChild(badge);
        }
        
        // Ajouter/mettre à jour le badge de statut
        let statutDiv = item.querySelector('.statut-actuel');
        if (!statutDiv) {
            statutDiv = document.createElement('div');
            statutDiv.className = 'statut-actuel';
            header.parentNode.insertBefore(statutDiv, header.nextSibling);
        }
        
        const labels = {
            'conforme': '✅ Conforme',
            'non_conforme': '❌ Non conforme',
            'non_applicable': '⚪ Non applicable',
            'a_verifier': '🔄 À vérifier'
        };
        
        statutDiv.innerHTML = `<span class="badge-statut badge-statut-${statut}">${labels[statut]}</span>`;
        
    } else {
        // Retirer le badge vérifié si retour à "à vérifier"
        item.removeAttribute('data-verifie');
        const badge = item.querySelector('.badge-verifie');
        if (badge) badge.remove();
        
        const statutDiv = item.querySelector('.statut-actuel');
        if (statutDiv) statutDiv.remove();
    }
    
    // Animation de mise à jour
    item.classList.add('statut-updated');
    setTimeout(() => {
        item.classList.remove('statut-updated');
    }, 1000);
}

/**
 * Modifier la fonction de sauvegarde existante pour appeler updateVisualIndicators
 * Dans votre gestionnaire de soumission de formulaire, après le succès :
 */

// Exemple d'intégration dans le then() existant :
/*
.then(data => {
    if (data.success) {
        statusSpan.textContent = '✅ Enregistré';
        statusSpan.className = 'statut-sauvegarde statut-succes';
        
        // NOUVEAU : Mettre à jour les indicateurs visuels
        const statut = form.querySelector('input[name="statut"]:checked').value;
        const critereId = form.getAttribute('data-critere-id');
        updateVisualIndicators(critereId, statut);
        
        // Mettre à jour la progression
        if (data.stats) {
            updateProgression(data.stats);
        }
        
        // Effacer le message après 3 secondes
        setTimeout(function() {
            statusSpan.textContent = '';
        }, 3000);
    }
})
*/

/**
 * Compteur de critères vérifiés (optionnel)
 * Affiche un résumé en haut de la page
 */
function updateCounterSummary() {
    const total = document.querySelectorAll('.critere-item').length;
    const verifies = document.querySelectorAll('.critere-item[data-verifie="true"]').length;
    const conformes = document.querySelectorAll('.critere-item[data-statut="conforme"]').length;
    const nonConformes = document.querySelectorAll('.critere-item[data-statut="non_conforme"]').length;
    
    // Créer ou mettre à jour le résumé
    let summary = document.getElementById('criteres-summary');
    if (!summary) {
        summary = document.createElement('div');
        summary.id = 'criteres-summary';
        summary.className = 'criteres-summary';
        summary.setAttribute('role', 'status');
        summary.setAttribute('aria-live', 'polite');
        
        const container = document.querySelector('.criteres-liste') || 
                         document.querySelector('main');
        if (container) {
            container.insertBefore(summary, container.firstChild);
        }
    }
    
    summary.innerHTML = `
        <div class="summary-cards">
            <div class="summary-card">
                <span class="summary-number">${verifies}</span>
                <span class="summary-label">Vérifiés</span>
            </div>
            <div class="summary-card summary-success">
                <span class="summary-number">${conformes}</span>
                <span class="summary-label">Conformes</span>
            </div>
            <div class="summary-card summary-error">
                <span class="summary-number">${nonConformes}</span>
                <span class="summary-label">Non conformes</span>
            </div>
            <div class="summary-card">
                <span class="summary-number">${total - verifies}</span>
                <span class="summary-label">Restants</span>
            </div>
        </div>
    `;
}

// Appeler au chargement et après chaque mise à jour
document.addEventListener('DOMContentLoaded', function() {
    updateCounterSummary();
});
    
    
    
    console.log('✅ Checklist JavaScript chargé');
})();
