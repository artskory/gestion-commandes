<?php
/**
 * SCRIPT DE MISE À JOUR - APPLICATION ÉTIQUETTES
 * 
 * Ce script effectue un git pull tout en préservant les fichiers
 * personnalisés (.htaccess, config/database.php).
 * 
 * PROTECTION : mot de passe requis
 * USAGE      : http://localhost/etiquette-app/update.php
 */

// ========================================
// CONFIGURATION
// ========================================

// Mot de passe pour accéder à la mise à jour
define('UPDATE_PASSWORD', 'update1234');

// Fichiers à préserver pendant la mise à jour
define('PROTECTED_FILES', [
    '.htaccess',
    'config/database.php',
    '.installation_complete',
]);

// Dossier racine de l'application (où se trouve .git)
define('APP_ROOT', __DIR__);

// ========================================
// HELPERS
// ========================================

function renderPage(string $title, string $content, string $bgClass = 'bg-primary'): void {
    ?>
    <!DOCTYPE html>
    <html lang="fr">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title><?php echo htmlspecialchars($title); ?> — Étiquettes</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
        <style>
            body {
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                min-height: 100vh;
                padding: 40px 0;
            }
            .update-container { max-width: 700px; margin: 0 auto; }
            .card { border: none; border-radius: 15px; box-shadow: 0 10px 40px rgba(0,0,0,0.15); }
            .logo { text-align: center; margin-bottom: 30px; }
            .step { border-left: 3px solid #dee2e6; padding-left: 15px; margin-bottom: 10px; }
            .step.success { border-color: #198754; }
            .step.error   { border-color: #dc3545; }
            .step.warning { border-color: #ffc107; }
            .step.info    { border-color: #0dcaf0; }
            pre { background: #f8f9fa; border-radius: 8px; padding: 12px; font-size: 0.8rem; max-height: 200px; overflow-y: auto; }
        </style>
    </head>
    <body>
        <div class="update-container">
            <div class="logo">
                <img src="image/logo.svg" alt="logo" style="max-height:80px;" onerror="this.style.display='none'">
            </div>
            <div class="card">
                <div class="card-body p-5">
                    <?php echo $content; ?>
                </div>
            </div>
            <div class="text-center mt-4 text-white">
                <small>Application Étiquettes — Mise à jour</small>
            </div>
        </div>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    </body>
    </html>
    <?php
}

function step(string $icon, string $message, string $type = 'info', string $detail = ''): string {
    $detail_html = $detail ? '<pre class="mt-2 mb-0">' . htmlspecialchars($detail) . '</pre>' : '';
    return '<div class="step ' . $type . ' mb-3">
        <div><i class="bi bi-' . $icon . ' me-2"></i>' . htmlspecialchars($message) . '</div>
        ' . $detail_html . '
    </div>';
}

// ========================================
// VÉRIFICATION MOT DE PASSE
// ========================================
session_start();

// Déconnexion
if (isset($_GET['logout'])) {
    unset($_SESSION['update_auth']);
    header('Location: update.php');
    exit;
}

// Traitement du formulaire de connexion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['password']) && !isset($_POST['run'])) {
    if ($_POST['password'] === UPDATE_PASSWORD) {
        $_SESSION['update_auth'] = true;
        header('Location: update.php');
        exit;
    } else {
        $loginError = true;
    }
}

// Afficher le formulaire de connexion si non authentifié
if (empty($_SESSION['update_auth'])) {
    $error = isset($loginError) ? '<div class="alert alert-danger"><i class="bi bi-x-circle me-2"></i>Mot de passe incorrect.</div>' : '';
    ob_start();
    ?>
    <h2 class="text-center mb-4">
        <i class="bi bi-arrow-repeat text-primary me-2"></i>Mise à jour
    </h2>
    <p class="text-center text-muted mb-4">Accès restreint — identifiez-vous pour continuer.</p>
    <?php echo $error; ?>
    <form method="POST" action="update.php">
        <div class="mb-4">
            <label for="password" class="form-label">
                <i class="bi bi-lock me-2"></i>Mot de passe
            </label>
            <input type="password" class="form-control" id="password" name="password" required autofocus>
        </div>
        <div class="d-grid">
            <button type="submit" class="btn btn-primary btn-lg">
                <i class="bi bi-box-arrow-in-right me-2"></i>Accéder
            </button>
        </div>
    </form>
    <?php
    $html = ob_get_clean();
    renderPage('Mise à jour', $html);
    exit;
}

// ========================================
// PAGE PRINCIPALE (authentifié)
// ========================================
if ($_SERVER['REQUEST_METHOD'] === 'GET' || (isset($_POST['run']) === false)) {
    // Vérifier git
    exec('git -C ' . escapeshellarg(APP_ROOT) . ' rev-parse --abbrev-ref HEAD 2>&1', $branchOut, $branchCode);
    exec('git -C ' . escapeshellarg(APP_ROOT) . ' log -1 --format="%h — %s (%cr)" 2>&1', $logOut, $logCode);
    exec('git -C ' . escapeshellarg(APP_ROOT) . ' remote show origin 2>&1', $remoteInfoOut);
    $remoteBranchName = 'main';
    foreach ($remoteInfoOut as $line) {
        if (preg_match('/HEAD branch:\s*(\S+)/', $line, $m)) {
            $remoteBranchName = trim($m[1]);
            break;
        }
    }
    exec('git -C ' . escapeshellarg(APP_ROOT) . ' fetch origin 2>&1', $fetchOut, $fetchCode);
    exec('git -C ' . escapeshellarg(APP_ROOT) . ' log HEAD..origin/' . $remoteBranchName . ' --oneline 2>&1', $pendingOut, $pendingCode);

    $branch       = htmlspecialchars(trim($branchOut[0] ?? 'inconnue'));
    $lastCommit   = htmlspecialchars(trim($logOut[0] ?? 'inconnu'));
    $pendingCount = count(array_filter($pendingOut));
    $hasPending   = $pendingCount > 0;

    $pendingList = '';
    if ($hasPending) {
        $pendingList = '<ul class="mb-0 small">';
        foreach ($pendingOut as $line) {
            if (trim($line)) $pendingList .= '<li>' . htmlspecialchars(trim($line)) . '</li>';
        }
        $pendingList .= '</ul>';
    }

    ob_start();
    ?>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">
            <i class="bi bi-arrow-repeat text-primary me-2"></i>Mise à jour
        </h2>
        <a href="update.php?logout=1" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-box-arrow-right me-1"></i>Déconnexion
        </a>
    </div>

    <!-- État actuel -->
    <div class="alert alert-light border mb-4">
        <div class="row">
            <div class="col-6">
                <small class="text-muted d-block">Branche</small>
                <strong><i class="bi bi-git me-1"></i><?php echo $branch; ?></strong>
            </div>
            <div class="col-6">
                <small class="text-muted d-block">Dernier commit</small>
                <strong class="small"><?php echo $lastCommit; ?></strong>
            </div>
        </div>
    </div>

    <!-- Fichiers protégés -->
    <div class="alert alert-info mb-4">
        <h6 class="alert-heading"><i class="bi bi-shield-check me-2"></i>Fichiers protégés (conservés après mise à jour)</h6>
        <ul class="mb-0 small">
            <?php foreach (PROTECTED_FILES as $f): ?>
                <li><code><?php echo htmlspecialchars($f); ?></code>
                    <?php echo file_exists(APP_ROOT . '/' . $f) ? '<span class="text-success">✓ présent</span>' : '<span class="text-muted">absent</span>'; ?>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>

    <!-- Commits en attente -->
    <?php if ($hasPending): ?>
    <div class="alert alert-warning mb-4">
        <h6 class="alert-heading"><i class="bi bi-cloud-download me-2"></i><?php echo $pendingCount; ?> commit(s) disponible(s)</h6>
        <?php echo $pendingList; ?>
    </div>
    <?php else: ?>
    <div class="alert alert-success mb-4">
        <i class="bi bi-check-circle me-2"></i>L'application est déjà à jour.
    </div>
    <?php endif; ?>

    <!-- Bouton lancer -->
    <form method="POST" action="update.php">
        <input type="hidden" name="run" value="1">
        <div class="d-grid">
            <button type="submit" class="btn btn-<?php echo $hasPending ? 'primary' : 'secondary'; ?> btn-lg">
                <i class="bi bi-arrow-repeat me-2"></i>
                <?php echo $hasPending ? 'Lancer la mise à jour' : 'Forcer la mise à jour'; ?>
            </button>
        </div>
    </form>
    <?php
    $html = ob_get_clean();
    renderPage('Mise à jour', $html);
    exit;
}

// ========================================
// EXÉCUTION DE LA MISE À JOUR
// ========================================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['run'])) {

    $steps  = '';
    $errors = false;

    // --- ÉTAPE 1 : Sauvegarder les fichiers protégés ---
    $backups = [];
    foreach (PROTECTED_FILES as $file) {
        $fullPath = APP_ROOT . '/' . $file;
        if (file_exists($fullPath)) {
            $backups[$file] = file_get_contents($fullPath);
            $steps .= step('save', 'Sauvegarde : ' . $file, 'success');
        } else {
            $steps .= step('exclamation-triangle', 'Absent (ignoré) : ' . $file, 'warning');
        }
    }

    // --- ÉTAPE 2 : Détecter la branche principale distante ---
    exec('git -C ' . escapeshellarg(APP_ROOT) . ' remote show origin 2>&1', $remoteOut);
    $remoteBranch = 'main'; // fallback
    foreach ($remoteOut as $line) {
        if (preg_match('/HEAD branch:\s*(\S+)/', $line, $m)) {
            $remoteBranch = trim($m[1]);
            break;
        }
    }

    // --- ÉTAPE 2 : git fetch + merge explicite ---
    exec('git -C ' . escapeshellarg(APP_ROOT) . ' fetch origin 2>&1', $fetchOut, $fetchCode);
    exec(
        'git -C ' . escapeshellarg(APP_ROOT) . ' merge origin/' . escapeshellarg($remoteBranch) . ' 2>&1',
        $pullOut,
        $pullCode
    );
    $pullOutput = 'Branche : ' . $remoteBranch . "\n" . implode("\n", $pullOut);

    if ($pullCode !== 0) {
        $steps  .= step('x-circle', 'Échec du git pull — restauration des fichiers', 'error', $pullOutput);
        $errors  = true;

        // Restaurer immédiatement
        foreach ($backups as $file => $content) {
            file_put_contents(APP_ROOT . '/' . $file, $content);
        }
        $steps .= step('arrow-counterclockwise', 'Fichiers protégés restaurés après échec', 'warning');

    } else {
        $steps .= step('cloud-download', 'git pull effectué avec succès', 'success', $pullOutput);

        // --- ÉTAPE 3 : Restaurer les fichiers protégés ---
        foreach ($backups as $file => $content) {
            if (file_put_contents(APP_ROOT . '/' . $file, $content) !== false) {
                $steps .= step('shield-check', 'Restauré : ' . $file, 'success');
            } else {
                $steps .= step('x-circle', 'Impossible de restaurer : ' . $file, 'error');
                $errors = true;
            }
        }
    }

    // --- RÉSUMÉ ---
    $appUrl = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'])), '/') . '/';

    ob_start();
    ?>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">
            <i class="bi bi-arrow-repeat text-primary me-2"></i>Mise à jour
        </h2>
        <a href="update.php?logout=1" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-box-arrow-right me-1"></i>Déconnexion
        </a>
    </div>

    <?php if (!$errors): ?>
    <div class="alert alert-success mb-4">
        <i class="bi bi-check-circle-fill me-2"></i><strong>Mise à jour terminée avec succès.</strong>
    </div>
    <?php else: ?>
    <div class="alert alert-danger mb-4">
        <i class="bi bi-exclamation-triangle-fill me-2"></i><strong>La mise à jour a rencontré des erreurs.</strong>
    </div>
    <?php endif; ?>

    <h6 class="text-muted mb-3">Détail des opérations</h6>
    <?php echo $steps; ?>

    <div class="d-flex gap-3 mt-4">
        <a href="<?php echo htmlspecialchars($appUrl); ?>" class="btn btn-primary">
            <i class="bi bi-house-door me-2"></i>Accéder à l'application
        </a>
        <a href="update.php" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-2"></i>Retour
        </a>
    </div>
    <?php
    $html = ob_get_clean();
    renderPage('Résultat mise à jour', $html);
    exit;
}
