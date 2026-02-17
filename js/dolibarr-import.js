/**
 * Script d'import depuis Dolibarr
 * Version 1.31
 * 
 * Permet d'importer automatiquement les données d'une commande Dolibarr
 * via le bookmarklet
 */

// Configuration
const DOLIBARR_IMPORT = {
    // Stockage temporaire des données
    storageKey: 'dolibarr_import_data',
    
    /**
     * Initialiser le système d'import
     */
    init: function() {
        console.log('Dolibarr Import: Initialisation...');
        
        // Vérifier si des données sont en attente
        this.checkPendingData();
    },
    
    /**
     * Vérifier s'il y a des données en attente dans le localStorage
     */
    checkPendingData: function() {
        const data = localStorage.getItem(this.storageKey);
        if (data) {
            try {
                const importData = JSON.parse(data);
                console.log('Données Dolibarr trouvées:', importData);
                
                // Remplir le formulaire
                this.fillForm(importData);
                
                // Nettoyer le storage immédiatement
                localStorage.removeItem(this.storageKey);
                
                // Afficher un message de succès
                this.showSuccess('✅ Données importées depuis Dolibarr avec succès !');
            } catch (e) {
                console.error('Erreur lors du parsing des données:', e);
                this.showError('Erreur lors de l\'import des données');
                // Nettoyer en cas d'erreur
                localStorage.removeItem(this.storageKey);
            }
        }
    },
    
    /**
     * Remplir le formulaire avec les données Dolibarr
     */
    fillForm: function(data) {
        console.log('Remplissage du formulaire avec:', data);
        
        // Mapping des champs
        const fieldMapping = {
            'societe': data.societe || data.client,
            'destinataire': data.destinataire,
            'n_commande_client': data.numero_commande,
            'reference_article': data.reference_article,
            'date_commande': data.date_commande,
            'n_devis': data.numero_devis,
            'quantite_par_modele': data.quantite,
            'dossier_suivi_par': data.suivi_par
        };
        
        // Remplir chaque champ
        Object.keys(fieldMapping).forEach(fieldId => {
            const value = fieldMapping[fieldId];
            if (value) {
                const field = document.getElementById(fieldId);
                if (field) {
                    field.value = value;
                    // Déclencher l'événement change pour les éventuels listeners
                    field.dispatchEvent(new Event('change', { bubbles: true }));
                    console.log(`Champ ${fieldId} rempli avec: ${value}`);
                    
                    // Animation visuelle
                    field.classList.add('border-success');
                    setTimeout(() => field.classList.remove('border-success'), 2000);
                }
            }
        });
        
        // Gérer le délai de fabrication
        if (data.delai_fabrication) {
            this.fillDelai(data.delai_fabrication);
        }
        
        // Scroll vers le haut pour voir le formulaire
        window.scrollTo({ top: 0, behavior: 'smooth' });
    },
    
    /**
     * Remplir le champ délai de fabrication
     */
    fillDelai: function(delai) {
        console.log('Remplissage du délai:', delai);
        
        // Si c'est au format "5 jours ouvrés à validation du BAT"
        // Extraire le nombre de jours
        const match = delai.match(/(\d+)\s*jours?/i);
        if (match) {
            const jours = match[1];
            const delaiListe = document.getElementById('delais_liste');
            if (delaiListe) {
                const optionValue = 'J+' + jours;
                // Vérifier si l'option existe
                const option = Array.from(delaiListe.options).find(opt => opt.value === optionValue);
                if (option) {
                    delaiListe.value = optionValue;
                    delaiListe.classList.add('border-success');
                    setTimeout(() => delaiListe.classList.remove('border-success'), 2000);
                    console.log(`Délai sélectionné: ${optionValue}`);
                }
            }
        }
        // Si c'est une date au format DD/MM/YYYY
        else if (delai.match(/\d{2}\/\d{2}\/\d{4}/)) {
            const parts = delai.split('/');
            const dateISO = `${parts[2]}-${parts[1]}-${parts[0]}`;
            const delaiDate = document.getElementById('delais_date');
            if (delaiDate) {
                delaiDate.value = dateISO;
                delaiDate.classList.add('border-success');
                setTimeout(() => delaiDate.classList.remove('border-success'), 2000);
                console.log(`Date de délai sélectionnée: ${dateISO}`);
            }
        }
    },
    
    /**
     * Afficher un message de succès
     */
    showSuccess: function(message) {
        // Créer une alerte Bootstrap
        const alertDiv = document.createElement('div');
        alertDiv.className = 'alert alert-success alert-dismissible fade show position-fixed top-0 start-50 translate-middle-x mt-3';
        alertDiv.style.zIndex = '9999';
        alertDiv.style.minWidth = '400px';
        alertDiv.innerHTML = `
            <strong><i class="bi bi-check-circle-fill"></i> ${message}</strong>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        document.body.appendChild(alertDiv);
        
        // Auto-fermer après 5 secondes
        setTimeout(() => {
            alertDiv.classList.remove('show');
            setTimeout(() => alertDiv.remove(), 150);
        }, 5000);
    },
    
    /**
     * Afficher un message d'erreur
     */
    showError: function(message) {
        // Créer une alerte Bootstrap
        const alertDiv = document.createElement('div');
        alertDiv.className = 'alert alert-danger alert-dismissible fade show position-fixed top-0 start-50 translate-middle-x mt-3';
        alertDiv.style.zIndex = '9999';
        alertDiv.style.minWidth = '400px';
        alertDiv.innerHTML = `
            <strong><i class="bi bi-exclamation-triangle-fill"></i> ${message}</strong>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        document.body.appendChild(alertDiv);
        
        // Auto-fermer après 7 secondes
        setTimeout(() => {
            alertDiv.classList.remove('show');
            setTimeout(() => alertDiv.remove(), 150);
        }, 7000);
    }
};

// Initialiser au chargement de la page
document.addEventListener('DOMContentLoaded', function() {
    DOLIBARR_IMPORT.init();
});

// Exporter pour utilisation externe si besoin
window.DOLIBARR_IMPORT = DOLIBARR_IMPORT;
