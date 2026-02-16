<?php
/**
 * Classe Database - Gestion de la connexion à la base de données
 */
class Database {
    private $host = 'localhost';
    private $db_name = 'gestion_commandes';
    private $username = 'root';
    private $password = '';
    private $conn;
    
    /**
     * Obtenir la connexion à la base de données
     */
    public function getConnection() {
        $this->conn = null;
        
        try {
            $this->conn = new PDO(
                "mysql:host=" . $this->host . ";dbname=" . $this->db_name . ";charset=utf8mb4",
                $this->username,
                $this->password,
                array(
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"
                )
            );
        } catch(PDOException $e) {
            echo "Erreur de connexion : " . $e->getMessage();
        }
        
        return $this->conn;
    }
}
?>
