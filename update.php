<?php
/**
 * Gestionnaire de mises à jour - Gestion des Commandes
 * Télécharge et applique les mises à jour depuis GitHub
 * ATTENTION : Protégez ce fichier - il a accès complet au système de fichiers
 */

ini_set('display_errors', 0);
error_reporting(E_ALL);
session_start();

// =====================================================
// CONFIGURATION
// =====================================================
define('GITHUB_REPO',    'artskory/gestion-commandes');
define('GITHUB_API',     'https://api.github.com/repos/' . GITHUB_REPO);
define('GITHUB_ZIP',     'https://github.com/' . GITHUB_REPO . '/archive/refs/heads/main.zip');
define('APP_CONFIG',     __DIR__ . '/.app_config');
define('BACKUP_DIR',     __DIR__ . '/backups');
define('MIGRATIONS_LOG', __DIR__ . '/.migrations_done');
define('MIGRATIONS_DIR', 'migrations');

// Fichiers et dossiers protégés (jamais écrasés lors d'une mise à jour)
define('WHITELIST', [
    '.htaccess',
    '404.php',
    'classes/Database.php',
    'update.php',
    '.app_config',
    '.installation_complete',
    '.migrations_done',
    'downloads',
    'backups',
]);

// =====================================================
// CHARGEMENT DE LA CONFIGURATION
// =====================================================
if (!file_exists(APP_CONFIG)) {
    die('<div style="font-family:sans-serif;padding:2rem;background:#f8d7da;border-radius:8px;max-width:600px;margin:2rem auto">
        <h2>⚠️ Configuration manquante</h2>
        <p>Le fichier <code>.app_config</code> est introuvable.<br>
        Relancez <a href="install.php">install.php</a> pour configurer l\'application.</p>
    </div>');
}

$config     = json_decode(file_get_contents(APP_CONFIG), true);
$app_folder = $config['app_folder'] ?? 'gestion-commandes';
$basePath   = '/' . $app_folder . '/';

// =====================================================
// FONCTIONS UTILITAIRES
// =====================================================

function getAppVersion() {
    $install = __DIR__ . '/install.php';
    if (!file_exists($install)) return 'inconnue';
    preg_match("/installer_version\s*=\s*'([^']+)'/", file_get_contents($install), $m);
    return $m[1] ?? 'inconnue';
}

function getGithubVersion() {
    $ctx = stream_context_create(['http' => [
        'method'  => 'GET',
        'header'  => "User-Agent: gestion-commandes-updater\r\n",
        'timeout' => 10,
    ]]);
    $json = @file_get_contents(GITHUB_API . '/contents/install.php', false, $ctx);
    if (!$json) return null;
    $data = json_decode($json, true);
    if (empty($data['content'])) return null;
    $content = base64_decode($data['content']);
    preg_match("/installer_version\s*=\s*'([^']+)'/", $content, $m);
    return $m[1] ?? null;
}

function getGithubCommit() {
    $ctx = stream_context_create(['http' => [
        'method'  => 'GET',
        'header'  => "User-Agent: gestion-commandes-updater\r\n",
        'timeout' => 10,
    ]]);
    $json = @file_get_contents(GITHUB_API . '/commits/main', false, $ctx);
    if (!$json) return null;
    $data = json_decode($json, true);
    return [
        'sha'     => substr($data['sha'] ?? '', 0, 7),
        'message' => $data['commit']['message'] ?? '',
        'date'    => $data['commit']['committer']['date'] ?? '',
    ];
}

function getMigrationsDone() {
    if (!file_exists(MIGRATIONS_LOG)) return [];
    return json_decode(file_get_contents(MIGRATIONS_LOG), true) ?? [];
}

function isWhitelisted($path) {
    $path = ltrim(str_replace('\\', '/', $path), '/');
    foreach (WHITELIST as $protected) {
        if ($path === $protected || strpos($path, $protected . '/') === 0) {
            return true;
        }
    }
    return false;
}

function createBackup() {
    if (!is_dir(BACKUP_DIR)) mkdir(BACKUP_DIR, 0755, true);
    $version  = getAppVersion();
    $filename = BACKUP_DIR . '/backup_v' . $version . '_' . date('Ymd_His') . '.zip';
    $zip      = new ZipArchive();
    if ($zip->open($filename, ZipArchive::CREATE) !== true) {
        throw new Exception("Impossible de créer le fichier ZIP de backup.");
    }
    $it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator(__DIR__));
    foreach ($it as $file) {
        if ($file->isDir()) continue;
        $realPath = $file->getRealPath();
        $relPath  = str_replace(__DIR__ . DIRECTORY_SEPARATOR, '', $realPath);
        $relPath  = str_replace('\\', '/', $relPath);
        // Exclure les backups eux-mêmes et les fichiers temporaires
        if (strpos($relPath, 'backups/') === 0) continue;
        if (strpos($relPath, '.tmp_update') !== false) continue;
        $zip->addFile($realPath, $relPath);
    }
    $zip->close();
    return $filename;
}

