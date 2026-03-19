<?php
/**
 * update.php — Mise à jour depuis GitHub
 * Gestion des Commandes
 */
ini_set('display_errors', 0);
error_reporting(E_ALL);
session_start();

if (!file_exists('.app_config')) {
    die('<div style="font-family:sans-serif;padding:2rem;background:#f8d7da;border-radius:8px;max-width:600px;margin:2rem auto"><h2>⚠️ Configuration manquante</h2><p>Le fichier <code>.app_config</code> est introuvable.<br>Relancez <a href="install.php">install.php</a>.</p></div>');
}

$config     = json_decode(file_get_contents('.app_config'), true);
require_once __DIR__ . '/version.php';

$APP_FOLDER = $config['app_folder'] ?? 'gestion-commandes';
$BASE_PATH  = '/' . $APP_FOLDER . '/';
$rawRepo    = $config['github_repo'] ?? 'artskory/gestion-commandes';
if (preg_match('#github\.com[/:](.+?)(?:\.git)?$#', $rawRepo, $m)) {
    $GITHUB_REPO = trim($m[1], '/');
} else {
    $GITHUB_REPO = trim($rawRepo, '/');
}
$GITHUB_ZIP = 'https://github.com/' . $GITHUB_REPO . '/archive/refs/heads/main.zip';
$GITHUB_API = 'https://api.github.com/repos/' . $GITHUB_REPO;

define('BACKUP_DIR',     __DIR__ . '/backups');
define('MIGRATIONS_LOG', __DIR__ . '/.migrations_done');
define('MIGRATIONS_DIR', 'migrations');
define('WHITELIST', ['.app_config','.installation_complete','.migrations_done','update.php','backups','downloads','logs']);
define('SMART_FILES', ['classes/Database.php','.htaccess','404.php']);

function isWhitelisted(string $path): bool {
    $path = ltrim(str_replace('\\','/',$path),'/');
    foreach (WHITELIST as $p) { if ($path===$p||strpos($path,$p.'/')===0) return true; }
    return false;
}
function isSmartFile(string $path): bool { return in_array(ltrim(str_replace('\\','/',$path),'/'),SMART_FILES); }

function captureSmartValues(): array {
    $v=[];
    if (file_exists('classes/Database.php')) {
        $c=file_get_contents('classes/Database.php');
        preg_match("/private \\\$host\s*=\s*'([^']*)'/",    $c,$m); $v['db_host']    =$m[1]??'';
        preg_match("/private \\\$db_name\s*=\s*'([^']*)'/", $c,$m); $v['db_name']    =$m[1]??'';
        preg_match("/private \\\$username\s*=\s*'([^']*)'/", $c,$m); $v['db_username']=$m[1]??'';
        preg_match("/private \\\$password\s*=\s*'([^']*)'/", $c,$m); $v['db_password']=$m[1]??'';
    }
    if (file_exists('.htaccess')) {
        $h=file_get_contents('.htaccess');
        preg_match('/RewriteBase\s+(\S+)/i',          $h,$m); $v['rewrite_base']=$m[1]??'/';
        preg_match('/ErrorDocument\s+404\s+(\S+)/i',  $h,$m); $v['error_404']   =$m[1]??'';
        preg_match('/ErrorDocument\s+403\s+(\S+)/i',  $h,$m); $v['error_403']   =$m[1]??'';
    }
    if (file_exists('404.php')) {
        preg_match("/\\\$basePath\s*=\s*'([^']*)'/",file_get_contents('404.php'),$m);
        $v['base_path']=$m[1]??'/';
    }
    return $v;
}

