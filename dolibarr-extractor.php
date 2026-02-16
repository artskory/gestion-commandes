<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Import Dolibarr - Extraction en cours...</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 20px;
        }
        .extractor-card {
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
            text-align: center;
        }
        .spinner {
            width: 60px;
            height: 60px;
            margin: 20px auto;
        }
        .status {
            margin-top: 20px;
            font-size: 14px;
            color: #666;
        }
        .success-icon {
            color: #28a745;
            font-size: 60px;
            margin: 20px 0;
        }
        .error-icon {
            color: #dc3545;
            font-size: 60px;
            margin: 20px 0;
        }
        #dolibarr-frame {
            width: 100%;
            height: 400px;
            border: 1px solid #ddd;
            border-radius: 10px;
            margin-top: 20px;
        }
        .debug-info {
            margin-top: 20px;
            text-align: left;
            background: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            font-family: monospace;
            font-size: 12px;
            max-height: 200px;
            overflow-y: auto;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="extractor-card">
            <h2 id="title">Import depuis Dolibarr</h2>
            
            <div id="loading" class="d-none">
                <div class="spinner-border text-primary spinner" role="status">
                    <span class="visually-hidden">Chargement...</span>
                </div>
                <div class="status">
                    <strong>Extraction des données en cours...</strong><br>
                    <span id="status-text">Chargement de la page Dolibarr...</span>
                </div>
            </div>
            
            <div id="success" class="d-none">
                <i class="bi bi-check-circle-fill success-icon"></i>
                <h4>Données extraites avec succès !</h4>
                <p>La fenêtre va se fermer automatiquement...</p>
            </div>
            
            <div id="error" class="d-none">
                <i class="bi bi-x-circle-fill error-icon"></i>
                <h4>Erreur lors de l'extraction</h4>
                <p id="error-message"></p>
                <button class="btn btn-secondary mt-3" onclick="window.close()">Fermer</button>
            </div>
            
            <!-- Frame cachée pour charger la page Dolibarr -->
            <iframe id="dolibarr-frame" class="d-none"></iframe>
            
            <!-- Debug info -->
            <div id="debug-info" class="debug-info d-none"></div>
        </div>
    </div>

    <script>
        // Configuration
        const DEBUG = true; // Mettre à false en production
        
        /**
         * Extraire les données de la page Dolibarr
         */
        function extractDolibarrData() {
            updateStatus('Extraction des données...');
            
            try {
                const frame = document.getElementById('dolibarr-frame');
                const doc = frame.contentDocument || frame.contentWindow.document;
                
                // Debug: afficher le HTML
                if (DEBUG) {
                    logDebug('Document chargé, extraction en cours...');
                }
                
                // Extraction des données
                const data = {
                    // Client/Société
                    societe: extractSociete(doc),
                    
                    // Numéro de commande
                    numero_commande: extractNumeroCommande(doc),
                    
                    // Numéro de devis
                    numero_devis: extractNumeroDevis(doc),
                    
                    // Date
                    date_commande: extractDate(doc),
                    
                    // Délai de fabrication
                    delai_fabrication: extractDelai(doc),
                    
                    // Référence article
                    reference_article: extractReference(doc),
                    
                    // Quantité
                    quantite: extractQuantite(doc)
                };
                
                if (DEBUG) {
                    logDebug('Données extraites:', JSON.stringify(data, null, 2));
                }
                
                return data;
                
            } catch (error) {
                console.error('Erreur lors de l\'extraction:', error);
                if (DEBUG) {
                    logDebug('ERREUR: ' + error.message);
                }
                throw error;
            }
        }
        
        /**
         * Extraire le nom de la société/client
         */
        function extractSociete(doc) {
            // Chercher le nom du client dans différents endroits possibles
            
            // 1. Dans le lien vers la société
            let societe = doc.querySelector('.refidno a[href*="societe/card.php"]');
            if (societe) {
                // Extraire le texte (sans l'icône)
                const text = societe.textContent.trim();
                return text;
            }
            
            // 2. Alternative: chercher dans le span avec l'icône building
            societe = doc.querySelector('.fas.fa-building + .classfortooltip');
            if (societe) {
                return societe.textContent.trim();
            }
            
            // 3. Dans le tooltip
            const tooltip = doc.querySelector('[title*="Tiers"]');
            if (tooltip && tooltip.textContent) {
                const match = tooltip.textContent.match(/([A-Za-zÀ-ÿ\s]+)/);
                if (match) return match[1].trim();
            }
            
            return '';
        }
        
        /**
         * Extraire le numéro de commande
         */
        function extractNumeroCommande(doc) {
            // Le numéro de commande est dans la classe "refid"
            const refid = doc.querySelector('.refid.refidpadding');
            if (refid) {
                // Prendre uniquement le premier texte (le numéro)
                const text = refid.childNodes[0].textContent.trim();
                return text;
            }
            
            // Alternative
            const title = doc.querySelector('.tabTitleText');
            if (title) {
                const match = title.textContent.match(/CO\d+-\d+/);
                if (match) return match[0];
            }
            
            return '';
        }
        
        /**
         * Extraire le numéro de devis
         */
        function extractNumeroDevis(doc) {
            // Le devis est souvent dans la section "Objets liés"
            
            // 1. Chercher dans le tableau des objets liés
            const propalLink = doc.querySelector('a[href*="/comm/propal/card.php"]');
            if (propalLink) {
                const text = propalLink.textContent.trim();
                // Extraire le numéro (format PR2602-4076)
                const match = text.match(/PR\d+-\d+/);
                if (match) return match[0];
            }
            
            // 2. Chercher dans le champ "Notre N° de devis"
            const rows = doc.querySelectorAll('tr');
            for (let row of rows) {
                const label = row.querySelector('td');
                if (label && label.textContent.includes('de devis')) {
                    const valueCell = row.querySelector('.valuefield');
                    if (valueCell) {
                        return valueCell.textContent.trim();
                    }
                }
            }
            
            return '';
        }
        
        /**
         * Extraire la date de commande
         */
        function extractDate(doc) {
            // Chercher la ligne avec "Date"
            const rows = doc.querySelectorAll('tr');
            for (let row of rows) {
                const cells = row.querySelectorAll('td');
                if (cells.length >= 2 && cells[0].textContent.trim() === 'Date') {
                    const dateText = cells[1].textContent.trim();
                    // Convertir DD/MM/YYYY en YYYY-MM-DD
                    const match = dateText.match(/(\d{2})\/(\d{2})\/(\d{4})/);
                    if (match) {
                        return `${match[3]}-${match[2]}-${match[1]}`;
                    }
                }
            }
            
            return '';
        }
        
        /**
         * Extraire le délai de fabrication
         */
        function extractDelai(doc) {
            // Chercher "Délai de livraison" ou "Délai de fabrication"
            const rows = doc.querySelectorAll('tr');
            for (let row of rows) {
                const label = row.querySelector('td');
                if (label && (label.textContent.includes('livraison') || label.textContent.includes('fabrication'))) {
                    const valueCell = row.querySelector('.valuefield');
                    if (valueCell) {
                        return valueCell.textContent.trim();
                    }
                }
            }
            
            return '';
        }
        
        /**
         * Extraire la référence article
         */
        function extractReference(doc) {
            // Chercher dans la description de la ligne de commande
            const refLink = doc.querySelector('a[href*="/product/card.php"]');
            if (refLink) {
                // Extraire le texte (numéro de référence)
                return refLink.textContent.trim();
            }
            
            return '';
        }
        
        /**
         * Extraire la quantité
         */
        function extractQuantite(doc) {
            // Chercher dans la colonne quantité
            const qtyCell = doc.querySelector('.linecolqty.right');
            if (qtyCell) {
                // Enlever les espaces et convertir
                const text = qtyCell.textContent.trim().replace(/\s/g, '');
                return text;
            }
            
            return '';
        }
        
        /**
         * Envoyer les données à la fenêtre parente
         */
        function sendDataToParent(data) {
            if (window.opener) {
                window.opener.postMessage({
                    type: 'DOLIBARR_IMPORT',
                    data: data
                }, '*');
                
                updateStatus('Données envoyées !');
                showSuccess();
                
                // Fermer après 2 secondes
                setTimeout(() => {
                    window.close();
                }, 2000);
            } else {
                // Si pas de window.opener, utiliser sessionStorage
                sessionStorage.setItem('dolibarr_import_data', JSON.stringify(data));
                
                updateStatus('Données sauvegardées ! Redirection...');
                showSuccess();
                
                // Rediriger vers le formulaire après 2 secondes
                setTimeout(() => {
                    window.location.href = 'nouvelle-commande.php';
                }, 2000);
            }
        }
        
        /**
         * Mettre à jour le statut
         */
        function updateStatus(message) {
            const statusText = document.getElementById('status-text');
            if (statusText) {
                statusText.textContent = message;
            }
            if (DEBUG) {
                logDebug(message);
            }
        }
        
        /**
         * Afficher l'écran de succès
         */
        function showSuccess() {
            document.getElementById('loading').classList.add('d-none');
            document.getElementById('success').classList.remove('d-none');
        }
        
        /**
         * Afficher l'écran d'erreur
         */
        function showError(message) {
            document.getElementById('loading').classList.add('d-none');
            document.getElementById('error').classList.remove('d-none');
            document.getElementById('error-message').textContent = message;
        }
        
        /**
         * Logger des infos de debug
         */
        function logDebug(message, data = null) {
            if (!DEBUG) return;
            
            const debugDiv = document.getElementById('debug-info');
            debugDiv.classList.remove('d-none');
            
            const timestamp = new Date().toLocaleTimeString();
            let logEntry = `[${timestamp}] ${message}`;
            
            if (data) {
                logEntry += '\n' + data;
            }
            
            debugDiv.innerHTML += logEntry + '\n\n';
            debugDiv.scrollTop = debugDiv.scrollHeight;
        }
        
        /**
         * Initialisation
         */
        function init() {
            // Récupérer l'URL depuis les paramètres
            const urlParams = new URLSearchParams(window.location.search);
            const dolibarrURL = urlParams.get('url');
            
            if (!dolibarrURL) {
                showError('Aucune URL Dolibarr fournie');
                return;
            }
            
            if (DEBUG) {
                logDebug('URL Dolibarr:', dolibarrURL);
            }
            
            // Afficher le loading
            document.getElementById('loading').classList.remove('d-none');
            
            // Charger la page Dolibarr dans l'iframe
            const frame = document.getElementById('dolibarr-frame');
            
            // Écouter le chargement de l'iframe
            frame.onload = function() {
                try {
                    updateStatus('Page chargée, extraction en cours...');
                    
                    // Attendre un peu que la page soit bien rendue
                    setTimeout(() => {
                        try {
                            const data = extractDolibarrData();
                            
                            // Vérifier qu'on a au moins quelques données
                            if (!data.numero_commande && !data.societe) {
                                throw new Error('Aucune donnée n\'a pu être extraite. Vérifiez que vous êtes bien sur une page de commande Dolibarr.');
                            }
                            
                            sendDataToParent(data);
                        } catch (error) {
                            console.error('Erreur extraction:', error);
                            showError('Erreur lors de l\'extraction: ' + error.message);
                        }
                    }, 1000);
                    
                } catch (error) {
                    console.error('Erreur:', error);
                    showError('Impossible d\'accéder à la page Dolibarr. Assurez-vous d\'être connecté.');
                }
            };
            
            frame.onerror = function() {
                showError('Impossible de charger la page Dolibarr. Vérifiez l\'URL et votre connexion.');
            };
            
            // Charger l'URL
            frame.src = dolibarrURL;
        }
        
        // Démarrer au chargement
        window.addEventListener('DOMContentLoaded', init);
    </script>
</body>
</html>