function downloadUpdate() {
    $tmpDir  = __DIR__ . '/.tmp_update_' . time();
    $zipFile = $tmpDir . '.zip';
    mkdir($tmpDir, 0755, true);

    // Télécharger le ZIP
    $ctx = stream_context_create(['http' => [
        'method'          => 'GET',
        'header'          => "User-Agent: gestion-commandes-updater\r\n",
        'timeout'         => 60,
        'follow_location' => true,
    ]]);
    $data = @file_get_contents(GITHUB_ZIP, false, $ctx);
    if (!$data) throw new Exception("Impossible de télécharger la mise à jour depuis GitHub.");
    file_put_contents($zipFile, $data);

    // Extraire le ZIP
    $zip = new ZipArchive();
    if ($zip->open($zipFile) !== true) throw new Exception("Impossible d'ouvrir le ZIP téléchargé.");
    $zip->extractTo($tmpDir);
    $zip->close();
    unlink($zipFile);

    // Le ZIP GitHub crée un sous-dossier du type "gestion-commandes-main/"
    $dirs = glob($tmpDir . '/*/');
    if (empty($dirs)) throw new Exception("Structure du ZIP inattendue.");
    return [$tmpDir, rtrim($dirs[0], '/')];
}

function applyUpdate($sourceDir) {
    $applied = [];
    $skipped = [];
    $it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($sourceDir));
    foreach ($it as $file) {
        if ($file->isDir()) continue;
        $relPath = ltrim(str_replace($sourceDir, '', $file->getRealPath()), DIRECTORY_SEPARATOR);
        $relPath = str_replace('\\', '/', $relPath);
        $destPath = __DIR__ . '/' . $relPath;
        if (isWhitelisted($relPath)) {
            $skipped[] = $relPath;
            continue;
        }
        // Créer le dossier de destination si nécessaire
        $destDir = dirname($destPath);
        if (!is_dir($destDir)) mkdir($destDir, 0755, true);
        copy($file->getRealPath(), $destPath);
        $applied[] = $relPath;
    }
    return ['applied' => $applied, 'skipped' => $skipped];
}

function runMigrations($sourceDir) {
    $done    = getMigrationsDone();
    $results = [];
    $migDir  = $sourceDir . '/' . MIGRATIONS_DIR;
    if (!is_dir($migDir)) return $results;

    $files = glob($migDir . '/*.php');
    sort($files); // Ordre alphabétique = ordre de version

    foreach ($files as $file) {
        $name = basename($file, '.php');
        if (in_array($name, $done)) {
            $results[] = ['name' => $name, 'status' => 'skipped', 'desc' => 'Déjà appliquée'];
            continue;
        }
        try {
            $migration = require $file;
            // Connexion BDD si nécessaire
            $pdo = null;
            if (isset($migration['up'])) {
                if (file_exists(__DIR__ . '/classes/Database.php')) {
                    require_once __DIR__ . '/classes/Database.php';
                    $db  = new Database();
                    $pdo = $db->getConnection();
                }
                ($migration['up'])($pdo);
            }
            $done[] = $name;
            file_put_contents(MIGRATIONS_LOG, json_encode($done, JSON_PRETTY_PRINT));
            $results[] = ['name' => $name, 'status' => 'ok', 'desc' => $migration['description'] ?? ''];
        } catch (Exception $e) {
            $results[] = ['name' => $name, 'status' => 'error', 'desc' => $e->getMessage()];
        }
    }
    return $results;
}