function reinjSmartValues(array $v): array {
    $results=[];
    if (file_exists('classes/Database.php')&&!empty($v['db_name'])) {
        $c=file_get_contents('classes/Database.php');
        $c=preg_replace("/private \\\$host\s*=\s*'[^']*'/"    ,"private \$host = '".addslashes($v['db_host'])."'"    ,$c);
        $c=preg_replace("/private \\\$db_name\s*=\s*'[^']*'/" ,"private \$db_name = '".addslashes($v['db_name'])."'" ,$c);
        $c=preg_replace("/private \\\$username\s*=\s*'[^']*'/","private \$username = '".addslashes($v['db_username'])."'",$c);
        $c=preg_replace("/private \\\$password\s*=\s*'[^']*'/","private \$password = '".addslashes($v['db_password'])."'",$c);
        file_put_contents('classes/Database.php',$c);
        $results[]=['ok','classes/Database.php — paramètres BDD réinjectés'];
    }
    if (file_exists('.htaccess')&&!empty($v['rewrite_base'])) {
        $h=file_get_contents('.htaccess');
        $h=preg_replace('/RewriteBase\s+\S+/i','RewriteBase '.$v['rewrite_base'],$h);
        if (!empty($v['error_404'])) $h=preg_replace('/ErrorDocument\s+404\s+\S+/i','ErrorDocument 404 '.$v['error_404'],$h);
        if (!empty($v['error_403'])) $h=preg_replace('/ErrorDocument\s+403\s+\S+/i','ErrorDocument 403 '.$v['error_403'],$h);
        file_put_contents('.htaccess',$h);
        $results[]=['ok','.htaccess — RewriteBase réinjecté : <code>'.htmlspecialchars($v['rewrite_base']).'</code>'];
    }
    if (file_exists('404.php')&&!empty($v['base_path'])) {
        $e=file_get_contents('404.php');
        $e=preg_replace("/\\\$basePath\s*=\s*'[^']*';/","\$basePath = '".$v['base_path']."';",$e);
        file_put_contents('404.php',$e);
        $results[]=['ok','404.php — $basePath réinjecté : <code>'.htmlspecialchars($v['base_path']).'</code>'];
    }
    return $results;
}

function curlGet(string $url): ?string {
    if (function_exists('curl_init')) {
        $ch=curl_init($url);
        curl_setopt_array($ch,[CURLOPT_RETURNTRANSFER=>true,CURLOPT_TIMEOUT=>15,CURLOPT_FOLLOWLOCATION=>true,CURLOPT_USERAGENT=>'gestion-commandes-updater',CURLOPT_SSL_VERIFYPEER=>false,CURLOPT_SSL_VERIFYHOST=>0]);
        $r=curl_exec($ch); $e=curl_error($ch);
        return ($r!==false&&empty($e))?$r:null;
    }
    $ctx=stream_context_create(['http'=>['method'=>'GET','header'=>"User-Agent: gestion-commandes-updater\r\n",'timeout'=>15],'ssl'=>['verify_peer'=>false,'verify_peer_name'=>false]]);
    $r=@file_get_contents($url,false,$ctx);
    return $r!==false?$r:null;
}

function createBackup(): string {
    if (!is_dir(BACKUP_DIR)) mkdir(BACKUP_DIR,0755,true);
    $fn=BACKUP_DIR.'/backup_'.date('Ymd_His').'.zip';
    $zip=new ZipArchive();
    if ($zip->open($fn,ZipArchive::CREATE)!==true) throw new Exception("Impossible de créer le backup ZIP.");
    $it=new RecursiveIteratorIterator(new RecursiveDirectoryIterator(__DIR__,RecursiveDirectoryIterator::SKIP_DOTS));
    foreach ($it as $file) {
        if ($file->isDir()) continue;
        $rel=str_replace('\\','/',str_replace(__DIR__.DIRECTORY_SEPARATOR,'',$file->getRealPath()));
        if (strpos($rel,'backups/')===0) continue;
        $zip->addFile($file->getRealPath(),$rel);
    }
    $zip->close();
    return $fn;
}

function downloadAndExtract(): array {
    global $GITHUB_ZIP;
    $tmpDir=__DIR__.'/.tmp_update_'.time(); $zipFile=$tmpDir.'.zip';
    mkdir($tmpDir,0755,true);
    $data=curlGet($GITHUB_ZIP);
    if (!$data) throw new Exception("Impossible de télécharger depuis GitHub.");
    file_put_contents($zipFile,$data);
    $zip=new ZipArchive();
    if ($zip->open($zipFile)!==true) throw new Exception("Impossible d'ouvrir le ZIP.");
    $zip->extractTo($tmpDir); $zip->close(); unlink($zipFile);
    $dirs=glob($tmpDir.'/*/');
    if (empty($dirs)) throw new Exception("Structure ZIP inattendue.");
    return [$tmpDir,rtrim($dirs[0],'/')];
}

