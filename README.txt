# Système de Gestion des Commandes

## Description
Application web PHP orientée objet pour gérer des commandes avec export CSV automatique.

## Fonctionnalités
- ✅ Liste de toutes les commandes (ID, Société, N° Commande)
- ✅ Création de nouvelles commandes via formulaire
- ✅ Génération automatique de fichiers CSV (UTF-8, séparateur point-virgule)
- ✅ Système de versioning des commandes (V2, V3, etc.)
- ✅ Interface Bootstrap responsive
- ✅ Architecture orientée objet

## Installation

### 1. Prérequis
- PHP 7.4 ou supérieur
- MySQL 5.7 ou supérieur
- Serveur web (Apache/Nginx) ou XAMPP/WAMP

### 2. Base de données
```sql
-- Importer le fichier database.sql dans phpMyAdmin
-- OU via ligne de commande :
mysql -u root -p < database.sql
```

### 3. Configuration
Modifier `classes/Database.php` si nécessaire :
```php
private $host = 'localhost';
private $db_name = 'gestion_commandes';
private $username = 'root';
private $password = '';
```

### 4. Structure des fichiers
```
/votre-projet/
├── classes/
│   ├── Database.php       (Connexion BDD)
│   ├── Commande.php       (Gestion commandes)
│   └── CSVExporter.php    (Export CSV)
├── downloads/             (Fichiers CSV générés)
├── index.php              (Page liste)
├── nouvelle-commande.php  (Page formulaire)
├── database.sql           (Structure BDD)
├── config.php             (Configuration)
└── README.txt             (Ce fichier)
```

### 5. Permissions
```bash
chmod 777 downloads/
```

## Utilisation

### Page 1 - Liste des commandes (index.php)
- Affiche toutes les commandes
- Bouton "Nouveau" → redirection vers formulaire
- Bouton "Rechargement" → créer nouvelle version CSV + mise à jour BDD

### Page 2 - Nouvelle commande (nouvelle-commande.php)
- Formulaire complet avec tous les champs
- Champ "Dossier Suivi Par" pré-rempli avec "Matthieu"
- Boutons radio : Fichier Créé / Fichier Fourni
- Bouton "Sauvegarder" → sauvegarde BDD + génération CSV + retour page 1

## Format CSV
- Encodage : UTF-8 avec BOM
- Séparateur : point-virgule (;)
- Nom du fichier : N° de commande (ex: CO2601-4804.csv)
- Contenu :
  - Ligne 1 : En-têtes
  - Ligne 2 : Données de la commande

## Système de versioning
- Première commande : CO2601-4804
- Rechargement 1 : CO2601-4804-V2
- Rechargement 2 : CO2601-4804-V3
- etc.

## Technologies utilisées
- PHP 7+ (POO)
- MySQL
- Bootstrap 5.3
- PDO pour la sécurité SQL

## Support
Pour toute question ou problème, vérifier :
1. Connexion à la base de données
2. Permissions sur le dossier downloads/
3. Version PHP compatible
4. Logs d'erreurs PHP

## Auteur
Développé pour la gestion des commandes client
