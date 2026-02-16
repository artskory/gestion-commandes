<?php
/**
 * Fichier de configuration
 * 
 * INSTRUCTIONS D'INSTALLATION :
 * 
 * 1. Créer la base de données :
 *    - Importer le fichier database.sql dans phpMyAdmin ou via ligne de commande
 * 
 * 2. Configurer la connexion :
 *    - Modifier les paramètres dans classes/Database.php si nécessaire
 *    - Par défaut : host=localhost, user=root, password='', database=gestion_commandes
 * 
 * 3. Structure des fichiers :
 *    /votre-projet/
 *    ├── classes/
 *    │   ├── Database.php
 *    │   ├── Commande.php
 *    │   └── CSVExporter.php
 *    ├── downloads/          (créé automatiquement)
 *    ├── index.php
 *    ├── nouvelle-commande.php
 *    ├── database.sql
 *    └── README.txt
 * 
 * 4. Permissions :
 *    - Le dossier downloads/ doit être accessible en écriture (chmod 777)
 * 
 * 5. Accéder au site :
 *    - http://localhost/votre-projet/index.php
 */

// Configuration des erreurs PHP (à désactiver en production)
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Timezone
date_default_timezone_set('Europe/Paris');
?>
