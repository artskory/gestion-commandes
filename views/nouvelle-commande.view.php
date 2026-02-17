<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nouvelle Commande</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css"/>
    <link rel="stylesheet" href="css/style.css">
    <link rel="apple-touch-icon" sizes="180x180" href="image/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="image/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="image/favicon-16x16.png">
    <link rel="manifest" href="image/site.webmanifest">
</head>
<body class="bg-gradient-to-br"
      <?php if (!empty($this->errors)): 
          $msg = implode('<br>', array_map('htmlspecialchars', $this->errors));
          echo 'data-error-msg="' . htmlspecialchars($msg, ENT_QUOTES, 'UTF-8') . '"';
      endif; ?>
>
    <div class="container mt-5 mb-5">
        <div class="row justify-content-center">
            <div class="col-md-7">
                <div class="card card-shadow">
                    <form method="POST" action="">
                        <div class="card-header d-flex align-items-center justify-content-between">
                            <h2 class="font-bold">Nouvelle Commande</h2>
                            <div>
                                <button type="submit" class="btn btn-primary me-2 shadow-blue"><i class="bi bi-floppy icons"></i>Sauvegarder</button>
                                <a href="./" class="btn btn-danger me-2 shadow-red"><i class="bi bi-x-circle icons"></i>Annuler</a>
                            </div>
                        </div>
                        <div class="card-body">
                            <!-- Les alertes sont maintenant gérées par JavaScript -->
                            
                            <div class="row">
                                <!-- Colonne de gauche -->
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <i class="bi bi-buildings blue icons"></i><label for="societe" class="form-label font-medium">Société *</label>
                                        <input type="text" class="form-control" id="societe" name="societe" 
                                               value="<?php echo isset($_POST['societe']) ? htmlspecialchars($_POST['societe']) : ''; ?>" required>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <i class="bi bi-hash blue icons"></i><label for="n_commande_client" class="form-label font-medium">N° Commande Client *</label>
                                        <input type="text" class="form-control" id="n_commande_client" name="n_commande_client"
                                               value="<?php echo isset($_POST['n_commande_client']) ? htmlspecialchars($_POST['n_commande_client']) : ''; ?>" required>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <i class="bi bi-calendar blue icons"></i><label for="date_commande" class="form-label font-medium">Date</label>
                                        <input type="date" class="form-control" id="date_commande" name="date_commande"
                                               value="<?php echo isset($_POST['date_commande']) ? htmlspecialchars($_POST['date_commande']) : date('Y-m-d'); ?>">
                                    </div>
                                    
                                    <div class="mb-3">
                                        <i class="bi bi-stack blue icons"></i><label for="quantite_par_modele" class="form-label font-medium">Quantité par Modèle</label>
                                        <input type="number" class="form-control" id="quantite_par_modele" name="quantite_par_modele"
                                               value="<?php echo isset($_POST['quantite_par_modele']) ? htmlspecialchars($_POST['quantite_par_modele']) : ''; ?>">
                                    </div>
                                </div>
                                
                                <!-- Colonne de droite -->
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <i class="bi bi-person blue icons"></i><label for="destinataire" class="form-label font-medium">Destinataire</label>
                                        <input type="text" class="form-control" id="destinataire" name="destinataire"
                                               value="<?php echo isset($_POST['destinataire']) ? htmlspecialchars($_POST['destinataire']) : ''; ?>">
                                    </div>
                                    
                                    <div class="mb-3">
                                        <i class="bi bi-file-earmark text blue icons"></i><label for="reference_article" class="form-label font-medium">Référence Article</label>
                                        <input type="text" class="form-control" id="reference_article" name="reference_article"
                                               value="<?php echo isset($_POST['reference_article']) ? htmlspecialchars($_POST['reference_article']) : ''; ?>">
                                    </div>
                                    
                                    <div class="mb-3">
                                        <i class="bi bi-box-seam blue icons"></i><label for="n_devis" class="form-label font-medium">Notre N° de Devis</label>
                                        <input type="text" class="form-control" id="n_devis" name="n_devis"
                                               value="<?php echo isset($_POST['n_devis']) ? htmlspecialchars($_POST['n_devis']) : ''; ?>">
                                    </div>
                                    
                                    <div class="mb-3">
                                        <i class="bi bi-person blue icons"></i><label for="dossier_suivi_par" class="form-label font-medium">Dossier Suivi Par</label>
                                        <input type="text" class="form-control" id="dossier_suivi_par" name="dossier_suivi_par"
                                               value="<?php echo isset($_POST['dossier_suivi_par']) ? htmlspecialchars($_POST['dossier_suivi_par']) : 'Matthieu'; ?>">
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <i class="bi bi-clock blue icons"></i><label class="form-label font-medium">Délais de Fabrication</label>
                                <div class="row">
                                    <div class="col-md-6">
                                        <select class="form-select" id="delais_liste" name="delais_liste" onchange="clearDatePicker()">
                                            <option value="">Sélectionner un délai</option>
                                            <option value="J+0" <?php echo (isset($_POST['delais_liste']) && $_POST['delais_liste'] == 'J+0') ? 'selected' : ''; ?>>J+0</option>
                                            <option value="J+1" <?php echo (isset($_POST['delais_liste']) && $_POST['delais_liste'] == 'J+1') ? 'selected' : ''; ?>>J+1</option>
                                            <option value="J+2" <?php echo (isset($_POST['delais_liste']) && $_POST['delais_liste'] == 'J+2') ? 'selected' : ''; ?>>J+2</option>
                                            <option value="J+3" <?php echo (isset($_POST['delais_liste']) && $_POST['delais_liste'] == 'J+3') ? 'selected' : ''; ?>>J+3</option>
                                            <option value="J+4" <?php echo (isset($_POST['delais_liste']) && $_POST['delais_liste'] == 'J+4') ? 'selected' : ''; ?>>J+4</option>
                                            <option value="J+5" <?php echo (isset($_POST['delais_liste']) && $_POST['delais_liste'] == 'J+5') ? 'selected' : ''; ?>>J+5</option>
                                            <option value="J+6" <?php echo (isset($_POST['delais_liste']) && $_POST['delais_liste'] == 'J+6') ? 'selected' : ''; ?>>J+6</option>
                                            <option value="J+7" <?php echo (isset($_POST['delais_liste']) && $_POST['delais_liste'] == 'J+7') ? 'selected' : ''; ?>>J+7</option>
                                            <option value="J+8" <?php echo (isset($_POST['delais_liste']) && $_POST['delais_liste'] == 'J+8') ? 'selected' : ''; ?>>J+8</option>
                                            <option value="J+9" <?php echo (isset($_POST['delais_liste']) && $_POST['delais_liste'] == 'J+9') ? 'selected' : ''; ?>>J+9</option>
                                            <option value="J+10" <?php echo (isset($_POST['delais_liste']) && $_POST['delais_liste'] == 'J+10') ? 'selected' : ''; ?>>J+10</option>
                                            <option value="J+15" <?php echo (isset($_POST['delais_liste']) && $_POST['delais_liste'] == 'J+15') ? 'selected' : ''; ?>>J+15</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <input type="date" class="form-control" id="delais_date" name="delais_date"
                                               value="<?php echo isset($_POST['delais_date']) ? htmlspecialchars($_POST['delais_date']) : ''; ?>"
                                               onchange="clearDropdown()">
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label font-medium d-block"><i class="bi bi-filetype-pdf blue icons"></i>Statut du Fichier</label>
                                <div class="form-check form-switch rounded-xl border mb-3">
                                    <input class="form-check-input" type="radio" name="fichier_statut" id="fichier_cree" 
                                           value="cree" <?php echo (!isset($_POST['fichier_statut']) || $_POST['fichier_statut'] == 'cree') ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="fichier_cree">
                                        Fichier créé
                                    </label>
                                </div>
                                <div class="form-check form-switch rounded-xl border">
                                    <input class="form-check-input" type="radio" name="fichier_statut" id="fichier_fourni" 
                                           value="fourni" <?php echo (isset($_POST['fichier_statut']) && $_POST['fichier_statut'] == 'fourni') ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="fichier_fourni">
                                        Fichier fourni
                                    </label>
                                </div>
                            </div>
                        </div>
                    </form>
                    <a href="dolibarr-bookmarklet.html" class="btn btn-primary btn-vertical" target="_blank">
                        <i class="bi bi-bookmark-star"></i> Installer le Bookmarklet
                    </a>
                </div>
            </div>
        </div>
    </div>

    <footer class="text-center text-muted py-3 mt-5">
        <small>Version 2.1.0</small>
    </footer>
    
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/alert.js"></script>
    <script src="js/dolibarr-import.js"></script>
    <script>
        // Remplir le formulaire depuis les paramètres URL (import Dolibarr)
        document.addEventListener('DOMContentLoaded', function() {
            const urlParams = new URLSearchParams(window.location.search);
            
            if (urlParams.get('import') === 'dolibarr') {
                console.log('Import Dolibarr détecté depuis URL');
                
                // Mapping des paramètres URL vers les champs du formulaire
                const mapping = {
                    'societe': 'societe',
                    'n_commande_client': 'n_commande_client',
                    'n_devis': 'n_devis',
                    'reference_article': 'reference_article',
                    'quantite_par_modele': 'quantite_par_modele',
                    'delai_fabrication': 'delai_fabrication'
                };
                
                let hasData = false;
                Object.keys(mapping).forEach(function(param) {
                    const value = urlParams.get(param);
                    if (value) {
                        const field = document.getElementById(mapping[param]);
                        if (field) {
                            field.value = value;
                            field.style.borderColor = '#28a745';
                            field.style.borderWidth = '2px';
                            hasData = true;
                            console.log('Champ rempli:', mapping[param], '=', value);
                        }
                    }
                });
                
                if (hasData) {
                    // Afficher un message de succès
                    const alert = document.createElement('div');
                    alert.className = 'alert alert-success alert-dismissible fade show position-fixed top-0 start-50 translate-middle-x mt-3';
                    alert.style.zIndex = '9999';
                    alert.innerHTML = '<strong><i class="bi bi-check-circle-fill"></i> Données importées depuis Dolibarr !</strong><button type="button" class="btn-close" data-bs-dismiss="alert"></button>';
                    document.body.appendChild(alert);
                    setTimeout(function() {
                        alert.classList.remove('show');
                        setTimeout(function() { alert.remove(); }, 150);
                    }, 5000);
                    
                    // Nettoyer l'URL (enlever les paramètres)
                    setTimeout(function() {
                        const cleanUrl = window.location.origin + window.location.pathname;
                        window.history.replaceState({}, document.title, cleanUrl);
                    }, 1000);
                }
            }
        });
        
        function clearDatePicker() {
            if (document.getElementById('delais_liste').value !== '') {
                document.getElementById('delais_date').value = '';
            }
        }
        
        function clearDropdown() {
            if (document.getElementById('delais_date').value !== '') {
                document.getElementById('delais_liste').value = '';
            }
        }
    </script>
</body>
</html>
