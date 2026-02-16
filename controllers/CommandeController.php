<?php
/**
 * CommandeController - Gestion de la création et édition de commandes
 */
class CommandeController {
    private $commande;
    private $csvExporter;
    public $errors = [];
    public $data = null;
    
    public function __construct() {
        require_once 'classes/Database.php';
        require_once 'classes/Commande.php';
        require_once 'classes/CSVExporter.php';
        
        $database = new Database();
        $db = $database->getConnection();
        
        $this->commande = new Commande($db);
        $this->csvExporter = new CSVExporter();
    }
    
    /**
     * Obtenir l'URL de base de l'application
     */
    private function getBaseUrl() {
        // Obtenir le chemin du script sans le nom du fichier
        $scriptPath = dirname($_SERVER['SCRIPT_NAME']);
        // Normaliser le chemin (enlever les / multiples)
        return rtrim($scriptPath, '/');
    }
    
    /**
     * Créer une nouvelle commande
     */
    public function create() {
        // Traitement du formulaire
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $this->processCreate();
        }
        
        // Afficher la vue
        include 'views/nouvelle-commande.view.php';
    }
    
    /**
     * Éditer une commande existante
     */
    public function edit($id) {
        $baseUrl = $this->getBaseUrl();
        
        // Vérifier si un ID est fourni
        if (!$id) {
            header('Location: ' . $baseUrl . '/');
            exit;
        }
        
        // Traitement du formulaire
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $this->processEdit($id);
        }
        
        // Récupérer les données de la commande
        $this->data = $this->commande->getById($id);
        
        if (!$this->data) {
            header('Location: ' . $baseUrl . '/');
            exit;
        }
        
        // Déterminer le type de délais (liste ou date)
        $this->data['delais_type'] = 'liste';
        $this->data['delais_liste_value'] = $this->data['delais_fabrication'];
        $this->data['delais_date_value'] = '';
        
        // Si le délais correspond à un format de date
        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $this->data['delais_fabrication'])) {
            $this->data['delais_type'] = 'date';
            $this->data['delais_date_value'] = $this->data['delais_fabrication'];
            $this->data['delais_liste_value'] = '';
        }
        
        // Afficher la vue
        include 'views/editer-commande.view.php';
    }
    
    /**
     * Traiter la création d'une commande
     */
    private function processCreate() {
        // Récupération et validation des données
        $this->commande->societe = trim($_POST['societe']);
        $this->commande->destinataire = trim($_POST['destinataire']);
        $this->commande->n_commande_client = trim($_POST['n_commande_client']);
        $this->commande->reference_article = trim($_POST['reference_article']);
        $this->commande->date_commande = $_POST['date_commande'];
        $this->commande->n_devis = trim($_POST['n_devis']);
        $this->commande->quantite_par_modele = intval($_POST['quantite_par_modele']);
        $this->commande->dossier_suivi_par = trim($_POST['dossier_suivi_par']);
        
        // Gestion du délais de fabrication (prioriser le date picker)
        if (!empty($_POST['delais_date'])) {
            $this->commande->delais_fabrication = $_POST['delais_date'];
        } else {
            $this->commande->delais_fabrication = $_POST['delais_liste'];
        }
        
        $this->commande->fichier_statut = $_POST['fichier_statut'];
        
        // Validation
        if (empty($this->commande->societe)) {
            $this->errors[] = "La société est obligatoire";
        }
        if (empty($this->commande->n_commande_client)) {
            $this->errors[] = "Le numéro de commande client est obligatoire";
        }
        
        // Si pas d'erreurs, créer la commande
        if (empty($this->errors)) {
            $id = $this->commande->create();
            
            if ($id) {
                // Récupérer les données pour le CSV
                $data = $this->commande->getById($id);
                
                // Générer le fichier CSV (sans forcer le téléchargement)
                $filepath = $this->csvExporter->exportCommande($data, $this->commande->n_commande_client);
                
                // Rediriger vers l'index avec le chemin du fichier pour téléchargement
                $baseUrl = $this->getBaseUrl();
                header('Location: ' . $baseUrl . '/?success=creation&download=' . urlencode(basename($filepath)));
                exit;
            } else {
                $this->errors[] = "Erreur lors de la création de la commande";
            }
        }
    }
    
    /**
     * Traiter l'édition d'une commande
     */
    private function processEdit($id) {
        // Récupération et validation des données
        $this->commande->id = $id;
        $this->commande->societe = trim($_POST['societe']);
        $this->commande->destinataire = trim($_POST['destinataire']);
        $this->commande->n_commande_client = trim($_POST['n_commande_client']);
        $this->commande->reference_article = trim($_POST['reference_article']);
        $this->commande->date_commande = $_POST['date_commande'];
        $this->commande->n_devis = trim($_POST['n_devis']);
        $this->commande->quantite_par_modele = intval($_POST['quantite_par_modele']);
        $this->commande->dossier_suivi_par = trim($_POST['dossier_suivi_par']);
        
        // Gestion du délais de fabrication (prioriser le date picker)
        if (!empty($_POST['delais_date'])) {
            $this->commande->delais_fabrication = $_POST['delais_date'];
        } else {
            $this->commande->delais_fabrication = $_POST['delais_liste'];
        }
        
        $this->commande->fichier_statut = $_POST['fichier_statut'];
        
        // Vérifier si on doit faire un rechargement
        $faire_rechargement = isset($_POST['action']) && $_POST['action'] == 'recharger';
        
        // Validation
        if (empty($this->commande->societe)) {
            $this->errors[] = "La société est obligatoire";
        }
        if (empty($this->commande->n_commande_client)) {
            $this->errors[] = "Le numéro de commande client est obligatoire";
        }
        
        // Si pas d'erreurs, mettre à jour la commande
        if (empty($this->errors)) {
            // Si rechargement demandé, incrémenter la version
            if ($faire_rechargement) {
                $this->commande->n_commande_client = $this->commande->incrementerVersion($this->commande->n_commande_client);
            }
            
            if ($this->commande->update()) {
                // Récupérer les données mises à jour pour le CSV
                $data = $this->commande->getById($id);
                
                // Générer le fichier CSV (sans forcer le téléchargement)
                $filepath = $this->csvExporter->exportCommande($data, $this->commande->n_commande_client);
                
                // Rediriger vers l'index avec le chemin du fichier pour téléchargement
                $baseUrl = $this->getBaseUrl();
                if ($faire_rechargement) {
                    header('Location: ' . $baseUrl . '/?success=rechargement&download=' . urlencode(basename($filepath)));
                } else {
                    header('Location: ' . $baseUrl . '/?success=modification&download=' . urlencode(basename($filepath)));
                }
                exit;
            } else {
                $this->errors[] = "Erreur lors de la modification de la commande";
            }
        }
    }
}
?>
