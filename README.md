# PharmaFEFO — Système de Gestion de Stock Pharmaceutique

PharmaFEFO est une application web qui aide une pharmacie à gérer son stock de médicaments en respectant la règle **FEFO** (First Expired, First Out) : le produit qui expire en premier doit toujours sortir en premier.

---

## 🧩 C'est quoi le projet ?

L'application permet de :

- Voir tous les lots de médicaments en stock (avec leur date de péremption)
- Ajouter un nouveau lot quand on reçoit des produits
- Sortir automatiquement le bon lot (celui qui expire le plus tôt) quand on délivre un médicament
- Recevoir des alertes quand un lot est proche de la péremption
- Gérer les comptes des employés (admin uniquement)
- Voir un rapport financier des pertes (produits périmés)

---

## ⚙️ Comment ça fonctionne (en simple)

Le projet est découpé en deux parties qui communiquent ensemble :

```
Le Navigateur (HTML + JavaScript)
        ⬇️  envoie une requête
Le Serveur PHP (API qui répond en JSON)
        ⬇️  va chercher les données
La Base de Données (MySQL)
```

Concrètement :
1. Tu ouvres une page (par exemple le tableau de bord)
2. La page est juste un squelette HTML vide au départ
3. Le JavaScript va chercher les données auprès du serveur (via `fetch`)
4. Le serveur répond avec du JSON (texte structuré)
5. Le JavaScript affiche les données à l'écran

C'est pour ça qu'on dit que l'architecture est **"API-Ready"** : le serveur ne renvoie jamais directement du HTML avec les données dedans, il renvoie seulement du JSON, et c'est le JavaScript qui construit la page.

---

## 📁 Structure des dossiers

```
pharmafefo_p2/
│
├── config/                  → Réglages (connexion base de données, etc.)
│
├── public/                  → Tout ce qui est accessible depuis le navigateur
│   ├── index.php             → Point d'entrée unique du site
│   └── js/                   → Fichiers JavaScript (app.js, dashboard.js, login.js)
│
├── src/
│   ├── Controller/
│   │   ├── Api/               → Contrôleurs qui répondent en JSON
│   │   └── Web/                → Contrôleurs qui affichent les pages HTML
│   ├── Service/                → La logique métier (calcul FEFO, alertes, etc.)
│   └── Repository/             → Tout ce qui parle à la base de données (SQL)
│
└── templates/                  → Les fichiers HTML (squelettes, sans données)
    ├── auth/                    → Page de connexion
    ├── dashboard/                → Pages principales
    └── layout/                   → Le cadre commun (menu, header, footer)
```

**Règle simple à retenir :** chaque dossier a un seul rôle.
- `templates/` = juste du HTML
- `src/Repository/` = juste du SQL
- `src/Service/` = les calculs et règles métier
- `src/Controller/` = fait le lien entre tout ça
- `public/js/` = va chercher les données et les affiche

---

## 🔑 Les rôles des utilisateurs

| Rôle | Ce qu'il peut faire |
|---|---|
| **Préparateur** | Ajouter des lots, délivrer des médicaments |
| **Pharmacien** | Délivrer des médicaments, voir les alertes |
| **Admin** | Tout faire + gérer les comptes + voir le rapport financier |

---

## 🚀 Comment lancer le projet

### Ce qu'il te faut avant de commencer
- Un serveur avec PHP (par exemple **XAMPP**, **WAMP** ou **MAMP**)
- MySQL (généralement inclus avec XAMPP/WAMP)

### Étapes

1. **Copier le projet** dans le dossier de ton serveur web
   - Avec XAMPP par exemple : `htdocs/pharmafefo_p2`

2. **Créer la base de données**
   - Ouvre phpMyAdmin
   - Crée une base appelée `pharmafefo_db`
   - Importe les tables nécessaires (utilisateurs, produits, lots, mouvements)

3. **Vérifier la connexion**
   - Ouvre le fichier `config/database.php`
   - Vérifie que le nom de la base, l'utilisateur et le mot de passe correspondent à ta config MySQL (par défaut : utilisateur `root`, pas de mot de passe)

4. **Démarrer Apache et MySQL** (depuis le panneau XAMPP/WAMP)

5. **Ouvrir le site dans ton navigateur**
   ```
   http://localhost/pharmafefo_p2/public/index.php
   ```

6. **Se connecter** avec un compte existant dans la table `users`

---

## 🔌 Les routes principales de l'API

Toutes les routes API commencent par `?action=api/v1/...` et renvoient du JSON.

| Quoi | Méthode | Route |
|---|---|---|
| Se connecter | POST | `api/v1/login` |
| Voir mon profil | GET | `api/v1/me` |
| Liste des lots | GET | `api/v1/batches` |
| Ajouter un lot | POST | `api/v1/batches` |
| Délivrer 1 boîte | POST | `api/v1/batches/checkout` |
| Marquer un lot périmé | PATCH | `api/v1/batches/{id}/expire` |
| Liste des produits | GET | `api/v1/products` |
| Liste des utilisateurs | GET | `api/v1/users` |
| Créer un utilisateur | POST | `api/v1/users` |
| Rapport des pertes | GET | `api/v1/report/loss` |

---

## 🐛 Problèmes fréquents et solutions

**"Erreur lors du chargement des lots"**
→ Vérifie que MySQL est bien démarré et que la base de données existe.

**Page blanche ou erreur 500**
→ Regarde les logs d'erreur PHP (souvent dans le dossier `logs` de XAMPP), une erreur de syntaxe ou de connexion BDD en est souvent la cause.

**Erreur 403 "Accès refusé"**
→ Le compte connecté n'a pas le bon rôle pour cette action (exemple : un préparateur ne peut pas créer d'utilisateurs).

**Erreur 404 sur une API**
→ Vérifie l'URL appelée dans le fichier JavaScript concerné — un caractère en trop (comme un `?` en double) suffit à casser la route.

---

## 📝 Bon à savoir

- Aucune donnée n'est codée en dur dans les pages HTML : tout vient de la base de données via l'API.
- Le design (couleurs, mise en page) reste identique partout, seule la façon de charger les données a changé.
- Si tu veux ajouter une nouvelle fonctionnalité, le bon ordre est généralement :
  1. Ajouter la requête SQL dans un `Repository`
  2. Ajouter la logique dans un `Service`
  3. Créer la route dans un `Controller Api`
  4. Appeler cette route depuis le JavaScript

---
## 🌐 Lien de déploiement

🔗 [Accéder à PharmaFEFO](https://pharmafefo-application-asynchrone-production.up.railway.app)


*Projet réalisé dans le cadre d'un exercice d'architecture MVC + API REST en PHP.*
