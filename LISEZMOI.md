# 🎉 Gestion des Commandes v1.31 - Package Complet

## 📦 Contenu du package

Ce package contient tout ce dont vous avez besoin pour installer et utiliser l'application de gestion des commandes.

---

## 🚀 Démarrage rapide (2 minutes)

### Option 1 : Installation automatique (RECOMMANDÉE) ⭐

1. **Extraire l'archive**
   ```bash
   unzip gestion-commandes-v1.31.zip
   ```

2. **Placer sur votre serveur web**
   ```
   /var/www/html/gestion-commandes/
   ou
   C:\xampp\htdocs\gestion-commandes\
   ```

3. **Accéder à install.php**
   ```
   http://localhost/gestion-commandes/install.php
   ```

4. **Suivre l'assistant (3 écrans)**
   - Vérification des prérequis
   - Configuration MySQL
   - Installation terminée

5. **Supprimer install.php** (important !)

✅ **C'est terminé !** Accédez à votre application.

---

## 📁 Structure des fichiers

```
gestion-commandes-v1.31/
│
├── 📄 install.php                      ⭐ NOUVEAU - Installation automatique
│
├── 📂 Documentation/
│   ├── README_INSTALL.md              ⭐ Guide complet install.php
│   ├── GUIDE_INSTALLATION.md          📖 Guide détaillé installation
│   ├── INSTALLATION_RAPIDE.md         ⚡ Guide rapide 5 étapes
│   ├── README_MODIFICATIONS.md        📝 Changements v1.31
│   ├── CHANGELOG.md                   📜 Historique versions
│   ├── README.txt                     📚 Documentation complète
│   └── INSTALLATION.txt               🔧 Instructions originales
│
├── 📂 Application/
│   ├── index.php                      🏠 Page d'accueil
│   ├── nouvelle-commande.php          ➕ Nouvelle commande
│   ├── editer-commande.php            ✏️ Éditer commande
│   ├── config.php                     ⚙️ Configuration
│   └── .htaccess                      🔀 URL rewriting
│
├── 📂 classes/
│   ├── Database.php                   💾 Connexion BDD
│   ├── Commande.php                   📋 Gestion commandes
│   └── CSVExporter.php                📊 Export CSV
│
├── 📂 controllers/
│   ├── IndexController.php            🎛️ Contrôleur principal
│   └── CommandeController.php         🎛️ Contrôleur commandes
│
├── 📂 views/
│   ├── index.view.php                 👁️ Vue liste
│   ├── nouvelle-commande.view.php     👁️ Vue création
│   └── editer-commande.view.php       👁️ Vue édition
│
├── 📂 css/
│   └── style.css                      🎨 Styles
│
├── 📂 js/
│   └── alert.js                       ⚡ Scripts
│
├── 📂 image/
│   └── [favicons]                     🖼️ Images
│
├── 📂 downloads/                       📥 CSV générés (auto-créé)
│
└── 📄 database.sql                     🗄️ Structure BDD
```

---

## 🆕 Nouveautés v1.31

### ✨ Installation automatique
- **install.php** : Installation en 3 clics
- Interface graphique intuitive
- Vérification automatique des prérequis
- Configuration automatique de Database.php
- Protection de sécurité automatique

### 🗑️ Suppression du bouton "Nettoyer CSV"
- Bouton manuel supprimé
- Suppression automatique des CSV avec les commandes
- Dossier downloads/ toujours propre

### 📚 Documentation enrichie
- 7 fichiers de documentation
- Guides d'installation multiples
- Dépannage détaillé
- Exemples et captures

---

## 📖 Quelle documentation lire ?

### Vous débutez ? 👶
→ **README_INSTALL.md** : Guide install.php pas à pas

### Vous voulez installer vite ? ⚡
→ **INSTALLATION_RAPIDE.md** : 5 minutes chrono

### Vous voulez tout comprendre ? 🎓
→ **GUIDE_INSTALLATION.md** : Guide complet

### Vous migrez depuis v1.30 ? 🔄
→ **README_MODIFICATIONS.md** : Liste des changements

### Vous voulez l'historique ? 📜
→ **CHANGELOG.md** : Toutes les versions

---

## 🎯 Trois méthodes d'installation

### 1. Avec install.php (automatique) ⭐ RECOMMANDÉE
- ✅ Installation en 3 clics
- ✅ 2 minutes chrono
- ✅ Vérifications automatiques
- ✅ Idéal pour débutants

### 2. Manuelle avec phpMyAdmin 🔧
- ✅ Contrôle total
- ✅ Importer database.sql
- ✅ Éditer Database.php
- ✅ Pour utilisateurs avancés

### 3. Manuelle en ligne de commande 💻
- ✅ Rapide pour experts
- ✅ Via terminal/SSH
- ✅ Scripts automatisables
- ✅ Pour serveurs sans GUI

---

## 🔧 Configuration requise

### Prérequis système
- **PHP** : 7.4 ou supérieur
- **MySQL** : 5.7 ou supérieur (ou MariaDB 10.2+)
- **Extensions** : PDO, PDO_MySQL
- **Apache** : mod_rewrite activé (pour URL rewriting)

### Serveurs testés
- ✅ XAMPP 7.4+
- ✅ WAMP 3.2+
- ✅ MAMP
- ✅ Ubuntu/Apache
- ✅ CentOS/Apache
- ✅ Hébergement mutualisé

---

## 🚦 Démarrage étape par étape

### 1️⃣ Préparation
```bash
# Extraire l'archive
unzip gestion-commandes-v1.31.zip

# Placer sur le serveur web
mv gestion-commandes /var/www/html/
```