function cleanTmp($tmpDir) {
    if (!is_dir($tmpDir)) return;
    $it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($tmpDir, RecursiveDirectoryIterator::SKIP_DOTS), RecursiveIteratorIterator::CHILD_FIRST);
    foreach ($it as $f) {
        $f->isDir() ? rmdir($f->getRealPath()) : unlink($f->getRealPath());
    }
    rmdir($tmpDir);
}

function restoreBackup($backupFile) {
    $zip = new ZipArchive();
    if ($zip->open($backupFile) !== true) throw new Exception("Impossible d'ouvrir le backup.");
    $zip->extractTo(__DIR__);
    $zip->close();
}

function getBackups() {
    if (!is_dir(BACKUP_DIR)) return [];
    $files = glob(BACKUP_DIR . '/backup_*.zip');
    rsort($files);
    return array_map(function($f) {
        return ['path' => $f, 'name' => basename($f), 'size' => round(filesize($f) / 1024) . ' Ko'];
    }, $files);
}

// =====================================================
// VÉRIFICATION MOT DE PASSE
// =====================================================
$auth_error = '';
if (isset($_POST['logout'])) {
    unset($_SESSION['update_auth']);
}
if (isset($_POST['password'])) {
    if (password_verify($_POST['password'], $config['update_password'] ?? '')) {
        $_SESSION['update_auth'] = true;
    } else {
        $auth_error = 'Mot de passe incorrect.';
    }
}
$authenticated = !empty($_SESSION['update_auth']);

// =====================================================
// ACTIONS (nécessitent authentification)
// =====================================================
$action_result = null;

if ($authenticated && isset($_POST['action'])) {
    try {
        switch ($_POST['action']) {

            case 'update':
                // 1. Backup
                $backupFile = createBackup();
                // 2. Téléchargement
                [$tmpDir, $sourceDir] = downloadUpdate();
                // 3. Application
                $updateResult = applyUpdate($sourceDir);
                // 4. Migrations
                $migrations = runMigrations($sourceDir);
                // 5. Nettoyage
                cleanTmp($tmpDir);
                $action_result = [
                    'type'       => 'update',
                    'backup'     => basename($backupFile),
                    'applied'    => count($updateResult['applied']),
                    'skipped'    => $updateResult['skipped'],
                    'migrations' => $migrations,
                ];
                break;

            case 'restore':
                $backupName = basename($_POST['backup'] ?? '');
                $backupPath = BACKUP_DIR . '/' . $backupName;
                if (!file_exists($backupPath)) throw new Exception("Backup introuvable.");
                restoreBackup($backupPath);
                $action_result = ['type' => 'restore', 'backup' => $backupName];
                break;

            case 'delete_backup':
                $backupName = basename($_POST['backup'] ?? '');
                $backupPath = BACKUP_DIR . '/' . $backupName;
                if (file_exists($backupPath)) unlink($backupPath);
                $action_result = ['type' => 'delete_backup', 'backup' => $backupName];
                break;
        }
    } catch (Exception $e) {
        $action_result = ['type' => 'error', 'message' => $e->getMessage()];
        // Nettoyage en cas d'erreur
        if (isset($tmpDir) && is_dir($tmpDir)) cleanTmp($tmpDir);
    }
}

