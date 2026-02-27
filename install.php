<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

/**
 * Installation automatique - Gestion des Commandes v1.31
 * 
 * Ce fichier permet d'installer automatiquement la base de données
 * ATTENTION : Supprimez ce fichier après installation !
 */

// Démarrer la session pour stocker les messages
session_start();

// Écriture sécurisée avec gestion des permissions
function safe_write($file, $data) {
    // Tenter de rendre le fichier accessible en écriture si besoin
    if (file_exists($file) && !is_writable($file)) {
        @chmod($file, 0664);
    }
    $result = @file_put_contents($file, $data);
    if ($result === false) {
        $os = PHP_OS_FAMILY;
        if ($os === 'Darwin') {
            $hint = "Sur Mac/XAMPP, exécutez dans le Terminal :<br>" .
                    "<code>sudo chown -R daemon:staff /Applications/XAMPP/xamppfiles/htdocs/</code><br>" .
                    "ou : <code>sudo chmod -R 777 /Applications/XAMPP/xamppfiles/htdocs/</code>";
        } elseif ($os === 'Windows') {
            $hint = "Sur Windows/XAMPP, faites clic droit sur le dossier <code>htdocs</code> " .
                    "&rarr; Propriétés &rarr; Sécurité &rarr; donner le Contrôle total à l'utilisateur courant.";
        } else {
            $dir = dirname(realpath($file));
            $hint = "Sur Linux : <code>sudo chmod -R 755 $dir</code><br>" .
                    "ou : <code>sudo chown -R www-data:www-data $dir</code>";
        }
        throw new Exception("Impossible d'écrire dans <code>$file</code>.<br>" . $hint);
    }
    return $result;
}

// Configuration
$installer_version = '1.31';
$db_sql_file = 'database/database.sql';

// Vérifier si l'installation est déjà effectuée
$installation_lock = '.installation_complete';

if (file_exists($installation_lock) && !isset($_GET['force'])) {
    die('⚠️ L\'installation a déjà été effectuée. Supprimez le fichier .installation_complete pour réinstaller ou ajoutez ?force=1 à l\'URL.');
}

