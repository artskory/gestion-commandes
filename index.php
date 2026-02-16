<?php
/**
 * Page d'accueil - Liste des commandes
 * Version 1.18 - Architecture MVC
 */

require_once 'controllers/IndexController.php';

$controller = new IndexController();
$controller->index();
?>
