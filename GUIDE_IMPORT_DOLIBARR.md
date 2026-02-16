# 📥 Guide d'import depuis Dolibarr

## 🎯 Introduction

Ce guide vous explique comment importer automatiquement les données d'une commande depuis votre Dolibarr vers l'application Gestion des Commandes.

**Deux méthodes disponibles :**
1. **Import par URL** (copier-coller) - Simple et universelle
2. **Bookmarklet** (1 clic) - Rapide et automatique

---

## 🔧 Méthode 1 : Import par URL (Recommandée)

### Avantages
- ✅ Aucune installation nécessaire
- ✅ Fonctionne sur tous les navigateurs
- ✅ Simple à utiliser

### Procédure

#### Étape 1 : Dans Dolibarr
1. Connectez-vous à votre Dolibarr
2. Ouvrez la fiche d'une **commande client**
3. Copiez l'URL de la page
   - Windows : `Ctrl + L` puis `Ctrl + C`
   - Mac : `Cmd + L` puis `Cmd + C`
   
Exemple d'URL :
```
https://votre-dolibarr.com/commande/card.php?id=4456
```

#### Étape 2 : Dans Gestion des Commandes
1. Allez sur **Nouvelle Commande**
2. En haut du formulaire, trouvez la section **"Importer depuis Dolibarr"**
3. Collez l'URL copiée dans le champ
4. Cliquez sur **"Importer"**

#### Étape 3 : Extraction
1. Une fenêtre popup s'ouvre
2. L'extraction des données se fait automatiquement
3. La popup se ferme toute seule
4. Le formulaire est rempli ! ✨

### Ce qui est importé

| Donnée Dolibarr | Champ rempli |
|----------------|--------------|
| Nom du client | Société |
| Numéro de commande (ex: CO2602-4359) | N° Commande Client |
| Numéro de devis lié (ex: PR2602-4076) | Notre N° de Devis |
| Date de commande | Date |
| Délai de livraison | Délais de Fabrication |
| Référence article | Référence Article |
| Quantité | Quantité par Modèle |

---

## ⚡ Méthode 2 : Bookmarklet (1 clic)

### Avantages
- ✅ Ultra rapide (1 seul clic)
- ✅ Pas besoin de copier-coller
- ✅ Fonctionne depuis n'importe quelle commande Dolibarr

### Installation du bookmarklet

#### Étape 1 : Afficher la barre de favoris

**Chrome / Edge :**
- Windows : `Ctrl + Shift + B`
- Mac : `Cmd + Shift + B`

**Firefox :**
- Windows : `Ctrl + Shift + B`
- Mac : `Cmd + Shift + B`

**Safari :**
- Mac : `Cmd + Shift + B`

#### Étape 2 : Installer le bookmarklet

1. Allez sur la page : `http://votre-site/gestion-commandes/dolibarr-bookmarklet.html`
2. Vous verrez un gros bouton violet **"Exporter vers Gestion Commandes"**
3. **Glissez ce bouton** dans votre barre de favoris
4. C'est installé ! 🎉

**Méthode alternative (si le glisser-déposer ne fonctionne pas) :**
1. Clic droit sur le bouton violet
2. "Copier l'adresse du lien"
3. Créez un nouveau favori manuellement
4. Collez le lien comme URL du favori
5. Nommez-le "Exporter vers Gestion Commandes"

### Utilisation du bookmarklet

1. Dans Dolibarr, ouvrez une **commande client**
2. Dans votre barre de favoris, cliquez sur **"Exporter vers Gestion Commandes"**
3. Un nouvel onglet s'ouvre avec le formulaire **pré-rempli** ! 🎉

**C'est tout !** Aucun copier-coller, aucune popup.

---

## 🔍 Comparaison des méthodes

| Critère | Import par URL | Bookmarklet |
|---------|---------------|-------------|
| **Installation** | Aucune | Glisser-déposer dans favoris |
| **Nombre de clics** | 3-4 | 1 |
| **Copier-coller** | Oui | Non |
| **Popup** | Oui | Non |
| **Compatibilité** | Tous navigateurs | Tous navigateurs |
| **Facilité** | ⭐⭐⭐ Facile | ⭐⭐⭐⭐⭐ Très facile |

