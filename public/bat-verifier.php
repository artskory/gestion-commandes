<?php
/**
 * BAT — Étape 2 : Vérifications prepress
 */
require_once __DIR__ . '/../version.php';
require_once __DIR__ . '/../classes/Security.php';
Security::applyHeaders();

require_once __DIR__ . '/../controllers/BatController.php';

$controller = new BatController();
$controller->verifier($_GET['id'] ?? null);
?>
