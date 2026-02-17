<?php
/**
 * IndexController - Gestion de la page d'accueil et des actions sur les commandes
 */
class IndexController {
    private $commande;
    private $csvExporter;
    public $commandes = [];
    public $success = null;
    public $error = null;
    public $count = 0;
    
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
     * Afficher la liste des commandes
     */
    public function index() {
        // Gestion de la suppression individuelle
        if (isset($_GET['supprimer']) && isset($_GET['id'])) {
            $this->supprimerCommande($_GET['id']);
        }
        
        // Gestion de la suppression des commandes de plus de 7 jours
        if (isset($_GET['corbeille']) && $_GET['corbeille'] == 'confirme') {
            $this->supprimerCommandesAnciennes();
        }
        
        // Gestion du rechargement (création nouvelle version)
        if (isset($_GET['recharger']) && isset($_GET['id'])) {
            $this->rechargerCommande($_GET['id']);
        }
        
        // Récupérer les messages de succès/erreur
        if (isset($_GET['success'])) {
            $this->success = $_GET['success'];
        }
        if (isset($_GET['error'])) {
            $this->error = $_GET['error'];
        }
        if (isset($_GET['count'])) {
            $this->count = intval($_GET['count']);
        }
        
        // Récupérer toutes les commandes
        $stmt = $this->commande->getAll();
        $this->commandes = $stmt->fetchAll();
        
        // Afficher la vue
        include 'views/index.view.php';
    }
    
    /**
     * Supprimer une ou plusieurs commandes (IDs séparés par virgules)
     */
    private function supprimerCommande($ids) {
        $baseUrl = $this->getBaseUrl();
        
        // Accepter un ou plusieurs IDs séparés par virgules
        $listeIds = array_filter(array_map('intval', explode(',', $ids)));
        
        if (empty($listeIds)) {
            header('Location: ' . $baseUrl . '/?error=suppression');
            exit;
        }
        
        $succes = true;
        foreach ($listeIds as $id) {
            if (!$this->commande->delete($id)) {
                $succes = false;
            }
        }
        
        if ($succes) {
            header('Location: ' . $baseUrl . '/?success=suppression_individuelle');
        } else {
            header('Location: ' . $baseUrl . '/?error=suppression');
        }
        exit;
    }
    
    /**
     * Supprimer les commandes de plus de 7 jours
     */
    private function supprimerCommandesAnciennes() {
        $baseUrl = $this->getBaseUrl();
        $count = $this->commande->deleteOldCommandes();
        header('Location: ' . $baseUrl . '/?success=suppression&count=' . $count);
        exit;
    }
    
    /**
     * Recharger une commande (créer une nouvelle version)
     */
    private function rechargerCommande($id) {
        $data = $this->commande->getById($id);
        
        if ($data) {
            // Supprimer l'ancien CSV avant de générer le nouveau
            $ancienCsv = 'downloads/' . $data['n_commande_client'] . '.csv';
            if (file_exists($ancienCsv)) {
                unlink($ancienCsv);
            }
            
            // Incrémenter la version
            $nouveau_numero = $this->commande->incrementerVersion($data['n_commande_client']);
            
            // Mettre à jour en base de données
            $this->commande->updateNumeroCommande($id, $nouveau_numero);
            
            // Recharger les données mises à jour
            $data['n_commande_client'] = $nouveau_numero;
            
            // Générer le CSV (sans forcer le téléchargement)
            $filepath = $this->csvExporter->exportCommande($data, $nouveau_numero);
            
            // Rediriger vers l'index avec le chemin du fichier pour téléchargement
            $baseUrl = $this->getBaseUrl();
            header('Location: ' . $baseUrl . '/?success=rechargement&download=' . urlencode(basename($filepath)));
            exit;
        }
    }
}
?>
