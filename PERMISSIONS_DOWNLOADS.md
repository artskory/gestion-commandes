# 🚨 Résolution des problèmes de permissions

## ❌ Erreur : "Permission denied" sur le dossier downloads/

### Symptômes
```
Warning: mkdir(): Permission denied in .../CSVExporter.php
```
Ou lors de la création d'une commande, le CSV ne se télécharge pas.

---

## ✅ SOLUTIONS

### Solution 1 : Via Terminal (Recommandée)

#### Sur Mac (XAMPP)
```bash
cd /Applications/XAMPP/xamppfiles/htdocs/gestion-commandes
mkdir downloads
chmod 777 downloads
```

#### Sur Windows (XAMPP)
```cmd
cd C:\xampp\htdocs\gestion-commandes
mkdir downloads
# Puis clic droit → Propriétés → Sécurité → Modifier → Autoriser "Contrôle total"
```

#### Sur Linux
```bash
cd /var/www/html/gestion-commandes
sudo mkdir downloads
sudo chmod 777 downloads
sudo chown www-data:www-data downloads
```

---

### Solution 2 : Via Explorateur de fichiers

#### Sur Mac
1. Ouvre Finder
2. Va dans `/Applications/XAMPP/xamppfiles/htdocs/gestion-commandes/`
3. Crée un dossier `downloads`
4. Clic droit sur le dossier → "Lire les informations"
5. En bas, déverrouille le cadenas 🔒
6. Change les permissions pour "Tout le monde" → "Lecture et écriture"
7. Clique sur l'engrenage ⚙️ → "Appliquer aux éléments inclus"

#### Sur Windows
1. Ouvre l'Explorateur Windows
2. Va dans `C:\xampp\htdocs\gestion-commandes\`
3. Crée un dossier `downloads`
4. Clic droit sur le dossier → Propriétés
5. Onglet "Sécurité"
6. Cliquez sur "Modifier"
7. Sélectionnez "Utilisateurs"
8. Cochez "Contrôle total"
9. OK → Appliquer

---

### Solution 3 : Via install.php

Si tu n'as pas encore installé :

1. Lance `install.php`
2. Il créera automatiquement le dossier avec les bonnes permissions
3. Si ça échoue, un message d'avertissement s'affichera

---

### Solution 4 : Via FTP (Hébergement distant)

1. Connecte-toi en FTP à ton hébergement
2. Va dans le dossier `gestion-commandes/`
3. Crée un dossier `downloads`
4. Clic droit → Permissions (CHMOD)
5. Mets `777` (ou `755` si 777 n'est pas autorisé)

---

## 🔍 Vérifier que ça fonctionne

### Test rapide
```bash
cd /chemin/vers/gestion-commandes
touch downloads/test.txt
```

Si le fichier `test.txt` est créé → **C'est bon !** ✅

Si erreur "Permission denied" → **Permissions insuffisantes** ❌

---

## 🛠️ Diagnostiquer le problème

### Vérifier les permissions actuelles

```bash
ls -la /chemin/vers/gestion-commandes/
```

Tu devrais voir quelque chose comme :
```
drwxrwxrwx   2 user  staff    64  downloads/
```

Les lettres importantes :
- `d` = c'est un dossier ✅
- `rwx` (3 fois) = lecture, écriture, exécution pour tout le monde ✅

Si tu vois :
```
drwxr-xr-x   2 user  staff    64  downloads/
```
→ Pas de permissions d'écriture pour "others" ❌

**Solution :**
```bash
chmod 777 downloads/
```

---

## 📋 Permissions recommandées

### En développement (XAMPP/WAMP/MAMP)
```bash
chmod 777 downloads/
```
**Pourquoi 777 ?** Permissions maximales pour éviter tout problème.

### En production
```bash
chmod 755 downloads/
chown www-data:www-data downloads/
```
**Pourquoi 755 ?** Plus sécurisé, suffisant si le propriétaire est correct.

---

## ❓ FAQ

### Pourquoi cette erreur ?
Le serveur web (Apache) s'exécute sous un utilisateur spécifique (souvent `_www`, `www-data`, ou `daemon`). Cet utilisateur doit avoir les permissions pour créer des fichiers.

### Est-ce que 777 est dangereux ?
- **En développement local (XAMPP)** : Non, c'est OK
- **En production sur Internet** : Oui, c'est mieux d'utiliser 755

### Le dossier existe déjà mais j'ai quand même l'erreur
Les permissions sont probablement trop restrictives. Change-les :
```bash
chmod 777 downloads/
```

### Après avoir changé les permissions, ça ne marche toujours pas
Vérifie que :
1. Le dossier parent (`gestion-commandes/`) est aussi accessible
2. Apache a le droit de lire le dossier
3. SELinux n'est pas actif (sur Linux) :
```bash
sudo setenforce 0  # Temporaire
```

---

## 🔒 Sécurité en production

Si tu mets en production, voici les bonnes pratiques :

### 1. Permissions strictes
```bash
# Dossiers
find gestion-commandes -type d -exec chmod 755 {} \;

# Fichiers
find gestion-commandes -type f -exec chmod 644 {} \;

# Sauf downloads/
chmod 755 downloads/
```

### 2. Bon propriétaire
```bash
sudo chown -R www-data:www-data gestion-commandes/
```

### 3. Vérifier
```bash
ls -la gestion-commandes/
```

---

## 🆘 Toujours bloqué ?

### Collecte ces informations :

```bash
# 1. Système d'exploitation
uname -a

# 2. Propriétaire et permissions
ls -la gestion-commandes/

# 3. Utilisateur Apache
# Mac
ps aux | grep httpd | head -1

# Linux
ps aux | grep apache2 | head -1

# 4. Tester création de fichier
touch gestion-commandes/downloads/test.txt
```

Envoie ces informations au support.

---

## 📝 Checklist de résolution

- [ ] Le dossier `downloads/` existe
- [ ] Les permissions sont `755` ou `777`
- [ ] L'utilisateur Apache peut écrire dedans
- [ ] Test de création de fichier réussi
- [ ] L'application fonctionne et génère les CSV

---

**Gestion des Commandes v1.31**
Guide de résolution des problèmes de permissions
