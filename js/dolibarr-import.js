/**
 * Script d'import depuis Dolibarr
 * Version 1.41
 *
 * Deux cas gérés :
 *  1. L'onglet gestion-commandes était ouvert sur une AUTRE page (index, édition…)
 *     → le bookmarklet navigue vers nouvelle-commande.php
 *     → au chargement, checkPendingData() lit le localStorage et remplit le formulaire
 *
 *  2. L'onglet était DÉJÀ sur nouvelle-commande.php
 *     → window.open navigue vers la même URL (rechargement)
 *     → idem : checkPendingData() au chargement
 *
 * Dans les deux cas, window.name = "gestion_commandes" (défini dans chaque vue)
 * permet au bookmarklet de retrouver l'onglet existant via window.open(url, 'gestion_commandes').
 */

const DOLIBARR_IMPORT = {

    storageKey: 'dolibarr_import_data',

    // ─────────────────────────────────────────
    // Initialisation
    // ─────────────────────────────────────────
    init: function () {
        // Lire les données en attente au chargement de la page
        this.checkPendingData();
    },

    // ─────────────────────────────────────────
    // Lecture des données au chargement
    // ─────────────────────────────────────────
    checkPendingData: function () {
        const raw = localStorage.getItem(this.storageKey);
        if (!raw) return;

        try {
            const data = JSON.parse(raw);
            localStorage.removeItem(this.storageKey);
            this.fillForm(data);
            this.showSuccess('✅ Données importées depuis Dolibarr avec succès !');
        } catch (e) {
            console.error('Erreur parsing données:', e);
            this.showError("Erreur lors de l'import des données");
            localStorage.removeItem(this.storageKey);
        }
    },

    // ─────────────────────────────────────────
    // Remplissage du formulaire
    // ─────────────────────────────────────────
    fillForm: function (data) {
        const fieldMapping = {
            'societe':             data.societe || data.client,
            'destinataire':        data.destinataire,
            'n_commande_client':   data.numero_commande,
            'reference_article':   data.reference_article,
            'date_commande':       data.date_commande,
            'n_devis':             data.numero_devis,
            'quantite_par_modele': data.quantite,
            'dossier_suivi_par':   data.suivi_par
        };

        Object.keys(fieldMapping).forEach(fieldId => {
            const value = fieldMapping[fieldId];
            if (!value) return;
            const field = document.getElementById(fieldId);
            if (!field) return;

            field.value = value;
            field.dispatchEvent(new Event('change', { bubbles: true }));
            field.classList.add('border-success');
            setTimeout(() => field.classList.remove('border-success'), 2000);
        });

        if (data.delai_fabrication) this.fillDelai(data.delai_fabrication);

        window.scrollTo({ top: 0, behavior: 'smooth' });
    },

    // ─────────────────────────────────────────
    // Remplissage du délai de fabrication
    // ─────────────────────────────────────────
    fillDelai: function (delai) {
        const matchJours = delai.match(/(\d+)\s*jours?/i);
        if (matchJours) {
            const delaiListe = document.getElementById('delais_liste');
            if (!delaiListe) return;
            const optionValue = 'J+' + matchJours[1];
            const option = Array.from(delaiListe.options).find(o => o.value === optionValue);
            if (option) {
                delaiListe.value = optionValue;
                delaiListe.classList.add('border-success');
                setTimeout(() => delaiListe.classList.remove('border-success'), 2000);
            }
            return;
        }

        if (/\d{2}\/\d{2}\/\d{4}/.test(delai)) {
            const parts = delai.split('/');
            const delaiDate = document.getElementById('delais_date');
            if (delaiDate) {
                delaiDate.value = `${parts[2]}-${parts[1]}-${parts[0]}`;
                delaiDate.classList.add('border-success');
                setTimeout(() => delaiDate.classList.remove('border-success'), 2000);
            }
        }
    },

    // ─────────────────────────────────────────
    // Alertes
    // ─────────────────────────────────────────
    showSuccess: function (message) { this._showAlert(message, 'success', 5000); },
    showError:   function (message) { this._showAlert(message, 'danger',  7000); },

    _showAlert: function (message, type, delay) {
        const icon = type === 'success' ? 'bi-check-circle-fill' : 'bi-exclamation-triangle-fill';
        const div  = document.createElement('div');
        div.className = `alert alert-${type} alert-dismissible fade show position-fixed top-0 start-50 translate-middle-x mt-3`;
        div.style.cssText = 'z-index:9999;min-width:400px';
        div.innerHTML = `<strong><i class="bi ${icon}"></i> ${message}</strong>
                         <button type="button" class="btn-close" data-bs-dismiss="alert"></button>`;
        document.body.appendChild(div);
        setTimeout(() => {
            div.classList.remove('show');
            setTimeout(() => div.remove(), 150);
        }, delay);
    }
};

document.addEventListener('DOMContentLoaded', () => DOLIBARR_IMPORT.init());
window.DOLIBARR_IMPORT = DOLIBARR_IMPORT;