function applyUpdate(string $sourceDir): array {
    $applied=$skipped=$smart=[];
    $sourceDir=rtrim(str_replace('\\','/',$sourceDir),'/');
    $it=new RecursiveIteratorIterator(new RecursiveDirectoryIterator($sourceDir,RecursiveDirectoryIterator::SKIP_DOTS));
    foreach ($it as $file) {
        if ($file->isDir()) continue;
        $real=str_replace('\\','/',$file->getRealPath());
        $rel=ltrim(str_replace($sourceDir.'/','',$real),'/');
        $dest=__DIR__.'/'.$rel;
        if (isWhitelisted($rel)){$skipped[]=$rel;continue;}
        $d=dirname($dest); if (!is_dir($d)) mkdir($d,0755,true);
        copy($real,$dest);
        if (isSmartFile($rel)){$smart[]=$rel;}else{$applied[]=$rel;}
    }
    return ['applied'=>$applied,'skipped'=>$skipped,'smart'=>$smart];
}

function runMigrations(string $sourceDir): array {
    $done=file_exists(MIGRATIONS_LOG)?(json_decode(file_get_contents(MIGRATIONS_LOG),true)??[]):[];
    $results=[];
    $migDir=rtrim(str_replace('\\','/',$sourceDir),'/').'/'.MIGRATIONS_DIR;
    if (!is_dir($migDir)) return $results;
    $files=glob($migDir.'/*.php'); sort($files);
    foreach ($files as $file) {
        $name=basename($file,'.php');
        if (in_array($name,$done)){$results[]=['name'=>$name,'status'=>'skipped','desc'=>'Déjà appliquée'];continue;}
        try {
            $migration=require $file;
            if (isset($migration['up'])) {
                $pdo=null;
                if (file_exists(__DIR__.'/classes/Database.php')){require_once __DIR__.'/classes/Database.php';$db=new Database();$pdo=$db->getConnection();}
                ($migration['up'])($pdo);
            }
            $done[]=$name; file_put_contents(MIGRATIONS_LOG,json_encode($done,JSON_PRETTY_PRINT));
            $results[]=['name'=>$name,'status'=>'ok','desc'=>$migration['description']??''];
        } catch (Exception $e) {$results[]=['name'=>$name,'status'=>'error','desc'=>$e->getMessage()];}
    }
    return $results;
}

function cleanTmp(string $d): void {
    if (!is_dir($d)) return;
    $it=new RecursiveIteratorIterator(new RecursiveDirectoryIterator($d,RecursiveDirectoryIterator::SKIP_DOTS),RecursiveIteratorIterator::CHILD_FIRST);
    foreach ($it as $f){$f->isDir()?rmdir($f->getRealPath()):unlink($f->getRealPath());}
    rmdir($d);
}
function cleanOldBackups(int $keep=3): void {
    if (!is_dir(BACKUP_DIR)) return;
    $files=glob(BACKUP_DIR.'/backup_*.zip');
    if (!$files||count($files)<=$keep) return;
    rsort($files); foreach (array_slice($files,$keep) as $f) unlink($f);
}
function getBackups(): array {
    if (!is_dir(BACKUP_DIR)) return [];
    $files=glob(BACKUP_DIR.'/backup_*.zip'); rsort($files);
    return array_map(fn($f)=>['path'=>$f,'name'=>basename($f),'size'=>round(filesize($f)/1024).' Ko','date'=>date('d/m/Y H:i',filemtime($f))],$files);
}
function getLocalVersion(): string {
    if (file_exists(__DIR__.'/version.php')){preg_match("/define\('APP_VERSION',\s*'([^']+)'/",file_get_contents(__DIR__.'/version.php'),$m);return $m[1]??'?';}
    return defined('APP_VERSION')?APP_VERSION:'?';
}
function getRemoteVersionFile(): ?string { global $GITHUB_REPO; return curlGet('https://raw.githubusercontent.com/'.$GITHUB_REPO.'/main/version.php'); }
function getRemoteVersion(): ?string { $c=getRemoteVersionFile(); if (!$c) return null; preg_match("/define\('APP_VERSION',\s*'([^']+)'/", $c, $m); return $m[1]??null; }
function getRemoteVersionDate(): ?string { $c=getRemoteVersionFile(); if (!$c) return null; preg_match("/define\('APP_VERSION_DATE',\s*'([^']+)'/", $c, $m); return $m[1]??null; }
function isUpToDate(string $l, string $r): bool { return version_compare($l,$r,'>='); }
function getGithubInfo(): array {
    global $GITHUB_API;
    $json=curlGet($GITHUB_API.'/commits/main'); if (!$json) return [];
    $data=json_decode($json,true); if (!$data||isset($data['message'])) return [];
    return ['sha'=>substr($data['sha']??'',0,7),'message'=>$data['commit']['message']??'','date'=>isset($data['commit']['committer']['date'])?date('d/m/Y H:i',strtotime($data['commit']['committer']['date'])):''];
}

