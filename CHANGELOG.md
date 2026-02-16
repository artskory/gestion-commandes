# Historique des Versions

## Version 1.31 - Gestion automatique des CSV

### 🎯 Modifications principales

#### Suppression du bouton "Nettoyer CSV"
- ❌ Bouton "Nettoyer CSV" retiré de l'interface
- ✅ Simplification de l'interface utilisateur
- ✅ Moins de confusion pour les utilisateurs

#### Suppression automatique des fichiers CSV
- ✅ Les fichiers CSV sont maintenant **automatiquement supprimés** lors de la suppression d'une commande
- ✅ Suppression individuelle : le fichier CSV associé est supprimé en même temps que la commande
- ✅ Suppression par corbeille (+7 jours) : tous les fichiers CSV des commandes supprimées sont effacés

### 📝 Détails techniques

**Fichiers modifiés :**

1. **views/index.view.php**
   - Suppression du bouton "Nettoyer CSV"
   - Suppression de la fonction JavaScript `confirmerNettoyage()`
   - Suppression du message de succès "nettoyage"
   - Mise à jour version → 1.31

2. **classes/Commande.php**
   - Méthode `delete($id)` : ajout de la suppression du fichier CSV associé
   - Méthode `deleteOldCommandes()` : ajout de la suppression des fichiers CSV de toutes les commandes supprimées

3. **controllers/IndexController.php**
   - Suppression de la gestion de la route `nettoyer_downloads`
   - Suppression de la méthode `nettoyerDownloads()`

4. **.htaccess**
   - Suppression de la route de réécriture `/nettoyer`

**Nouveaux fichiers :**

- `INSTALLATION_RAPIDE.md` : Guide d'installation en 5 étapes
- `CHANGELOG.md` : Ce fichier d'historique

### 🔄 Comportement

**Avant (v1.30) :**
- Les fichiers CSV s'accumulaient dans le dossier `downloads/`
- Il fallait cliquer manuellement sur "Nettoyer CSV" pour les supprimer
- Risque d'accumulation de fichiers obsolètes

**Maintenant (v1.31) :**
- Les fichiers CSV sont automatiquement supprimés avec les commandes
- Plus de gestion manuelle nécessaire
- Le dossier `downloads/` reste propre automatiquement

### ⚠️ Notes de migration

Si vous mettez à jour depuis la v1.30 :
1. Les fichiers CSV existants dans `downloads/` ne seront pas automatiquement supprimés
2. Vous pouvez les supprimer manuellement si nécessaire
3. À partir de maintenant, tout sera géré automatiquement

---

## Version 1.30 - Version précédente

### Fonctionnalités
- Bouton "Nettoyer CSV" pour vider manuellement le dossier downloads
- Gestion des commandes avec versions
- Export CSV automatique
- Suppression individuelle et par corbeille (+7 jours)
