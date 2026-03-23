<?php
/**
 * BatFichier — Gestion des fichiers PDF d'un BAT
 */
class BatFichier {
    private $conn;
    private $table = 'bat_fichiers';

    public function __construct($db) {
        $this->conn = $db;
    }

    /**
     * Créer un enregistrement de fichier
     */
    public function create($commande_id, $nom_original, $nom_stockage, $ordre, $taille, $nb_pages = 0) {
        $stmt = $this->conn->prepare(
            "INSERT INTO {$this->table}
             SET commande_id   = :cid,
                 nom_original  = :no,
                 nom_stockage  = :ns,
                 ordre         = :ordre,
                 taille_octets = :taille,
                 nb_pages      = :pages"
        );
        $stmt->bindValue(':cid',    $commande_id, PDO::PARAM_INT);
        $stmt->bindValue(':no',     $nom_original);
        $stmt->bindValue(':ns',     $nom_stockage);
        $stmt->bindValue(':ordre',  $ordre,        PDO::PARAM_INT);
        $stmt->bindValue(':taille', $taille,       PDO::PARAM_INT);
        $stmt->bindValue(':pages',  $nb_pages,     PDO::PARAM_INT);
        return $stmt->execute();
    }

    /**
     * Récupérer les fichiers d'une commande (triés par ordre)
     */
    public function getByCommandeId($commande_id) {
        $stmt = $this->conn->prepare(
            "SELECT * FROM {$this->table}
             WHERE commande_id = :id
             ORDER BY ordre ASC"
        );
        $stmt->bindValue(':id', $commande_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Mettre à jour le nombre de pages d'un fichier
     */
    public function updatePages($id, $nb_pages) {
        $stmt = $this->conn->prepare(
            "UPDATE {$this->table} SET nb_pages = :pages WHERE id = :id"
        );
        $stmt->bindValue(':pages', $nb_pages, PDO::PARAM_INT);
        $stmt->bindValue(':id',    $id,       PDO::PARAM_INT);
        return $stmt->execute();
    }

    /**
     * Supprimer tous les fichiers d'une commande
     */
    public function deleteByCommandeId($commande_id) {
        $stmt = $this->conn->prepare(
            "DELETE FROM {$this->table} WHERE commande_id = :id"
        );
        $stmt->bindValue(':id', $commande_id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    /**
     * Supprimer un fichier par ID
     */
    public function delete($id) {
        $stmt = $this->conn->prepare(
            "DELETE FROM {$this->table} WHERE id = :id"
        );
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }
}
?>
