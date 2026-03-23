<?php
/**
 * CommandeController - Gestion de la création et édition de commandes
 */
require_once __DIR__ . '/../classes/Security.php';

class CommandeController {
    private $commande;
    private $batFichier;
    private $db;
    public $errors = [];
    public $data   = null;

    public function __construct() {
        require_once __DIR__ . '/../classes/Database.php';
        require_once __DIR__ . '/../classes/Commande.php';
        require_once __DIR__ . '/../classes/BatFichier.php';

        $database       = new Database();
        $this->db       = $database->getConnection();
        $this->commande   = new Commande($this->db);
        $this->batFichier = new BatFichier($this->db);
    }

    /**
     * Obtenir l'URL de base de l'application
     */
    private function getBaseUrl() {
        $scriptPath = dirname($_SERVER['SCRIPT_NAME']);
        if (basename($scriptPath) === 'public') {
            $scriptPath = dirname($scriptPath);
        }
        return '/' . trim($scriptPath, '/');
    }

    /**
     * Créer une nouvelle commande (étape 1 BAT)
     */
    public function create() {
        // Bookmarklet détecté : poser le cookie
        if (isset($_GET['bookmarklet'])) {
            setcookie('bookmarklet_installed', '1', time() + (365 * 24 * 3600), '/');
        }

        // Traitement du formulaire
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->processCreate();
        }

