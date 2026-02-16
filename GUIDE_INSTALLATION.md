# 🚀 Guide d'installation avec install.php

## Installation automatique en 3 étapes

### ✨ Méthode recommandée : Installation automatique

1. **Accédez à install.php**
   ```
   http://votre-site.com/gestion-commandes/install.php
   ```

2. **Suivez les 3 étapes** :
   - **Étape 1** : Vérification automatique des prérequis
   - **Étape 2** : Configuration de la base de données
   - **Étape 3** : Installation terminée

3. **Supprimez install.php** après installation (pour la sécurité)

---

## 📋 Détails de chaque étape

### Étape 1 : Vérification des prérequis

Le système vérifie automatiquement :
- ✅ Version PHP (7.4+ requis)
- ✅ Extension PDO MySQL
- ✅ Présence du fichier database.sql
- ✅ Permissions d'écriture

### Étape 2 : Configuration de la base de données

Remplissez le formulaire avec vos informations :

| Champ | Description | Exemple |
|-------|-------------|---------|
| **Hôte** | Serveur de base de données | `localhost` |
| **Nom BDD** | Nom de la base | `gestion_commandes` |
| **Utilisateur** | User MySQL | `root` |
| **Mot de passe** | Password MySQL | (peut être vide en local) |

**Options :**
- ☑️ Cochez "Créer la base de données" si elle n'existe pas encore

**Actions automatiques :**
1. Création de la base de données (si demandé)
2. Création de la table `commandes`
3. Création du dossier `downloads/`
4. Mise à jour automatique du fichier `classes/Database.php`
5. Test de connexion

### Étape 3 : Installation terminée

L'installateur affiche :
- ✅ Confirmation de chaque étape
- ⚠️ Rappel de sécurité : supprimer install.php
- 🔗 Lien pour accéder à l'application

---

## 🔒 Sécurité

### Après installation, IMPÉRATIF :

```bash
# Supprimez le fichier d'installation
rm install.php

# Ou via FTP : supprimez manuellement install.php
```

**Pourquoi ?**
- Le fichier install.php peut recréer/écraser votre base de données
- C'est un risque de sécurité si accessible publiquement

### Fichier de verrouillage

L'installation crée automatiquement `.installation_complete` pour éviter une réinstallation accidentelle.

Pour réinstaller (⚠️ DANGEREUX) :
```bash
rm .installation_complete
# Puis accédez à install.php?force=1
```

---

## 🎯 Ce que fait install.php automatiquement

### 1. Vérifications
- Version PHP compatible
- Extensions PDO disponibles
- Fichiers nécessaires présents

### 2. Installation BDD
```sql
CREATE DATABASE gestion_commandes;
CREATE TABLE commandes (...);
```

### 3. Configuration automatique
Le fichier `classes/Database.php` est automatiquement mis à jour avec vos paramètres :
```php
private $host = 'votre_host';
private $db_name = 'votre_bdd';
private $username = 'votre_user';
private $password = 'votre_password';
```

### 4. Création des dossiers
```bash
downloads/  # Créé avec permissions 755
```

### 5. Tests
- Test de connexion à MySQL
- Test de création de table
- Test d'accès en écriture

---

## 🆚 Comparaison des méthodes

### Méthode automatique (install.php) ⭐ Recommandée
✅ Installation en 3 clics  
✅ Vérification automatique des prérequis  
✅ Configuration automatique de Database.php  
✅ Messages d'erreur clairs  
✅ Test de connexion intégré  
✅ Interface graphique intuitive  

### Méthode manuelle (database.sql)
✅ Contrôle total  
❌ Nécessite phpMyAdmin ou ligne de commande  
❌ Configuration manuelle de Database.php  
❌ Pas de vérification des prérequis  
❌ Peut être complexe pour débutants  

---

## ⚙️ Configuration manuelle (alternative)

Si vous préférez ne pas utiliser install.php :

### 1. Créer la base de données
```bash
mysql -u root -p
CREATE DATABASE gestion_commandes CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
exit;
```

### 2. Importer database.sql
```bash
mysql -u root -p gestion_commandes < database.sql
```

### 3. Modifier classes/Database.php
```php
private $host = 'localhost';
private $db_name = 'gestion_commandes';
private $username = 'root';
private $password = '';
```

### 4. Créer le dossier downloads
```bash
mkdir downloads
chmod 755 downloads
```

---

## 🐛 Dépannage

### "Connexion à la base de données impossible"
→ Vérifiez les identifiants MySQL
→ Vérifiez que MySQL est démarré
→ Testez la connexion : `mysql -u root -p`

### "Extension PDO non trouvée"
```bash
# Ubuntu/Debian
sudo apt-get install php-mysql php-pdo

# CentOS/RHEL
sudo yum install php-mysql php-pdo
```

### "Permission denied sur downloads/"
```bash
chmod 755 downloads/
# Ou si nécessaire :
chmod 777 downloads/
```

### "fichier database.sql non trouvé"
→ Vérifiez que database.sql est dans le même dossier que install.php

---

## 📞 Aide rapide

**Installation bloquée ?**
1. Vérifiez les logs d'erreur PHP
2. Activez l'affichage des erreurs (déjà fait dans install.php)
3. Testez la connexion MySQL manuellement

**Besoin de réinstaller ?**
```bash
rm .installation_complete
# Accédez à install.php?force=1
```

---

## ✅ Checklist après installation

- [ ] install.php supprimé
- [ ] Connexion à l'application réussie
- [ ] Dossier downloads/ accessible en écriture
- [ ] .htaccess configuré (RewriteBase)
- [ ] Création d'une commande de test réussie
- [ ] Export CSV fonctionne

---

**Version 1.31** - Installation automatique sécurisée
