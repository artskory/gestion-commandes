<?php
/**
 * SCRIPT D'INSTALLATION - GESTION DES COMMANDES
 * La version est gérée automatiquement via version.php
 */

require_once __DIR__ . '/version.php';

// ── Protection contre double installation ─────────────────────────────────────
if (file_exists('.installation_complete')) {
    $installDate = file_get_contents('.installation_complete');
    $appUrl = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'])), '/') . '/';
    ?><!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Installation déjà effectuée</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="icon" type="image/png" href="image/favicon-96x96.png" sizes="96x96">
    <link rel="icon" type="image/svg+xml" href="image/favicon.svg">
    <link rel="shortcut icon" href="image/favicon.ico">
    <style>body{background:linear-gradient(135deg,#243142 0%,#1a2535 100%);min-height:100vh;display:flex;align-items:center;justify-content:center;}.card{border:none;border-radius:15px;box-shadow:0 10px 40px rgba(0,0,0,.3);max-width:600px;}</style>
</head>
<body>
    <div class="card"><div class="card-body p-5 text-center">
        <i class="bi bi-shield-fill-check text-success" style="font-size:4rem;"></i>
        <h2 class="mt-4">Installation déjà effectuée</h2>
        <p class="text-muted">Installée le <strong><?= htmlspecialchars($installDate) ?></strong></p>
        <div class="alert alert-info text-start mt-4">
            <h6><i class="bi bi-info-circle me-2"></i>Pour réinstaller :</h6>
            <ol class="mb-0 small"><li>Supprimez <code>.installation_complete</code></li><li>Supprimez la base de données</li><li>Rechargez cette page</li></ol>
        </div>
        <a href="<?= htmlspecialchars($appUrl) ?>" class="btn btn-primary btn-lg mt-4"><i class="bi bi-house-door me-2"></i>Accéder à l'application</a>
    </div></div>
</body></html><?php
    exit;
}

error_reporting(E_ALL);
ini_set('display_errors', 1);

function safe_write($file, $data) {
    if (file_exists($file) && !is_writable($file)) @chmod($file, 0664);
    $result = @file_put_contents($file, $data);
    if ($result === false) throw new Exception("Impossible d'écrire dans <code>$file</code>.");
    return $result;
}

// ── Formulaire ────────────────────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { ?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Installation — Gestion des Commandes</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="css/style.css">
    <link rel="icon" type="image/png" href="image/favicon-96x96.png" sizes="96x96">
    <link rel="icon" type="image/svg+xml" href="image/favicon.svg">
    <link rel="shortcut icon" href="image/favicon.ico">
    <link rel="apple-touch-icon" sizes="180x180" href="image/apple-touch-icon.png">
    <style>
        body{background:linear-gradient(135deg,#243142 0%,#1a2535 100%);min-height:100vh;padding:40px 0;}
        .install-container{max-width:720px;margin:0 auto;}
        .card{border:none;border-radius:15px;box-shadow:0 10px 40px rgba(0,0,0,.3);}
        .section-title{font-weight:700;color:#444;border-bottom:2px solid #e9ecef;padding-bottom:6px;margin:28px 0 18px;font-size:.95rem;text-transform:uppercase;letter-spacing:.05em;}
        .passphrase-box{font-family:monospace;font-size:1.1rem;background:#f0f4ff;border:2px solid #b0c4ff;border-radius:8px;padding:10px 14px;cursor:pointer;user-select:all;}
        .strength-bar{height:6px;border-radius:3px;transition:all .3s;}
    </style>
</head>
<body>
<div class="install-container">
    <div class="card"><div class="card-body p-5">
        <h1 class="text-center mb-1"><i class="bi bi-gear text-primary"></i> Installation</h1>
        <p class="text-center text-muted mb-4">Gestion des Commandes v<?= APP_VERSION ?></p>

        <form method="POST" action="install.php" id="installForm">

            <div class="section-title"><i class="bi bi-database me-2"></i>Base de données</div>
            <div class="row g-3 mb-3">
                <div class="col-md-4">
                    <label class="form-label">Hôte MySQL</label>
                    <input type="text" class="form-control" name="db_host" value="localhost" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Utilisateur</label>
                    <input type="text" class="form-control" name="db_user" value="root" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Mot de passe MySQL</label>
                    <input type="password" class="form-control" name="db_pass" placeholder="Vide si XAMPP">
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label">Nom de la base de données <span class="text-danger">*</span></label>
                <input type="text" class="form-control" name="db_name" value="gestion_commandes" required
                       pattern="[a-zA-Z0-9_]+" title="Lettres, chiffres et underscores uniquement">
            </div>

            <div class="section-title"><i class="bi bi-folder me-2"></i>Application</div>
            <div class="mb-3">
                <label class="form-label">Nom du dossier <span class="text-danger">*</span></label>
                <div class="input-group">
                    <span class="input-group-text text-muted">localhost/</span>
                    <input type="text" class="form-control" name="app_folder" value="gestion-commandes" required
                           pattern="[a-zA-Z0-9_\-]+" title="Lettres, chiffres, tirets et underscores">
                </div>
                <div class="form-text">Mis à jour dans <code>.htaccess</code> et <code>404.php</code></div>
            </div>
            <div class="mb-3">
                <label class="form-label">Dépôt GitHub <span class="text-danger">*</span></label>
                <div class="input-group">
                    <span class="input-group-text text-muted">github.com/</span>
                    <input type="text" class="form-control" name="github_repo"
                           value="artskory/gestion-commandes" required placeholder="compte/depot">
                </div>
                <div class="form-text">Utilisé par le système de mise à jour</div>
            </div>
            <div class="mb-3">
                <label class="form-label">URL Dolibarr</label>
                <input type="url" class="form-control" name="dolibarr_url"
                       value="https://" placeholder="https://crm.mondomaine.fr">
                <div class="form-text">URL de votre Dolibarr — utilisée pour le bookmarklet</div>
            </div>

            <div class="section-title"><i class="bi bi-shield-lock me-2"></i>Mot de passe de mise à jour</div>
            <div class="mb-2">
                <p class="text-muted small mb-2">Une passphrase a été générée automatiquement. Vous pouvez la modifier.</p>
                <div class="d-flex align-items-center gap-2 mb-2">
                    <div class="passphrase-box flex-grow-1" id="generatedPassphrase" title="Cliquez pour copier"></div>
                    <button type="button" class="btn btn-outline-primary btn-sm" onclick="generatePassphrase()" title="Regénérer">
                        <i class="bi bi-arrow-clockwise"></i>
                    </button>
                    <button type="button" class="btn btn-outline-success btn-sm" onclick="copyPassphrase()" title="Copier">
                        <i class="bi bi-clipboard" id="copyIcon"></i>
                    </button>
                </div>
                <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox" id="useCustom" onchange="toggleCustom()">
                    <label class="form-check-label" for="useCustom">Utiliser mon propre mot de passe</label>
                </div>
            </div>
            <div id="customPasswordBlock" style="display:none;" class="mb-3">
                <label class="form-label">Mon mot de passe</label>
                <div class="input-group">
                    <input type="password" class="form-control" id="customPassword" placeholder="Votre mot de passe" oninput="checkStrength(this.value)">
                    <button class="btn btn-outline-secondary" type="button" onclick="toggleVis('customPassword','eyeIcon')">
                        <i class="bi bi-eye" id="eyeIcon"></i>
                    </button>
                </div>
                <div class="mt-2">
                    <div class="progress mb-1" style="height:6px;">
                        <div class="strength-bar progress-bar" id="strengthBar" style="width:0%"></div>
                    </div>
                    <span id="strengthLabel" class="text-muted small">—</span>
                </div>
                <label class="form-label mt-3">Confirmer le mot de passe</label>
                <div class="input-group">
                    <input type="password" class="form-control" id="confirmPassword" placeholder="Répétez" oninput="checkConfirm()">
                    <button class="btn btn-outline-secondary" type="button" onclick="toggleVis('confirmPassword','eyeIcon2')">
                        <i class="bi bi-eye" id="eyeIcon2"></i>
                    </button>
                </div>
                <div id="confirmFeedback" class="form-text mt-1"></div>
            </div>
            <input type="hidden" name="update_password" id="finalPassword">
            <div class="alert alert-info small mb-4">
                <i class="bi bi-info-circle me-1"></i>Ce mot de passe sera demandé lors des mises à jour. Conservez-le précieusement.
            </div>

            <div class="d-grid">
                <button type="submit" class="btn btn-primary btn-lg" onclick="return setFinalPassword()">
                    <i class="bi bi-rocket-takeoff me-2"></i>Lancer l'installation
                </button>
            </div>
        </form>
    </div></div>
    <div class="text-center mt-4 text-white"><small>Gestion des Commandes v<?= APP_VERSION ?></small></div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
const WORDS = ['soleil','nuage','riviere','montagne','foret','jardin','maison','fenetre','bureau','cahier','stylo','crayon','tableau','chaise','table','lampe','camion','voiture','bateau','avion','train','velo','cerise','pomme','fraise','citron','poire','mangue','raisin','orange','tigre','lapin','renard','mouton','cheval','baleine','aigle','loutre','chemin','sentier','village','prairie','colline','vallee','plaine','ocean','marbre','granit','ardoise','sable','argile','brique','pierre','metal'];

function generatePassphrase() {
    const pool=[...WORDS],words=[];
    for(let i=0;i<4;i++){const idx=Math.floor(Math.random()*pool.length);words.push(pool.splice(idx,1)[0]);}
    document.getElementById('generatedPassphrase').textContent=words.join('-');
}
function copyPassphrase() {
    navigator.clipboard.writeText(document.getElementById('generatedPassphrase').textContent).then(()=>{
        const i=document.getElementById('copyIcon');i.className='bi bi-clipboard-check';
        setTimeout(()=>i.className='bi bi-clipboard',2000);
    });
}
function toggleCustom(){document.getElementById('customPasswordBlock').style.display=document.getElementById('useCustom').checked?'block':'none';}
function toggleVis(id,ico){const i=document.getElementById(id),ic=document.getElementById(ico);i.type=i.type==='password'?'text':'password';ic.className=i.type==='password'?'bi bi-eye':'bi bi-eye-slash';}
function checkStrength(v){
    let s=0;if(v.length>=12)s++;if(v.length>=20)s++;if(/[a-z]/.test(v)&&/[A-Z]/.test(v))s++;if(/[0-9]/.test(v))s++;if(/[-_!@#$%]/.test(v))s++;if((v.match(/-/g)||[]).length>=2)s++;
    const cfgs=[{p:10,c:'bg-danger',t:'Très faible',cl:'text-danger'},{p:30,c:'bg-danger',t:'Faible',cl:'text-danger'},{p:50,c:'bg-warning',t:'Moyen',cl:'text-warning'},{p:75,c:'bg-info',t:'Fort',cl:'text-info'},{p:90,c:'bg-success',t:'Très fort',cl:'text-success'},{p:100,c:'bg-success',t:'Excellent',cl:'text-success'}];
    const cf=cfgs[Math.min(s,cfgs.length-1)];
    document.getElementById('strengthBar').style.width=cf.p+'%';document.getElementById('strengthBar').className='strength-bar progress-bar '+cf.c;
    document.getElementById('strengthLabel').textContent=v?cf.t:'—';document.getElementById('strengthLabel').className=v?cf.cl:'text-muted small';
}
function checkConfirm(){
    const p=document.getElementById('customPassword').value,c=document.getElementById('confirmPassword').value,fb=document.getElementById('confirmFeedback');
    if(!c){fb.textContent='';return;}
    fb.innerHTML=p===c?'<span class="text-success">✓ Correspondent</span>':'<span class="text-danger">✗ Ne correspondent pas</span>';
}
function setFinalPassword(){
    const useC=document.getElementById('useCustom').checked,custom=document.getElementById('customPassword').value.trim(),confirm=document.getElementById('confirmPassword').value.trim(),gen=document.getElementById('generatedPassphrase').textContent.trim();
    if(useC&&custom){if(custom!==confirm){alert('Les mots de passe ne correspondent pas.');return false;}document.getElementById('finalPassword').value=custom;}
    else{document.getElementById('finalPassword').value=gen;}
    return true;
}
document.getElementById('generatedPassphrase').addEventListener('click',copyPassphrase);
generatePassphrase();
</script>
</body></html>
<?php exit; }

// ── Traitement POST ────────────────────────────────────────────────────────────
$DB_HOST      = trim($_POST['db_host']         ?? '');
$DB_USER      = trim($_POST['db_user']         ?? '');
$DB_PASS      =      $_POST['db_pass']         ?? '';
$DB_NAME      = trim($_POST['db_name']         ?? '');
$APP_FOLDER   = trim($_POST['app_folder']      ?? '', '/');
$GITHUB_REPO  = trim($_POST['github_repo']     ?? '');
$DOLIBARR_URL = rtrim(trim($_POST['dolibarr_url'] ?? ''), '/');
$UPDATE_PASS  = trim($_POST['update_password'] ?? '');

if (empty($DB_HOST)||empty($DB_USER)||empty($DB_NAME)||empty($APP_FOLDER)||empty($UPDATE_PASS))
    die('Erreur : Tous les champs obligatoires doivent être remplis.');
if (!preg_match('/^[a-zA-Z0-9_\-]+$/', $APP_FOLDER)) die('Erreur : Nom de dossier invalide.');
if (!preg_match('/^[a-zA-Z0-9_]+$/', $DB_NAME))      die('Erreur : Nom de base invalide.');
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Installation en cours…</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="icon" type="image/png" href="image/favicon-96x96.png" sizes="96x96">
    <link rel="icon" type="image/svg+xml" href="image/favicon.svg">
    <link rel="shortcut icon" href="image/favicon.ico">
    <style>
        body{background:linear-gradient(135deg,#243142 0%,#1a2535 100%);min-height:100vh;padding:40px 0;}
        .install-container{max-width:900px;margin:0 auto;}
        .card{border:none;border-radius:15px;box-shadow:0 10px 40px rgba(0,0,0,.3);}
        .step{padding:20px;margin-bottom:15px;border-radius:10px;background:#f8f9fa;}
        .step.processing{background:#fff3cd;border-left:4px solid #ffc107;}
        .step.success{background:#d1e7dd;border-left:4px solid #198754;}
        .step.error{background:#f8d7da;border-left:4px solid #dc3545;}
    </style>
</head>
<body>
<div class="install-container"><div class="card"><div class="card-body p-5">
    <h1 class="text-center mb-4"><i class="bi bi-hourglass-split text-primary"></i> Installation en cours</h1>
    <div id="progress">
    <?php
    $hasError = false;

    // Étape 1 : Connexion MySQL
    echo '<div class="step processing" id="step1"><h5><i class="bi bi-server me-2"></i>Étape 1 : Connexion MySQL</h5>';
    try {
        $conn = new PDO("mysql:host=$DB_HOST;charset=utf8mb4", $DB_USER, $DB_PASS);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        echo '<p class="text-success mb-0"><i class="bi bi-check-circle me-2"></i>Connexion établie</p></div>';
        echo '<script>document.getElementById("step1").className="step success";</script>';
    } catch (PDOException $e) {
        echo '<p class="text-danger mb-0"><i class="bi bi-x-circle me-2"></i>'.htmlspecialchars($e->getMessage()).'</p></div>';
        echo '<script>document.getElementById("step1").className="step error";</script>';
        $hasError = true;
    }

    if (!$hasError) {
        // Étape 2 : Création BDD
        echo '<div class="step processing" id="step2"><h5><i class="bi bi-database me-2"></i>Étape 2 : Base de données</h5>';
        try {
            $conn->exec("CREATE DATABASE IF NOT EXISTS `$DB_NAME` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
            $conn->exec("USE `$DB_NAME`");
            echo '<p class="text-success mb-0"><i class="bi bi-check-circle me-2"></i>Base <strong>'.htmlspecialchars($DB_NAME).'</strong> créée/vérifiée</p></div>';
            echo '<script>document.getElementById("step2").className="step success";</script>';
        } catch (PDOException $e) {
            echo '<p class="text-danger mb-0"><i class="bi bi-x-circle me-2"></i>'.htmlspecialchars($e->getMessage()).'</p></div>';
            echo '<script>document.getElementById("step2").className="step error";</script>';
            $hasError = true;
        }
    }

    if (!$hasError) {
        // Étape 3 : Import SQL
        echo '<div class="step processing" id="step3"><h5><i class="bi bi-file-earmark-code me-2"></i>Étape 3 : Création des tables</h5>';
        $sqlFile = 'database/database.sql';
        if (!file_exists($sqlFile)) {
            echo '<p class="text-danger mb-0"><i class="bi bi-x-circle me-2"></i>Fichier '.htmlspecialchars($sqlFile).' introuvable</p></div>';
            echo '<script>document.getElementById("step3").className="step error";</script>';
            $hasError = true;
        } else {
            try {
                $sql = preg_replace('/USE\s+[`]?[\w]+[`]?\s*;/i','',file_get_contents($sqlFile));
                $conn->exec($sql);
                $tables = $conn->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
                echo '<p class="text-success"><i class="bi bi-check-circle me-2"></i>Tables créées :</p><ul class="mb-0">';
                foreach ($tables as $t) echo '<li>'.htmlspecialchars($t).'</li>';
                echo '</ul></div>';
                echo '<script>document.getElementById("step3").className="step success";</script>';
            } catch (Exception $e) {
                echo '<p class="text-danger mb-0"><i class="bi bi-x-circle me-2"></i>'.htmlspecialchars($e->getMessage()).'</p></div>';
                echo '<script>document.getElementById("step3").className="step error";</script>';
                $hasError = true;
            }
        }
    }

    if (!$hasError) {
        // Étape 4 : Database.php
        echo '<div class="step processing" id="step4"><h5><i class="bi bi-file-code me-2"></i>Étape 4 : Configuration BDD</h5>';
        try {
            $c = file_get_contents('classes/Database.php');
            $c = preg_replace("/private \\\$host = '[^']*';/",     "private \$host = '$DB_HOST';",                $c);
            $c = preg_replace("/private \\\$db_name = '[^']*';/",  "private \$db_name = '$DB_NAME';",             $c);
            $c = preg_replace("/private \\\$username = '[^']*';/", "private \$username = '$DB_USER';",            $c);
            $c = preg_replace("/private \\\$password = '[^']*';/", "private \$password = '".addslashes($DB_PASS)."';", $c);
            safe_write('classes/Database.php', $c);
            echo '<p class="text-success mb-0"><i class="bi bi-check-circle me-2"></i>classes/Database.php mis à jour</p></div>';
            echo '<script>document.getElementById("step4").className="step success";</script>';
        } catch (Exception $e) {
            echo '<p class="text-danger mb-0"><i class="bi bi-x-circle me-2"></i>'.htmlspecialchars($e->getMessage()).'</p></div>';
            echo '<script>document.getElementById("step4").className="step error";</script>';
            $hasError = true;
        }
    }

    if (!$hasError) {
        // Étape 5 : .htaccess
        echo '<div class="step processing" id="step5"><h5><i class="bi bi-signpost-split me-2"></i>Étape 5 : Mise à jour .htaccess</h5>';
        try {
            $ht = file_get_contents('.htaccess');
            $ht = preg_replace('/RewriteBase\s+\S+/i',              'RewriteBase /'.$APP_FOLDER.'/',    $ht);
            $ht = preg_replace('/ErrorDocument\s+404\s+\S+/i',      'ErrorDocument 404 /'.$APP_FOLDER.'/404.php', $ht);
            $ht = preg_replace('/ErrorDocument\s+403\s+\S+/i',      'ErrorDocument 403 /'.$APP_FOLDER.'/404.php', $ht);
            $ht = preg_replace('/RedirectMatch 403 \^\/[^\/\(]+\//i','RedirectMatch 403 ^/'.$APP_FOLDER.'/',       $ht);
            safe_write('.htaccess', $ht);
            echo '<p class="text-success mb-0"><i class="bi bi-check-circle me-2"></i>RewriteBase → <code>/'.htmlspecialchars($APP_FOLDER).'/</code></p></div>';
            echo '<script>document.getElementById("step5").className="step success";</script>';
        } catch (Exception $e) {
            echo '<p class="text-danger mb-0"><i class="bi bi-x-circle me-2"></i>'.htmlspecialchars($e->getMessage()).'</p></div>';
            echo '<script>document.getElementById("step5").className="step error";</script>';
            $hasError = true;
        }
    }

    if (!$hasError) {
        // Étape 6 : 404.php
        echo '<div class="step processing" id="step6"><h5><i class="bi bi-exclamation-triangle me-2"></i>Étape 6 : Mise à jour 404.php</h5>';
        try {
            if (file_exists('404.php')) {
                $e = file_get_contents('404.php');
                $e = preg_replace("/\\\$basePath\s*=\s*'[^']*';/", "\$basePath = '/$APP_FOLDER/';", $e);
                safe_write('404.php', $e);
            }
            echo '<p class="text-success mb-0"><i class="bi bi-check-circle me-2"></i>404.php mis à jour</p></div>';
            echo '<script>document.getElementById("step6").className="step success";</script>';
        } catch (Exception $e) {
            echo '<p class="text-danger mb-0"><i class="bi bi-x-circle me-2"></i>'.htmlspecialchars($e->getMessage()).'</p></div>';
            echo '<script>document.getElementById("step6").className="step error";</script>';
            $hasError = true;
        }
    }

    if (!$hasError) {
        // Étape 7 : Bookmarklet
        echo '<div class="step processing" id="step7"><h5><i class="bi bi-bookmark-star me-2"></i>Étape 7 : Bookmarklet</h5>';
        try {
            $bk = 'tools/dolibarr-bookmarklet.html';
            if (file_exists($bk)) {
                $c = file_get_contents($bk);
                $c = preg_replace('/http[s]?:\/\/[^\/]+\/[^\/]+\/nouvelle-commande\.php/', 'http://localhost/'.$APP_FOLDER.'/nouvelle-commande.php', $c);
                safe_write($bk, $c);
            }
            echo '<p class="text-success mb-0"><i class="bi bi-check-circle me-2"></i>Bookmarklet mis à jour</p></div>';
            echo '<script>document.getElementById("step7").className="step success";</script>';
        } catch (Exception $e) {
            echo '<p class="text-danger mb-0"><i class="bi bi-x-circle me-2"></i>'.htmlspecialchars($e->getMessage()).'</p></div>';
            echo '<script>document.getElementById("step7").className="step error";</script>';
            $hasError = true;
        }
    }

    if (!$hasError) {
        // Étape 8 : .app_config
        echo '<div class="step processing" id="step8"><h5><i class="bi bi-shield-lock me-2"></i>Étape 8 : Configuration applicative</h5>';
        try {
            $appConfig = [
                'app_folder'      => $APP_FOLDER,
                'github_repo'     => $GITHUB_REPO,
                'dolibarr_url'    => $DOLIBARR_URL,
                'db_host'         => $DB_HOST,
                'db_user'         => $DB_USER,
                'db_name'         => $DB_NAME,
                'update_password' => password_hash($UPDATE_PASS, PASSWORD_BCRYPT),
                'installed_at'    => date('Y-m-d H:i:s'),
                'version'         => APP_VERSION,
            ];
            safe_write('.app_config', json_encode($appConfig, JSON_PRETTY_PRINT));
            echo '<p class="text-success mb-0"><i class="bi bi-check-circle me-2"></i>Fichier .app_config créé (bcrypt)</p></div>';
            echo '<script>document.getElementById("step8").className="step success";</script>';
        } catch (Exception $e) {
            echo '<p class="text-danger mb-0"><i class="bi bi-x-circle me-2"></i>'.htmlspecialchars($e->getMessage()).'</p></div>';
            echo '<script>document.getElementById("step8").className="step error";</script>';
            $hasError = true;
        }
    }

    if (!$hasError) {
        // Étape 9 : Vérification finale + verrou
        echo '<div class="step processing" id="step9"><h5><i class="bi bi-check2-all me-2"></i>Étape 9 : Vérification finale</h5>';
        try {
            require_once 'classes/Database.php';
            $db    = new Database();
            $conn2 = $db->getConnection();
            $count = $conn2->query("SELECT COUNT(*) FROM commandes")->fetchColumn();
            echo '<p class="text-success"><i class="bi bi-check-circle me-2"></i>Connexion finale réussie !</p>';
            echo '<p class="mb-0 small text-muted">Table <code>commandes</code> : '.$count.' enregistrement(s)</p></div>';
            echo '<script>document.getElementById("step9").className="step success";</script>';
            safe_write('.installation_complete', date('Y-m-d H:i:s'));
            echo '<div class="alert alert-warning mt-3"><i class="bi bi-shield-check me-2"></i><strong>Protection activée :</strong> <code>.installation_complete</code> créé.</div>';
        } catch (Exception $e) {
            echo '<p class="text-danger mb-0"><i class="bi bi-x-circle me-2"></i>'.htmlspecialchars($e->getMessage()).'</p></div>';
            echo '<script>document.getElementById("step9").className="step error";</script>';
            $hasError = true;
        }
    }
    ?>
    </div>
    <hr class="my-4">
    <?php if (!$hasError): ?>
    <div class="alert alert-success">
        <h5 class="alert-heading"><i class="bi bi-check-circle-fill me-2"></i>Installation réussie !</h5>
        <p>Base de données <strong><?= htmlspecialchars($DB_NAME) ?></strong> prête.</p>
        <hr><p class="mb-0 small"><strong>À faire :</strong> Notez votre mot de passe de mise à jour et supprimez <code>install.php</code>.</p>
    </div>
    <div class="text-center">
        <a href="/<?= htmlspecialchars($APP_FOLDER) ?>/" class="btn btn-primary btn-lg">
            <i class="bi bi-house-door me-2"></i>Accéder à l'application
        </a>
    </div>
    <?php else: ?>
    <div class="alert alert-danger">
        <h5><i class="bi bi-exclamation-triangle-fill me-2"></i>Erreur d'installation</h5>
        <p class="mb-0">Corrigez les erreurs ci-dessus et réessayez.</p>
    </div>
    <div class="text-center"><a href="install.php" class="btn btn-warning btn-lg"><i class="bi bi-arrow-left me-2"></i>Retour</a></div>
    <?php endif; ?>
</div></div>
<div class="text-center mt-4 text-white"><small>Gestion des Commandes v<?= APP_VERSION ?></small></div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body></html>
