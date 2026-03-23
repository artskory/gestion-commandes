-- Base de données pour la gestion des commandes
CREATE DATABASE IF NOT EXISTS gestion_commandes CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE gestion_commandes;

-- Table des commandes
CREATE TABLE IF NOT EXISTS commandes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    societe VARCHAR(255) NOT NULL,
    destinataire VARCHAR(255) NOT NULL,
    n_commande_client VARCHAR(100) NOT NULL,
    reference_article VARCHAR(255),
    date_commande DATE,
    alerte_depuis DATE NULL DEFAULT NULL,
    descriptif TEXT NULL DEFAULT NULL,
    bat_type ENUM('print','label') NULL DEFAULT NULL,
    n_devis VARCHAR(100),
    quantite_par_modele INT,
    dossier_suivi_par VARCHAR(100) DEFAULT 'Matthieu',
    delais_fabrication VARCHAR(100),
    fichier_statut ENUM('cree', 'fourni') DEFAULT 'cree',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_n_commande (n_commande_client)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table des fichiers PDF uploadés pour les BAT
CREATE TABLE IF NOT EXISTS bat_fichiers (
    id            INT AUTO_INCREMENT PRIMARY KEY,
    commande_id   INT NOT NULL,
    nom_original  VARCHAR(255) NOT NULL,
    nom_stockage  VARCHAR(255) NOT NULL,
    ordre         TINYINT UNSIGNED DEFAULT 0,
    taille_octets INT UNSIGNED,
    nb_pages      SMALLINT UNSIGNED DEFAULT 0,
    created_at    TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_bat_commande (commande_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