**Recommandation :**
- **Utilisez le bookmarklet** si vous importez souvent (quotidien)
- **Utilisez l'URL** pour un usage occasionnel

---

## ⚙️ Fonctionnement technique

### Prérequis
- ✅ Vous devez être **connecté à Dolibarr** dans le même navigateur
- ✅ La page doit être une **commande client** Dolibarr
- ✅ Les popups doivent être **autorisées** (pour méthode URL)

### Extraction des données

Le système extrait automatiquement :

#### 1. Nom du client
- Recherche dans le lien vers la fiche société
- Exemple : `Mme Patricia Paul`

#### 2. Numéro de commande
- Extrait depuis la référence en haut de page
- Format : `CO2602-4359`

#### 3. Numéro de devis
- Cherche dans les "Objets liés"
- Format : `PR2602-4076`

#### 4. Date de commande
- Convertit du format `DD/MM/YYYY` vers `YYYY-MM-DD`
- Exemple : `12/02/2026` → `2026-02-12`

#### 5. Délai de fabrication
- Extrait depuis "Délai de livraison"
- Exemples :
  - `5 jours ouvrés à validation du BAT` → Sélectionne J+5
  - `28/02/2026` → Remplit la date

#### 6. Référence article
- Premier produit de la commande
- Exemple : `04413`

#### 7. Quantité
- Quantité du premier produit
- Exemple : `1000` → `1000`

---

## 🐛 Dépannage

### Problème : La popup ne s'ouvre pas

**Cause :** Les popups sont bloquées

**Solution :**
1. Cherchez l'icône de popup bloquée dans la barre d'adresse
2. Cliquez et autorisez les popups pour ce site
3. Réessayez

### Problème : Aucune donnée n'est extraite

**Causes possibles :**
- ❌ Vous n'êtes pas sur une page de commande Dolibarr
- ❌ Vous n'êtes pas connecté à Dolibarr
- ❌ La structure HTML de votre Dolibarr est différente

**Solutions :**
1. Vérifiez que vous êtes bien sur une **commande client** (pas devis, pas facture)
2. Reconnectez-vous à Dolibarr
3. Actualisez la page Dolibarr
4. Si le problème persiste, contactez le support

### Problème : Certains champs ne se remplissent pas

**C'est normal si :**
- Ces données n'existent pas dans Dolibarr
- Exemple : pas de devis lié → champ "N° de devis" vide

**Solution :** Remplissez manuellement les champs manquants

### Problème : Le bookmarklet ne fonctionne pas

**Solutions :**
1. Vérifiez que vous avez bien glissé le bouton dans les **favoris**
2. Assurez-vous d'être sur une **page de commande**
3. Essayez de recharger la page Dolibarr
4. Vérifiez dans la console (F12) s'il y a des erreurs
5. En dernier recours, utilisez la **méthode par URL**

### Problème : Erreur "Impossible d'accéder à la page Dolibarr"

**Cause :** Restriction de sécurité (iframe bloquée)

**Solution :**
1. Utilisez plutôt le **bookmarklet** (contourne ce problème)
2. Ou contactez votre administrateur Dolibarr pour configurer les en-têtes CORS

---

## 🔒 Sécurité et confidentialité

### Où vont vos données ?

**Nulle part !** 

- ✅ Toutes les données restent dans **votre navigateur**
- ✅ Aucun envoi vers un serveur externe
- ✅ Aucune donnée n'est stockée (sauf temporairement dans sessionStorage)
- ✅ Le code est exécuté localement sur votre machine

### Comment ça fonctionne ?

1. **Bookmarklet** : Extrait les données **directement depuis la page Dolibarr** dans votre navigateur
2. **Import URL** : Ouvre la page Dolibarr **dans votre session**, extrait les données, puis les transfère au formulaire

**Aucune communication avec un serveur tiers.**

