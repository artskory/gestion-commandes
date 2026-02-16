# Gestion des Commandes v1.31 - Modifications

## ✅ Modifications effectuées

### 1. Suppression du bouton "Nettoyer CSV"
- Le bouton jaune "Nettoyer CSV" a été complètement retiré de l'interface
- L'action manuelle de nettoyage n'est plus nécessaire

### 2. Suppression automatique des fichiers CSV

Les fichiers CSV sont maintenant **automatiquement supprimés** dans les cas suivants :

#### 🗑️ Suppression individuelle
Lorsque vous cliquez sur l'icône poubelle d'une commande :
- La commande est supprimée de la base de données
- Le fichier CSV associé (ex: `CO2601-4804.csv`) est automatiquement supprimé du dossier `downloads/`

#### 🗑️ Suppression par corbeille (>7 jours)
Lorsque vous cliquez sur le bouton "Corbeille" :
- Toutes les commandes de plus de 7 jours sont supprimées
- Tous les fichiers CSV de ces commandes sont automatiquement supprimés

### 3. Nouveaux fichiers créés
- `INSTALLATION_RAPIDE.md` : Guide d'installation en 5 étapes
- `CHANGELOG.md` : Historique détaillé des versions

## 📂 Fichiers modifiés

### Interface utilisateur
- **views/index.view.php**
  - Ligne 43-45 : Bouton "Nettoyer CSV" supprimé
  - Ligne 114-118 : Fonction JavaScript `confirmerNettoyage()` supprimée
  - Ligne 24 : Message de succès "nettoyage" retiré
  - Ligne 101 : Version mise à jour → 1.31

### Logique métier
- **classes/Commande.php**
  - Méthode `delete()` : supprime le fichier CSV lors de la suppression d'une commande
  - Méthode `deleteOldCommandes()` : supprime tous les CSV des commandes anciennes

### Contrôleur
- **controllers/IndexController.php**
  - Route et méthode `nettoyerDownloads()` supprimées
  - Gestion de l'URL `/nettoyer` retirée

### Configuration
- **.htaccess**
  - Ligne 30-31 : Route de réécriture `/nettoyer` supprimée

## 🚀 Installation

Consultez le fichier **INSTALLATION_RAPIDE.md** pour une installation en 5 étapes.

## 📊 Impact

**Avant (v1.30) :**
```
Créer commande → CSV créé dans downloads/
Supprimer commande → Commande supprimée, CSV reste
(accumulation de fichiers CSV obsolètes)
→ Nécessite de cliquer sur "Nettoyer CSV" manuellement
```

**Maintenant (v1.31) :**
```
Créer commande → CSV créé dans downloads/
Supprimer commande → Commande ET CSV supprimés automatiquement
→ Aucune action manuelle nécessaire, le dossier reste propre
```

## 🔄 Migration depuis v1.30

1. Remplacez tous les fichiers de votre installation
2. Les fichiers CSV existants dans `downloads/` ne seront pas automatiquement supprimés
3. Vous pouvez les supprimer manuellement si souhaité
4. Les prochaines suppressions géreront automatiquement les CSV

## ⚙️ Fonctionnement technique

### Suppression individuelle
```php
// Dans Commande.php - méthode delete()
public function delete($id) {
    // 1. Récupérer les infos de la commande
    $commande = $this->getById($id);
    
    // 2. Supprimer de la BDD
    $query = "DELETE FROM commandes WHERE id = :id";
    $stmt->execute();
    
    // 3. Supprimer le CSV associé
    if ($commande) {
        $csvFile = 'downloads/' . $commande['n_commande_client'] . '.csv';
        if (file_exists($csvFile)) {
            unlink($csvFile);
        }
    }
}
```

### Suppression par corbeille
```php
// Dans Commande.php - méthode deleteOldCommandes()
public function deleteOldCommandes() {
    // 1. Récupérer les numéros de commande à supprimer
    $commandes = SELECT n_commande_client WHERE created_at < NOW() - 7 days
    
    // 2. Supprimer de la BDD
    DELETE WHERE created_at < NOW() - 7 days
    
    // 3. Supprimer tous les CSV
    foreach ($commandes as $cmd) {
        unlink('downloads/' . $cmd['n_commande_client'] . '.csv');
    }
}
```

## 🎯 Avantages

✅ Interface plus simple et claire
✅ Gestion automatique, pas d'intervention manuelle
✅ Dossier `downloads/` toujours propre
✅ Moins de risque d'erreur utilisateur
✅ Cohérence entre base de données et fichiers

## 📞 Support

Pour toute question, consultez :
- `INSTALLATION_RAPIDE.md` pour l'installation
- `CHANGELOG.md` pour l'historique complet
- `README.txt` pour la documentation complète

---

**Version 1.31** - Février 2026
