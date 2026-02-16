<?php
/**
 * Éditer une commande
 * Version 1.28 - Architecture MVC
 */

require_once 'controllers/CommandeController.php';

// Vérifier si un ID est fourni
if (!isset($_GET['id'])) {
    $baseUrl = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/');
    header('Location: ' . $baseUrl . '/');
    exit;
}

$controller = new CommandeController();
$controller->edit($_GET['id']);
?>
