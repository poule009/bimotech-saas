# 🏢 BimoTech Immo

**Plateforme SaaS multi-agences de gestion immobilière pour le Sénégal**
Laravel 11 · PHP 8.2 · MySQL · Tailwind CSS · PayDunya

---

## 📋 Table des matières

1. [Présentation](#-présentation)
2. [Fonctionnalités](#-fonctionnalités)
3. [Architecture multi-agences](#-architecture-multi-agences)
4. [Rôles et permissions](#-rôles-et-permissions)
5. [Prérequis](#-prérequis)
6. [Installation](#-installation)
7. [Configuration](#-configuration)
8. [Paiements & Abonnements (PayDunya)](#-paiements--abonnements-paydunya)
9. [Structure du projet](#-structure-du-projet)
10. [Commandes artisan](#-commandes-artisan)
11. [Tests](#-tests)
12. [Notifications & emails](#-notifications--emails)
13. [Changelog](#-changelog)

---

## 🎯 Présentation

BimoTech Immo est une application web SaaS permettant à des **agences immobilières sénégalaises** de gérer leurs biens, contrats de location et paiements de loyers.

Chaque agence dispose de son propre espace isolé. Un **Super Administrateur** de la plateforme supervise l'ensemble des agences, leurs abonnements et leur activité.

> 🇸🇳 **Conçu pour le Sénégal** : devise FCFA, TVA 18%, modes de paiement locaux (Wave, Orange Money, Free Money), validation des numéros sénégalais, villes et quartiers locaux.

---

## ✨ Fonctionnalités

### Gestion des biens
- Création, modification et suppression de biens immobiliers
- Galerie photos avec photo principale
- Champs locaux : **quartier**, **commune**, **meublé** (oui/non)
- Statuts : `disponible`, `loué`, `en_travaux`
- Référence unique par agence (format : `AGC-YYYYMMDD-XXXXX`)
- Types : Appartement, Villa, Studio, Bureau, Commerce, Terrain, Maison, Entrepôt

### Gestion des contrats
- Création de contrats de location (bien ↔ locataire)
- **Types de bail** : habitation, commercial, professionnel, mixte, saisonnier
- Champs financiers : frais d'agence, charges mensuelles, indexation annuelle, nombre de mois de caution
- **Garant** : nom, téléphone, adresse
- Modification complète via formulaire dédié
- Suivi du statut : `actif`, `résilié`, `expiré`

### Gestion des paiements
- Enregistrement des loyers mensuels
- Calcul automatique : commission HT, TVA (18%), commission TTC, net propriétaire
- **Modes de paiement sénégalais** : Espèces, Wave, Orange Money, Free Money, Virement, Chèque
- Génération de **quittances PDF** (DomPDF) avec données dynamiques de l'agence
- Détection des **doublons** (même contrat + même période)
- Statuts : `en_attente`, `valide`, `annule`, `impaye`, `unpaid`
- Génération automatique mensuelle des loyers (commande planifiée)

### Tableau de bord
- **Admin** : statistiques globales, graphiques mensuels, impayés du mois, onboarding
- **Propriétaire** : vue de ses biens et revenus
- **Locataire** : historique de ses paiements et téléchargement des quittances

### Rapports financiers
- Rapport financier mensuel/annuel par agence
- **Export PDF** : en-tête agence, KPIs du mois, détail des paiements, récapitulatif par propriétaire, biens impayés
- Suivi des impayés avec relance par email

### Abonnements
- Plans : Mensuel (25 000 F), Trimestriel (67 500 F), Semestriel (127 500 F), Annuel (240 000 F)
- Période d'essai gratuite (14 jours)
- Intégration **PayDunya** (simulation → test → live)
- Rappels automatiques avant expiration (J-7, J-1)
- Blocage de l'accès si abonnement expiré
- Historique complet des paiements d'abonnement

### Gestion des utilisateurs
- Création et **modification** des propriétaires et locataires
- Profil propriétaire : CNI, NINEA, TVA, Wave, Orange Money, banque, compte
- Profil locataire : profession, employeur, revenu mensuel, contact d'urgence
- Validation des numéros de téléphone sénégalais (70/75/76/77/78/33/30)

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
| **Biens** — créer/modifier/supprimer | ❌ | ✅ | ❌ | ❌ |
| **Contrats** — toutes actions | ✅ | ✅ | ❌ | ❌ |
| **Paiements** — voir liste | ✅ | ✅ | ❌ | ✅ (siens) |
| **Paiements** — créer/modifier | ✅ | ✅ | ❌ | ❌ |
| **Paiements** — télécharger PDF | ✅ | ✅ | ✅ (siens) | ✅ (siens) |
| Gestion utilisateurs (CRUD) | ❌ | ✅ | ❌ | ❌ |
| Rapports financiers + export PDF | ❌ | ✅ | ❌ | ❌ |
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
├── Rules/
│   └── TelephoneSenegalais.php ← Validation numéros sénégalais
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
- Extensions PHP : `pdo_mysql`, `mbstring`, `openssl`, `tokenizer`, `xml`, `gd`
- Package Composer : `barryvdh/laravel-dompdf` (génération PDF)

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
APP_LOCALE=fr
APP_TIMEZONE=UTC

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
MAIL_FROM_ADDRESS="noreply@bimotech.sn"
MAIL_FROM_NAME="BimoTech Immo"

# Queue (pour les notifications asynchrones)
QUEUE_CONNECTION=database

# Filesystem
FILESYSTEM_DISK=local

# PayDunya (paiement abonnements)
PAYDUNYA_MODE=simulation        # simulation | test | live
PAYDUNYA_MASTER_KEY=            # Clé maître PayDunya
PAYDUNYA_PRIVATE_KEY=           # Clé privée PayDunya
PAYDUNYA_TOKEN=                 # Token PayDunya
```

### Comptes de démonstration (après seeding)

| Rôle | Email | Mot de passe |
|---|---|---|
| Super Admin | superadmin@bimotech.com | password |
| Admin Agence | admin@agence-demo.com | password |
| Propriétaire | proprietaire@demo.com | password |
| Locataire | locataire@demo.com | password |

---

## 💳 Paiements & Abonnements (PayDunya)

### Plans disponibles

| Plan | Prix | Durée | Économie |
|---|---|---|---|
| Mensuel | 25 000 FCFA | 1 mois | — |
| Trimestriel | 67 500 FCFA | 3 mois | 10% |
| Semestriel | 127 500 FCFA | 6 mois | 15% |
| Annuel | 240 000 FCFA | 12 mois | 20% |

### Modes de paiement acceptés

- 🌊 **Wave** (Sénégal)
- 🟠 **Orange Money**
- 🟢 **Free Money**
- 🏦 **Virement bancaire**
- 💵 **Espèces** (via admin)

### Flux de paiement

```
Agence → /subscription → Choisir plan
    → POST /subscription/initier
        → [SIMULATION] Activation immédiate
        → [TEST/LIVE]  Redirection PayDunya
            → Callback IPN → /subscription/callback
            → Retour succès → /subscription/succes
            → Retour échec  → /subscription/echec
```

### Activer le mode live

1. Obtenir les clés sur [paydunya.com](https://paydunya.com)
2. Mettre à jour `.env` :
```env
PAYDUNYA_MODE=live
PAYDUNYA_MASTER_KEY=votre_master_key
PAYDUNYA_PRIVATE_KEY=votre_private_key
PAYDUNYA_TOKEN=votre_token
```

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
│   │   ├── Requests/              # Form Requests (validation)
│   │   └── Controllers/Auth/      # Authentification + inscription agence
│   ├── Models/                    # Modèles Eloquent
│   │   ├── Scopes/AgencyScope.php # Filtre automatique par agence
│   │   └── Traits/LogsActivity.php# Trait de journalisation
│   ├── Notifications/             # Notifications email (quittances, relances...)
│   ├── Policies/                  # Politiques d'autorisation
│   ├── Providers/
│   │   └── AppServiceProvider.php # Enregistrement des Gates et Policies
│   ├── Rules/
│   │   └── TelephoneSenegalais.php# Validation numéros sénégalais
│   └── Services/
│       ├── NombreEnLettres.php    # Conversion montant → lettres (quittances)
│       └── PaymentService.php     # Intégration PayDunya (simulation/test/live)
│
├── database/
│   ├── factories/                 # Factories pour les tests
│   ├── migrations/                # Migrations de la base de données
│   └── seeders/                   # Données de démonstration
│
├── resources/
│   ├── css/                       # Styles (Tailwind + bimotech.css)
│   ├── js/                        # JavaScript
│   └── views/
│       ├── admin/                 # Vues admin (dashboard, contrats CRUD...)
│       ├── superadmin/            # Vues super admin
│       ├── proprietaire/          # Dashboard propriétaire
│       ├── locataire/             # Dashboard locataire
│       ├── biens/                 # CRUD biens
│       ├── paiements/             # CRUD paiements + PDF quittance
│       ├── rapports/              # Rapport financier + export PDF
│       ├── subscription/          # Abonnement + pages succès/échec PayDunya
│       ├── users/                 # CRUD utilisateurs (create, edit, show...)
│       ├── errors/                # Pages d'erreur personnalisées (403, 404, 500)
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

> ⚠️ Utilise `$contrat->bien->taux_commission` (corrigé — anciennement `$contrat->taux_commission`).

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

## 📝 Changelog

### v1.1.0 — Corrections & Améliorations Sénégal (Avril 2026)

#### 🔴 Bugs critiques corrigés
- **Quittances PDF** : données d'agence hardcodées remplacées par les données dynamiques de l'agence connectée
- **Commission mensuelle** : `GenerateMonthlyRent` utilisait `$contrat->taux_commission` (inexistant) → corrigé en `$contrat->bien->taux_commission`
- **Fuite de données inter-agences** : `RapportController` filtrait les utilisateurs sans `agency_id` → corrigé
- **Routes corrompues** : fichier `routes/web.php` contenait des caractères `NaN` → reconstruit
- **Référence des biens** : `Bien::count()` pouvait créer des doublons entre agences → remplacé par un identifiant unique par agence

#### 🟠 Paiements & Abonnements
- **PayDunya** : nouveau `PaymentService` avec modes simulation/test/live
- **Mobile Money** : Wave, Orange Money, Free Money ajoutés comme modes de paiement distincts
- **Pages abonnement** : pages de retour succès et échec PayDunya
- **Historique** : tableau des paiements d'abonnement avec méthode et référence

#### 🟡 Modèles & Migrations
- **Biens** : ajout des champs `quartier`, `commune`, `meuble`
- **Contrats** : ajout de `type_bail`, `frais_agence`, `charges_mensuelles`, `indexation_annuelle`, `nombre_mois_caution`, `garant_nom/telephone/adresse`
- **Paiement** : constantes `MODES_PAIEMENT` et `STATUTS`
- **Contrat** : constantes `TYPES_BAIL` et `STATUTS`
- **Bien** : constantes `TYPES` et `STATUTS`

#### 🟡 Fonctionnalités ajoutées
- **Édition contrats** : formulaire `edit/update` complet avec tous les nouveaux champs
- **Édition utilisateurs** : formulaire `edit/update` pour propriétaires et locataires
- **Export PDF** : rapport financier mensuel en PDF (KPIs, détail paiements, récap par propriétaire, biens impayés)
- **Locale française** : `config/app.php` → `locale=fr`, `faker_locale=fr_FR`

#### 🔵 Qualité & Sécurité
- **Validation téléphone** : règle `TelephoneSenegalais` (préfixes 70/75/76/77/78/33/30)
- **Pages d'erreur** : 403, 404, 500 personnalisées en français
- **Genre cohérent** : validation `M/F` alignée avec la base de données (était `homme/femme`)
- **Méthode HTTP** : formulaire édition utilisateur corrigé `PATCH` (était `PUT`)

---

## 📄 Licence

Projet propriétaire — © BimoTech. Tous droits réservés.