// Traitement du formulaire
$step = isset($_GET['step']) ? $_GET['step'] : 1;
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $step == 2) {
    // Récupération des données du formulaire
    $db_host    = trim($_POST['db_host']);
    $db_name    = trim($_POST['db_name']);
    $db_user    = trim($_POST['db_user']);
    $db_pass    = $_POST['db_pass'];
    $create_db  = isset($_POST['create_db']) ? true : false;
    $app_folder = trim($_POST['app_folder'], '/');   // ex: gestion-commandes
    $dolibarr_url = rtrim(trim($_POST['dolibarr_url']), '/'); // ex: https://crm.mexichrome.fr
    
    try {
        // 1. Connexion au serveur MySQL (sans base de données)
        $pdo = new PDO(
            "mysql:host=$db_host;charset=utf8mb4",
            $db_user,
            $db_pass,
            array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION)
        );

        // 2. Créer la base de données si demandé
        if ($create_db) {
            $pdo->exec("CREATE DATABASE IF NOT EXISTS `$db_name` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
            $success .= "✅ Base de données '$db_name' créée avec succès.<br>";
        }

        // 3. Se connecter à la base de données
        $pdo = new PDO(
            "mysql:host=$db_host;dbname=$db_name;charset=utf8mb4",
            $db_user,
            $db_pass,
            array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION)
        );
        $success .= "✅ Connexion à la base de données réussie.<br>";
        
        // 3. Lire et exécuter le fichier SQL
        if (!file_exists($db_sql_file)) {
            throw new Exception("Le fichier $db_sql_file n'existe pas !");
        }
        
        $sql = file_get_contents($db_sql_file);
        
        // Retirer les commentaires et la création de base si elle existe déjà
        $sql = preg_replace('/^--.*$/m', '', $sql);
        $sql = preg_replace('/CREATE DATABASE.*?;/is', '', $sql);
        $sql = preg_replace('/USE .*;/i', '', $sql);
        
        // Séparer les requêtes
        $queries = array_filter(
            array_map('trim', explode(';', $sql)),
            function($query) { return !empty($query); }
        );
        
        // Exécuter chaque requête
        foreach ($queries as $query) {
            if (!empty($query)) {
                $pdo->exec($query);
            }
        }
        $success .= "✅ Table 'commandes' créée avec succès.<br>";
        
        // 4. Créer le dossier downloads s'il n'existe pas
        if (!file_exists('downloads')) {
            if (@mkdir('downloads', 0777, true)) {
                @chmod('downloads', 0777); // Force les permissions
                $success .= "✅ Dossier 'downloads' créé avec succès.<br>";
            } else {
                // Si la création échoue, essayer quand même de continuer
                $success .= "⚠️ Impossible de créer le dossier 'downloads'. Veuillez le créer manuellement avec les permissions 755 ou 777.<br>";
            }
        } else {
            // Vérifier les permissions
            if (is_writable('downloads')) {
                $success .= "✅ Dossier 'downloads' déjà existant et accessible en écriture.<br>";
            } else {
                $success .= "⚠️ Dossier 'downloads' existe mais n'est pas accessible en écriture. Veuillez donner les permissions 755 ou 777.<br>";
            }
        }
        
        // 5. Mettre à jour le fichier Database.php avec les nouvelles informations
        $database_file = 'classes/Database.php';
        if (file_exists($database_file)) {
            $content = file_get_contents($database_file);
            
            // Remplacer les valeurs
            $content = preg_replace(
                "/private \\\$host = '[^']*';/",
                "private \$host = '$db_host';",
                $content
            );
            $content = preg_replace(
                "/private \\\$db_name = '[^']*';/",
                "private \$db_name = '$db_name';",
                $content
            );
            $content = preg_replace(
                "/private \\\$username = '[^']*';/",
                "private \$username = '$db_user';",
                $content
            );
            $content = preg_replace(
                "/private \\\$password = '[^']*';/",
                "private \$password = '" . addslashes($db_pass) . "';",
                $content
            );
            
            safe_write($database_file, $content);
            $success .= "✅ Fichier Database.php mis à jour avec succès.<br>";
        }
        
        // 6. Créer le fichier de verrouillage
        safe_write($installation_lock, date('Y-m-d H:i:s'));
        $success .= "✅ Installation verrouillée.<br>";
        // 7. Mettre à jour .htaccess avec le bon RewriteBase
        $htaccess_file = '.htaccess';
        if (file_exists($htaccess_file)) {
            $htaccess = file_get_contents($htaccess_file);
            $htaccess = preg_replace(
                '/RewriteBase\s+\/[^
]*/i',
                'RewriteBase /' . $app_folder . '/',
                $htaccess
            );
            safe_write($htaccess_file, $htaccess);
            $success .= "✅ .htaccess mis à jour (RewriteBase /$app_folder/).<br>";
        }

        // 8. Mettre à jour l'URL dans dolibarr-bookmarklet.html
        $bk_file = 'tools/dolibarr-bookmarklet.html';
        if (file_exists($bk_file)) {
            $bk = file_get_contents($bk_file);
            // Remplacer l'URL de l'application dans le bookmarklet
            $bk = preg_replace(
                '/http[s]?:\/\/[^\/]+\/[^\/]+\/nouvelle-commande\.php/',
                'http://localhost/' . $app_folder . '/nouvelle-commande.php',
                $bk
            );
            safe_write($bk_file, $bk);
            $success .= "✅ Bookmarklet mis à jour (/$app_folder/).<br>";
        }

        // 9. Test de connexion final
        require_once 'classes/Database.php';
        $db = new Database();
        $conn = $db->getConnection();
        
        if ($conn) {
            $success .= "✅ Test de connexion final réussi !<br>";
            $step = 3; // Passer à l'étape de fin
        }
        
    } catch (PDOException $e) {
        $error = "❌ Erreur de base de données : " . $e->getMessage() .
                 "<br><small>Vérifiez l'hôte, le nom d'utilisateur et le mot de passe MySQL.</small>";
    } catch (Exception $e) {
        $error = "❌ " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Installation - Gestion des Commandes v<?php echo $installer_version; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px 0;
        }
        .installer-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
            max-width: 700px;
            margin: 0 auto;
        }
        .installer-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            border-radius: 15px 15px 0 0;
        }
        .step-indicator {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
            position: relative;
        }
        .step-indicator::before {
            content: '';
            position: absolute;
            top: 20px;
            left: 0;
            right: 0;
            height: 2px;
            background: #e0e0e0;
            z-index: 0;
        }
        .step {
            background: white;
            border: 3px solid #e0e0e0;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            position: relative;
            z-index: 1;
        }
        .step.active {
            border-color: #667eea;
            color: #667eea;
        }
        .step.complete {
            background: #667eea;
            border-color: #667eea;
            color: white;
        }
        .form-label {
            font-weight: 600;
            color: #333;
        }
        .info-box {
            background: #f8f9fa;
            border-left: 4px solid #667eea;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
        }
        .warning-box {
            background: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
        }
        .success-box {
            background: #d4edda;
            border-left: 4px solid #28a745;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
        }
        .error-box {
            background: #f8d7da;
            border-left: 4px solid #dc3545;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="installer-card">
            <div class="installer-header">
                <h1 class="mb-0">
                    <i class="bi bi-gear-fill"></i> Installation
                </h1>
                <p class="mb-0 mt-2">Gestion des Commandes v<?php echo $installer_version; ?></p>
            </div>
            
            <div class="p-4">
                <!-- Indicateur d'étapes -->
                <div class="step-indicator mb-4">
                    <div class="step <?php echo $step >= 1 ? 'active' : ''; ?> <?php echo $step > 1 ? 'complete' : ''; ?>">1</div>
                    <div class="step <?php echo $step >= 2 ? 'active' : ''; ?> <?php echo $step > 2 ? 'complete' : ''; ?>">2</div>
                    <div class="step <?php echo $step >= 3 ? 'active' : ''; ?>">3</div>
                </div>

                <?php if ($step == 1): ?>
                <!-- ÉTAPE 1 : Vérification des prérequis -->
                <h3 class="mb-4">Étape 1 : Vérification des prérequis</h3>
                
                <div class="info-box">
                    <h5><i class="bi bi-info-circle"></i> Avant de commencer</h5>
                    <p class="mb-2">Assurez-vous d'avoir :</p>
                    <ul class="mb-0">
                        <li>Un serveur MySQL/MariaDB en cours d'exécution</li>
                        <li>Les identifiants de connexion à la base de données</li>
                        <li>Les permissions nécessaires pour créer une base de données</li>
                    </ul>
                </div>

                <h5 class="mt-4 mb-3">Vérification du système</h5>
                
                <table class="table">
                    <tr>
                        <td>Version PHP</td>
                        <td>
                            <?php 
                            $php_version = phpversion();
                            $php_ok = version_compare($php_version, '7.4', '>=');
                            echo $php_ok 
                                ? "<span class='text-success'><i class='bi bi-check-circle-fill'></i> $php_version (OK)</span>" 
                                : "<span class='text-danger'><i class='bi bi-x-circle-fill'></i> $php_version (requis: 7.4+)</span>";
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td>Extension PDO</td>
                        <td>
                            <?php 
                            $pdo_ok = extension_loaded('pdo');
                            echo $pdo_ok 
                                ? "<span class='text-success'><i class='bi bi-check-circle-fill'></i> Installée</span>" 
                                : "<span class='text-danger'><i class='bi bi-x-circle-fill'></i> Non installée</span>";
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td>Extension PDO MySQL</td>
                        <td>
                            <?php 
                            $pdo_mysql_ok = extension_loaded('pdo_mysql');
                            echo $pdo_mysql_ok 
                                ? "<span class='text-success'><i class='bi bi-check-circle-fill'></i> Installée</span>" 
                                : "<span class='text-danger'><i class='bi bi-x-circle-fill'></i> Non installée</span>";
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td>Fichier database.sql</td>
                        <td>
                            <?php 
                            $sql_ok = file_exists($db_sql_file);
                            echo $sql_ok 
                                ? "<span class='text-success'><i class='bi bi-check-circle-fill'></i> Présent</span>" 
                                : "<span class='text-danger'><i class='bi bi-x-circle-fill'></i> Manquant</span>";
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td>Permissions dossier</td>
                        <td>
                            <?php 
                            $writable = is_writable('.');
                            echo $writable 
                                ? "<span class='text-success'><i class='bi bi-check-circle-fill'></i> Écriture autorisée</span>" 
                                : "<span class='text-warning'><i class='bi bi-exclamation-triangle-fill'></i> Écriture limitée</span>";
                            ?>
                        </td>
                    </tr>
                </table>

                <?php 
                $can_proceed = $php_ok && $pdo_ok && $pdo_mysql_ok && $sql_ok;
                if ($can_proceed): 
                ?>
                    <div class="success-box">
                        <i class="bi bi-check-circle-fill"></i> Tous les prérequis sont satisfaits !
                    </div>
                    <div class="text-center">
                        <a href="?step=2" class="btn btn-primary btn-lg">
                            Continuer <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>
                <?php else: ?>
                    <div class="error-box">
                        <i class="bi bi-x-circle-fill"></i> Certains prérequis ne sont pas satisfaits. Veuillez les corriger avant de continuer.
                    </div>
                <?php endif; ?>

                <?php elseif ($step == 2): ?>
                <!-- ÉTAPE 2 : Configuration de la base de données -->
                <h3 class="mb-4">Étape 2 : Configuration de la base de données</h3>
                
                <?php if ($error): ?>
                    <div class="error-box">
                        <?php echo $error; ?>
                    </div>
                <?php endif; ?>

                <?php if ($success): ?>
                    <div class="success-box">
                        <?php echo $success; ?>
                    </div>
                <?php endif; ?>

                <?php if ($step == 2 && !$success): ?>
                <form method="POST" action="?step=2">
                    <div class="mb-3">
                        <label for="db_host" class="form-label">Hôte de la base de données</label>
                        <input type="text" class="form-control" id="db_host" name="db_host" value="localhost" required>
                        <small class="text-muted">Généralement "localhost" ou "127.0.0.1"</small>
                    </div>

                    <div class="mb-3">
                        <label for="db_name" class="form-label">Nom de la base de données</label>
                        <input type="text" class="form-control" id="db_name" name="db_name" value="gestion_commandes" required>
                        <small class="text-muted">Le nom de votre base de données</small>
                    </div>

                    <div class="mb-3">
                        <label for="db_user" class="form-label">Utilisateur</label>
                        <input type="text" class="form-control" id="db_user" name="db_user" value="root" required>
                        <small class="text-muted">Nom d'utilisateur MySQL</small>
                    </div>

                    <div class="mb-3">
                        <label for="db_pass" class="form-label">Mot de passe</label>
                        <input type="password" class="form-control" id="db_pass" name="db_pass" value="">
                        <small class="text-muted">Mot de passe MySQL (peut être vide en local)</small>
                    </div>

                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="create_db" name="create_db" checked>
                        <label class="form-check-label" for="create_db">
                            Créer la base de données si elle n'existe pas
                        </label>
                    </div>

                    <hr class="my-4">
                    <h5 class="mb-3"><i class="bi bi-folder"></i> Configuration de l'application</h5>

                    <div class="mb-3">
                        <label for="app_folder" class="form-label">Nom du dossier de l'application</label>
                        <div class="input-group">
                            <span class="input-group-text">localhost/</span>
                            <input type="text" class="form-control" id="app_folder" name="app_folder"
                                   value="gestion-commandes" required
                                   pattern="[a-zA-Z0-9_-]+"
                                   title="Lettres, chiffres, tirets et underscores uniquement">
                        </div>
                        <small class="text-muted">Le nom du dossier sur votre serveur (ex: <code>gestion-commandes</code>, <code>commandes</code>)</small>
                    </div>

                    <div class="mb-3">
                        <label for="dolibarr_url" class="form-label">URL de Dolibarr</label>
                        <input type="url" class="form-control" id="dolibarr_url" name="dolibarr_url"
                               value="https://" placeholder="https://crm.mondomaine.fr">
                        <small class="text-muted">URL de votre Dolibarr — utilisée pour la configuration du bookmarklet</small>
                    </div>

                    <div class="warning-box">
                        <i class="bi bi-exclamation-triangle-fill"></i> <strong>Attention :</strong> Si la base de données existe déjà avec des données, elles seront conservées mais la table 'commandes' sera recréée.
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="?step=1" class="btn btn-secondary">
                            <i class="bi bi-arrow-left"></i> Retour
                        </a>
                        <button type="submit" class="btn btn-primary">
                            Installer <i class="bi bi-arrow-right"></i>
                        </button>
                    </div>
                </form>
                <?php endif; ?>

                <?php elseif ($step == 3): ?>
                <!-- ÉTAPE 3 : Installation terminée -->
                <h3 class="mb-4">Étape 3 : Installation terminée !</h3>
                
                <div class="success-box">
                    <h5><i class="bi bi-check-circle-fill"></i> Installation réussie !</h5>
                    <?php echo $success; ?>
                </div>

                <div class="warning-box">
                    <h5><i class="bi bi-shield-exclamation"></i> Sécurité importante</h5>
                    <p class="mb-2"><strong>Supprimez immédiatement le fichier install.php !</strong></p>
                    <p class="mb-0">Pour des raisons de sécurité, vous devez supprimer ce fichier d'installation maintenant.</p>
                </div>

                <div class="info-box">
                    <h5><i class="bi bi-info-circle"></i> Prochaines étapes</h5>
                    <ol class="mb-0">
                        <li>Supprimez le fichier <code>install.php</code></li>
                        <li>Vérifiez les permissions du dossier <code>downloads/</code> (755 ou 777)</li>
                        <li>Configurez votre <code>.htaccess</code> si nécessaire</li>
                        <li>Accédez à votre application</li>
                    </ol>
                </div>

                <div class="text-center mt-4">
                    <a href="index.php" class="btn btn-success btn-lg">
                        <i class="bi bi-house-fill"></i> Accéder à l'application
                    </a>
                </div>

                <?php endif; ?>
            </div>
        </div>
        
        <div class="text-center mt-4">
            <small class="text-white">
                Gestion des Commandes v<?php echo $installer_version; ?> - Installation automatique
            </small>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
