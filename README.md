# 💊 Gestion Pharmacie

Application web de gestion pour une pharmacie : médicaments, catégories, ventes, factures et alertes de stock/péremption. Gestion multi-rôles (administrateur, pharmacien, caissier).

Projet personnel réalisé par **Adji Farimata CISSE**, étudiante en Génie Logiciel à l'Institut Supérieur d'Informatique (ISI), Dakar.

---

## ✨ Fonctionnalités

- 🔐 Authentification avec 3 rôles : **Administrateur**, **Pharmacien**, **Caissier**
- 💊 Gestion des médicaments (ajout, modification, suppression) avec catégories
- 🗂️ Gestion des catégories de médicaments
- 🛒 Enregistrement des ventes avec recherche en temps réel (AJAX)
- 🧾 Génération de factures imprimables
- 📋 Historique des ventes (avec annulation possible pour l'admin)
- 📊 Tableau de bord avec alertes automatiques :
  - Stock faible (≤ 10 unités)
  - Médicaments périmés
  - Médicaments bientôt périmés (30 jours)

## 🛠️ Technologies utilisées

- **PHP** (PDO pour l'accès à la base de données)
- **MySQL / MariaDB**
- **Bootstrap 5**
- **HTML / CSS / JavaScript (AJAX)**

## 📂 Structure du projet

```
PHARMACIE/
├── index.php                   # Page de connexion
├── db.php                      # Connexion à la base de données
├── auth.php                    # Gestion des rôles et sécurité des pages
├── dashboard.php                # Tableau de bord (alertes stock/péremption)
├── liste.php                    # Liste des médicaments
├── ajouter.php / modifier.php / supprimer.php   # CRUD médicaments
├── nouvelle.php                 # Nouvelle vente
├── rechercheMedocVente.php      # Recherche AJAX pour la vente
├── historique.php                # Historique des ventes
├── facture.php                  # Facture imprimable
├── supprimerVente.php           # Annulation d'une vente (admin)
├── navbar.php / footer.php      # Composants communs
└── pharmacie_db.sql             # Script de création de la base de données
```

## 🚀 Installation locale (XAMPP)

1. Clone ce dépôt dans le dossier `htdocs` de XAMPP :
```bash
git clone https://github.com/farimah12/Gestion-Pharmacie.git
```
2. Démarre **Apache** et **MySQL** depuis le panneau de contrôle XAMPP.
3. Importe la base de données :
   - Ouvre **phpMyAdmin**
   - Onglet **Importer** → sélectionne `pharmacie_db.sql`
   - La base `pharmacie_db` sera créée automatiquement avec des données de test
4. Accède à l'application : `http://localhost/Gestion-Pharmacie/index.php`

## 🔑 Identifiants de démonstration

Mot de passe commun : **12345**

| Login | Rôle |
|-------|------|
| `ADMIN` | Administrateur |
| `PHARM` | Pharmacien |
| `CAISS` | Caissier |


## 👩‍💻 Auteure

**Adji Farimata CISSE**
Étudiante en Génie Logiciel — ISI, Dakar
📧 adjifarimahcisse@gmail.com
