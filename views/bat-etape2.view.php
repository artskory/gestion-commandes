<?php
// ─── Variables du layout ──────────────────────────────────────────────────────
$pageTitle = 'BAT — Vérifications';
$basePath  = '';
$bodyAttr = '';
// Message de succès à l'arrivée sur l'étape 2
$bodyAttr .= 'data-success-msg="Fichiers uploadés avec succès. Vérifiez les informations ci-dessous."';

include __DIR__ . '/layout/header.php';
?>

<div class="container mt-5 mb-5">
    <div class="row justify-content-center">
        <div class="col-md-9">

            <!-- Indicateur d'étapes -->
            <div class="d-flex align-items-center mb-4 gap-2">
                <a href="<?php echo $appBase; ?>/nouvelle" class="bat-step-done">
                    <i class="bi bi-check-circle-fill me-1"></i>1. Informations &amp; fichiers
                </a>
                <i class="bi bi-chevron-right text-muted"></i>
                <span class="bat-step-current">
                    <i class="bi bi-shield-check me-1"></i>2. Vérifications
                </span>
            </div>

            <!-- Résumé commande -->
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 font-bold">
                        <i class="bi bi-receipt icon-primary icons"></i>Résumé de la commande
                    </h5>
                    <span class="badge <?php echo $commande['bat_type'] === 'label' ? 'bg-warning text-dark' : 'bg-primary'; ?>">
                        <i class="bi bi-<?php echo $commande['bat_type'] === 'label' ? 'tag-fill' : 'printer-fill'; ?> me-1"></i>
                        <?php echo ucfirst($commande['bat_type'] ?? 'print'); ?>
                    </span>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <small class="text-muted d-block">Société</small>
                            <strong><?php echo htmlspecialchars($commande['societe']); ?></strong>
                        </div>
                        <div class="col-md-6">
                            <small class="text-muted d-block">N° Commande Client</small>
                            <strong><?php echo htmlspecialchars($commande['n_commande_client']); ?></strong>
                        </div>
                        <div class="col-md-6">
                            <small class="text-muted d-block">Référence article</small>
                            <?php echo htmlspecialchars($commande['reference_article'] ?: '—'); ?>
                        </div>
                        <div class="col-md-6">
                            <small class="text-muted d-block">Délai de fabrication</small>
                            <?php echo htmlspecialchars($commande['delais_fabrication'] ?: '—'); ?>
                        </div>
                        <?php if (!empty($commande['descriptif']) && $commande['bat_type'] === 'print'): ?>
                        <div class="col-12">
                            <small class="text-muted d-block">Descriptif</small>
                            <div class="border rounded p-2 bg-light" style="font-size:.9rem;">
                                <?php echo $commande['descriptif']; ?>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Fichiers uploadés -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0 font-bold">
                        <i class="bi bi-file-earmark-pdf icon-primary icons"></i>
                        Fichiers PDF
                        <span class="badge bg-secondary ms-2"><?php echo count($fichiers); ?></span>
                    </h5>
                </div>
                <div class="card-body p-0">
                    <?php if (empty($fichiers)): ?>
                        <p class="text-muted p-3 mb-0">Aucun fichier.</p>
                    <?php else: ?>
                    <table class="table table-sm mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Fichier</th>
                                <th>Taille</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($fichiers as $i => $f): ?>
                            <tr>
                                <td class="text-muted small"><?php echo $i + 1; ?></td>
                                <td>
                                    <i class="bi bi-file-earmark-pdf text-danger me-1"></i>
                                    <?php echo htmlspecialchars($f['nom_original']); ?>
                                </td>
                                <td class="text-muted small">
                                    <?php
                                    $t = $f['taille_octets'];
                                    echo $t < 1048576
                                        ? round($t / 1024, 1) . ' Ko'
                                        : round($t / 1048576, 1) . ' Mo';
                                    ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Vérifications prepress (placeholder) -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0 font-bold">
                        <i class="bi bi-shield-check icon-primary icons"></i>Vérifications prepress
                    </h5>
                </div>
                <div class="card-body text-center py-5">
                    <i class="bi bi-hourglass-split text-muted" style="font-size:3rem;"></i>
                    <p class="text-muted mt-3 mb-0">
                        Les vérifications prepress automatiques seront disponibles prochainement.<br>
                        <small>(Résolution, colorimétrie, polices, fonds perdus…)</small>
                    </p>
                </div>
            </div>

            <!-- Actions -->
            <div class="d-flex justify-content-between align-items-center">
                <a href="<?php echo $appBase; ?>/nouvelle" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-1"></i>Retour
                </a>
                <button type="button" class="btn btn-primary" onclick="window.location='<?php echo $appBase; ?>/'">
                    <i class="bi bi-floppy me-2"></i>Enregistrer
                </button>
            </div>

        </div>
    </div>
</div>

<style>
.bat-step-done {
    color: var(--color-primary);
    text-decoration: none;
    font-size: .9rem;
    opacity: .7;
}
.bat-step-done:hover { opacity: 1; }
.bat-step-current {
    font-weight: 600;
    font-size: .9rem;
    color: var(--color-primary);
}
</style>

<?php include __DIR__ . '/layout/footer.php'; ?>