// =====================================================
// DONNÉES POUR L'AFFICHAGE
// =====================================================
$current_version = getAppVersion();
$backups         = getBackups();

if ($authenticated && !isset($_POST['action'])) {
    $github_version = getGithubVersion();
    $github_commit  = getGithubCommit();
    $update_available = $github_version && $github_version !== $current_version;
} else {
    $github_version   = null;
    $github_commit    = null;
    $update_available = false;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mise à jour - Gestion des Commandes</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        body {
            background: linear-gradient(135deg, #243142 0%, #1a2535 100%);
            min-height: 100vh;
            padding: 40px 0;
        }
        .update-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.3);
            max-width: 760px;
            margin: 0 auto;
            overflow: hidden;
        }
        .update-header {
            background: linear-gradient(135deg, #243142 0%, #364f6b 100%);
            color: white;
            padding: 28px 36px;
        }
        .update-body { padding: 32px 36px; }
        .version-badge {
            display: inline-block;
            font-family: monospace;
            font-size: 0.9rem;
            padding: 3px 10px;
            border-radius: 20px;
        }
        .v-current { background: #e8f4f8; color: #243142; border: 1px solid #b0d4e3; }
        .v-new     { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .v-same    { background: #e2e3e5; color: #383d41; border: 1px solid #d6d8db; }
        .whitelist-item { font-family: monospace; font-size: 0.82rem; background: #f8f9fa; padding: 2px 8px; border-radius: 4px; margin: 2px; display: inline-block; }
        .migration-ok    { color: #28a745; }
        .migration-skip  { color: #6c757d; }
        .migration-error { color: #dc3545; }
        .backup-row td { vertical-align: middle; font-size: 0.88rem; }
        .btn-update {
            background: linear-gradient(135deg, #5d87ff 0%, #4f73d9 100%);
            border: none; color: white; font-weight: 600;
            padding: 12px 32px; border-radius: 8px; font-size: 1rem;
        }
        .btn-update:hover { background: linear-gradient(135deg, #4f73d9 0%, #3d5fc7 100%); color: white; }
        .section-title { font-size: 1rem; font-weight: 700; color: #243142; margin-bottom: 1rem; border-bottom: 2px solid #e9ecef; padding-bottom: 6px; }
    </style>
</head>
<body>
<div class="container">

    <div class="update-card">
        <div class="update-header">
            <h1 class="mb-1 fs-4"><i class="bi bi-cloud-download me-2"></i>Gestionnaire de mises à jour</h1>
            <p class="mb-0 text-white-50 small">Gestion des Commandes — dépôt <code class="text-white-50">artskory/gestion-commandes</code></p>
        </div>

        <div class="update-body">

        <?php if (!$authenticated): ?>
        <!-- ======= FORMULAIRE DE CONNEXION ======= -->
        <div class="row justify-content-center">
            <div class="col-md-8">
                <h5 class="section-title"><i class="bi bi-lock me-1"></i>Accès sécurisé</h5>
                <?php if ($auth_error): ?>
                    <div class="alert alert-danger"><i class="bi bi-x-circle me-1"></i><?php echo htmlspecialchars($auth_error); ?></div>
                <?php endif; ?>
                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Mot de passe</label>
                        <input type="password" name="password" class="form-control" autofocus required>
                    </div>
                    <button type="submit" class="btn btn-update w-100"><i class="bi bi-unlock me-2"></i>Accéder</button>
                </form>
            </div>
        </div>

        <?php elseif ($action_result): ?>
        <!-- ======= RÉSULTAT DE L'ACTION ======= -->

        <?php if ($action_result['type'] === 'error'): ?>
            <div class="alert alert-danger">
                <h5><i class="bi bi-x-circle-fill me-2"></i>Erreur</h5>
                <?php echo htmlspecialchars($action_result['message']); ?>
            </div>

        <?php elseif ($action_result['type'] === 'restore'): ?>
            <div class="alert alert-success">
                <h5><i class="bi bi-check-circle-fill me-2"></i>Restauration effectuée</h5>
                Le backup <strong><?php echo htmlspecialchars($action_result['backup']); ?></strong> a été restauré avec succès.
            </div>

        <?php elseif ($action_result['type'] === 'delete_backup'): ?>
            <div class="alert alert-info">
                <i class="bi bi-trash me-1"></i>Backup <strong><?php echo htmlspecialchars($action_result['backup']); ?></strong> supprimé.
            </div>

        <?php elseif ($action_result['type'] === 'update'): ?>
            <div class="alert alert-success">
                <h5><i class="bi bi-check-circle-fill me-2"></i>Mise à jour appliquée !</h5>
                <strong><?php echo $action_result['applied']; ?> fichiers</strong> mis à jour.
                Backup créé : <code><?php echo htmlspecialchars($action_result['backup']); ?></code>
            </div>

            <?php if (!empty($action_result['skipped'])): ?>
            <div class="mb-3">
                <div class="section-title">Fichiers protégés (non écrasés)</div>
                <?php foreach ($action_result['skipped'] as $f): ?>
                    <span class="whitelist-item"><i class="bi bi-shield-check text-success me-1"></i><?php echo htmlspecialchars($f); ?></span>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>

            <?php if (!empty($action_result['migrations'])): ?>
            <div class="mb-3">
                <div class="section-title">Migrations</div>
                <table class="table table-sm">
                    <?php foreach ($action_result['migrations'] as $m): ?>
                    <tr>
                        <td>
                            <?php if ($m['status'] === 'ok'): ?>
                                <i class="bi bi-check-circle-fill migration-ok"></i>
                            <?php elseif ($m['status'] === 'skipped'): ?>
                                <i class="bi bi-dash-circle migration-skip"></i>
                            <?php else: ?>
                                <i class="bi bi-x-circle-fill migration-error"></i>
                            <?php endif; ?>
                        </td>
                        <td><code><?php echo htmlspecialchars($m['name']); ?></code></td>
                        <td class="text-muted"><?php echo htmlspecialchars($m['desc']); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </table>
            </div>
            <?php endif; ?>
        <?php endif; ?>

            <div class="d-flex gap-2 mt-3">
                <a href="update.php" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>Retour</a>
                <a href="<?php echo $basePath; ?>" class="btn btn-outline-primary"><i class="bi bi-house me-1"></i>Accueil</a>
            </div>

        <?php else: ?>
        <!-- ======= TABLEAU DE BORD ======= -->

            <!-- Versions -->
            <div class="section-title"><i class="bi bi-info-circle me-1"></i>État de l'application</div>
            <div class="row g-3 mb-4">
                <div class="col-sm-6">
                    <div class="p-3 rounded border">
                        <div class="text-muted small mb-1">Version installée</div>
                        <span class="version-badge v-current">v<?php echo htmlspecialchars($current_version); ?></span>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="p-3 rounded border">
                        <div class="text-muted small mb-1">Version disponible sur GitHub</div>
                        <?php if ($github_version): ?>
                            <span class="version-badge <?php echo $update_available ? 'v-new' : 'v-same'; ?>">
                                v<?php echo htmlspecialchars($github_version); ?>
                            </span>
                            <?php if (!$update_available): ?>
                                <span class="text-success small ms-2"><i class="bi bi-check2"></i> À jour</span>
                            <?php endif; ?>
                        <?php else: ?>
                            <span class="text-muted small">Impossible de vérifier</span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <?php if ($github_commit): ?>
            <div class="p-3 bg-light rounded mb-4 small">
                <strong>Dernier commit GitHub :</strong>
                <code><?php echo htmlspecialchars($github_commit['sha']); ?></code>
                — <?php echo htmlspecialchars(substr($github_commit['message'], 0, 80)); ?>
                <span class="text-muted ms-2"><?php echo date('d/m/Y H:i', strtotime($github_commit['date'])); ?></span>
            </div>
            <?php endif; ?>

            <!-- Bouton de mise à jour -->
            <?php if ($update_available): ?>
            <div class="alert alert-warning d-flex align-items-center gap-3 mb-4">
                <i class="bi bi-exclamation-triangle-fill fs-4"></i>
                <div class="flex-grow-1">
                    <strong>Mise à jour disponible : v<?php echo htmlspecialchars($github_version); ?></strong><br>
                    <small>Un backup complet sera créé automatiquement avant la mise à jour.</small>
                </div>
                <form method="POST" onsubmit="return confirm('Lancer la mise à jour vers v<?php echo htmlspecialchars($github_version); ?> ?\n\nUn backup sera créé automatiquement.')">
                    <input type="hidden" name="action" value="update">
                    <button type="submit" class="btn btn-update btn-sm">
                        <i class="bi bi-cloud-download me-1"></i>Mettre à jour
                    </button>
                </form>
            </div>
            <?php else: ?>
            <div class="alert alert-success mb-4">
                <i class="bi bi-check-circle-fill me-2"></i>
                Votre application est <strong>à jour</strong>.
                <?php if ($github_version): ?>
                    Vous utilisez la dernière version (v<?php echo htmlspecialchars($current_version); ?>).
                <?php endif; ?>
            </div>
            <?php endif; ?>

            <!-- Fichiers protégés -->
            <div class="mb-4">
                <div class="section-title"><i class="bi bi-shield-lock me-1"></i>Fichiers protégés lors des mises à jour</div>
                <?php foreach (WHITELIST as $f): ?>
                    <span class="whitelist-item"><?php echo htmlspecialchars($f); ?></span>
                <?php endforeach; ?>
            </div>

            <!-- Backups & Rollback -->
            <div class="mb-3">
                <div class="section-title"><i class="bi bi-clock-history me-1"></i>Backups & Rollback</div>
                <?php if (empty($backups)): ?>
                    <p class="text-muted small">Aucun backup disponible. Un backup est créé automatiquement avant chaque mise à jour.</p>
                <?php else: ?>
                <table class="table table-sm table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Fichier</th>
                            <th>Taille</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($backups as $b): ?>
                        <tr class="backup-row">
                            <td><i class="bi bi-archive me-1 text-muted"></i><?php echo htmlspecialchars($b['name']); ?></td>
                            <td class="text-muted"><?php echo $b['size']; ?></td>
                            <td class="text-end">
                                <form method="POST" class="d-inline" onsubmit="return confirm('Restaurer ce backup ?\n\nLes fichiers actuels seront écrasés.')">
                                    <input type="hidden" name="action" value="restore">
                                    <input type="hidden" name="backup" value="<?php echo htmlspecialchars($b['name']); ?>">
                                    <button type="submit" class="btn btn-sm btn-outline-warning">
                                        <i class="bi bi-arrow-counterclockwise me-1"></i>Restaurer
                                    </button>
                                </form>
                                <form method="POST" class="d-inline" onsubmit="return confirm('Supprimer ce backup ?')">
                                    <input type="hidden" name="action" value="delete_backup">
                                    <input type="hidden" name="backup" value="<?php echo htmlspecialchars($b['name']); ?>">
                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
                <?php endif; ?>
            </div>

            <!-- Déconnexion -->
            <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
                <a href="<?php echo $basePath; ?>" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-house me-1"></i>Retour à l'application
                </a>
                <form method="POST" class="d-inline">
                    <input type="hidden" name="logout" value="1">
                    <button type="submit" class="btn btn-outline-danger btn-sm">
                        <i class="bi bi-box-arrow-right me-1"></i>Se déconnecter
                    </button>
                </form>
            </div>

        <?php endif; ?>

        </div><!-- /.update-body -->
    </div><!-- /.update-card -->

    <div class="text-center mt-4">
        <small class="text-white-50">Gestion des Commandes — Mise à jour sécurisée</small>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