### 2️⃣ Installation
```
Ouvrir : http://localhost/gestion-commandes/install.php
Suivre l'assistant
```

### 3️⃣ Sécurité
```bash
# Supprimer install.php
rm /var/www/html/gestion-commandes/install.php
```

### 4️⃣ Configuration
```bash
# Vérifier .htaccess (ligne 8)
RewriteBase /gestion-commandes/

# Permissions downloads
chmod 755 downloads/
```

### 5️⃣ Test
```
Ouvrir : http://localhost/gestion-commandes/
Cliquer "Nouveau"
Créer une commande de test
Vérifier l'export CSV
```

---

## 🎨 Fonctionnalités

### Gestion des commandes
- ✅ Création de commandes
- ✅ Édition de commandes
- ✅ Suppression individuelle
- ✅ Suppression par lot (>7 jours)
- ✅ Rechargement (versions : V2, V3, etc.)

### Export CSV
- ✅ Génération automatique
- ✅ Téléchargement direct
- ✅ Format compatible Excel
- ✅ Encodage UTF-8 avec BOM

### Automatisation v1.31
- ✅ Suppression auto des CSV avec les commandes
- ✅ Dossier downloads/ auto-nettoyé
- ✅ Aucune action manuelle requise

---

## 🔒 Sécurité

### Après installation
- [ ] Supprimer install.php
- [ ] Vérifier .installation_complete créé
- [ ] Modifier les identifiants MySQL par défaut
- [ ] Désactiver display_errors en production
- [ ] Utiliser HTTPS

### Protection automatique
- ✅ Verrouillage de l'installation
- ✅ Fichier .htaccess_security créé
- ✅ Protection contre les injections SQL (PDO)
- ✅ Validation des entrées utilisateur

---

## 📊 Utilisation rapide

### Créer une commande
```
1. Cliquer "Nouveau"
2. Remplir le formulaire
3. Cliquer "Créer la commande"
→ CSV téléchargé automatiquement
```

### Créer une nouvelle version
```
1. Cliquer "Rechargement" sur une commande
2. Confirmer
→ Nouvelle version créée (ex: CO2601-4804-V2)
→ Nouveau CSV téléchargé
```

### Supprimer une commande
```
Option 1 : Icône poubelle sur la ligne
         → Supprime commande + CSV

Option 2 : Bouton "Corbeille"
         → Supprime toutes les commandes >7 jours + CSV
```

---

## 🐛 Dépannage rapide

### Installation échoue
→ Vérifier les logs : `/var/log/apache2/error.log`
→ Vérifier MySQL démarré : `systemctl status mysql`
→ Tester connexion : `mysql -u root -p`

### Page blanche
→ Activer display_errors dans config.php
→ Vérifier permissions : `chmod 755 gestion-commandes/`
→ Vérifier .htaccess RewriteBase

### CSV ne se télécharge pas
→ Vérifier permissions : `chmod 755 downloads/`
→ Vérifier que le dossier existe
→ Vérifier les headers PHP (pas de echo avant)

### URLs ne fonctionnent pas (404)
→ Activer mod_rewrite : `a2enmod rewrite`
→ Vérifier .htaccess RewriteBase
→ Redémarrer Apache : `systemctl restart apache2`

---

## 📞 Support & Aide

### Documentation incluse
- 📄 **README_INSTALL.md** - Guide install.php
- 📄 **GUIDE_INSTALLATION.md** - Installation complète
- 📄 **INSTALLATION_RAPIDE.md** - Installation 5 min
- 📄 **README_MODIFICATIONS.md** - Nouveautés v1.31
- 📄 **CHANGELOG.md** - Historique

### Fichiers de support
- 📄 **README.txt** - Documentation générale
- 📄 **INSTALLATION.txt** - Instructions détaillées
- 📄 **URL_REWRITING.txt** - Configuration Apache

---

## ✅ Checklist finale

Après installation, vérifiez :

- [ ] ✅ Application accessible (index.php)
- [ ] ✅ install.php supprimé
- [ ] ✅ Base de données créée
- [ ] ✅ Table commandes existe
- [ ] ✅ Dossier downloads/ créé
- [ ] ✅ Permissions correctes (755)
- [ ] ✅ .htaccess configuré
- [ ] ✅ Création de commande fonctionne
- [ ] ✅ Export CSV fonctionne
- [ ] ✅ Rechargement fonctionne
- [ ] ✅ Suppression fonctionne

---

## 🎓 Prochaines étapes

### Utilisation basique
1. Créer votre première commande
2. Tester l'export CSV
3. Tester le rechargement
4. Tester la suppression

### Configuration avancée
1. Modifier le délai de corbeille (actuellement 7 jours)
2. Personnaliser les styles CSS
3. Ajouter des champs personnalisés
4. Configurer les sauvegardes automatiques

### Production
1. Configurer SSL/HTTPS
2. Optimiser les performances
3. Mettre en place les sauvegardes
4. Surveiller les logs

---

## 📦 Fichiers à ne jamais supprimer

### Critiques
- `classes/` - Logique métier
- `controllers/` - Contrôleurs
- `views/` - Interfaces
- `database.sql` - Structure BDD (garder pour backup)
- `.htaccess` - URL rewriting

### À supprimer après installation
- `install.php` ⚠️ IMPORTANT

### Optionnels (documentation)
- Tous les `.md` et `.txt`
- Peuvent être supprimés après lecture

---

**Version 1.31** - Package complet avec installation automatique

🎉 **Merci d'utiliser Gestion des Commandes !**

Pour toute question, consultez la documentation incluse.