// ── Auth ──────────────────────────────────────────────────────────────────────
if (isset($_POST['logout'])) unset($_SESSION['update_auth'],$_SESSION['update_auth_time']);
if (!empty($_SESSION['update_auth'])&&time()-($_SESSION['update_auth_time']??0)>1800) {
    unset($_SESSION['update_auth'],$_SESSION['update_auth_time']);
    $authError='Session expirée. Veuillez vous reconnecter.';
}
if (isset($_POST['password'])) {
    if (password_verify($_POST['password'],$config['update_password']??'')) {
        $_SESSION['update_auth']=true; $_SESSION['update_auth_time']=time();
    } else { $authError='Mot de passe incorrect.'; }
}
$authenticated=!empty($_SESSION['update_auth']);

// ── Actions ───────────────────────────────────────────────────────────────────
$actionResult=null;
if ($authenticated&&isset($_POST['action'])) {
    try {
        switch ($_POST['action']) {
            case 'update':
                $smartValues=captureSmartValues();
                $backupFile=createBackup(); cleanOldBackups(3);
                [$tmpDir,$sourceDir]=downloadAndExtract();
                $updateResult=applyUpdate($sourceDir);
                $reinjResults=reinjSmartValues($smartValues);
                $migrations=runMigrations($sourceDir);
                cleanTmp($tmpDir);
                if (function_exists('opcache_reset')) opcache_reset();
                if (file_exists('.app_config')) {
                    $cfg=json_decode(file_get_contents('.app_config'),true)??[];
                    preg_match("/define\('APP_VERSION',\s*'([^']+)'/",file_get_contents(__DIR__.'/version.php'),$mv);
                    $cfg['version']=$mv[1]??($cfg['version']??'?');
                    file_put_contents('.app_config',json_encode($cfg,JSON_PRETTY_PRINT));
                }
                $actionResult=['type'=>'update','backup'=>basename($backupFile),'applied'=>count($updateResult['applied']),'smart'=>$updateResult['smart'],'skipped'=>$updateResult['skipped'],'reinj'=>$reinjResults,'migrations'=>$migrations];
                break;
            case 'restore':
                $bp=BACKUP_DIR.'/'.basename($_POST['backup']??'');
                if (!file_exists($bp)) throw new Exception("Backup introuvable.");
                $zip=new ZipArchive(); if ($zip->open($bp)!==true) throw new Exception("Impossible d'ouvrir le backup.");
                $zip->extractTo(__DIR__); $zip->close();
                $actionResult=['type'=>'restore','backup'=>basename($bp)];
                break;
            case 'delete_backup':
                $bp=BACKUP_DIR.'/'.basename($_POST['backup']??'');
                if (file_exists($bp)) unlink($bp);
                $actionResult=['type'=>'delete_backup','backup'=>basename($bp)];
                break;
        }
    } catch (Exception $e) {
        $actionResult=['type'=>'error','message'=>$e->getMessage()];
        if (isset($tmpDir)&&is_dir($tmpDir)) cleanTmp($tmpDir);
    }
}

