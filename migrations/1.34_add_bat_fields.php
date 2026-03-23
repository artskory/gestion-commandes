<?php
/**
 * Migration 1.34 — Ajout champs BAT et table bat_fichiers
 */
return [
    'version'     => '1.34',
    'description' => 'Ajout descriptif, bat_type dans commandes + table bat_fichiers',
    'up' => function($pdo) {
        // Colonne descriptif
        $c = $pdo->query("SHOW COLUMNS FROM commandes LIKE 'descriptif'")->fetchAll();
        if (empty($c)) {
            $pdo->exec("ALTER TABLE commandes ADD COLUMN descriptif TEXT NULL DEFAULT NULL AFTER alerte_depuis");
        }
        // Colonne bat_type
        $c2 = $pdo->query("SHOW COLUMNS FROM commandes LIKE 'bat_type'")->fetchAll();
        if (empty($c2)) {
            $pdo->exec("ALTER TABLE commandes ADD COLUMN bat_type ENUM('print','label') NULL DEFAULT NULL AFTER descriptif");
        }
        // Table bat_fichiers
        $pdo->exec("CREATE TABLE IF NOT EXISTS bat_fichiers (
            id            INT AUTO_INCREMENT PRIMARY KEY,
            commande_id   INT NOT NULL,
            nom_original  VARCHAR(255) NOT NULL,
            nom_stockage  VARCHAR(255) NOT NULL,
            ordre         TINYINT UNSIGNED DEFAULT 0,
            taille_octets INT UNSIGNED,
            nb_pages      SMALLINT UNSIGNED DEFAULT 0,
            created_at    TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_bat_commande (commande_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    }
];
