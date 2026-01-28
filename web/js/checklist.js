/**
 * Checklist Accessibilité - JavaScript
 *
 * Patterns ARIA implémentés :
 * - Accordion (catégories)
 * - Disclosure (panneaux d'aide)
 * - Radio Group (statuts)
 * - Toolbar (filtres)
 * - Alert (messages)
 *
 * Fonctionnalités :
 * - Raccourcis clavier
 * - Thème sombre
 * - Filtres persistants (localStorage)
 * - Sauvegarde AJAX
 */

(function() {
    'use strict';

    // ===========================================
    // Configuration et État
    // ===========================================

    const STORAGE_KEYS = {
        theme: 'checklist-theme',
        filters: 'checklist-filters',
        accordions: 'checklist-accordions'
    };

    const STATUTS = {
        conforme: '1',
        non_conforme: '2',
        non_applicable: '3',
        a_verifier: '4'
    };

    let currentCritereIndex = -1;
    let critereItems = [];

    // ===========================================
    // Initialisation
    // ===========================================

    document.addEventListener('DOMContentLoaded', function() {
        initTheme();
        initAccordions();
        initDisclosures();
        initRadioGroups();
        initFilters();
        initForms();
        initKeyboardNavigation();
        initCriteresList();

        console.log('Checklist accessible initialisée');
    });

    // ===========================================
    // Thème Sombre
    // ===========================================

    function initTheme() {
        const savedTheme = localStorage.getItem(STORAGE_KEYS.theme);
        if (savedTheme) {
            document.documentElement.setAttribute('data-theme', savedTheme);
        }

        const toggleBtn = document.querySelector('.theme-toggle');
        if (toggleBtn) {
            toggleBtn.addEventListener('click', toggleTheme);
        }
    }

    function toggleTheme() {
        const html = document.documentElement;
        const currentTheme = html.getAttribute('data-theme');
        let newTheme;

        if (currentTheme === 'dark') {
            newTheme = 'light';
        } else if (currentTheme === 'light') {
            newTheme = 'dark';
        } else {
            // Pas de thème défini, on bascule selon la préférence système
            const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
            newTheme = prefersDark ? 'light' : 'dark';
        }

        html.setAttribute('data-theme', newTheme);
        localStorage.setItem(STORAGE_KEYS.theme, newTheme);

        // Annonce pour lecteur d'écran
        announceToScreenReader('Thème ' + (newTheme === 'dark' ? 'sombre' : 'clair') + ' activé');
    }

    // ===========================================
    // Accordion (ARIA Pattern)
    // ===========================================

    function initAccordions() {
        const accordions = document.querySelectorAll('.accordion');

        accordions.forEach(function(accordion) {
            const triggers = accordion.querySelectorAll('.accordion-trigger');

            // Restaurer l'état sauvegardé
            const savedState = getSavedAccordionState();

            triggers.forEach(function(trigger, index) {
                const panelId = trigger.getAttribute('aria-controls');
                const panel = document.getElementById(panelId);

                if (!panel) return;

                // État initial : restaurer ou premier ouvert par défaut
                const isExpanded = savedState[panelId] !== undefined
                    ? savedState[panelId]
                    : index === 0;

                setAccordionState(trigger, panel, isExpanded);

                // Click handler
                trigger.addEventListener('click', function() {
                    const currentlyExpanded = trigger.getAttribute('aria-expanded') === 'true';
                    setAccordionState(trigger, panel, !currentlyExpanded);
                    saveAccordionState();
                });

                // Keyboard navigation
                trigger.addEventListener('keydown', function(e) {
                    handleAccordionKeydown(e, triggers, index);
                });
            });
        });
    }

    function setAccordionState(trigger, panel, expanded) {
        trigger.setAttribute('aria-expanded', expanded.toString());
        if (expanded) {
            panel.removeAttribute('hidden');
        } else {
            panel.setAttribute('hidden', '');
        }
    }

    function handleAccordionKeydown(event, triggers, currentIndex) {
        let newIndex = currentIndex;

        switch (event.key) {
            case 'ArrowDown':
                event.preventDefault();
                newIndex = (currentIndex + 1) % triggers.length;
                break;
            case 'ArrowUp':
                event.preventDefault();
                newIndex = (currentIndex - 1 + triggers.length) % triggers.length;
                break;
            case 'Home':
                event.preventDefault();
                newIndex = 0;
                break;
            case 'End':
                event.preventDefault();
                newIndex = triggers.length - 1;
                break;
            default:
                return;
        }

        triggers[newIndex].focus();
    }

    function getSavedAccordionState() {
        try {
            const saved = localStorage.getItem(STORAGE_KEYS.accordions);
            return saved ? JSON.parse(saved) : {};
        } catch (e) {
            return {};
        }
    }

    function saveAccordionState() {
        const state = {};
        document.querySelectorAll('.accordion-trigger').forEach(function(trigger) {
            const panelId = trigger.getAttribute('aria-controls');
            state[panelId] = trigger.getAttribute('aria-expanded') === 'true';
        });
        localStorage.setItem(STORAGE_KEYS.accordions, JSON.stringify(state));
    }

    // ===========================================
    // Disclosure - Panneaux d'aide (ARIA Pattern)
    // ===========================================

    function initDisclosures() {
        document.querySelectorAll('.btn-aide').forEach(function(btn) {
            btn.addEventListener('click', function() {
                toggleDisclosure(btn);
            });

            btn.addEventListener('keydown', function(e) {
                if (e.key === 'Enter' || e.key === ' ') {
                    e.preventDefault();
                    toggleDisclosure(btn);
                }
            });
        });
    }

    function toggleDisclosure(btn) {
        const panelId = btn.getAttribute('aria-controls');
        const panel = document.getElementById(panelId);

        if (!panel) return;

        const isExpanded = btn.getAttribute('aria-expanded') === 'true';

        btn.setAttribute('aria-expanded', (!isExpanded).toString());

        if (isExpanded) {
            panel.setAttribute('hidden', '');
        } else {
            panel.removeAttribute('hidden');
            // Focus le panneau pour les lecteurs d'écran
            panel.focus();
        }
    }

    function closeAllDisclosures() {
        document.querySelectorAll('.btn-aide[aria-expanded="true"]').forEach(function(btn) {
            toggleDisclosure(btn);
        });
    }

    // ===========================================
    // Radio Group - Statuts (ARIA Pattern)
    // ===========================================

    function initRadioGroups() {
        document.querySelectorAll('.statuts-radiogroup').forEach(function(group) {
            const radios = group.querySelectorAll('.statut-radio');

            radios.forEach(function(radio, index) {
                radio.addEventListener('keydown', function(e) {
                    handleRadioKeydown(e, radios, index);
                });

                radio.addEventListener('change', function() {
                    // Mise à jour visuelle de l'item parent
                    const critereItem = radio.closest('.critere-item');
                    if (critereItem) {
                        critereItem.setAttribute('data-statut', radio.value);
                    }
                });
            });
        });
    }

    function handleRadioKeydown(event, radios, currentIndex) {
        let newIndex = currentIndex;

        switch (event.key) {
            case 'ArrowRight':
            case 'ArrowDown':
                event.preventDefault();
                newIndex = (currentIndex + 1) % radios.length;
                break;
            case 'ArrowLeft':
            case 'ArrowUp':
                event.preventDefault();
                newIndex = (currentIndex - 1 + radios.length) % radios.length;
                break;
            default:
                return;
        }

        radios[newIndex].focus();
        radios[newIndex].checked = true;
        radios[newIndex].dispatchEvent(new Event('change', { bubbles: true }));
    }

    // ===========================================
    // Filtres avec Toolbar (ARIA Pattern)
    // ===========================================

    function initFilters() {
        // Restaurer les filtres sauvegardés
        restoreFilters();

        // Event listeners
        document.querySelectorAll('.filtre-checkbox').forEach(function(checkbox) {
            checkbox.addEventListener('change', function() {
                updateFilterPill(checkbox);
                applyFilters();
                saveFilters();
            });

            // Initialiser l'état visuel des pills
            updateFilterPill(checkbox);
        });

        // Keyboard navigation pour toolbar
        const toolbar = document.querySelector('.filtres-toolbar');
        if (toolbar) {
            const checkboxes = toolbar.querySelectorAll('.filtre-checkbox');
            checkboxes.forEach(function(checkbox, index) {
                checkbox.addEventListener('keydown', function(e) {
                    handleToolbarKeydown(e, checkboxes, index);
                });
            });
        }
    }

    function updateFilterPill(checkbox) {
        const pill = checkbox.closest('.filtre-pill');
        if (pill) {
            pill.classList.toggle('filtre-active', checkbox.checked);
        }
    }

    function handleToolbarKeydown(event, items, currentIndex) {
        let newIndex = currentIndex;

        switch (event.key) {
            case 'ArrowRight':
                event.preventDefault();
                newIndex = (currentIndex + 1) % items.length;
                break;
            case 'ArrowLeft':
                event.preventDefault();
                newIndex = (currentIndex - 1 + items.length) % items.length;
                break;
            case 'Home':
                event.preventDefault();
                newIndex = 0;
                break;
            case 'End':
                event.preventDefault();
                newIndex = items.length - 1;
                break;
            default:
                return;
        }

        items[newIndex].focus();
    }

    function applyFilters() {
        const filters = getActiveFilters();

        document.querySelectorAll('.critere-item').forEach(function(item) {
            const priorite = item.getAttribute('data-priorite');
            const statut = item.getAttribute('data-statut');

            const prioriteVisible = filters.priorites[priorite] !== false;
            const statutVisible = filters.statuts[statut] !== false;

            if (prioriteVisible && statutVisible) {
                item.style.display = '';
                item.removeAttribute('aria-hidden');
            } else {
                item.style.display = 'none';
                item.setAttribute('aria-hidden', 'true');
            }
        });

        // Mettre à jour les compteurs dans les accordions
        updateAccordionCounts();

        // Mettre à jour la liste des critères navigables
        initCriteresList();
    }

    function getActiveFilters() {
        const filters = {
            priorites: {},
            statuts: {}
        };

        // Priorités
        ['critique', 'importante', 'recommandee'].forEach(function(p) {
            const checkbox = document.getElementById('filtre-' + p);
            filters.priorites[p] = checkbox ? checkbox.checked : true;
        });

        // Statuts
        ['a_verifier', 'non_conforme', 'conforme', 'non_applicable'].forEach(function(s) {
            const checkbox = document.getElementById('filtre-' + s.replace('_', '-'));
            filters.statuts[s] = checkbox ? checkbox.checked : true;
        });

        return filters;
    }

    function saveFilters() {
        const filters = getActiveFilters();
        localStorage.setItem(STORAGE_KEYS.filters, JSON.stringify(filters));
    }

    function restoreFilters() {
        try {
            const saved = localStorage.getItem(STORAGE_KEYS.filters);
            if (!saved) return;

            const filters = JSON.parse(saved);

            // Restaurer les priorités
            Object.keys(filters.priorites || {}).forEach(function(p) {
                const checkbox = document.getElementById('filtre-' + p);
                if (checkbox) {
                    checkbox.checked = filters.priorites[p];
                }
            });

            // Restaurer les statuts
            Object.keys(filters.statuts || {}).forEach(function(s) {
                const checkbox = document.getElementById('filtre-' + s.replace('_', '-'));
                if (checkbox) {
                    checkbox.checked = filters.statuts[s];
                }
            });

            // Appliquer les filtres restaurés
            applyFilters();
        } catch (e) {
            console.warn('Erreur restauration filtres:', e);
        }
    }

    function updateAccordionCounts() {
        document.querySelectorAll('.accordion-item').forEach(function(item) {
            const panel = item.querySelector('.accordion-panel');
            if (!panel) return;

            const total = panel.querySelectorAll('.critere-item').length;
            const visible = panel.querySelectorAll('.critere-item:not([aria-hidden="true"])').length;

            const countEl = item.querySelector('.accordion-count');
            if (countEl) {
                countEl.textContent = visible + '/' + total + ' critères';
            }
        });
    }

    // ===========================================
    // Formulaires AJAX
    // ===========================================

    function initForms() {
        document.querySelectorAll('.verification-form').forEach(function(form) {
            form.addEventListener('submit', handleFormSubmit);
        });
    }

    function handleFormSubmit(event) {
        event.preventDefault();

        const form = event.target;
        const formData = new FormData(form);
        const statusSpan = form.querySelector('.statut-sauvegarde');
        const submitBtn = form.querySelector('.btn-enregistrer');

        // État loading
        if (submitBtn) submitBtn.disabled = true;
        if (statusSpan) {
            statusSpan.textContent = 'Enregistrement...';
            statusSpan.className = 'statut-sauvegarde statut-loading';
        }

        fetch(form.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(function(response) {
            if (!response.ok) throw new Error('Erreur HTTP ' + response.status);
            return response.json();
        })
        .then(function(data) {
            if (data.success) {
                if (statusSpan) {
                    statusSpan.textContent = 'Enregistré';
                    statusSpan.className = 'statut-sauvegarde statut-succes';
                }

                // Mettre à jour l'état visuel
                const critereItem = form.closest('.critere-item');
                if (critereItem && data.verification) {
                    critereItem.setAttribute('data-statut', data.verification.statut);
                }

                // Mettre à jour la progression
                if (data.stats) {
                    updateProgression(data.stats);
                }

                // Effacer le message après 3s
                setTimeout(function() {
                    if (statusSpan) statusSpan.textContent = '';
                }, 3000);
            } else {
                throw new Error(data.message || 'Erreur inconnue');
            }
        })
        .catch(function(error) {
            console.error('Erreur:', error);
            if (statusSpan) {
                statusSpan.textContent = 'Erreur: ' + error.message;
                statusSpan.className = 'statut-sauvegarde statut-erreur';
            }
        })
        .finally(function() {
            if (submitBtn) submitBtn.disabled = false;
        });
    }

    function updateProgression(stats) {
        const progressBar = document.querySelector('.barre-remplie');
        const progressLabel = document.querySelector('.progression-stats');

        if (progressBar && stats) {
            const verifies = stats.total - stats.a_verifier;
            const progression = Math.round((verifies / stats.total) * 100);

            progressBar.style.width = progression + '%';
            progressBar.setAttribute('aria-valuenow', progression);

            if (progressLabel) {
                progressLabel.textContent = verifies + '/' + stats.total + ' (' + progression + '%)';
            }
        }
    }

    // ===========================================
    // Navigation Clavier
    // ===========================================

    function initCriteresList() {
        critereItems = Array.from(
            document.querySelectorAll('.critere-item:not([aria-hidden="true"])')
        );
        currentCritereIndex = -1;
    }

    function initKeyboardNavigation() {
        document.addEventListener('keydown', handleGlobalKeydown);
    }

    function handleGlobalKeydown(event) {
        // Ignorer si on est dans un champ de saisie
        if (isInputElement(event.target)) {
            // Échap pour sortir du champ
            if (event.key === 'Escape') {
                event.target.blur();
            }
            return;
        }

        switch (event.key) {
            case 'j':
                event.preventDefault();
                navigateToCritere(1);
                break;

            case 'k':
                event.preventDefault();
                navigateToCritere(-1);
                break;

            case '1':
                event.preventDefault();
                setCurrentCritereStatut('conforme');
                break;

            case '2':
                event.preventDefault();
                setCurrentCritereStatut('non_conforme');
                break;

            case '3':
                event.preventDefault();
                setCurrentCritereStatut('non_applicable');
                break;

            case '4':
                event.preventDefault();
                setCurrentCritereStatut('a_verifier');
                break;

            case 'h':
                event.preventDefault();
                toggleCurrentCritereHelp();
                break;

            case 't':
                event.preventDefault();
                toggleTheme();
                break;

            case 'Escape':
                closeAllDisclosures();
                clearCurrentCritere();
                break;

            case '?':
                event.preventDefault();
                showKeyboardHelp();
                break;
        }
    }

    function isInputElement(element) {
        const tagName = element.tagName.toLowerCase();
        return tagName === 'input' || tagName === 'textarea' || tagName === 'select' ||
               element.isContentEditable;
    }

    function navigateToCritere(direction) {
        if (critereItems.length === 0) {
            initCriteresList();
        }

        if (critereItems.length === 0) return;

        // Retirer la classe du critère actuel
        if (currentCritereIndex >= 0 && critereItems[currentCritereIndex]) {
            critereItems[currentCritereIndex].classList.remove('critere-current');
        }

        // Calculer le nouvel index
        if (currentCritereIndex === -1) {
            currentCritereIndex = direction > 0 ? 0 : critereItems.length - 1;
        } else {
            currentCritereIndex = (currentCritereIndex + direction + critereItems.length) % critereItems.length;
        }

        // Activer le nouveau critère
        const newCritere = critereItems[currentCritereIndex];
        if (newCritere) {
            newCritere.classList.add('critere-current');
            newCritere.scrollIntoView({ behavior: 'smooth', block: 'center' });

            // Ouvrir l'accordion parent si fermé
            const accordionPanel = newCritere.closest('.accordion-panel');
            if (accordionPanel && accordionPanel.hasAttribute('hidden')) {
                const accordionId = accordionPanel.id;
                const trigger = document.querySelector('[aria-controls="' + accordionId + '"]');
                if (trigger) {
                    setAccordionState(trigger, accordionPanel, true);
                    saveAccordionState();
                }
            }

            // Annoncer pour lecteur d'écran
            const title = newCritere.querySelector('h3');
            if (title) {
                announceToScreenReader('Critère ' + (currentCritereIndex + 1) + ' sur ' + critereItems.length + ': ' + title.textContent);
            }
        }
    }

    function setCurrentCritereStatut(statut) {
        if (currentCritereIndex < 0 || !critereItems[currentCritereIndex]) {
            announceToScreenReader('Aucun critère sélectionné. Utilisez J ou K pour naviguer.');
            return;
        }

        const critere = critereItems[currentCritereIndex];
        const radio = critere.querySelector('.statut-radio[value="' + statut + '"]');

        if (radio) {
            radio.checked = true;
            radio.dispatchEvent(new Event('change', { bubbles: true }));

            // Soumettre le formulaire
            const form = critere.querySelector('.verification-form');
            if (form) {
                form.dispatchEvent(new Event('submit', { bubbles: true, cancelable: true }));
            }

            // Annoncer
            const labels = {
                conforme: 'Conforme',
                non_conforme: 'Non conforme',
                non_applicable: 'Non applicable',
                a_verifier: 'À vérifier'
            };
            announceToScreenReader('Statut défini: ' + labels[statut]);
        }
    }

    function toggleCurrentCritereHelp() {
        if (currentCritereIndex < 0 || !critereItems[currentCritereIndex]) {
            announceToScreenReader('Aucun critère sélectionné. Utilisez J ou K pour naviguer.');
            return;
        }

        const critere = critereItems[currentCritereIndex];
        const btnAide = critere.querySelector('.btn-aide');

        if (btnAide) {
            toggleDisclosure(btnAide);
        }
    }

    function clearCurrentCritere() {
        if (currentCritereIndex >= 0 && critereItems[currentCritereIndex]) {
            critereItems[currentCritereIndex].classList.remove('critere-current');
        }
        currentCritereIndex = -1;
    }

    function showKeyboardHelp() {
        const helpText = [
            'Raccourcis clavier :',
            'j/k - Critère suivant/précédent',
            '1 - Conforme',
            '2 - Non conforme',
            '3 - Non applicable',
            '4 - À vérifier',
            'h - Afficher/masquer l\'aide',
            't - Basculer le thème',
            'Échap - Fermer/désélectionner'
        ].join('\n');

        announceToScreenReader(helpText);
        alert(helpText);
    }

    // ===========================================
    // Utilitaires
    // ===========================================

    function announceToScreenReader(message) {
        let announcer = document.getElementById('sr-announcer');

        if (!announcer) {
            announcer = document.createElement('div');
            announcer.id = 'sr-announcer';
            announcer.setAttribute('role', 'status');
            announcer.setAttribute('aria-live', 'polite');
            announcer.setAttribute('aria-atomic', 'true');
            announcer.className = 'visually-hidden';
            document.body.appendChild(announcer);
        }

        // Vider puis remplir pour forcer l'annonce
        announcer.textContent = '';
        setTimeout(function() {
            announcer.textContent = message;
        }, 100);
    }

    // ===========================================
    // Accordion Controls (Expand/Collapse All)
    // ===========================================

    function initAccordionControls() {
        const expandAllBtn = document.getElementById('btn-expand-all');
        const collapseAllBtn = document.getElementById('btn-collapse-all');

        if (expandAllBtn) {
            expandAllBtn.addEventListener('click', expandAllAccordions);
        }

        if (collapseAllBtn) {
            collapseAllBtn.addEventListener('click', collapseAllAccordions);
        }
    }

    function expandAllAccordions() {
        document.querySelectorAll('.accordion-trigger').forEach(function(trigger) {
            const panelId = trigger.getAttribute('aria-controls');
            const panel = document.getElementById(panelId);
            if (panel) {
                setAccordionState(trigger, panel, true);
            }
        });
        saveAccordionState();
        announceToScreenReader('Toutes les catégories sont dépliées');
    }

    function collapseAllAccordions() {
        document.querySelectorAll('.accordion-trigger').forEach(function(trigger) {
            const panelId = trigger.getAttribute('aria-controls');
            const panel = document.getElementById(panelId);
            if (panel) {
                setAccordionState(trigger, panel, false);
            }
        });
        saveAccordionState();
        announceToScreenReader('Toutes les catégories sont repliées');
    }

    // ===========================================
    // Mobile Menu
    // ===========================================

    function initMobileMenu() {
        const toggle = document.querySelector('.mobile-menu-toggle');
        const nav = document.querySelector('.main-nav');

        if (toggle && nav) {
            toggle.addEventListener('click', function() {
                const isExpanded = toggle.getAttribute('aria-expanded') === 'true';
                toggle.setAttribute('aria-expanded', (!isExpanded).toString());
                nav.classList.toggle('open');
            });

            // Fermer le menu en cliquant à l'extérieur
            document.addEventListener('click', function(e) {
                if (!toggle.contains(e.target) && !nav.contains(e.target)) {
                    toggle.setAttribute('aria-expanded', 'false');
                    nav.classList.remove('open');
                }
            });
        }
    }

    // ===========================================
    // Alert Close Buttons
    // ===========================================

    function initAlertCloseButtons() {
        document.querySelectorAll('.alert-close').forEach(function(btn) {
            btn.addEventListener('click', function() {
                const alert = btn.closest('.alert');
                if (alert) {
                    alert.style.display = 'none';
                }
            });
        });
    }

    // Initialiser les nouveaux composants
    document.addEventListener('DOMContentLoaded', function() {
        initAccordionControls();
        initMobileMenu();
        initAlertCloseButtons();
    });

    // Exposer certaines fonctions globalement pour debug
    window.ChecklistA11y = {
        toggleTheme: toggleTheme,
        applyFilters: applyFilters,
        navigateToCritere: navigateToCritere,
        expandAllAccordions: expandAllAccordions,
        collapseAllAccordions: collapseAllAccordions
    };

})();
