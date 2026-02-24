<?php
// ─── Variables du layout ──────────────────────────────────────────────────
$pageTitle = 'Gestion des Commandes';
$basePath  = '';

// Construction de l'attribut <body>
$bodyAttr = '';
if ($this->success) {
    $msg = '';
    if      ($this->success == 'creation')                $msg = 'Commande créée avec succès !';
    elseif  ($this->success == 'rechargement')            $msg = 'Nouvelle version générée avec succès !';
    elseif  ($this->success == 'suppression')             $msg = $this->count . ' commande(s) de plus de 7 jours supprimée(s) avec succès !';
    elseif  ($this->success == 'suppression_individuelle') $msg = 'Commande(s) supprimée(s) avec succès !';
    elseif  ($this->success == 'modification')            $msg = 'Commande modifiée avec succès !';
    $bodyAttr .= 'data-success-msg="' . htmlspecialchars($msg, ENT_QUOTES, 'UTF-8') . '"';
}
if ($this->error) {
    $msg = '';
    if ($this->error == 'suppression') $msg = 'Erreur lors de la suppression de la commande.';
    $bodyAttr .= ' data-error-msg="' . htmlspecialchars($msg, ENT_QUOTES, 'UTF-8') . '"';
}

// Script spécifique à index (checkboxes + téléchargement auto)
$extraScripts = <<<'JS'
<script>
    const checkAll    = document.getElementById('check-all');
    const btnSuppr    = document.getElementById('btn-supprimer-selection');
    const nbSelection = document.getElementById('nb-selection');

    function mettreAJourBouton() {
        const nb    = getChecked().length;
        const total = document.querySelectorAll('.check-commande').length;

        if (nb > 0) {
            btnSuppr.classList.remove('d-none');
            nbSelection.textContent = nb;
        } else {
            btnSuppr.classList.add('d-none');
        }

        const btnSupprMobile    = document.getElementById('btn-supprimer-selection-mobile');
        const nbSelectionMobile = document.getElementById('nb-selection-mobile');
        if (btnSupprMobile) {
            if (nb > 0) {
                btnSupprMobile.classList.remove('d-none');
                nbSelectionMobile.textContent = nb;
            } else {
                btnSupprMobile.classList.add('d-none');
            }
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
JS;

include 'views/layout/header.php';
?>

    <a href="dolibarr-bookmarklet.html" class="btn btn-primary btn-vertical" target="_blank">
        <i class="bi bi-bookmark-star"></i> Installer le Bookmarklet
    </a>
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

                        <!-- Boutons visibles au dessus de 1024px -->
                        <div class="show-above-1024 align-items-center gap-2">
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

                        <!-- Menu hamburger visible en dessous de 1024px -->
                        <div class="show-below-1024 dropdown">
                            <button class="btn-hamburger" id="hamburger-btn" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <svg class="bars" id="hamburger-svg" viewBox="0 0 100 100">
                                    <path class="line top"    d="m 30,33 h 40 c 13.100415,0 14.380204,31.80258 6.899646,33.421777 -24.612039,5.327373 9.016154,-52.337577 -12.75751,-30.563913 l -28.284272,28.284272"></path>
                                    <path class="line middle" d="m 70,50 c 0,0 -32.213436,0 -40,0 -7.786564,0 -6.428571,-4.640244 -6.428571,-8.571429 0,-5.895471 6.073743,-11.783399 12.286435,-5.570707 6.212692,6.212692 28.284272,28.284272 28.284272,28.284272"></path>
                                    <path class="line bottom" d="m 69.575405,67.073826 h -40 c -13.100415,0 -14.380204,-31.80258 -6.899646,-33.421777 24.612039,-5.327373 -9.016154,52.337577 12.75751,30.563913 l 28.284272,-28.284272"></path>
                                </svg>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li>
                                    <a href="nouvelle" class="dropdown-item">
                                        <i class="bi bi-plus-lg icon-primary icons"></i> Nouveau
                                    </a>
                                </li>
                                <li id="btn-supprimer-selection-mobile" class="d-none">
                                    <hr class="dropdown-divider">
                                    <button class="dropdown-item text-danger" onclick="confirmerSuppressionSelection()">
                                        <i class="bi bi-trash icons"></i>
                                        Supprimer (<span id="nb-selection-mobile">0</span>)
                                    </button>
                                </li>
                            </ul>
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
                                            <td class="actions-col">
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

<?php include 'views/layout/footer.php'; ?>
