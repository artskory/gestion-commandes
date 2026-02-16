<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Commandes</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css"/>
    <link rel="stylesheet" href="css/style.css">
    <link rel="apple-touch-icon" sizes="180x180" href="image/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="image/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="image/favicon-16x16.png">
    <link rel="manifest" href="image/site.webmanifest">
</head>
<body class="bg-gradient-to-br" 
      <?php if ($this->success): 
          $msg = '';
          if ($this->success == 'creation') $msg = 'Commande créée avec succès !';
          elseif ($this->success == 'rechargement') $msg = 'Nouvelle version générée avec succès !';
          elseif ($this->success == 'suppression') $msg = $this->count . ' commande(s) de plus de 7 jours supprimée(s) avec succès !';
          elseif ($this->success == 'suppression_individuelle') $msg = 'Commande supprimée avec succès !';
          elseif ($this->success == 'modification') $msg = 'Commande modifiée avec succès !';
          echo 'data-success-msg="' . htmlspecialchars($msg, ENT_QUOTES, 'UTF-8') . '"';
      endif; ?>
      <?php if ($this->error): 
          $msg = '';
          if ($this->error == 'suppression') $msg = 'Erreur lors de la suppression de la commande.';
          echo 'data-error-msg="' . htmlspecialchars($msg, ENT_QUOTES, 'UTF-8') . '"';
      endif; ?>
>
    <div class="container mt-5 mb-5">
        <div class="row justify-content-center">
            <div class="col-md-9">
                <div class="card card-shadow">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <h2 class="font-bold"><a href="./" class="title-link">Liste des Commandes</a></h2>
                        <div>
                            <a href="nouvelle" class="btn btn-primary shadow-blue me-2">
                                <i class="bi bi-plus-lg"></i> Nouveau
                            </a>
                            <button class="btn btn-danger me-2 shadow-red" onclick="confirmerSuppression()">
                                <i class="bi bi-trash"></i> Corbeille
                            </button>
                        </div>
                    </div>
                    
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Société</th>
                                    <th>N° Commande Client</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (count($this->commandes) > 0): ?>
                                    <?php foreach ($this->commandes as $cmd): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($cmd['id']); ?></td>
                                            <td><?php echo htmlspecialchars($cmd['societe']); ?></td>
                                            <td><?php echo htmlspecialchars($cmd['n_commande_client']); ?></td>
                                            <td>
                                                <a href="recharger/<?php echo $cmd['id']; ?>" 
                                                   class="btn btn-sm btn-outline-warning me-2"
                                                   onclick="return confirm('Créer une nouvelle version de cette commande ?');">
                                                    <i class="bi bi-arrow-clockwise"></i> Rechargement
                                                </a>
                                                <a href="editer/<?php echo $cmd['id']; ?>" 
                                                   class="btn btn-sm btn-outline-primary me-2">
                                                    <i class="bi bi-pencil"></i> Édition
                                                </a>
                                                <a href="supprimer/<?php echo $cmd['id']; ?>" 
                                                   class="btn btn-sm btn-outline-danger"
                                                   onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette commande ? Cette action est irréversible.');">
                                                    <i class="bi bi-trash"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="4" class="text-center">Aucune commande trouvée</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <footer class="text-center text-muted py-3 mt-5">
        <small>Version 1.31</small>
    </footer>
    
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/alert.js"></script>
    <script>
        function confirmerSuppression() {
            if (confirm('Êtes-vous sûr de vouloir supprimer toutes les commandes de plus de 7 jours ? Cette action est irréversible.')) {
                window.location.href = 'corbeille';
            }
        }
        
        // Téléchargement automatique si un fichier est spécifié
        window.addEventListener('DOMContentLoaded', function() {
            const urlParams = new URLSearchParams(window.location.search);
            const downloadFile = urlParams.get('download');
            if (downloadFile) {
                // Créer un lien de téléchargement invisible
                const link = document.createElement('a');
                link.href = 'downloads/' + downloadFile;
                link.download = downloadFile;
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
            }
            
            // Nettoyer l'URL après affichage de l'alerte (supprimer success, error, count, download)
            if (urlParams.has('success') || urlParams.has('error') || urlParams.has('count') || urlParams.has('download')) {
                // Attendre que l'alerte soit affichée (1 seconde pour l'animation)
                setTimeout(function() {
                    // Nettoyer l'URL en gardant juste le chemin de base
                    window.history.replaceState({}, document.title, './');
                }, 1000);
            }
        });
    </script>
</body>
</html>
