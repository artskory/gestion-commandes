/**
 * Script d'import depuis Dolibarr
 * Version 1.31
 * 
 * Permet d'importer automatiquement les données d'une commande Dolibarr
 * Méthodes disponibles :
 * 1. Import par URL (avec popup)
 * 2. Réception de données depuis bookmarklet
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
        
        // Attacher les événements
        this.attachEvents();
    },
    
    /**
     * Vérifier s'il y a des données en attente dans le sessionStorage
     */
    checkPendingData: function() {
        const data = sessionStorage.getItem(this.storageKey);
        if (data) {
            try {
                const importData = JSON.parse(data);
                console.log('Données Dolibarr trouvées:', importData);
                
                // Remplir le formulaire
                this.fillForm(importData);
                
                // Nettoyer le storage
                sessionStorage.removeItem(this.storageKey);
                
                // Afficher un message de succès
                this.showSuccess('Données importées depuis Dolibarr avec succès !');
            } catch (e) {
                console.error('Erreur lors du parsing des données:', e);
            }
        }
    },
    
    /**
     * Attacher les événements aux boutons
     */
    attachEvents: function() {
        // Bouton d'import par URL
        const importBtn = document.getElementById('dolibarr-import-btn');
        if (importBtn) {
            importBtn.addEventListener('click', () => this.importFromURL());
        }
        
        // Input URL - Import au Enter
        const urlInput = document.getElementById('dolibarr-url');
        if (urlInput) {
            urlInput.addEventListener('keypress', (e) => {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    this.importFromURL();
                }
            });
        }
        
        // Écouter les messages du popup/bookmarklet
        window.addEventListener('message', (event) => {
            this.receiveData(event);
        });
    },
    
    /**
     * Importer depuis une URL (ouvre un popup)
     */
    importFromURL: function() {
        const urlInput = document.getElementById('dolibarr-url');
        const url = urlInput ? urlInput.value.trim() : '';
        
        if (!url) {
            this.showError('Veuillez entrer une URL Dolibarr');
            return;
        }
        
        // Valider que c'est bien une URL Dolibarr
        if (!this.isValidDolibarrURL(url)) {
            this.showError('L\'URL ne semble pas être une URL Dolibarr valide');
            return;
        }
        
        console.log('Ouverture du popup pour:', url);
        
        // Ouvrir le popup extracteur
        const popup = window.open(
            'dolibarr-extractor.php?url=' + encodeURIComponent(url),
            'dolibarr_extractor',
            'width=800,height=600,scrollbars=yes'
        );
        
        if (!popup) {
            this.showError('Le popup a été bloqué. Veuillez autoriser les popups pour ce site.');
        }
    },
    
    /**
     * Valider qu'une URL est bien une URL Dolibarr
     */
    isValidDolibarrURL: function(url) {
        // Vérifier que c'est une URL valide
        try {
            new URL(url);
        } catch (e) {
            return false;
        }
        
        // Vérifier qu'elle contient "commande" ou "card.php"
        return url.includes('commande') || url.includes('card.php');
    },
    
    /**
     * Recevoir les données depuis le popup ou le bookmarklet
     */
    receiveData: function(event) {
        // Vérifier l'origine (sécurité de base)
        // Note: En production, vérifier l'origine de manière plus stricte
        
        if (event.data && event.data.type === 'DOLIBARR_IMPORT') {
            console.log('Données reçues:', event.data.data);
            
            // Remplir le formulaire
            this.fillForm(event.data.data);
            
            // Afficher un message de succès
            this.showSuccess('Données importées depuis Dolibarr avec succès !');
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
                }
            }
        });
        
        // Gérer le délai de fabrication
        if (data.delai_fabrication) {
            this.fillDelai(data.delai_fabrication);
        }
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
                console.log(`Date de délai sélectionnée: ${dateISO}`);
            }
        }
    },
    
    /**
     * Afficher un message de succès
     */
    showSuccess: function(message) {
        // Utiliser le système d'alertes existant si disponible
        if (typeof showAlert === 'function') {
            showAlert(message, 'success');
        } else {
            alert(message);
        }
    },
    
    /**
     * Afficher un message d'erreur
     */
    showError: function(message) {
        // Utiliser le système d'alertes existant si disponible
        if (typeof showAlert === 'function') {
            showAlert(message, 'error');
        } else {
            alert(message);
        }
    }
};

// Initialiser au chargement de la page
document.addEventListener('DOMContentLoaded', function() {
    DOLIBARR_IMPORT.init();
});

// Exporter pour utilisation externe si besoin
window.DOLIBARR_IMPORT = DOLIBARR_IMPORT;