        // Afficher la vue (GET ou erreurs sans AJAX)
        $appBase = $this->getBaseUrl();
        include __DIR__ . '/../views/nouvelle-commande.view.php';
    }

    /**
     * Éditer une commande existante
     */
    public function edit($id) {
        $baseUrl = $this->getBaseUrl();

        if (!$id) {
            header('Location: ' . $baseUrl . '/');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->processEdit($id);
        }

        $this->data = $this->commande->getById($id);
        if (!$this->data) {
            header('Location: ' . $baseUrl . '/');
            exit;
        }

        // Déterminer le type de délais
        $this->data['delais_type']        = 'liste';
        $this->data['delais_liste_value'] = $this->data['delais_fabrication'];
        $this->data['delais_date_value']  = '';
        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $this->data['delais_fabrication'])) {
            $this->data['delais_type']        = 'date';
            $this->data['delais_date_value']  = $this->data['delais_fabrication'];
            $this->data['delais_liste_value'] = '';
        }

        $appBase = $this->getBaseUrl();
        include __DIR__ . '/../views/editer-commande.view.php';
    }

    // ──────────────────────────────────────────────────────────────────────────
    // Traitement création
    // ──────────────────────────────────────────────────────────────────────────
    private function processCreate() {
        $isBatAjax = !empty($_POST['bat_submit']);

        // Récupération des champs
        $this->commande->societe             = trim($_POST['societe']             ?? '');
        $this->commande->destinataire        = trim($_POST['destinataire']        ?? '');
        $this->commande->n_commande_client   = trim($_POST['n_commande_client']   ?? '');
        $this->commande->reference_article   = trim($_POST['reference_article']   ?? '');
        $this->commande->date_commande       = $_POST['date_commande']            ?? '';
        $this->commande->n_devis             = trim($_POST['n_devis']             ?? '');
        $this->commande->quantite_par_modele = intval($_POST['quantite_par_modele'] ?? 0);
        $this->commande->dossier_suivi_par   = trim($_POST['dossier_suivi_par']   ?? 'Matthieu');
        $this->commande->fichier_statut      = $_POST['fichier_statut']           ?? 'cree';
        $this->commande->descriptif          = $_POST['descriptif']               ?? '';
        $this->commande->bat_type            = in_array($_POST['bat_type'] ?? '', ['print', 'label'])
                                               ? $_POST['bat_type'] : 'print';

        // Délais de fabrication (datepicker prioritaire)
        $this->commande->delais_fabrication = !empty($_POST['delai_bat_date'])
            ? $_POST['delai_bat_date']
            : ($_POST['delai_bat_liste'] ?? '');

        // Validation
        if (empty($this->commande->societe)) {
            $this->errors[] = "La société est obligatoire";
        }
        if (empty($this->commande->n_commande_client)) {
            $this->errors[] = "Le numéro de commande client est obligatoire";
        }
        if ($isBatAjax && empty($_FILES['fichiers']['name'][0])) {
            $this->errors[] = "Veuillez ajouter au moins un fichier PDF";
        }

        if (empty($this->errors)) {
            $id = $this->commande->create();

            if ($id) {
                // Upload des fichiers
                $fichiers = $this->handleBatUploads((int)$id);
                foreach ($fichiers as $ordre => $f) {
                    $this->batFichier->create(
                        $id,
                        $f['nom_original'],
                        $f['nom_stockage'],
                        $ordre,
                        $f['taille'],
                        0
                    );
                }

                Security::logInfo('Nouvelle commande BAT créée', [
                    'id'      => $id,
                    'type'    => $this->commande->bat_type,
                    'societe' => $this->commande->societe,
                    'fichiers'=> count($fichiers),
                ]);

                $baseUrl = $this->getBaseUrl();
                $redirect = $baseUrl . '/bat/verifier/' . $id;

                if ($isBatAjax) {
                    header('Content-Type: application/json');
                    echo json_encode(['success' => true, 'redirect' => $redirect]);
                    exit;
                }

                header('Location: ' . $redirect);
                exit;

            } else {
                Security::logError('Échec création commande BAT', []);
                $this->errors[] = "Erreur lors de la création de la commande";
            }
        }

        // Retour JSON en cas d'erreurs AJAX
        if ($isBatAjax) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'errors' => $this->errors]);
            exit;
        }
    }

    // ──────────────────────────────────────────────────────────────────────────
    // Traitement édition
    // ──────────────────────────────────────────────────────────────────────────
    private function processEdit($id) {
        $this->commande->id                  = $id;
        $this->commande->societe             = trim($_POST['societe']             ?? '');
        $this->commande->destinataire        = trim($_POST['destinataire']        ?? '');
        $this->commande->n_commande_client   = trim($_POST['n_commande_client']   ?? '');
        $this->commande->reference_article   = trim($_POST['reference_article']   ?? '');
        $this->commande->date_commande       = $_POST['date_commande']            ?? '';
        $this->commande->n_devis             = trim($_POST['n_devis']             ?? '');
        $this->commande->quantite_par_modele = intval($_POST['quantite_par_modele'] ?? 0);
        $this->commande->dossier_suivi_par   = trim($_POST['dossier_suivi_par']   ?? '');
        $this->commande->fichier_statut      = $_POST['fichier_statut']           ?? 'cree';
        $this->commande->descriptif          = $_POST['descriptif']               ?? '';
        $this->commande->bat_type            = in_array($_POST['bat_type'] ?? '', ['print', 'label'])
                                               ? $_POST['bat_type'] : null;

        $this->commande->delais_fabrication = !empty($_POST['delai_bat_date'])
            ? $_POST['delai_bat_date']
            : ($_POST['delai_bat_liste'] ?? '');

        $faire_rechargement = isset($_POST['action']) && $_POST['action'] === 'recharger';

        if (empty($this->commande->societe)) {
            $this->errors[] = "La société est obligatoire";
        }
        if (empty($this->commande->n_commande_client)) {
            $this->errors[] = "Le numéro de commande client est obligatoire";
        }

        if (empty($this->errors)) {
            if ($faire_rechargement) {
                $ancienCsv = 'downloads/' . $this->commande->n_commande_client . '.csv';
                if (file_exists($ancienCsv)) unlink($ancienCsv);
                $this->commande->n_commande_client = $this->commande->incrementerVersion($this->commande->n_commande_client);
                $this->commande->date_commande     = date('Y-m-d');
                $this->commande->resetAlerte($id);
            }

            if ($this->commande->update()) {
                $data    = $this->commande->getById($id);
                $baseUrl = $this->getBaseUrl();

                Security::logInfo('Commande modifiée', ['n_commande' => $data['n_commande_client']]);

                $successParam = $faire_rechargement ? 'rechargement' : 'modification';
                header('Location: ' . $baseUrl . '/?success=' . $successParam);
                exit;
            } else {
                Security::logError('Échec modification commande', ['id' => $id]);
                $this->errors[] = "Erreur lors de la modification de la commande";
            }
        }
    }

    // ──────────────────────────────────────────────────────────────────────────
    // Upload des fichiers PDF
    // ──────────────────────────────────────────────────────────────────────────
    private function handleBatUploads($commande_id) {
        $saved = [];

        if (empty($_FILES['fichiers']['name'][0])) return $saved;

        $uploadDir = __DIR__ . '/../uploads/bat/' . $commande_id . '/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $count = count($_FILES['fichiers']['name']);

        for ($i = 0; $i < $count; $i++) {
            if ($_FILES['fichiers']['error'][$i] !== UPLOAD_ERR_OK) {
                Security::logWarning('Upload erreur', [
                    'file'  => $_FILES['fichiers']['name'][$i],
                    'error' => $_FILES['fichiers']['error'][$i],
                ]);
                continue;
            }

            // Vérification MIME
            $finfo    = new finfo(FILEINFO_MIME_TYPE);
            $mimeType = $finfo->file($_FILES['fichiers']['tmp_name'][$i]);
            if ($mimeType !== 'application/pdf') {
                Security::logSecurity('Upload fichier non-PDF refusé', [
                    'file' => $_FILES['fichiers']['name'][$i],
                    'mime' => $mimeType,
                ]);
                continue;
            }

            // Nom de stockage sécurisé
            $safeName  = preg_replace('/[^a-zA-Z0-9._\-]/', '_', $_FILES['fichiers']['name'][$i]);
            $stockage  = uniqid('', true) . '_' . $safeName;

            if (move_uploaded_file($_FILES['fichiers']['tmp_name'][$i], $uploadDir . $stockage)) {
                $saved[] = [
                    'nom_original' => $_FILES['fichiers']['name'][$i],
                    'nom_stockage' => $stockage,
                    'taille'       => $_FILES['fichiers']['size'][$i],
                ];
            }
        }

        return $saved;
    }
}
?>
