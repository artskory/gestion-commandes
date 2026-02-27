<?php
/**
 * Nouvelle commande
 * Version 1.18 - Architecture MVC
 */

require_once __DIR__ . '/../controllers/CommandeController.php';

$controller = new CommandeController();
$controller->create();
?>