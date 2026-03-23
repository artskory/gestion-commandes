<?php
/**
 * BatController — Gestion des étapes BAT (vérification, génération)
 */
require_once __DIR__ . '/../classes/Security.php';

class BatController {
    private $commande;
    private $batFichier;

    public function __construct() {
        require_once __DIR__ . '/../classes/Database.php';
        require_once __DIR__ . '/../classes/Commande.php';
        require_once __DIR__ . '/../classes/BatFichier.php';

        $db = (new Database())->getConnection();
        $this->commande   = new Commande($db);
        $this->batFichier = new BatFichier($db);
    }

    /**
     * Étape 2 — Vérifications prepress + résumé
     */
    public function verifier($id) {
        $baseUrl = $this->getBaseUrl();

        if (!$id || !is_numeric($id)) {
            header('Location: ' . $baseUrl . '/');
            exit;
        }

        $commande = $this->commande->getById((int)$id);
        if (!$commande) {
            header('Location: ' . $baseUrl . '/');
            exit;
        }

        $fichiers = $this->batFichier->getByCommandeId((int)$id);
        $appBase  = $baseUrl;

        include __DIR__ . '/../views/bat-etape2.view.php';
    }

    private function getBaseUrl() {
        $scriptPath = dirname($_SERVER['SCRIPT_NAME']);
        if (basename($scriptPath) === 'public') {
            $scriptPath = dirname($scriptPath);
        }
        return '/' . trim($scriptPath, '/');
    }
}
?>
