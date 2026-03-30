# 🏢 BimoTech Immo

**Plateforme SaaS multi-agences de gestion immobilière** — Laravel 11 · PHP 8.2 · MySQL · Tailwind CSS

---

## 📋 Table des matières

1. [Présentation](#-présentation)
2. [Fonctionnalités](#-fonctionnalités)
3. [Architecture multi-agences](#-architecture-multi-agences)
4. [Rôles et permissions](#-rôles-et-permissions)
5. [Prérequis](#-prérequis)
6. [Installation](#-installation)
7. [Configuration](#-configuration)
8. [Structure du projet](#-structure-du-projet)
9. [Commandes artisan](#-commandes-artisan)
10. [Tests](#-tests)
11. [Notifications & emails](#-notifications--emails)

---

## 🎯 Présentation

BimoTech Immo est une application web SaaS permettant à des **agences immobilières** de gérer leurs biens, contrats de location et paiements de loyers.

Chaque agence dispose de son propre espace isolé. Un **Super Administrateur** de la plateforme supervise l'ensemble des agences, leurs abonnements et leur activité.

---

## ✨ Fonctionnalités

### Gestion des biens
- Création, modification et suppression de biens immobiliers
- Galerie photos avec photo principale
- Statuts : `disponible`, `loué`, `en maintenance`
- Référence unique par bien

### Gestion des contrats
- Création de contrats de location (bien ↔ locataire)
- Suivi du statut : `actif`, `résilié`, `expiré`
- Résiliation avec remise automatique du bien en `disponible`

### Gestion des paiements
- Enregistrement des loyers mensuels
- Calcul automatique : commission HT, TVA (18%), commission TTC, net propriétaire
- Génération de **quittances PDF** (DomPDF)
- Détection des **doublons** (même contrat + même période)
- Statuts : `en_attente`, `valide`, `annule`, `impaye`
- Génération automatique mensuelle des loyers (commande planifiée)

### Tableau de bord
- **Admin** : statistiques globales, graphiques mensuels, impayés du mois
- **Propriétaire** : vue de ses biens et revenus
- **Locataire** : historique de ses paiements

### Rapports
- Rapport financier mensuel/annuel par agence
- Suivi des impayés avec relance par email

### Abonnements
- Gestion des abonnements par agence (mensuel/annuel)
- Rappels automatiques avant expiration
- Blocage de l'accès si abonnement expiré

### Journal d'activité
- Traçabilité de toutes les actions (création, modification, suppression)
- Consultable par l'admin de l'agence

---

## 🏗 Architecture multi-agences

Chaque entité métier (`Bien`, `Contrat`, `Paiement`) est liée à une agence via `agency_id`.

Un **Global Scope** (`AgencyScope`) est appliqué automatiquement sur tous les modèles pour que chaque utilisateur ne voie que les données de **son agence**.

```
Platform (SuperAdmin)
│
├── Agence A  ──  Admin A  ──  Propriétaires  ──  Biens  ──  Contrats  ──  Paiements
│                          └─  Locataires
│
├── Agence B  ──  Admin B  ──  ...
│
└── Agence C  ──  ...
```

Le SuperAdmin voit **toutes** les agences (le Global Scope est désactivé pour lui).

---

## 🔐 Rôles et permissions

### Les 4 rôles

| Rôle | Description |
|---|---|
| `superadmin` | Administrateur de la plateforme BimoTech. Gère les agences, abonnements. N'appartient à aucune agence. |
| `admin` | Administrateur d'une agence. Gère tous les biens, contrats, paiements et utilisateurs de son agence. |
| `proprietaire` | Propriétaire de biens. Consulte ses biens et ses revenus en lecture seule. |
| `locataire` | Locataire. Consulte ses paiements et télécharge ses quittances. |

### Matrice des permissions

| Action | superadmin | admin | proprietaire | locataire |
|---|:---:|:---:|:---:|:---:|
| Dashboard superadmin | ✅ | ❌ | ❌ | ❌ |
| Dashboard admin | ↪ redirect | ✅ | ❌ | ❌ |
| Dashboard propriétaire | ❌ | ❌ | ✅ | ❌ |
| Dashboard locataire | ❌ | ❌ | ❌ | ✅ |
| **Biens** — voir liste | ❌ | ✅ | ✅ (ses biens) | ❌ |
| **Biens** — voir détail | ❌ | ✅ | ✅ (ses biens) | ❌ |
| **Biens** — créer | ❌ | ✅ | ❌ | ❌ |
| **Biens** — modifier | ❌ | ✅ | ❌ | ❌ |
| **Biens** — supprimer | ❌ | ✅ | ❌ | ❌ |
| **Contrats** — toutes actions | ✅ | ✅ | ❌ | ❌ |
| **Paiements** — voir liste | ✅ | ✅ | ❌ | ✅ (siens) |
| **Paiements** — créer/modifier | ✅ | ✅ | ❌ | ❌ |
| **Paiements** — télécharger PDF | ✅ | ✅ | ✅ (siens) | ✅ (siens) |
| Gestion utilisateurs | ❌ | ✅ | ❌ | ❌ |
| Rapports financiers | ❌ | ✅ | ❌ | ❌ |
| Impayés & relances | ❌ | ✅ | ❌ | ❌ |
| Journal d'activité | ❌ | ✅ | ❌ | ❌ |
| Paramètres agence | ❌ | ✅ | ❌ | ❌ |
| Gestion agences (plateforme) | ✅ | ❌ | ❌ | ❌ |
| Gestion abonnements (plateforme) | ✅ | ❌ | ❌ | ❌ |

### Implémentation technique

```
app/
├── Http/Middleware/
│   ├── IsSuperAdmin.php       ← Vérifie role === 'superadmin'
│   ├── IsAdmin.php            ← Vérifie role IN ('admin', 'superadmin')
│   ├── CheckSubscription.php  ← Bloque si abonnement expiré
│   └── CheckAgencyActif.php   ← Bloque si agence désactivée
│
├── Policies/
│   ├── BienPolicy.php         ← Permissions CRUD sur les biens
│   ├── ContratPolicy.php      ← Permissions CRUD sur les contrats
│   └── PaiementPolicy.php     ← Permissions CRUD + PDF sur les paiements
│
└── Providers/
    └── AppServiceProvider.php ← Définition des Gates (isSuperAdmin, isAdmin, isStaff, isProprietaire, isLocataire)
```

**Gates disponibles** (utilisables avec `@can` dans les vues Blade) :

| Gate | Rôles autorisés |
|---|---|
| `isSuperAdmin` | superadmin |
| `isAdmin` | admin, superadmin |
| `isProprietaire` | proprietaire |
| `isLocataire` | locataire |
| `isStaff` | admin, proprietaire |

---

## ✅ Prérequis

- **PHP** >= 8.2
- **Composer** >= 2.x
- **MySQL** >= 8.0
- **Node.js** >= 18.x & **npm** >= 9.x
- Extension PHP : `pdo_mysql`, `mbstring`, `openssl`, `tokenizer`, `xml`, `gd`

---

## 🚀 Installation

### 1. Cloner le dépôt

```bash
git clone https://github.com/votre-org/bimotech-immo.git
cd bimotech-immo
```

### 2. Installer les dépendances PHP

```bash
composer install
```

### 3. Installer les dépendances JavaScript

```bash
npm install
```

### 4. Copier le fichier d'environnement

```bash
cp .env.example .env
```

### 5. Générer la clé d'application

```bash
php artisan key:generate
```

### 6. Configurer la base de données

Éditer `.env` :

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=bimotech_immo
DB_USERNAME=root
DB_PASSWORD=votre_mot_de_passe
```

### 7. Exécuter les migrations et les seeders

```bash
php artisan migrate --seed
```

> Cela crée la base de données et insère les données de démonstration (agences, utilisateurs, biens, contrats, paiements).

### 8. Créer le lien symbolique pour le stockage

```bash
php artisan storage:link
```

### 9. Compiler les assets

```bash
npm run build
# ou en développement :
npm run dev
```

### 10. Démarrer le serveur

```bash
php artisan serve
```

L'application est accessible sur **http://localhost:8000**

---

## ⚙️ Configuration

### Fichier `.env` — variables importantes

```env
# Application
APP_NAME="BimoTech Immo"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000

# Base de données
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=bimotech_immo
DB_USERNAME=root
DB_PASSWORD=

# Email (exemple avec Mailtrap pour le développement)
MAIL_MAILER=smtp
MAIL_HOST=sandbox.smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=votre_username
MAIL_PASSWORD=votre_password
MAIL_FROM_ADDRESS="noreply@bimotech.com"
MAIL_FROM_NAME="BimoTech Immo"

# Queue (pour les notifications asynchrones)
QUEUE_CONNECTION=database

# Filesystem
FILESYSTEM_DISK=local
```

### Comptes de démonstration (après seeding)

| Rôle | Email | Mot de passe |
|---|---|---|
| Super Admin | superadmin@bimotech.com | password |
| Admin Agence | admin@agence-demo.com | password |
| Propriétaire | proprietaire@demo.com | password |
| Locataire | locataire@demo.com | password |

---

## 📁 Structure du projet

```
bimotech-immo/
│
├── app/
│   ├── Console/Commands/          # Commandes artisan planifiées
│   ├── Http/
│   │   ├── Controllers/           # Contrôleurs (Admin, SuperAdmin, Locataire...)
│   │   ├── Middleware/            # Middlewares d'authentification et de rôles
│   │   └── Requests/              # Form Requests (validation)
│   ├── Models/                    # Modèles Eloquent
│   │   ├── Scopes/AgencyScope.php # Filtre automatique par agence
│   │   └── Traits/LogsActivity.php# Trait de journalisation
│   ├── Notifications/             # Notifications email (quittances, relances...)
│   ├── Policies/                  # Politiques d'autorisation
│   ├── Providers/
│   │   └── AppServiceProvider.php # Enregistrement des Gates et Policies
│   └── Services/
│       └── NombreEnLettres.php    # Conversion montant → lettres (quittances)
│
├── database/
│   ├── factories/                 # Factories pour les tests
│   ├── migrations/                # Migrations de la base de données
│   └── seeders/                   # Données de démonstration
│
├── resources/
│   ├── css/                       # Styles (Tailwind + bimotech.css)
│   ├── js/                        # JavaScript
│   └── views/                     # Templates Blade
│       ├── admin/                 # Vues admin (dashboard, contrats...)
│       ├── superadmin/            # Vues super admin
│       ├── proprietaire/          # Dashboard propriétaire
│       ├── locataire/             # Dashboard locataire
│       ├── biens/                 # CRUD biens
│       ├── paiements/             # CRUD paiements + PDF quittance
│       └── layouts/               # Layouts partagés
│
├── routes/
│   ├── web.php                    # Routes web (avec middlewares de rôles)
│   ├── auth.php                   # Routes d'authentification
│   └── console.php                # Planification des tâches
│
└── tests/
    ├── Feature/                   # Tests d'intégration
    └── Unit/                      # Tests unitaires
```

---

## 🛠 Commandes artisan

### Génération automatique des loyers

Génère les paiements mensuels pour tous les contrats actifs :

```bash
php artisan rent:generate
```

### Vérification des abonnements

Vérifie et met à jour le statut des abonnements expirés :

```bash
php artisan subscriptions:check
```

### Rappels d'abonnement

Envoie des emails de rappel aux agences dont l'abonnement expire bientôt :

```bash
php artisan subscriptions:remind
```

### Rapport hebdomadaire des paiements

Envoie un résumé hebdomadaire des paiements aux admins :

```bash
php artisan payments:weekly-report
```

### Nettoyage des agences inactives

Supprime les agences inactives depuis plus de 90 jours :

```bash
php artisan agencies:clean-inactive
```

### Planification (Scheduler)

Toutes ces commandes sont planifiées dans `routes/console.php`. Pour les activer en production, ajouter au cron :

```bash
* * * * * cd /chemin/vers/projet && php artisan schedule:run >> /dev/null 2>&1
```

---

## 🧪 Tests

### Lancer tous les tests

```bash
php artisan test
```

### Lancer uniquement les tests unitaires

```bash
php artisan test --testsuite=Unit
```

### Tests disponibles

| Suite | Fichier | Description |
|---|---|---|
| Unit | `NombreEnLettresTest` | Conversion de montants en lettres |
| Unit | `PaiementCalculTest` | Calcul commission, TVA, net propriétaire |
| Feature | `PaiementDoublonTest` | Détection des paiements en double |
| Feature | `ProfileTest` | Mise à jour du profil utilisateur |
| Feature | `Auth/*` | Authentification, inscription, reset password |

> **Note** : Les tests Feature nécessitent une connexion MySQL active. Configurer `DB_CONNECTION=sqlite` dans `.env.testing` pour des tests sans MySQL :
>
> ```env
> # .env.testing
> DB_CONNECTION=sqlite
> DB_DATABASE=:memory:
> ```

---

## 📧 Notifications & emails

| Notification | Déclencheur | Destinataire |
|---|---|---|
| `AgencyWelcomeNotification` | Inscription d'une nouvelle agence | Admin de l'agence |
| `PaiementProprietaireNotification` | Paiement de loyer enregistré | Propriétaire du bien |
| `QuittanceLocataireNotification` | Paiement validé | Locataire |
| `RelanceImpayeNotification` | Relance manuelle d'un impayé | Locataire |
| `SubscriptionReminderNotification` | J-7 avant expiration abonnement | Admin de l'agence |
| `SubscriptionExpiredNotification` | Abonnement expiré | Admin de l'agence |
| `WeeklyPaymentsSummaryNotification` | Chaque lundi matin | Admins des agences actives |

---

## 📄 Licence

Projet propriétaire — © BimoTech. Tous droits réservés.
