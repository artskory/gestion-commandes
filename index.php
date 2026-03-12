<?php
/**
 * Page d'accueil - Liste des commandes
 * Version 1.18 - Architecture MVC
 */

// Ping bookmarklet : posé un cookie de longue durée et répond en JSON
if (isset($_GET['ping-bookmarklet'])) {
    setcookie('bookmarklet_installed', '1', time() + (365 * 24 * 3600), '/');
    header('Content-Type: application/json');
    header('Access-Control-Allow-Origin: *');
    echo json_encode(['status' => 'ok']);
    exit;
}

require_once 'classes/Security.php';
Security::applyHeaders();

require_once 'controllers/IndexController.php';

$controller = new IndexController();
$controller->index();
?>
