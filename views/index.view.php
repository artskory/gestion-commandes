<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Commandes</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css"/>
    <link rel="stylesheet" href="css/style.css">
    <link rel="icon" type="image/png" href="image/favicon-96x96.png" sizes="96x96" />
    <link rel="icon" type="image/svg+xml" href="image/favicon.svg" />
    <link rel="shortcut icon" href="image/favicon.ico" />
    <link rel="apple-touch-icon" sizes="180x180" href="image/apple-touch-icon.png" />
    <link rel="manifest" href="image/site.webmanifest" />
    <style>
        /* Modal de confirmation stylisé */
        .custom-modal-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.6);
            z-index: 9998;
            animation: fadeIn 0.3s ease;
        }

        .custom-modal {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: white;
            border-radius: 12px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.3);
            z-index: 9999;
            max-width: 500px;
            width: 90%;
            animation: slideDown 0.3s ease;
        }

        .custom-modal.show {
            display: block;
        }

        .custom-modal-overlay.show {
            display: block;
        }

        .modal-header-custom {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px 24px;
            border-radius: 12px 12px 0 0;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .modal-header-custom.danger {
            background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
        }

        .modal-header-custom.warning {
            background: linear-gradient(135deg, #ffc107 0%, #ff9800 100%);
        }

        .modal-header-custom i {
            font-size: 28px;
        }

        .modal-header-custom h5 {
            margin: 0;
            font-weight: 600;
            font-size: 20px;
        }

        .modal-body-custom {
            padding: 24px;
            font-size: 16px;
            line-height: 1.6;
            color: #333;
        }

        .modal-list {
            background: #f8f9fa;
            border-left: 4px solid #667eea;
            padding: 16px;
            border-radius: 6px;
            margin: 16px 0;
            max-height: 200px;
            overflow-y: auto;
        }

        .modal-list-item {
            padding: 8px 0;
            border-bottom: 1px solid #dee2e6;
        }

        .modal-list-item:last-child {
            border-bottom: none;
        }

        .modal-list-item i {
            color: #667eea;
            margin-right: 8px;
        }

        .modal-footer-custom {
            padding: 16px 24px;
            border-top: 1px solid #e9ecef;
            display: flex;
            justify-content: flex-end;
            gap: 12px;
        }

        .modal-btn {
            padding: 10px 24px;
            border: none;
            border-radius: 6px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s ease;
            font-size: 15px;
        }

        .modal-btn-cancel {
            background: #6c757d;
            color: white;
        }

        .modal-btn-cancel:hover {
            background: #5a6268;
            transform: translateY(-1px);
        }

        .modal-btn-confirm {
            background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
            color: white;
        }

        .modal-btn-confirm:hover {
            background: linear-gradient(135deg, #c82333 0%, #bd2130 100%);
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(220, 53, 69, 0.4);
        }

        .modal-btn-confirm.warning {
            background: linear-gradient(135deg, #ffc107 0%, #ff9800 100%);
            color: #333;
        }

        .modal-btn-confirm.warning:hover {
            background: linear-gradient(135deg, #ff9800 0%, #f57c00 100%);
            box-shadow: 0 4px 12px rgba(255, 193, 7, 0.4);
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translate(-50%, -60%);
            }
            to {
                opacity: 1;
                transform: translate(-50%, -50%);
            }
        }
    </style>
</head>
<body 
      <?php if ($this->success): 
          $msg = '';
          if ($this->success == 'creation') $msg = 'Commande créée avec succès !';
          elseif ($this->success == 'rechargement') $msg = 'Nouvelle version générée avec succès !';
          elseif ($this->success == 'suppression') $msg = $this->count . ' commande(s) de plus de 7 jours supprimée(s) avec succès !';
          elseif ($this->success == 'suppression_individuelle') $msg = 'Commande(s) supprimée(s) avec succès !';
          elseif ($this->success == 'modification') $msg = 'Commande modifiée avec succès !';
          echo 'data-success-msg="' . htmlspecialchars($msg, ENT_QUOTES, 'UTF-8') . '"';
      endif; ?>
      <?php if ($this->error): 
          $msg = '';
          if ($this->error == 'suppression') $msg = 'Erreur lors de la suppression de la commande.';
          echo 'data-error-msg="' . htmlspecialchars($msg, ENT_QUOTES, 'UTF-8') . '"';
      endif; ?>
>
    <!-- Modal de confirmation personnalisée -->
    <div class="custom-modal-overlay" id="modal-overlay"></div>
    <div class="custom-modal" id="custom-modal">
        <div class="modal-header-custom" id="modal-header">
            <i class="bi bi-exclamation-triangle-fill"></i>
            <h5 id="modal-title">Confirmation</h5>
        </div>
        <div class="modal-body-custom" id="modal-body">
            <!-- Le contenu sera injecté par JavaScript -->
        </div>
        <div class="modal-footer-custom">
            <button class="modal-btn modal-btn-cancel" onclick="fermerModal()">Annuler</button>
            <button class="modal-btn modal-btn-confirm" id="modal-confirm-btn">Confirmer</button>
        </div>
    </div>

    <div class="container mt-5 mb-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <h2 class="font-bold"><a href="./" class="title-link">Liste des Commandes</a></h2>
                        <div class="d-flex align-items-center gap-2">
                            <a href="nouvelle" class="btn btn-primary">
                                <i class="bi bi-plus-lg"></i> Nouveau
                            </a>
                            <button id="btn-supprimer-selection"
                                    class="btn btn-danger d-none"
                                    onclick="confirmerSuppressionSelection()">
                                <i class="bi bi-trash"></i>
                                Supprimer (<span id="nb-selection">0</span>)
                            </button>
                        </div>
                    </div>
                    
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr class="text-center">
                                    <th>ID</th>
                                    <th>Société</th>
                                    <th>N° Commande Client</th>
                                    <th>Actions</th>
                                    <th style="width: 40px;">
                                        <input type="checkbox" class="form-check-input" id="check-all" title="Tout sélectionner">
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (count($this->commandes) > 0): ?>
                                    <?php foreach ($this->commandes as $cmd): ?>
                                        <tr class="text-center align-middle">
                                            <td><?php echo htmlspecialchars($cmd['id']); ?></td>
                                            <td><?php echo htmlspecialchars($cmd['societe']); ?></td>
                                            <td><?php echo htmlspecialchars($cmd['n_commande_client']); ?></td>
                                            <td>
                                                <a href="#" 
                                                   class="btn btn-sm bg-warning-subtle icon-warning me-1"
                                                   title="Rechargement"
                                                   onclick="confirmerRechargement(event, <?php echo $cmd['id']; ?>);">
                                                    <i class="bi bi-arrow-clockwise"></i>
                                                </a>
                                                <a href="editer/<?php echo $cmd['id']; ?>" 
                                                   class="btn btn-sm bg-primary-subtle icon-primary"
                                                   title="Éditer">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                            </td>
                                            <td>
                                                <input type="checkbox" 
                                                       class="form-check-input check-commande" 
                                                       value="<?php echo $cmd['id']; ?>"
                                                       data-nom="<?php echo htmlspecialchars($cmd['n_commande_client']); ?>">
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="5" class="text-center">Aucune commande trouvée</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <footer class="text-center text-light py-3 mt-5">
        <small>Version 2.1.1</small>
    </footer>
    
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/alert.js"></script>
    <script>

        const checkAll    = document.getElementById('check-all');
        const btnSuppr    = document.getElementById('btn-supprimer-selection');
        const nbSelection = document.getElementById('nb-selection');

        function getChecked() {
            return document.querySelectorAll('.check-commande:checked');
        }

        function mettreAJourBouton() {
            const nb    = getChecked().length;
            const total = document.querySelectorAll('.check-commande').length;

            if (nb > 0) {
                btnSuppr.classList.remove('d-none');
                nbSelection.textContent = nb;
            } else {
                btnSuppr.classList.add('d-none');
            }

            checkAll.checked       = nb === total && total > 0;
            checkAll.indeterminate = nb > 0 && nb < total;
        }

        checkAll.addEventListener('change', function () {
            document.querySelectorAll('.check-commande').forEach(cb => {
                cb.checked = this.checked;
            });
            mettreAJourBouton();
        });

        document.querySelectorAll('.check-commande').forEach(cb => {
            cb.addEventListener('change', mettreAJourBouton);
        });

        // ══════════════════════════════════════════════════════════════
        // MODALS PERSONNALISÉES
        // ══════════════════════════════════════════════════════════════

        function afficherModal(titre, contenu, type, onConfirm) {
            const modal      = document.getElementById('custom-modal');
            const overlay    = document.getElementById('modal-overlay');
            const header     = document.getElementById('modal-header');
            const titleElem  = document.getElementById('modal-title');
            const body       = document.getElementById('modal-body');
            const confirmBtn = document.getElementById('modal-confirm-btn');

            // Configuration du header selon le type
            header.className = 'modal-header-custom';
            confirmBtn.className = 'modal-btn modal-btn-confirm';
            
            if (type === 'danger') {
                header.classList.add('danger');
                header.querySelector('i').className = 'bi bi-exclamation-triangle-fill';
            } else if (type === 'warning') {
                header.classList.add('warning');
                confirmBtn.classList.add('warning');
                header.querySelector('i').className = 'bi bi-arrow-clockwise';
            }

            titleElem.textContent = titre;
            body.innerHTML = contenu;

            // Retirer l'ancien event listener
            const newBtn = confirmBtn.cloneNode(true);
            confirmBtn.parentNode.replaceChild(newBtn, confirmBtn);
            document.getElementById('modal-confirm-btn').addEventListener('click', function() {
                fermerModal();
                onConfirm();
            });

            // Afficher
            overlay.classList.add('show');
            modal.classList.add('show');
        }

        function fermerModal() {
            document.getElementById('custom-modal').classList.remove('show');
            document.getElementById('modal-overlay').classList.remove('show');
        }

        // Fermer en cliquant sur l'overlay
        document.getElementById('modal-overlay').addEventListener('click', fermerModal);

        // Fermer avec Escape
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                fermerModal();
            }
        });

        // ══════════════════════════════════════════════════════════════
        // CONFIRMATION RECHARGEMENT
        // ══════════════════════════════════════════════════════════════

        function confirmerRechargement(event, id) {
            event.preventDefault();
            
            const titre = 'Créer une nouvelle version ?';
            const contenu = `
                <p>Vous allez créer une nouvelle version de cette commande.</p>
                <p style="margin-top: 12px; color: #666;">
                    <i class="bi bi-info-circle" style="color: #ffc107;"></i>
                    Un nouveau fichier CSV sera généré.
                </p>
            `;

            afficherModal(titre, contenu, 'warning', function() {
                window.location.href = 'recharger/' + id;
            });
        }

        // ══════════════════════════════════════════════════════════════
        // CONFIRMATION SUPPRESSION
        // ══════════════════════════════════════════════════════════════

        function confirmerSuppressionSelection() {
            const cases = getChecked();
            if (cases.length === 0) return;

            const noms = Array.from(cases).map(cb => cb.dataset.nom);
            
            const titre = 'Supprimer ' + cases.length + ' commande(s) ?';
            const listeHtml = noms.map(nom => 
                `<div class="modal-list-item"><i class="bi bi-file-earmark-text"></i>${nom}</div>`
            ).join('');
            
            const contenu = `
                <p><strong>Vous êtes sur le point de supprimer ${cases.length} commande(s) :</strong></p>
                <div class="modal-list">${listeHtml}</div>
                <p style="margin-top: 16px; color: #dc3545; font-weight: 500;">
                    <i class="bi bi-exclamation-triangle"></i>
                    Cette action est irréversible !
                </p>
            `;

            afficherModal(titre, contenu, 'danger', function() {
                const ids = Array.from(cases).map(cb => cb.value).join(',');
                window.location.href = 'supprimer/' + ids;
            });
        }

        // ══════════════════════════════════════════════════════════════
        // TÉLÉCHARGEMENT AUTO
        // ══════════════════════════════════════════════════════════════

        window.addEventListener('DOMContentLoaded', function () {
            const urlParams    = new URLSearchParams(window.location.search);
            const downloadFile = urlParams.get('download');

            if (downloadFile) {
                const link    = document.createElement('a');
                link.href     = 'downloads/' + downloadFile;
                link.download = downloadFile;
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
            }

            if (urlParams.has('success') || urlParams.has('error') || urlParams.has('count') || urlParams.has('download')) {
                setTimeout(function () {
                    window.history.replaceState({}, document.title, './');
                }, 1000);
            }
        });
    </script>
    <script>window.name = "gestion_commandes";</script>
</body>
</html>
