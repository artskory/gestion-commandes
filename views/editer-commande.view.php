<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nouvelle Commande</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css"/>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="icon" type="image/png" href="../image/favicon-96x96.png" sizes="96x96" />
    <link rel="icon" type="image/svg+xml" href="../image/favicon.svg" />
    <link rel="shortcut icon" href="../image/favicon.ico" />
    <link rel="apple-touch-icon" sizes="180x180" href="../image/apple-touch-icon.png" />
    <link rel="manifest" href="../image/site.webmanifest" />
</head>
<body"
      <?php if (!empty($this->errors)): 
          $msg = implode('<br>', array_map('htmlspecialchars', $this->errors));
          echo 'data-error-msg="' . htmlspecialchars($msg, ENT_QUOTES, 'UTF-8') . '"';
      endif; ?>
>
    <div class="container mt-5 mb-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <form method="POST" action="">
                        <div class="card-header d-flex align-items-center justify-content-between">
                            <h2 class="font-bold">Éditer Commande</h2>
                            <div>
                                <button type="submit" name="action" value="sauvegarder" class="btn btn-primary me-2"><i class="bi bi-floppy icons"></i>Enregistrer</button>
                                <button type="submit" name="action" value="recharger" class="btn btn-warning me-2"><i class="bi bi-arrow-clockwise icons"></i>Recharger</button>
                                <a href="../" class="btn btn-danger me-2"><i class="bi bi-x-circle icons"></i>Annuler</a>
                            </div>
                        </div>
                        <div class="card-body">
                            <!-- Les alertes sont maintenant gérées par JavaScript -->
                            
                            <div class="row">
                                <!-- Colonne de gauche -->
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <i class="bi bi-buildings icon-primary icons"></i><label for="societe" class="form-label font-medium">Société *</label>
                                        <input type="text" class="form-control text-input" id="societe" name="societe" 
                                               value="<?php echo isset($_POST['societe']) ? htmlspecialchars($_POST['societe']) : htmlspecialchars($this->data['societe']); ?>" required>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <i class="bi bi-hash icon-primary icons"></i><label for="n_commande_client" class="form-label font-medium">N° Commande Client *</label>
                                        <input type="text" class="form-control text-input" id="n_commande_client" name="n_commande_client"
                                               value="<?php echo isset($_POST['n_commande_client']) ? htmlspecialchars($_POST['n_commande_client']) : htmlspecialchars($this->data['n_commande_client']); ?>" required>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <i class="bi bi-calendar icon-primary icons"></i><label for="date_commande" class="form-label font-medium">Date</label>
                                        <input type="date" class="form-control text-input date-wrapper" id="date_commande" name="date_commande"
                                               value="<?php echo isset($_POST['date_commande']) ? htmlspecialchars($_POST['date_commande']) : htmlspecialchars($this->data['date_commande']); ?>">
                                    </div>
                                    
                                    <div class="mb-3">
                                        <i class="bi bi-stack icon-primary icons"></i><label for="quantite_par_modele" class="form-label font-medium">Quantité par Modèle</label>
                                        <input type="number" class="form-control text-input" id="quantite_par_modele" name="quantite_par_modele"
                                               value="<?php echo isset($_POST['quantite_par_modele']) ? htmlspecialchars($_POST['quantite_par_modele']) : htmlspecialchars($this->data['quantite_par_modele']); ?>">
                                    </div>
                                </div>
                                
                                <!-- Colonne de droite -->
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <i class="bi bi-person icon-primary icons"></i><label for="destinataire" class="form-label font-medium">Destinataire</label>
                                        <input type="text" class="form-control text-input" id="destinataire" name="destinataire"
                                               value="<?php echo isset($_POST['destinataire']) ? htmlspecialchars($_POST['destinataire']) : htmlspecialchars($this->data['destinataire']); ?>">
                                    </div>
                                    
                                    <div class="mb-3">
                                        <i class="bi bi-file-earmark text icon-primary icons"></i><label for="reference_article" class="form-label font-medium">Référence Article</label>
                                        <input type="text" class="form-control text-input" id="reference_article" name="reference_article"
                                               value="<?php echo isset($_POST['reference_article']) ? htmlspecialchars($_POST['reference_article']) : htmlspecialchars($this->data['reference_article']); ?>">
                                    </div>
                                    
                                    <div class="mb-3">
                                        <i class="bi bi-box-seam icon-primary icons"></i><label for="n_devis" class="form-label font-medium">Notre N° de Devis</label>
                                        <input type="text" class="form-control text-input" id="n_devis" name="n_devis"
                                               value="<?php echo isset($_POST['n_devis']) ? htmlspecialchars($_POST['n_devis']) : htmlspecialchars($this->data['n_devis']); ?>">
                                    </div>
                                    
                                    <div class="mb-3">
                                        <i class="bi bi-person icon-primary icons"></i><label for="dossier_suivi_par" class="form-label font-medium">Dossier Suivi Par</label>
                                        <input type="text" class="form-control text-input" id="dossier_suivi_par" name="dossier_suivi_par"
                                               value="<?php echo isset($_POST['dossier_suivi_par']) ? htmlspecialchars($_POST['dossier_suivi_par']) : htmlspecialchars($this->data['dossier_suivi_par']); ?>">
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <i class="bi bi-clock icon-primary icons"></i><label class="form-label font-medium">Délais de Fabrication</label>
                                <div class="row">
                                    <div class="col-md-6">
                                        <select class="form-select text-input" id="delais_liste" name="delais_liste" onchange="clearDatePicker()">
                                            <option value="">Sélectionner un délai</option>
                                            <?php
                                            $delais_options = ['J+0', 'J+1', 'J+2', 'J+3', 'J+4', 'J+5', 'J+6', 'J+7', 'J+8', 'J+9', 'J+10', 'J+15'];
                                            foreach ($delais_options as $option) {
                                                $selected = '';
                                                if (isset($_POST['delais_liste']) && $_POST['delais_liste'] == $option) {
                                                    $selected = 'selected';
                                                } elseif (!isset($_POST['delais_liste']) && $this->data['delais_liste_value'] == $option) {
                                                    $selected = 'selected';
                                                }
                                                echo "<option value=\"$option\" $selected>$option</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <input type="date" class="form-control text-input date-wrapper" id="delais_date" name="delais_date"
                                               value="<?php echo isset($_POST['delais_date']) ? htmlspecialchars($_POST['delais_date']) : htmlspecialchars($this->data['delais_date_value']); ?>"
                                               onchange="clearDropdown()">
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label font-medium d-block"><i class="bi bi-filetype-pdf icon-primary icons"></i>Statut du Fichier</label>
                                <div class="form-check form-switch rounded-xl border mb-3">
                                    <input class="form-check-input" type="radio" name="fichier_statut" id="fichier_cree" 
                                           value="cree" <?php echo (isset($_POST['fichier_statut']) ? ($_POST['fichier_statut'] == 'cree' ? 'checked' : '') : ($this->data['fichier_statut'] == 'cree' ? 'checked' : '')); ?>>
                                    <label class="form-check-label text-input" for="fichier_cree">
                                        Fichier créé
                                    </label>
                                </div>
                                <div class="form-check form-switch rounded-xl border">
                                    <input class="form-check-input" type="radio" name="fichier_statut" id="fichier_fourni" 
                                           value="fourni" <?php echo (isset($_POST['fichier_statut']) ? ($_POST['fichier_statut'] == 'fourni' ? 'checked' : '') : ($this->data['fichier_statut'] == 'fourni' ? 'checked' : '')); ?>>
                                    <label class="form-check-label text-input" for="fichier_fourni">
                                        Fichier fourni
                                    </label>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <footer class="text-center text-light py-3 mt-5">
        <small>Version 2.1.22</small>
    </footer>
    
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../js/alert.js"></script>
    <script>
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
    <script>window.name = "gestion_commandes";</script>
</body>
</html>