### Authentification

- Le système utilise votre **session Dolibarr existante**
- Aucun mot de passe n'est stocké ou transmis
- Si vous n'êtes pas connecté à Dolibarr, l'import ne fonctionnera pas

---

## 💡 Astuces et bonnes pratiques

### Astuce 1 : Créer un raccourci clavier pour le bookmarklet

**Chrome / Edge / Firefox :**
1. Clic droit sur le bookmarklet dans vos favoris
2. "Modifier"
3. Ajoutez un mot-clé court (ex: "export")
4. Maintenant tapez juste "export" dans la barre d'adresse !

### Astuce 2 : Garder Dolibarr ouvert dans un onglet

Pour importer plusieurs commandes rapidement :
1. Gardez Dolibarr ouvert dans un onglet
2. Gardez le formulaire ouvert dans un autre onglet
3. Naviguez entre les commandes dans Dolibarr
4. Cliquez sur le bookmarklet à chaque fois
5. Un nouvel onglet s'ouvre à chaque fois avec les données

### Astuce 3 : Vérifier les données avant de sauvegarder

Bien que l'import soit fiable, vérifiez toujours :
- ✅ Le nom du client
- ✅ Le numéro de commande
- ✅ Les quantités

### Astuce 4 : Utiliser le mode debug

Si vous rencontrez des problèmes :
1. Ouvrez `dolibarr-extractor.php`
2. En haut du fichier, `const DEBUG = true`
3. Relancez l'import
4. Vous verrez les détails de l'extraction

---

## 📱 Compatibilité

### Navigateurs supportés
- ✅ Chrome / Chromium (recommandé)
- ✅ Edge
- ✅ Firefox
- ✅ Safari
- ✅ Opera

### Versions Dolibarr testées
- ✅ Dolibarr 14.x
- ✅ Dolibarr 15.x
- ⚠️ Versions plus anciennes : non testées mais devraient fonctionner

### Appareils
- ✅ PC Windows
- ✅ PC Linux
- ✅ Mac
- ⚠️ Mobile : support limité (bookmarklet difficile à installer)

---

## 🆘 Support

### Vous avez un problème ?

1. **Consultez la section Dépannage** ci-dessus
2. **Activez le mode debug** pour voir les détails
3. **Essayez l'autre méthode** (URL vs Bookmarklet)
4. **Contactez le support** avec :
   - Votre navigateur et version
   - Votre version Dolibarr
   - Le message d'erreur exact
   - Une capture d'écran si possible

### Signaler un bug

Si l'import ne fonctionne pas :
1. Version de Dolibarr
2. URL de la commande (sans données sensibles)
3. Données qui ne s'importent pas
4. Console du navigateur (F12 → Console)

---

## 📋 Checklist rapide

### Avant le premier import
- [ ] Je suis connecté à Dolibarr
- [ ] J'ai une commande client ouverte dans Dolibarr
- [ ] J'ai choisi ma méthode (URL ou Bookmarklet)
- [ ] Si bookmarklet : il est installé dans mes favoris
- [ ] Les popups sont autorisées (pour méthode URL)

### Pour chaque import
- [ ] Je suis sur une page de commande Dolibarr
- [ ] J'ai copié l'URL (méthode URL) ou cliqué sur le bookmarklet
- [ ] Le formulaire s'est rempli automatiquement
- [ ] J'ai vérifié les données importées
- [ ] Je complète les champs manquants si besoin
- [ ] Je sauvegarde la commande

---

## 🎉 Conclusion

L'import depuis Dolibarr vous fait gagner un temps précieux en évitant la saisie manuelle des données.

**Temps gagné par commande :**
- Sans import : ~3 minutes de saisie
- Avec import URL : ~30 secondes
- Avec bookmarklet : ~10 secondes

**Pour 10 commandes par jour :**
- Gain de temps : **~25 minutes par jour**
- Sur un mois : **~8 heures économisées** 🚀

---

**Version 1.31** - Système d'import Dolibarr
Documentation mise à jour : Février 2026
