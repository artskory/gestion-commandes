# 🚀 Installation Rapide - Gestion des Commandes v1.31

## ⚡ Installation automatique (RECOMMANDÉE)

### 🎯 Méthode 1 : Avec install.php (3 clics) ⭐

1. **Extraire les fichiers**
   ```bash
   unzip gestion-commandes-v1.31.zip
   cd gestion-commandes
   ```

2. **Accéder à install.php**
   ```
   http://votre-site.com/gestion-commandes/install.php
   ```

3. **Suivre l'assistant d'installation**
   - Étape 1 : Vérification automatique des prérequis
   - Étape 2 : Saisir les identifiants MySQL
   - Étape 3 : Installation terminée !

4. **Supprimer install.php** (IMPORTANT pour la sécurité)
   ```bash
   rm install.php
   ```

**C'est tout !** 🎉

---

## 📖 Installation manuelle (alternative)

### 1️⃣ Prérequis
- Serveur web Apache avec PHP 7.4+ et MySQL 5.7+
- Extension PHP PDO activée
- mod_rewrite activé pour Apache

### 2️⃣ Extraire les fichiers
```bash
# Extraire le contenu dans votre dossier web
unzip gestion-commandes-v1.31.zip
cd gestion-commandes
```

### 3️⃣ Configuration de la base de données

**Option A - Import automatique :**
```bash
mysql -u votre_utilisateur -p nom_base_de_donnees < database.sql
```

**Option B - Via phpMyAdmin :**
1. Créez une nouvelle base de données
2. Importez le fichier `database.sql`

### 4️⃣ Configurer l'accès BDD
Éditez le fichier `config.php` :
```php
define('DB_HOST', 'localhost');        // Votre hôte
define('DB_NAME', 'nom_de_votre_bdd'); // Nom de votre base
define('DB_USER', 'votre_utilisateur'); // Votre utilisateur MySQL
define('DB_PASS', 'votre_mot_de_passe'); // Votre mot de passe
```

### 5️⃣ Configurer l'URL de base
Éditez le fichier `.htaccess` ligne 8 :
```apache
RewriteBase /gestion-commandes/
```
Remplacez `/gestion-commandes/` par le chemin de votre installation :
- Racine du site → `/`
- Sous-dossier → `/mon-dossier/`

## ✅ Vérification

Accédez à : `http://votre-site.com/gestion-commandes/`

Vous devriez voir la liste des commandes (vide au début).

---

## 💡 Quelle méthode choisir ?

### Utilisez install.php (automatique) si :
- ✅ Vous voulez gagner du temps
- ✅ Vous n'êtes pas à l'aise avec MySQL
- ✅ Vous voulez une vérification automatique
- ✅ C'est votre première installation

### Utilisez la méthode manuelle si :
- ✅ Vous préférez le contrôle total
- ✅ Vous avez déjà la base de données
- ✅ Vous utilisez un hébergeur spécifique
- ✅ Vous êtes un utilisateur avancé

---

## 📁 Structure des dossiers

```
gestion-commandes/
├── classes/          # Classes PHP (Database, Commande, CSVExporter)
├── controllers/      # Contrôleurs MVC
├── css/              # Fichiers CSS
├── downloads/        # Fichiers CSV générés (créé automatiquement)
├── image/            # Images et favicon
├── js/               # Fichiers JavaScript
├── views/            # Vues PHP
├── .htaccess         # Configuration Apache + URL rewriting
├── config.php        # Configuration BDD
├── database.sql      # Structure de la base de données
└── index.php         # Point d'entrée
```

## 🔧 Permissions des dossiers

```bash
# Le dossier downloads doit être accessible en écriture
chmod 755 downloads/
```

## 🆕 Nouveautés version 1.31

✅ **Suppression du bouton "Nettoyer CSV"**
- Les fichiers CSV sont maintenant automatiquement supprimés lors de la suppression d'une commande
- Plus besoin de nettoyer manuellement le dossier downloads

✅ **Suppression automatique des CSV**
- Suppression individuelle : le CSV est supprimé avec la commande
- Corbeille (>7 jours) : tous les CSV des commandes supprimées sont effacés

## 🎯 Utilisation rapide

### Créer une commande
1. Cliquez sur "Nouveau"
2. Remplissez le formulaire
3. Le CSV est généré automatiquement au téléchargement

### Recharger une commande (nouvelle version)
1. Cliquez sur "Rechargement" sur la commande
2. Une nouvelle version est créée (ex: CO2601-4804-V2)
3. Le nouveau CSV est téléchargé automatiquement

### Supprimer une commande
- **Suppression individuelle** : icône poubelle sur chaque ligne → supprime la commande ET son CSV
- **Corbeille** : supprime toutes les commandes de +7 jours ET leurs CSV

## ❓ Problèmes courants

### Les URLs ne fonctionnent pas (404)
→ Vérifiez que mod_rewrite est activé :
```bash
a2enmod rewrite
service apache2 restart
```

### Erreur de connexion BDD
→ Vérifiez `config.php` et que la base existe

### Les CSV ne se téléchargent pas
→ Vérifiez les permissions du dossier `downloads/`

## 📞 Support

Pour toute question, consultez le fichier `README.txt` complet.

---

**Version 1.31** - Gestion automatique des fichiers CSV
