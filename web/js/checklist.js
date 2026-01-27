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
    
    function appliquerFiltres() {
        const prioritesFiltrees = {
            critique: document.getElementById('filtre-critique') ? document.getElementById('filtre-critique').checked : true,
            importante: document.getElementById('filtre-importante') ? document.getElementById('filtre-importante').checked : true,
            recommandee: document.getElementById('filtre-recommandee') ? document.getElementById('filtre-recommandee').checked : true
        };
        
        const statutsFiltres = {
            'a_verifier': document.getElementById('filtre-a-verifier') ? document.getElementById('filtre-a-verifier').checked : true,
            'non_conforme': document.getElementById('filtre-non-conforme') ? document.getElementById('filtre-non-conforme').checked : true
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
    
    console.log('✅ Checklist JavaScript chargé');
})();
