<?php
/**
 * Nouvelle commande
 * Version 1.18 - Architecture MVC
 */

require_once __DIR__ . '/../version.php';
require_once __DIR__ . '/../classes/Security.php';
Security::applyHeaders();

require_once __DIR__ . '/../controllers/CommandeController.php';

$controller = new CommandeController();
$controller->create();
?>