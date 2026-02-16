# 📦 Installation Automatique - install.php

## 🎯 Utilisation rapide

### Étape 1 : Accédez à install.php
```
http://votre-site.com/gestion-commandes/install.php
```

### Étape 2 : Suivez l'assistant (3 écrans)

#### Écran 1 : Vérification
- ✅ Vérification automatique de PHP, PDO, fichiers
- Cliquez sur "Continuer"

#### Écran 2 : Configuration
Remplissez le formulaire :
- **Hôte** : `localhost` (en général)
- **Nom BDD** : `gestion_commandes` (ou votre choix)
- **Utilisateur** : `root` (ou votre user MySQL)
- **Mot de passe** : (votre password MySQL)
- ☑️ Cochez "Créer la base de données" si besoin

Cliquez sur "Installer"

#### Écran 3 : Terminé
- ✅ Installation réussie
- ⚠️ **IMPORTANT : Supprimez install.php maintenant !**
- Cliquez sur "Accéder à l'application"

---

## 🔒 Sécurité après installation

### OBLIGATOIRE : Supprimer install.php

**Méthode 1 : Via FTP/SFTP**
Supprimez simplement le fichier `install.php`

**Méthode 2 : Via SSH**
```bash
cd /votre/chemin/gestion-commandes
rm install.php
```

**Méthode 3 : Via cPanel**
Allez dans le gestionnaire de fichiers et supprimez `install.php`

### Protection automatique

L'installateur crée automatiquement :
- `.installation_complete` : verrouille l'installation
- `.htaccess_security` : bloque l'accès à install.php

Même si vous oubliez de supprimer install.php, il sera protégé !

---

## ✨ Fonctionnalités de install.php

### Vérifications automatiques
- [x] Version PHP 7.4+
- [x] Extension PDO activée
- [x] Extension PDO MySQL disponible
- [x] Fichier database.sql présent
- [x] Permissions d'écriture

### Installation automatique
- [x] Création de la base de données (optionnel)
- [x] Import automatique de database.sql
- [x] Création de la table `commandes`
- [x] Création du dossier `downloads/`
- [x] Configuration automatique de `classes/Database.php`
- [x] Test de connexion

### Sécurité
- [x] Protection contre la réinstallation
- [x] Création d'un fichier de verrouillage
- [x] Blocage automatique de install.php après installation
- [x] Validation des entrées utilisateur

---

## 🛠️ Options avancées

### Forcer la réinstallation
```
http://votre-site.com/gestion-commandes/install.php?force=1
```
⚠️ **ATTENTION** : Cela écrasera votre base de données !

### Désactiver la création de BDD
Décochez "Créer la base de données si elle n'existe pas"

### Utiliser un autre nom de BDD
Changez simplement le nom dans le formulaire

---

## 🐛 Problèmes courants

### "Extension PDO non trouvée"
**Solution** : Installez l'extension PHP PDO
```bash
# Ubuntu/Debian
sudo apt-get install php-mysql php-pdo
sudo systemctl restart apache2

# CentOS
sudo yum install php-mysql php-pdo
sudo systemctl restart httpd
```

### "Connexion refusée"
**Solutions** :
1. Vérifiez que MySQL est démarré
   ```bash
   sudo systemctl status mysql
   ```
2. Testez la connexion manuellement
   ```bash
   mysql -u root -p
   ```
3. Vérifiez l'hôte (localhost vs 127.0.0.1)

### "Permission denied"
**Solution** : Vérifiez les permissions
```bash
chmod 755 gestion-commandes/
chmod 755 gestion-commandes/downloads/
```

### "database.sql non trouvé"
**Solution** : Vérifiez que `database.sql` est dans le même dossier que `install.php`

### "Installation déjà effectuée"
**Solutions** :
1. C'est normal si vous avez déjà installé
2. Pour réinstaller : supprimez `.installation_complete` puis ajoutez `?force=1` à l'URL
3. ⚠️ Cela supprimera vos données existantes !

---

## 📊 Comparaison avec l'installation manuelle

| Critère | install.php | Manuel |
|---------|-------------|--------|
| **Temps** | 2 minutes | 10-15 minutes |
| **Difficulté** | ⭐ Facile | ⭐⭐⭐ Moyen |
| **Vérifications** | ✅ Automatiques | ❌ Manuelles |
| **Config Database.php** | ✅ Auto | ❌ Manuel |
| **Erreurs** | ✅ Messages clairs | ❌ Silencieuses |
| **Sécurité** | ✅ Protection auto | ⚠️ À faire |

---

## 🔄 Que fait install.php exactement ?

### Backend (PHP)
```php
1. Connexion à MySQL
2. CREATE DATABASE (si demandé)
3. Lecture de database.sql
4. Nettoyage des commentaires SQL
5. Exécution des requêtes
6. Création du dossier downloads/
7. Mise à jour de Database.php
8. Test de connexion
9. Création du verrouillage
10. Création de la protection
```

### Frontend (Interface)
- Interface Bootstrap responsive
- Indicateur de progression (3 étapes)
- Messages d'erreur détaillés
- Codes couleur (succès, warning, erreur)
- Design moderne et intuitif

---

## 📝 Fichiers créés par install.php

Après installation, ces fichiers sont créés :
```
.installation_complete      # Verrouillage (date/heure)
.htaccess_security         # Protection de install.php
downloads/                 # Dossier pour les CSV
classes/Database.php       # Mis à jour avec vos paramètres
```

Base de données :
```sql
gestion_commandes          # Votre base de données
  └── commandes            # Table principale
```

---

## ✅ Checklist post-installation

Après avoir utilisé install.php :

- [ ] **Page d'accueil accessible** (`index.php` fonctionne)
- [ ] **install.php supprimé** (sécurité)
- [ ] **Création de commande testée** (bouton "Nouveau")
- [ ] **Export CSV fonctionne** (téléchargement OK)
- [ ] **.htaccess configuré** (RewriteBase correct)
- [ ] **Permissions downloads/ OK** (755 ou 777)

---

## 💡 Conseils

### Pour un environnement de production
1. Utilisez un utilisateur MySQL dédié (pas root)
2. Utilisez un mot de passe fort
3. Supprimez install.php immédiatement
4. Désactivez l'affichage des erreurs PHP
5. Utilisez HTTPS

### Pour un environnement de développement
1. Localhost + root est OK
2. Gardez les erreurs PHP activées
3. Testez sur données de test

### Après installation
1. Créez une commande de test
2. Vérifiez l'export CSV
3. Testez le rechargement de version
4. Testez la suppression
5. Vérifiez la corbeille (>7 jours)

---

## 📞 Besoin d'aide ?

### Documentation
- `GUIDE_INSTALLATION.md` : Guide complet
- `INSTALLATION_RAPIDE.md` : Guide rapide
- `README.txt` : Documentation générale

### Support
Si l'installation échoue :
1. Vérifiez les logs PHP (`/var/log/apache2/error.log`)
2. Activez les erreurs MySQL
3. Testez la connexion MySQL manuellement
4. Consultez le guide de dépannage

---

**Version 1.31** - Installation en 3 clics
Développé avec ❤️ pour simplifier votre vie
