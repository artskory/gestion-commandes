# 🐛 Le bookmarklet ne fait rien ? GUIDE DE DEBUG

## 📍 OÙ REGARDER LA CONSOLE ?

### ⚠️ IMPORTANT : Sur l'onglet DOLIBARR (pas Gestion Commandes)

```
1. Sur la page Dolibarr avec la commande ouverte
2. Appuie sur F12 (ou Cmd+Option+I sur Mac)
3. Va dans l'onglet "Console"
4. Clique sur le bookmarklet
5. Regarde les messages qui apparaissent
```

---

## 🔧 UTILISE LA VERSION DEBUG DU BOOKMARKLET

### Étape 1 : Installe la version DEBUG
1. Va sur `http://localhost/gestion-commandes/dolibarr-bookmarklet.html`
2. Tu verras **2 boutons** :
   - 🐛 **VERSION DEBUG** (avec logo bug) ← Utilise celle-ci
   - ⬇️ VERSION NORMALE
3. Glisse le bouton **DEBUG** dans tes favoris

### Étape 2 : Teste
1. Ouvre une commande dans Dolibarr
2. Ouvre la console (F12) **sur cet onglet**
3. Clique sur le bookmarklet DEBUG
4. Lis les messages dans la console

---

## 📊 MESSAGES QUE TU DOIS VOIR

### ✅ Si ça marche :
```
[BOOKMARKLET] Démarrage...
[BOOKMARKLET] Société: Mme Patricia Paul
[BOOKMARKLET] Numéro commande: CO2602-4359
[BOOKMARKLET] Fenêtre ouverte avec succès!
```

### ❌ Problèmes possibles :

**"Pas sur une page de commande"**
→ Tu dois être sur `/commande/card.php?id=123`

**"Société non trouvée"**
→ Ta version de Dolibarr a une structure HTML différente

**"Popup bloqué"**
→ Autorise les popups pour ton site

---

## 🆘 ENVOIE-MOI CES INFOS

1. Version Dolibarr (en bas à droite)
2. Messages de la console (copie/colle)
3. Capture d'écran de la page + console

---

**Guide de debug v1.31**
