<?php
/**
 * Classe CSVExporter - Gestion de l'export CSV
 */
class CSVExporter {
    private $download_path;
    
    /**
     * Constructeur
     */
    public function __construct($download_path = 'downloads/') {
        $this->download_path = $download_path;
        
        // Créer le dossier s'il n'existe pas
        if (!file_exists($this->download_path)) {
            mkdir($this->download_path, 0777, true);
        }
    }
    
    /**
     * Exporter une commande en CSV
     */
    public function exportCommande($commande, $filename) {
        $filepath = $this->download_path . $filename . '.csv';
        
        // Ouvrir le fichier en écriture avec encodage UTF-8
        $file = fopen($filepath, 'w');
        
        // Ajouter le BOM UTF-8 pour une meilleure compatibilité
        fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
        
        // En-têtes
        $headers = array(
            'societé',
            'destinataire',
            'N commande client',
            'référence article',
            'Date',
            'Notre N° de devis',
            'Quantité par modèle',
            'dossie suivi par :',
            'délais de fabrication',
            'fichier crée par nos soins',
            'fichier fourni'
        );
        
        // Écrire les en-têtes sans guillemets
        fwrite($file, implode(';', $headers) . "\n");
        
        // Données - avec les deux colonnes de fichier
        $data = array(
            $commande['societe'],
            $commande['destinataire'],
            $commande['n_commande_client'],
            $commande['reference_article'],
            $this->formatDateFr($commande['date_commande']),
            $commande['n_devis'],
            $commande['quantite_par_modele'],
            $commande['dossier_suivi_par'],
            $this->formatDelaisFabrication($commande['delais_fabrication']),
            $commande['fichier_statut'] == 'cree' ? 'x' : '',
            $commande['fichier_statut'] == 'fourni' ? 'x' : ''
        );
        
        // Écrire les données sans guillemets
        fwrite($file, implode(';', $data) . "\n");
        
        fclose($file);
        
        return $filepath;
    }
    
    /**
     * Formater les délais de fabrication
     */
    private function formatDelaisFabrication($delais) {
        if (empty($delais)) {
            return '';
        }
        
        // Si c'est au format J+X (de la liste), ajouter "à validation du BAT"
        if (preg_match('/^J\+\d+$/', $delais)) {
            return $delais . ' à validation du BAT';
        }
        
        // Si c'est une date (YYYY-MM-DD), la formater en français
        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $delais)) {
            return $this->formatDateFr($delais);
        }
        
        return $delais;
    }
    
    /**
     * Formater une date en format français (jj/mm/aaaa)
     */
    private function formatDateFr($date) {
        if (empty($date)) {
            return '';
        }
        
        // Convertir YYYY-MM-DD en DD/MM/YYYY
        if (preg_match('/^(\d{4})-(\d{2})-(\d{2})$/', $date, $matches)) {
            return $matches[3] . '/' . $matches[2] . '/' . $matches[1];
        }
        
        return $date;
    }
    
    /**
     * Télécharger le fichier CSV
     */
    public function downloadFile($filepath) {
        if (file_exists($filepath)) {
            // Nettoyer tous les buffers de sortie
            while (ob_get_level()) {
                ob_end_clean();
            }
            
            header('Content-Type: text/csv; charset=utf-8');
            header('Content-Disposition: attachment; filename="' . basename($filepath) . '"');
            header('Content-Length: ' . filesize($filepath));
            header('Cache-Control: no-cache, must-revalidate');
            header('Expires: 0');
            header('Pragma: public');
            
            readfile($filepath);
            exit;
        }
        return false;
    }
    
    /**
     * Vider le dossier downloads
     */
    public function clearDownloads() {
        $count = 0;
        if (is_dir($this->download_path)) {
            $files = glob($this->download_path . '*.csv');
            foreach ($files as $file) {
                if (is_file($file)) {
                    unlink($file);
                    $count++;
                }
            }
        }
        return $count;
    }
}
?>