$backups=$localVersion=$remoteVersion=$remoteDate=$githubInfo=null;
$backups=getBackups(); $localVersion=getLocalVersion();
if ($authenticated&&!isset($_POST['action'])) {
    $githubInfo=getGithubInfo(); $remoteVersion=getRemoteVersion(); $remoteDate=getRemoteVersionDate();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mise à jour — Gestion des Commandes</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="icon" type="image/png" href="<?= htmlspecialchars($BASE_PATH) ?>image/favicon-96x96.png" sizes="96x96">
    <link rel="icon" type="image/svg+xml" href="<?= htmlspecialchars($BASE_PATH) ?>image/favicon.svg">
    <link rel="shortcut icon" href="<?= htmlspecialchars($BASE_PATH) ?>image/favicon.ico">
    <style>
        body{background:linear-gradient(135deg,#243142 0%,#1a2535 100%);min-height:100vh;padding:40px 0;}
        .update-card{background:#fff;border-radius:15px;box-shadow:0 10px 40px rgba(0,0,0,.3);max-width:800px;margin:0 auto;overflow:hidden;}
        .update-header{background:linear-gradient(135deg,#243142 0%,#364f6b 100%);color:#fff;padding:28px 36px;}
        .update-body{padding:32px 36px;}
        .section-title{font-size:.9rem;font-weight:700;color:#444;text-transform:uppercase;letter-spacing:.05em;border-bottom:2px solid #e9ecef;padding-bottom:6px;margin:24px 0 16px;}
        .whitelist-item{font-family:monospace;font-size:.8rem;background:#f8f9fa;padding:2px 8px;border-radius:4px;margin:2px;display:inline-block;}
        .smart-item{font-family:monospace;font-size:.8rem;background:#e8f4f8;padding:2px 8px;border-radius:4px;margin:2px;display:inline-block;color:#1a6b8a;}
        .btn-update{background:linear-gradient(135deg,#5d87ff,#4f73d9);border:none;color:#fff;font-weight:600;padding:12px 32px;border-radius:8px;}
        .btn-update:hover{background:linear-gradient(135deg,#4f73d9,#3d5fc7);color:#fff;}
        .migration-ok{color:#28a745;}.migration-skip{color:#6c757d;}.migration-error{color:#dc3545;}
    </style>
</head>
<body>
<div class="container">
<div class="update-card">
    <div class="update-header">
        <h1 class="mb-1 fs-4"><i class="bi bi-cloud-download me-2"></i>Mise à jour</h1>
        <p class="mb-0 text-white-50 small">Gestion des Commandes — <code class="text-white-50"><?= htmlspecialchars($GITHUB_REPO?:'dépôt non configuré') ?></code></p>
    </div>
    <div class="update-body">

    <?php if (!$authenticated): ?>
    <div class="row justify-content-center"><div class="col-md-7">
        <h5 class="section-title"><i class="bi bi-lock me-1"></i>Accès sécurisé</h5>
        <?php if (isset($authError)): ?><div class="alert alert-danger"><i class="bi bi-x-circle me-1"></i><?= htmlspecialchars($authError) ?></div><?php endif; ?>
        <form method="POST">
            <div class="mb-3">
                <label class="form-label fw-semibold">Mot de passe</label>
                <div class="input-group">
                    <input type="password" name="password" class="form-control" id="lp" autofocus required>
                    <button class="btn btn-outline-secondary" type="button" onclick="let i=document.getElementById('lp');i.type=i.type==='password'?'text':'password'"><i class="bi bi-eye"></i></button>
                </div>
            </div>
            <button type="submit" class="btn btn-update w-100"><i class="bi bi-unlock me-2"></i>Accéder</button>
        </form>
    </div></div>

    <?php elseif ($actionResult): ?>
        <?php if ($actionResult['type']==='error'): ?>
            <div class="alert alert-danger"><h5><i class="bi bi-x-circle-fill me-2"></i>Erreur</h5><?= htmlspecialchars($actionResult['message']) ?></div>
        <?php elseif ($actionResult['type']==='restore'): ?>
            <div class="alert alert-success"><h5><i class="bi bi-check-circle-fill me-2"></i>Restauration effectuée</h5>Backup <strong><?= htmlspecialchars($actionResult['backup']) ?></strong> restauré.</div>
        <?php elseif ($actionResult['type']==='delete_backup'): ?>
            <div class="alert alert-info"><i class="bi bi-trash me-1"></i>Backup <strong><?= htmlspecialchars($actionResult['backup']) ?></strong> supprimé.</div>
        <?php elseif ($actionResult['type']==='update'): ?>
            <div class="alert alert-success">
                <h5><i class="bi bi-check-circle-fill me-2"></i>Mise à jour appliquée !</h5>
                <strong><?= $actionResult['applied'] ?></strong> fichiers mis à jour. Backup : <code><?= htmlspecialchars($actionResult['backup']) ?></code>
            </div>
            <?php if (!empty($actionResult['smart'])): ?>
            <div class="mb-3"><div class="section-title"><i class="bi bi-arrow-repeat me-1"></i>Traitement intelligent</div>
                <?php foreach ($actionResult['smart'] as $f): ?><span class="smart-item"><i class="bi bi-shield-check me-1"></i><?= htmlspecialchars($f) ?></span><?php endforeach; ?>
            </div><?php endif; ?>
            <?php if (!empty($actionResult['reinj'])): ?>
            <div class="mb-3"><div class="section-title"><i class="bi bi-arrow-left-right me-1"></i>Valeurs réinjectées</div>
                <?php foreach ($actionResult['reinj'] as [$t,$m]): ?>
                <div class="d-flex gap-2 mb-1"><i class="bi bi-check-circle-fill text-success mt-1"></i><span class="small"><?= $m ?></span></div>
                <?php endforeach; ?>
            </div><?php endif; ?>
            <?php if (!empty($actionResult['skipped'])): ?>
            <div class="mb-3"><div class="section-title"><i class="bi bi-shield-fill me-1"></i>Fichiers protégés</div>
                <?php foreach ($actionResult['skipped'] as $f): ?><span class="whitelist-item"><?= htmlspecialchars($f) ?></span><?php endforeach; ?>
            </div><?php endif; ?>
            <?php if (!empty($actionResult['migrations'])): ?>
            <div class="mb-3"><div class="section-title"><i class="bi bi-database-up me-1"></i>Migrations</div>
                <table class="table table-sm"><?php foreach ($actionResult['migrations'] as $mg): ?>
                <tr><td width="24"><?php if ($mg['status']==='ok'): ?><i class="bi bi-check-circle-fill migration-ok"></i><?php elseif ($mg['status']==='skipped'): ?><i class="bi bi-dash-circle migration-skip"></i><?php else: ?><i class="bi bi-x-circle-fill migration-error"></i><?php endif; ?></td>
                <td><code><?= htmlspecialchars($mg['name']) ?></code></td><td class="text-muted small"><?= htmlspecialchars($mg['desc']) ?></td></tr>
                <?php endforeach; ?></table>
            </div><?php endif; ?>
        <?php endif; ?>
        <div class="d-flex gap-2 mt-3">
            <a href="update.php" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>Retour</a>
            <a href="<?= htmlspecialchars($BASE_PATH) ?>" class="btn btn-outline-primary"><i class="bi bi-house me-1"></i>Application</a>
        </div>

    <?php else: ?>
        <div class="section-title"><i class="bi bi-info-circle me-1"></i>État de l'application</div>
        <div class="row g-3 mb-4">
            <div class="col-sm-6"><div class="p-3 rounded border">
                <div class="text-muted small mb-1">Version installée</div>
                <span class="badge bg-secondary fs-6 font-monospace">v<?= htmlspecialchars($localVersion) ?></span>
            </div></div>
            <div class="col-sm-6"><div class="p-3 rounded border">
                <div class="text-muted small mb-1">Dernière version disponible</div>
                <?php if ($remoteVersion): ?>
                    <?php if (isUpToDate($localVersion,$remoteVersion)): ?>
                        <span class="badge bg-success fs-6 font-monospace">v<?= htmlspecialchars($remoteVersion) ?></span>
                        <span class="text-success small ms-2"><i class="bi bi-check2"></i> À jour</span>
                    <?php else: ?>
                        <span class="badge bg-warning text-dark fs-6 font-monospace">v<?= htmlspecialchars($remoteVersion) ?></span>
                        <?php if ($remoteDate): ?><span class="text-muted small ms-2"><?= htmlspecialchars($remoteDate) ?></span><?php endif; ?>
                    <?php endif; ?>
                <?php else: ?><span class="text-muted small">Impossible de joindre GitHub</span><?php endif; ?>
            </div></div>
        </div>

        <?php if (!empty($githubInfo)): ?>
        <div class="p-3 bg-light rounded mb-3 small">
            <strong>Dernier commit :</strong> <code><?= htmlspecialchars($githubInfo['sha']) ?></code>
            — <?= htmlspecialchars(substr($githubInfo['message'],0,80)) ?>
            <span class="text-muted ms-2"><?= htmlspecialchars($githubInfo['date']) ?></span>
        </div>
        <?php endif; ?>

        <div class="mb-4">
        <?php if ($remoteVersion&&isUpToDate($localVersion,$remoteVersion)): ?>
            <div class="alert alert-success mb-0"><i class="bi bi-check-circle-fill me-2"></i>Votre application est à jour <strong>(v<?= htmlspecialchars($localVersion) ?>)</strong>.</div>
        <?php elseif ($remoteVersion&&!isUpToDate($localVersion,$remoteVersion)): ?>
            <div class="alert alert-warning mb-3"><i class="bi bi-exclamation-triangle-fill me-2"></i>Mise à jour disponible : <strong>v<?= htmlspecialchars($remoteVersion) ?></strong> (installée : v<?= htmlspecialchars($localVersion) ?>)</div>
            <form method="POST" onsubmit="return confirm('Lancer la mise à jour vers v<?= htmlspecialchars($remoteVersion) ?> ?\n\nUn backup complet sera créé automatiquement.')">
                <input type="hidden" name="action" value="update">
                <button type="submit" class="btn btn-update"><i class="bi bi-cloud-download me-2"></i>Mettre à jour depuis GitHub</button>
            </form>
            <p class="text-muted small mt-2">Un backup sera créé. Vos paramètres (BDD, dossier) seront préservés.</p>
        <?php else: ?>
            <div class="alert alert-secondary mb-3"><i class="bi bi-wifi-off me-2"></i>Impossible de vérifier la version distante.</div>
            <button class="btn btn-update" disabled style="opacity:.5;cursor:not-allowed;"><i class="bi bi-cloud-download me-2"></i>Mettre à jour depuis GitHub</button>
        <?php endif; ?>
        </div>

        <div class="section-title"><i class="bi bi-shield-fill me-1"></i>Fichiers protégés</div>
        <div class="mb-3"><?php foreach (WHITELIST as $f): ?><span class="whitelist-item"><?= htmlspecialchars($f) ?></span><?php endforeach; ?></div>

        <div class="section-title"><i class="bi bi-arrow-repeat me-1"></i>Fichiers mis à jour intelligemment</div>
        <div class="mb-4">
            <?php foreach (SMART_FILES as $f): ?><span class="smart-item"><i class="bi bi-shield-check me-1"></i><?= htmlspecialchars($f) ?></span><?php endforeach; ?>
            <p class="text-muted small mt-2">Ces fichiers sont mis à jour depuis GitHub, mais vos valeurs (BDD, RewriteBase, basePath) sont capturées avant et réinjectées après.</p>
        </div>

        <div class="section-title"><i class="bi bi-clock-history me-1"></i>Backups & Rollback</div>
        <?php if (empty($backups)): ?>
            <p class="text-muted small">Aucun backup. Un backup complet est créé automatiquement avant chaque mise à jour.</p>
        <?php else: ?>
        <table class="table table-sm table-hover mb-4">
            <thead class="table-light"><tr><th>Fichier</th><th>Date</th><th>Taille</th><th class="text-end">Actions</th></tr></thead>
            <tbody>
            <?php foreach ($backups as $b): ?>
            <tr>
                <td class="small"><i class="bi bi-archive me-1 text-muted"></i><?= htmlspecialchars($b['name']) ?></td>
                <td class="small text-muted"><?= $b['date'] ?></td>
                <td class="small text-muted"><?= $b['size'] ?></td>
                <td class="text-end">
                    <form method="POST" class="d-inline" onsubmit='return confirm("Restaurer ce backup ?")'>
                        <input type="hidden" name="action" value="restore">
                        <input type="hidden" name="backup" value="<?= htmlspecialchars($b['name']) ?>">
                        <button class="btn btn-sm btn-outline-warning"><i class="bi bi-arrow-counterclockwise me-1"></i>Restaurer</button>
                    </form>
                    <form method="POST" class="d-inline" onsubmit='return confirm("Supprimer ce backup ?")'>
                        <input type="hidden" name="action" value="delete_backup">
                        <input type="hidden" name="backup" value="<?= htmlspecialchars($b['name']) ?>">
                        <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>

        <div class="d-flex justify-content-between align-items-center pt-3 border-top">
            <a href="<?= htmlspecialchars($BASE_PATH) ?>" class="btn btn-outline-secondary btn-sm"><i class="bi bi-house me-1"></i>Application</a>
            <form method="POST"><input type="hidden" name="logout" value="1">
                <button class="btn btn-outline-danger btn-sm"><i class="bi bi-box-arrow-right me-1"></i>Déconnexion</button>
            </form>
        </div>
    <?php endif; ?>

    </div>
</div>
<div class="text-center mt-4"><small class="text-white-50">Gestion des Commandes — Mise à jour sécurisée</small></div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body></html>
