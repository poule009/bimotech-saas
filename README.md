# BIMO-Tech Immo

**Plateforme SaaS multi-agences de gestion immobilière pour le Sénégal**  
Laravel 12 · PHP 8.2 · MySQL · Tailwind CSS · PayTech

---

## Table des matières

1. [Présentation](#présentation)
2. [Fonctionnalités](#fonctionnalités)
3. [Architecture multi-agences](#architecture-multi-agences)
4. [Rôles et permissions](#rôles-et-permissions)
5. [Prérequis](#prérequis)
6. [Installation](#installation)
7. [Configuration](#configuration)
8. [Paiements & Abonnements (PayTech)](#paiements--abonnements-paytech)
9. [Moteur fiscal](#moteur-fiscal)
10. [Structure du projet](#structure-du-projet)
11. [Commandes artisan](#commandes-artisan)
12. [Tests](#tests)
13. [Notifications & emails](#notifications--emails)
14. [Sécurité](#sécurité)
15. [Changelog](#changelog)

---

## Présentation

BIMO-Tech Immo est une application web SaaS permettant à des **agences immobilières sénégalaises** de gérer leurs biens, contrats de location et paiements de loyers.

Chaque agence dispose de son propre espace isolé. Un **Super Administrateur** de la plateforme supervise l'ensemble des agences, leurs abonnements et leur activité.

> **Conçu pour le Sénégal** : devise FCFA, TVA 18%, BRS, calculs CGI, modes de paiement locaux (Wave, Orange Money, Free Money), validation des numéros sénégalais, villes et quartiers locaux, intégration PayTech.

---

## Fonctionnalités

### Gestion des biens
- Création, modification et suppression de biens immobiliers
- Galerie photos avec photo principale (JPG, PNG, WEBP — SVG exclu pour des raisons de sécurité XSS)
- Champs locaux : **quartier**, **commune**, **meublé** (oui/non)
- Statuts : `disponible`, `loué`, `en_travaux`
- Référence unique par agence
- Types : Appartement, Villa, Studio, Bureau, Commerce, Terrain, Maison, Entrepôt

### Gestion des contrats
- Création de contrats de location (bien ↔ locataire)
- **Types de bail** : habitation, commercial, mixte, saisonnier
- Champs financiers : frais d'agence, charges mensuelles, TOM (Taxe Ordures Ménagères), indexation annuelle, nombre de mois de caution
- **Garant** : nom, téléphone, adresse
- Référence bail unique par agence (validation à la création et à la modification)
- Suivi DGID : numéro de quittance, date d'enregistrement, montant du droit de bail
- Statuts : `actif`, `résilié`, `expiré`

### Gestion des paiements
- Enregistrement des loyers mensuels
- Calcul automatique via le moteur fiscal (TVA, BRS, commission HT/TTC, net propriétaire, prorata temporel)
- **Modes de paiement sénégalais** : Espèces, Wave, Orange Money, Free Money, E-Money, Virement, Chèque
- Génération de **quittances PDF** (DomPDF) avec données dynamiques de l'agence
- Détection des **doublons** (même contrat + même période)
- Statuts : `valide`, `annule`
- Aperçu fiscal en temps réel (AJAX) avant enregistrement

### Tableau de bord
- **Admin** : statistiques globales, graphiques mensuels, impayés du mois, onboarding
- **Propriétaire** : vue de ses biens et revenus (portail bailleurs)
- **Locataire** : historique de ses paiements et téléchargement des quittances

### Rapports financiers
- Rapport financier mensuel/annuel par agence
- **Export PDF** : en-tête agence, KPIs du mois, détail des paiements, récapitulatif par propriétaire, biens impayés
- Bilans fiscaux par propriétaire avec export PDF
- Portail bailleurs : suivi du portefeuille complet par bailleur
- Suivi des impayés avec relance par email

### Abonnements
- Plans : Mensuel (25 000 F), Trimestriel (67 500 F), Semestriel (127 500 F), Annuel (240 000 F)
- Période d'essai gratuite
- Intégration **PayTech** (simulation → test → prod)
- Rappels automatiques avant expiration
- Blocage de l'accès si abonnement expiré
- Historique complet des paiements d'abonnement

### Gestion des utilisateurs
- Création et modification des propriétaires et locataires
- Profil propriétaire : CNI, NINEA, TVA, Wave, Orange Money, banque, compte
- Profil locataire : profession, employeur, revenu mensuel, contact d'urgence
- Validation des numéros de téléphone sénégalais (70/75/76/77/78/33/30)

### Journal d'activité
- Traçabilité de toutes les actions (création, modification, suppression)
- Consultable par l'admin de l'agence et le super admin

---

## Architecture multi-agences

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

## Rôles et permissions

### Les 4 rôles

| Rôle | Description |
|---|---|
| `superadmin` | Administrateur de la plateforme BIMO-Tech. Gère les agences et abonnements. N'appartient à aucune agence. |
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
| Bilans fiscaux | ❌ | ✅ | ❌ | ❌ |
| Portail bailleurs | ❌ | ✅ | ❌ | ❌ |
| Impayés & relances | ❌ | ✅ | ❌ | ❌ |
| Journal d'activité | ✅ | ✅ | ❌ | ❌ |
| Paramètres agence | ❌ | ✅ | ❌ | ❌ |
| Gestion agences (plateforme) | ✅ | ❌ | ❌ | ❌ |
| Gestion abonnements (plateforme) | ✅ | ❌ | ❌ | ❌ |

### Implémentation technique

```
app/
├── Http/Middleware/
│   ├── IsSuperAdmin.php        ← Vérifie role === 'superadmin'
│   ├── IsAdmin.php             ← Vérifie role IN ('admin', 'superadmin')
│   ├── CheckSubscription.php   ← Bloque si abonnement expiré
│   └── CheckAgencyActif.php    ← Bloque si agence désactivée
│
├── Policies/
│   ├── BienPolicy.php          ← Permissions CRUD sur les biens
│   ├── ContratPolicy.php       ← Permissions CRUD sur les contrats
│   └── PaiementPolicy.php      ← Permissions CRUD + PDF sur les paiements
│
├── Rules/
│   └── TelephoneSenegalais.php ← Validation numéros sénégalais
│
└── Providers/
    └── AppServiceProvider.php  ← Définition des Gates
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

## Prérequis

- **PHP** >= 8.2
- **Composer** >= 2.x
- **MySQL** >= 8.0
- **Node.js** >= 18.x & **npm** >= 9.x
- Extensions PHP : `pdo_mysql`, `mbstring`, `openssl`, `tokenizer`, `xml`, `gd`
- Package Composer : `barryvdh/laravel-dompdf` (génération PDF)

---

## Installation

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

## Configuration

### Fichier `.env` — variables importantes

```env
# Application
APP_NAME="BIMO-Tech"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000
APP_LOCALE=fr
APP_TIMEZONE=UTC
APP_SUPPORT_EMAIL=contact@bimotech.sn  # Affiché dans les messages d'erreur aux utilisateurs

# Base de données
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=bimotech_immo
DB_USERNAME=root
DB_PASSWORD=

# Sessions (chiffrement activé par défaut en production)
SESSION_DRIVER=database
SESSION_LIFETIME=120
SESSION_ENCRYPT=true        # false uniquement en dev local si besoin
SESSION_SECURE_COOKIE=true  # false si pas encore HTTPS (développement)

# Email (Brevo, Mailgun ou Postmark recommandé pour le Sénégal)
MAIL_MAILER=smtp
MAIL_HOST=smtp-relay.brevo.com
MAIL_PORT=587
MAIL_USERNAME=votre_login_brevo
MAIL_PASSWORD=votre_cle_api_brevo
MAIL_FROM_ADDRESS="noreply@bimotech.sn"
MAIL_FROM_NAME="BIMO-Tech"

# Queue (notifications asynchrones)
QUEUE_CONNECTION=database

# PayTech (passerelle de paiement sénégalaise)
# Clés disponibles sur : https://paytech.sn → Tableau de bord → API
PAYTECH_MODE=simulation     # simulation | test | prod
PAYTECH_API_KEY=
PAYTECH_API_SECRET=
```

### Comptes de démonstration (après seeding)

| Rôle | Email | Mot de passe |
|---|---|---|
| Super Admin | superadmin@bimotech.com | password |
| Admin Agence | admin@agence-demo.com | password |
| Propriétaire | proprietaire@demo.com | password |
| Locataire | locataire@demo.com | password |

---

## Paiements & Abonnements (PayTech)

### Plans disponibles

| Plan | Prix | Durée | Économie |
|---|---|---|---|
| Mensuel | 25 000 FCFA | 1 mois | — |
| Trimestriel | 67 500 FCFA | 3 mois | 10% |
| Semestriel | 127 500 FCFA | 6 mois | 15% |
| Annuel | 240 000 FCFA | 12 mois | 20% |

### Modes de paiement acceptés

- Wave (Sénégal)
- Orange Money
- Free Money
- Virement bancaire
- Espèces (via admin)

### Flux de paiement

```
Agence → /subscription → Choisir plan
    → POST /subscription/initier
        → [SIMULATION] Activation immédiate (aucun appel API)
        → [TEST/PROD]  Redirection vers PayTech (https://paytech.sn)
            → Callback IPN → POST /subscription/callback (throttle: 10 req/min)
            → Retour succès → GET /subscription/succes?ref={ref_command}
            → Retour échec  → GET /subscription/echec
```

### Sécurité du flux PayTech

- **Vérification HMAC** : chaque IPN est authentifié via `HMAC-SHA256(amount|ref|api_key, api_secret)`.  
  En production (`PAYTECH_MODE=prod`), le fallback SHA256 est refusé — seul HMAC est accepté.
- **Whitelist de domaine** : les redirections vers PayTech sont vérifiées pour ne pointer que vers `https://paytech.sn/`.
- **Idempotence** : un IPN déjà traité pour une même `ref_command` est ignoré silencieusement.
- **Verrou DB** : `lockForUpdate()` empêche les doubles activations en cas d'IPN concurrent.
- **Rate limiting** : le endpoint IPN est limité à 10 requêtes/minute.

### Activer le mode prod

1. Obtenir les clés sur [paytech.sn](https://paytech.sn)
2. Mettre à jour `.env` :

```env
PAYTECH_MODE=prod
PAYTECH_API_KEY=votre_api_key
PAYTECH_API_SECRET=votre_api_secret
```

---

## Moteur fiscal

Le calcul fiscal est centralisé dans `app/Services/FiscalService.php` et son contexte `FiscalContext`.

### Règles appliquées (CGI Sénégal)

| Taxe | Condition | Base de calcul |
|---|---|---|
| TVA loyer (18%) | `loyer_assujetti_tva = true` | Loyer nu |
| BRS (Baux à Réhabilitation) | `brs_applicable = true` | Loyer nu |
| Commission HT | Taux agence (% du loyer nu) | Loyer nu |
| TVA commission (18%) | Toujours | Commission HT |
| Prorata temporel | 1er paiement, entrée hors 1er du mois | Loyer + charges + TOM |

### Champs DGID sur les contrats

Les contrats exposent des champs dédiés au suivi de l'enregistrement DGID :
- `date_enregistrement_dgid`
- `numero_quittance_dgid`
- `montant_droit_de_bail`
- `enregistrement_exonere`

> Ces champs sont saisis manuellement. L'envoi automatique vers l'API DGID n'est pas encore intégré.

---

## Structure du projet

```
bimotech-immo/
│
├── app/
│   ├── Console/Commands/           # Commandes artisan planifiées
│   ├── Enums/                      # BienStatut, ContratStatut, UserRole
│   ├── Http/
│   │   ├── Controllers/            # Contrôleurs (Admin, SuperAdmin, Locataire...)
│   │   │   ├── Admin/              # AgencySettingsController, BilanFiscalController
│   │   │   ├── Auth/               # AgencyRegistrationController
│   │   │   ├── Dashboard/          # AdminDashboard, LocataireDashboard, ProprietaireDashboard
│   │   │   └── SuperAdmin/         # SuperAdminController
│   │   ├── Middleware/             # Middlewares d'authentification et de rôles
│   │   └── Requests/               # StoreContratRequest, UpdateContratRequest...
│   ├── Models/
│   │   ├── Scopes/AgencyScope.php  # Filtre automatique par agence
│   │   └── Traits/LogsActivity.php # Trait de journalisation
│   ├── Notifications/              # Emails (quittances, relances, abonnements...)
│   ├── Policies/                   # BienPolicy, ContratPolicy, PaiementPolicy
│   ├── Providers/
│   │   └── AppServiceProvider.php  # Enregistrement des Gates et Policies
│   ├── Rules/
│   │   └── TelephoneSenegalais.php # Validation numéros sénégalais
│   └── Services/
│       ├── FiscalContext.php        # Contexte de calcul fiscal (données d'entrée)
│       ├── FiscalResult.php         # Résultat de calcul fiscal (données de sortie)
│       ├── FiscalService.php        # Moteur fiscal CGI Sénégal
│       ├── NombreEnLettres.php      # Conversion montant → lettres (quittances)
│       └── PaymentService.php       # Intégration PayTech (simulation/test/prod)
│
├── database/
│   ├── factories/                  # Factories pour les tests
│   ├── migrations/                 # Migrations de la base de données
│   └── seeders/                    # Données de démonstration
│
├── resources/
│   ├── css/                        # Styles (Tailwind + bimotech.css)
│   ├── js/                         # JavaScript
│   └── views/
│       ├── admin/                  # Vues admin (dashboard, contrats CRUD...)
│       ├── bailleurs/              # Portail bailleurs
│       ├── superadmin/             # Vues super admin
│       ├── proprietaire/           # Dashboard propriétaire
│       ├── locataire/              # Dashboard locataire
│       ├── biens/                  # CRUD biens
│       ├── paiements/              # CRUD paiements + PDF quittance
│       ├── rapports/               # Rapport financier + export PDF
│       ├── subscription/           # Abonnement + pages succès/échec PayTech
│       ├── users/                  # CRUD utilisateurs
│       ├── errors/                 # Pages d'erreur personnalisées (403, 404, 500)
│       └── layouts/                # Layouts partagés
│
├── routes/
│   ├── web.php                     # Routes web (avec middlewares de rôles)
│   ├── auth.php                    # Routes d'authentification
│   └── console.php                 # Planification des tâches
│
└── tests/
    ├── Feature/                    # Tests d'intégration
    └── Unit/                       # Tests unitaires
```

---

## Commandes artisan

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

Toutes ces commandes sont planifiées dans `routes/console.php`. Pour les activer en production :

```bash
* * * * * cd /chemin/vers/projet && php artisan schedule:run >> /dev/null 2>&1
```

---

## Tests

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

> Pour des tests sans MySQL, configurer `DB_CONNECTION=sqlite` dans `.env.testing` :
>
> ```env
> DB_CONNECTION=sqlite
> DB_DATABASE=:memory:
> ```

---

## Notifications & emails

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

## Sécurité

### Mesures en place

| Domaine | Mesure |
|---|---|
| Multi-tenancy | `AgencyScope` global sur tous les modèles — isolation stricte par agence |
| Autorisation | Policies Laravel (`BienPolicy`, `ContratPolicy`, `PaiementPolicy`) + Gates |
| CSRF | Protection Laravel activée sur toutes les routes POST/PATCH/DELETE |
| Sessions | Chiffrement activé par défaut (`SESSION_ENCRYPT=true`), cookies `HttpOnly` + `SameSite=lax` |
| Uploads | Formats raster uniquement (JPG, PNG, WEBP) — SVG exclu (risque XSS) |
| PayTech IPN | Vérification HMAC-SHA256 obligatoire en production, fallback SHA256 refusé |
| Redirect PayTech | Whitelist `https://paytech.sn/` avant toute redirection externe |
| Idempotence | Double-traitement des IPN bloqué par vérification `ref_command` + `lockForUpdate()` |
| Rate limiting | Endpoint IPN limité à 10 requêtes/minute |
| Mass assignment | `$fillable` strict sur tous les modèles — `role` et `agency_id` exclus du User |
| SQL | Requêtes paramétrées Eloquent — aucun `whereRaw` non bindé |
| Logs | Credentials PayTech jamais loggés — seules les métadonnées (IP, ref) sont tracées |

### Variables sensibles

Ne jamais committer les valeurs réelles de ces variables :

```
APP_KEY
DB_PASSWORD
PAYTECH_API_KEY
PAYTECH_API_SECRET
MAIL_PASSWORD
```

---

## Changelog

### v1.3.0 — Audit sécurité & corrections (Avril 2026)

#### Sécurité
- **Open redirect bloqué** : whitelist `https://paytech.sn/` sur les redirections PayTech avant `redirect()->away()`
- **HMAC-only en production** : le fallback SHA256 (vérification IPN) est refusé en mode `prod` avec log d'avertissement
- **SESSION_ENCRYPT** : valeur par défaut passée à `true` (était `false`)
- **Rate limit IPN** : réduit de 60 à 10 requêtes/minute sur `/subscription/callback`
- **Email support** : centralisé dans `config('app.support_email')` via `APP_SUPPORT_EMAIL`, plus d'adresse hardcodée dans le middleware

#### Performance
- **N+1 corrigé dans BienPhotoController** : `photos()->max('ordre')` et `photos()->count()` sortis de la boucle d'upload (2 requêtes au lieu de 2×N)
- **whereRaw supprimé dans PaiementController** : filtre par mois remplacé par `whereYear()`/`whereMonth()` (plus sûr et plus lisible)

#### Validation
- **Unicité `reference_bail` par agence** : règle `Rule::unique` ajoutée dans `StoreContratRequest` et `UpdateContratRequest` (ignore le contrat courant lors d'une mise à jour)

---

### v1.2.0 — Portail bailleurs, bilans fiscaux, dashboards (Avril 2026)

- Portail bailleurs avec suivi du portefeuille par bailleur (PDF inclus)
- Bilans fiscaux par propriétaire avec export PDF
- Moteur fiscal refactorisé : `FiscalService` + `FiscalContext` + `FiscalResult`
- Prorata temporel sur le premier paiement en cours de mois
- Aperçu fiscal AJAX en temps réel avant enregistrement d'un paiement
- Champs DGID sur les contrats (numéro quittance, date enregistrement, montant droit de bail)
- Dashboard locataire enrichi
- Indexes composites sur `(agency_id, statut)` et colonnes fréquemment filtrées

---

### v1.1.0 — Migration PayDunya → PayTech & durcissement sécurité (Avril 2026)

- **PayDunya remplacé par PayTech** (passerelle sénégalaise) : nouveau `PaymentService` avec modes simulation/test/prod
- Vérification HMAC-SHA256 des IPN PayTech + idempotence via `ref_command` + `lockForUpdate()`
- Page succès/échec retour PayTech avec gestion de session expirée
- Enums PHP 8.1 (`BienStatut`, `ContratStatut`, `UserRole`) remplaçant les constantes string
- `TelescopeServiceProvider` conditionné à la présence du package en production

---

### v1.0.0 — Version initiale (Mars 2026)

- Gestion multi-agences complète : biens, contrats, paiements, utilisateurs
- Architecture multi-tenant avec `AgencyScope`
- Policies d'autorisation (`BienPolicy`, `ContratPolicy`, `PaiementPolicy`)
- Génération de quittances PDF avec DomPDF
- Notifications email : paiement propriétaire, relance impayé, rappel abonnement
- Journal d'activité
- Pages d'erreur personnalisées (403, 404, 500)
- Validation numéros sénégalais (`TelephoneSenegalais`)
- Commandes artisan planifiées (loyers mensuels, abonnements, rapport hebdomadaire)

---

## Licence

Projet propriétaire — © BIMO-Tech. Tous droits réservés.
