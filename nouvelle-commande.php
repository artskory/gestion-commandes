<?php
/**
 * Nouvelle commande
 * Version 1.18 - Architecture MVC
 */

require_once 'controllers/CommandeController.php';

$controller = new CommandeController();
$controller->create();
?>
