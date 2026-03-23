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
    public $descriptif;
    public $bat_type;

    public function __construct($db) {
        $this->conn = $db;
    }

    /**
     * Récupérer toutes les commandes
     */
    public function getAll() {
        $query = "SELECT * FROM " . $this->table . " ORDER BY id DESC";
        $stmt  = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    /**
     * Récupérer une commande par ID
     */
    public function getById($id) {
        $query = "SELECT * FROM " . $this->table . " WHERE id = :id LIMIT 1";
        $stmt  = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch();
    }

    /**
     * Créer une nouvelle commande
     */
    public function create() {
        $query = "INSERT INTO " . $this->table . "
                  SET societe             = :societe,
                      destinataire        = :destinataire,
                      n_commande_client   = :n_commande_client,
                      reference_article   = :reference_article,
                      date_commande       = :date_commande,
                      n_devis             = :n_devis,
                      quantite_par_modele = :quantite_par_modele,
                      dossier_suivi_par   = :dossier_suivi_par,
                      delais_fabrication  = :delais_fabrication,
                      fichier_statut      = :fichier_statut,
                      descriptif          = :descriptif,
                      bat_type            = :bat_type";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':societe',             $this->societe);
        $stmt->bindParam(':destinataire',        $this->destinataire);
        $stmt->bindParam(':n_commande_client',   $this->n_commande_client);
        $stmt->bindParam(':reference_article',   $this->reference_article);
        $stmt->bindParam(':date_commande',       $this->date_commande);
        $stmt->bindParam(':n_devis',             $this->n_devis);
        $stmt->bindParam(':quantite_par_modele', $this->quantite_par_modele);
        $stmt->bindParam(':dossier_suivi_par',   $this->dossier_suivi_par);
        $stmt->bindParam(':delais_fabrication',  $this->delais_fabrication);
        $stmt->bindParam(':fichier_statut',      $this->fichier_statut);
        $stmt->bindParam(':descriptif',          $this->descriptif);
        $stmt->bindParam(':bat_type',            $this->bat_type);

        if ($stmt->execute()) {
            return $this->conn->lastInsertId();
        }
        return false;
    }

    /**
     * Mettre à jour une commande
     */
    public function update() {
        $query = "UPDATE " . $this->table . "
                  SET societe             = :societe,
                      destinataire        = :destinataire,
                      n_commande_client   = :n_commande_client,
                      reference_article   = :reference_article,
                      date_commande       = :date_commande,
                      n_devis             = :n_devis,
                      quantite_par_modele = :quantite_par_modele,
                      dossier_suivi_par   = :dossier_suivi_par,
                      delais_fabrication  = :delais_fabrication,
                      fichier_statut      = :fichier_statut,
                      descriptif          = :descriptif,
                      bat_type            = :bat_type
                  WHERE id = :id";

        $stmt = $this->conn->prepare($query);

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
        $stmt->bindValue(':descriptif',           $this->descriptif);
        $stmt->bindValue(':bat_type',             $this->bat_type);

        return $stmt->execute();
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
        if (preg_match('/^(.+)-V(\d+)$/', $numero_actuel, $matches)) {
            return $matches[1] . '-V' . (intval($matches[2]) + 1);
        }
        return $numero_actuel . '-V2';
    }

    /**
     * Supprimer les commandes de plus de 7 jours
     */
    public function deleteOldCommandes() {
        $query = "SELECT id, n_commande_client FROM " . $this->table . "
                  WHERE created_at < DATE_SUB(NOW(), INTERVAL 7 DAY)";
        $stmt  = $this->conn->prepare($query);
        $stmt->execute();
        $commandes = $stmt->fetchAll();

        $query2 = "DELETE FROM " . $this->table . "
                   WHERE created_at < DATE_SUB(NOW(), INTERVAL 7 DAY)";
        $stmt2 = $this->conn->prepare($query2);

        if ($stmt2->execute()) {
            $count = $stmt2->rowCount();
            foreach ($commandes as $commande) {
                // CSV (legacy)
                $csvFile = 'downloads/' . $commande['n_commande_client'] . '.csv';
                if (file_exists($csvFile)) unlink($csvFile);
            }
            return $count;
        }
        return false;
    }

    /**
     * Supprimer une commande par ID
     */
    public function delete($id) {
        $commande = $this->getById($id);

        $query = "DELETE FROM " . $this->table . " WHERE id = :id";
        $stmt  = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);

        if ($stmt->execute()) {
            if ($commande) {
                // Supprimer les fichiers uploadés BAT
                $uploadDir = __DIR__ . '/../uploads/bat/' . $id . '/';
                if (is_dir($uploadDir)) {
                    array_map('unlink', glob($uploadDir . '*'));
                    rmdir($uploadDir);
                }
                // CSV (legacy)
                if (!empty($commande['n_commande_client'])) {
                    $csvFile = 'downloads/' . $commande['n_commande_client'] . '.csv';
                    if (file_exists($csvFile)) unlink($csvFile);
                }
            }
            return true;
        }
        return false;
    }

    /**
     * Snooze l'alerte d'une commande
     */
    public function snoozeAlerte($id) {
        $stmt = $this->conn->prepare(
            "UPDATE " . $this->table . " SET alerte_depuis = :today WHERE id = :id"
        );
        $stmt->bindValue(':today', date('Y-m-d'));
        $stmt->bindValue(':id',    $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    /**
     * Remettre à zéro l'alerte
     */
    public function resetAlerte($id) {
        $stmt = $this->conn->prepare(
            "UPDATE " . $this->table . " SET alerte_depuis = NULL WHERE id = :id"
        );
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }
}
?>
