<?php
/**
 * Classe Commande - Gestion des commandes
 */
class Commande {
    private $conn;
    private $table = 'commandes';
    
    public $id;
    public $societe;
    public $destinataire;
    public $n_commande_client;
    public $reference_article;
    public $date_commande;
    public $n_devis;
    public $quantite_par_modele;
    public $dossier_suivi_par;
    public $delais_fabrication;
    public $fichier_statut;
    
    /**
     * Constructeur
     */
    public function __construct($db) {
        $this->conn = $db;
    }
    
    /**
     * Récupérer toutes les commandes
     */
    public function getAll() {
        $query = "SELECT * FROM " . $this->table . " ORDER BY id DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }
    
    /**
     * Récupérer une commande par ID
     */
    public function getById($id) {
        $query = "SELECT * FROM " . $this->table . " WHERE id = :id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch();
    }
    
    /**
     * Créer une nouvelle commande
     */
    public function create() {
        $query = "INSERT INTO " . $this->table . " 
                  SET societe = :societe,
                      destinataire = :destinataire,
                      n_commande_client = :n_commande_client,
                      reference_article = :reference_article,
                      date_commande = :date_commande,
                      n_devis = :n_devis,
                      quantite_par_modele = :quantite_par_modele,
                      dossier_suivi_par = :dossier_suivi_par,
                      delais_fabrication = :delais_fabrication,
                      fichier_statut = :fichier_statut";
        
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(':societe', $this->societe);
        $stmt->bindParam(':destinataire', $this->destinataire);
        $stmt->bindParam(':n_commande_client', $this->n_commande_client);
        $stmt->bindParam(':reference_article', $this->reference_article);
        $stmt->bindParam(':date_commande', $this->date_commande);
        $stmt->bindParam(':n_devis', $this->n_devis);
        $stmt->bindParam(':quantite_par_modele', $this->quantite_par_modele);
        $stmt->bindParam(':dossier_suivi_par', $this->dossier_suivi_par);
        $stmt->bindParam(':delais_fabrication', $this->delais_fabrication);
        $stmt->bindParam(':fichier_statut', $this->fichier_statut);
        
        if($stmt->execute()) {
            return $this->conn->lastInsertId();
        }
        return false;
    }
    
    /**
     * Mettre à jour le numéro de commande avec version et la date
     */
    public function updateNumeroCommande($id, $nouveau_numero, $date_commande = null) {
        $query = "UPDATE " . $this->table . " 
                  SET n_commande_client = :nouveau_numero
                  " . ($date_commande !== null ? ", date_commande = :date_commande" : "") . "
                  WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':nouveau_numero', $nouveau_numero);
        $stmt->bindValue(':id', $id);
        if ($date_commande !== null) {
            $stmt->bindValue(':date_commande', $date_commande);
        }
        
        return $stmt->execute();
    }
    
    /**
     * Incrémenter la version du numéro de commande
     */
    public function incrementerVersion($numero_actuel) {
        // Chercher si une version existe déjà (ex: CO2601-4804-V2)
        if (preg_match('/^(.+)-V(\d+)$/', $numero_actuel, $matches)) {
            $base = $matches[1];
            $version = intval($matches[2]) + 1;
            return $base . '-V' . $version;
        } else {
            // Première version
            return $numero_actuel . '-V2';
        }
    }
    
    /**
     * Supprimer les commandes de plus de 7 jours
     */
    public function deleteOldCommandes() {
        // Récupérer les commandes à supprimer pour obtenir leurs fichiers CSV
        $query = "SELECT n_commande_client FROM " . $this->table . " 
                  WHERE created_at < DATE_SUB(NOW(), INTERVAL 7 DAY)";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $commandes = $stmt->fetchAll();
        
        // Supprimer de la base de données
        $query = "DELETE FROM " . $this->table . " 
                  WHERE created_at < DATE_SUB(NOW(), INTERVAL 7 DAY)";
        
        $stmt = $this->conn->prepare($query);
        
        if($stmt->execute()) {
            $count = $stmt->rowCount();
            
            // Supprimer les fichiers CSV associés
            foreach ($commandes as $commande) {
                if (!empty($commande['n_commande_client'])) {
                    $csvFile = 'downloads/' . $commande['n_commande_client'] . '.csv';
                    if (file_exists($csvFile)) {
                        unlink($csvFile);
                    }
                }
            }
            
            return $count;
        }
        return false;
    }
    
    /**
     * Supprimer une commande par ID
     */
    public function delete($id) {
        // Récupérer les infos de la commande avant suppression
        $commande = $this->getById($id);
        
        // Supprimer de la base de données
        $query = "DELETE FROM " . $this->table . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        
        if ($stmt->execute()) {
            // Supprimer le fichier CSV associé si la commande existait
            if ($commande && !empty($commande['n_commande_client'])) {
                $csvFile = 'downloads/' . $commande['n_commande_client'] . '.csv';
                if (file_exists($csvFile)) {
                    unlink($csvFile);
                }
            }
            return true;
        }
        
        return false;
    }
    
    /**
     * Mettre à jour une commande
     */
    public function update() {
        $query = "UPDATE " . $this->table . " 
                  SET societe = :societe,
                      destinataire = :destinataire,
                      n_commande_client = :n_commande_client,
                      reference_article = :reference_article,
                      date_commande = :date_commande,
                      n_devis = :n_devis,
                      quantite_par_modele = :quantite_par_modele,
                      dossier_suivi_par = :dossier_suivi_par,
                      delais_fabrication = :delais_fabrication,
                      fichier_statut = :fichier_statut
                  WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        
        // bindValue copie la valeur au moment de l'appel (contrairement à bindParam
        // qui lie par référence et peut causer des problèmes avec les propriétés d'objet)
        $stmt->bindValue(':id',                   $this->id);
        $stmt->bindValue(':societe',              $this->societe);
        $stmt->bindValue(':destinataire',         $this->destinataire);
        $stmt->bindValue(':n_commande_client',    $this->n_commande_client);
        $stmt->bindValue(':reference_article',    $this->reference_article);
        $stmt->bindValue(':date_commande',        $this->date_commande);
        $stmt->bindValue(':n_devis',              $this->n_devis);
        $stmt->bindValue(':quantite_par_modele',  $this->quantite_par_modele, PDO::PARAM_INT);
        $stmt->bindValue(':dossier_suivi_par',    $this->dossier_suivi_par);
        $stmt->bindValue(':delais_fabrication',   $this->delais_fabrication);
        $stmt->bindValue(':fichier_statut',       $this->fichier_statut);
        
        return $stmt->execute();
    }
}
?>
