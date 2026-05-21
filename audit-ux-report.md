# Audit UX — Bimothèque Immo
> Généré le 2026-05-10 · Portée : routes/web.php + 95 vues Blade

---

## Résumé exécutif

1. **Responsive inexistant** — La sidebar est fixe à 248 px sans aucun breakpoint mobile. L'application est inutilisable sur téléphone et tablette, or les agences sénégalaises opèrent massivement sur mobile.
2. **Incohérence majeure dark/light** — Certaines pages (Agency Settings, Subscription) sont en dark mode (`#161b22`) alors que toutes les autres sont en light mode. Trois couleurs de bouton CTA coexistent sans raison.
3. **Données fictives en production** — Le graphique "Répartition des biens" du dashboard admin affiche des valeurs hardcodées `[37, 25, 19, 19]`. Un décideur peut prendre une mauvaise décision sur ces chiffres.
4. **Section Bailleurs inaccessible depuis la nav** — La route `admin.bailleurs.index` (relevés PDF, portefeuille propriétaire) existe mais n'est pas dans le menu sidebar. Elle est donc invisible pour tous les admins.
5. **Bug logique silencieux dans le layout** — La condition d'affichage des erreurs globales (`line 240, layouts/app.blade.php`) est toujours fausse due à une précédence d'opérateurs. Des erreurs peuvent passer sans être signalées.

---

## Problèmes classés par sévérité

### 🔴 Critique

---

**C-01 · Responsive absent — Application inutilisable sur mobile**
- **Fichier :** [resources/views/layouts/app.blade.php:39](resources/views/layouts/app.blade.php#L39)
- **Description :** `margin-left: 248px` est hardcodé sans media query. Les grilles KPI (`repeat(4,1fr)`), le bilan (`1fr 1fr 1fr`) et les formulaires en `grid-template-columns:1fr 320px` cassent sur tout écran < 900 px. La sidebar fixe couvre la totalité du contenu.
- **Correctif :** Ajouter au minimum :
  ```css
  @media (max-width: 768px) {
    .main-wrapper { margin-left: 0; }
    .bm-sidebar-wrap { transform: translateX(-100%); transition: transform .25s; }
    .bm-sidebar-wrap.open { transform: translateX(0); }
    .kpi-grid, .kpi-row { grid-template-columns: repeat(2,1fr) !important; }
    .bilan, .form-grid, .dash-grid { grid-template-columns: 1fr !important; }
  }
  ```
  Ajouter un bouton hamburger dans la topbar pour toggler la sidebar.

---

**C-02 · Données hardcodées en production (graphique Répartition des biens)**
- **Fichier :** [resources/views/admin/dashboard.blade.php:462-483](resources/views/admin/dashboard.blade.php#L462)
- **Description :** `data: [37, 25, 19, 19]` — ces pourcentages sont fictifs. Le commentaire dans le code indique "À terme : injecter les vraies données." Ce graphique est affiché aux admins comme une vraie statistique.
- **Correctif :** Calculer dans `AdminDashboardController` :
  ```php
  $repartitionTypes = $biens->groupBy('type')
      ->map(fn($g) => $g->count())
      ->toArray();
  ```
  Et injecter via `@json($repartitionTypes)`. Masquer le graphique s'il n'y a aucune donnée.

---

**C-03 · Bailleurs inaccessibles depuis la navigation**
- **Fichier :** [resources/views/components/sidebar.blade.php:14-36](resources/views/components/sidebar.blade.php#L14)
- **Description :** La route `admin.bailleurs.index` (portefeuille bailleur, relevés PDF) est totalement absente du tableau `$navAdmin`. Un administrateur ne peut pas naviguer vers cette section sans connaître l'URL directe.
- **Correctif :** Ajouter dans `$navAdmin` sous la section PATRIMOINE :
  ```php
  ['section' => null, 'route' => 'admin.bailleurs.index', 'label' => 'Portefeuille bailleurs'],
  ```
  Et ajouter l'icône correspondante dans `$svgs`.

---

**C-04 · Bug logique — Erreurs de validation silencieuses dans le layout**
- **Fichier :** [resources/views/layouts/app.blade.php:240](resources/views/layouts/app.blade.php#L240)
- **Description :** `@if($errors->any() && !$errors->hasBag('default') === false)` — l'opérateur `!` s'applique à `$errors->hasBag('default')` avant `=== false`, ce qui donne toujours `false`. Cette condition est donc toujours fausse et le bloc `@endif` ne s'exécute jamais. Les erreurs globales ne remontent jamais via le layout.
- **Correctif :** Supprimer cette condition inutile (les erreurs sont déjà gérées dans chaque vue individuellement) ou corriger en `@if($errors->any() && $errors->hasBag('default') === false)`.

---

**C-05 · Navigation propriétaire pointe vers des routes admin non protégées**
- **Fichier :** [resources/views/components/sidebar.blade.php:38-43](resources/views/components/sidebar.blade.php#L38)
- **Description :** Dans `$navProprietaire`, "Mes biens" → `admin.biens.index`, "Mes contrats" → `admin.contrats.index`, "Paiements" → `admin.impayes.index`. Ces routes sont sous middleware `can:isStaff`. Si un propriétaire peut y accéder, il voit les données de TOUS les biens/contrats de l'agence, pas seulement les siens. Le libellé "Paiements" pointant vers les impayés est aussi sémantiquement incorrect.
- **Correctif :** Créer des routes propriétaire dédiées (`proprietaire.biens.index`, `proprietaire.contrats.index`) qui filtrent sur `proprietaire_id = auth()->id()`. Renommer "Paiements" en "Impayés" ou créer `proprietaire.paiements.index`.

---

### 🟠 Élevé

---

**E-01 · Incohérence dark/light mode — 3 palettes de fond coexistent**
- **Fichiers :** [resources/views/admin/agency-settings.blade.php](resources/views/admin/agency-settings.blade.php), [resources/views/subscription/index.blade.php](resources/views/subscription/index.blade.php)
- **Description :** Agency Settings et Subscription utilisent `background:#161b22` (dark). Toutes les autres pages admin utilisent `#fff`/`#f9fafb`. L'utilisateur perçoit deux applications différentes en naviguant.
- **Correctif :** Migrer ces deux pages vers les classes `.card` et variables CSS du layout light, ou décider officiellement d'un dark mode global et l'appliquer partout.

---

**E-02 · 3 couleurs de bouton CTA primaire sans cohérence**
- **Fichiers :** [resources/views/paiements/create.blade.php:60](resources/views/paiements/create.blade.php#L60), [resources/views/admin/contrats/create.blade.php:157](resources/views/admin/contrats/create.blade.php#L157), [resources/views/users/create.blade.php:155](resources/views/users/create.blade.php#L155)
- **Description :** Trois couleurs de bouton submit `btn-submit` : `#2a4a7f` (bleu, paiements + contrats), `#0d1117` (noir, users/create), et `var(--ac)` (or agency, topbar). Le même bouton submit dans 3 couleurs différentes crée une confusion sur la hiérarchie des actions.
- **Correctif :** Utiliser systématiquement `var(--ac)` (couleur agence) pour tous les boutons submit primaires. Réserver `#2a4a7f` ou `#0d1117` uniquement pour les actions secondaires destructives.

---

**E-03 · Formulaire de création de contrat : 7 sections sans wizard ni sauvegarde auto**
- **Fichier :** [resources/views/admin/contrats/create.blade.php](resources/views/admin/contrats/create.blade.php)
- **Description :** Le formulaire contient 7 sections (Bien/Locataire, Durée, Loyer, Caution, Fiscal, Garant, Observations) sur une seule page. En cas d'erreur serveur, tout le formulaire est remis à zéro sauf les `old()` values. Pour un bail commercial (fiscal complexe), un admin peut passer 10+ min sur ce formulaire.
- **Correctif :** Conserver la single-page mais ajouter une barre de progression visuelle en haut (style wizard non-bloquant). S'assurer que tous les champs utilisent `old()`. Envisager un auto-save en brouillon via AJAX pour les formulaires > 5 min.

---

**E-04 · Pas de recherche globale dans l'interface**
- **Description :** Aucune barre de recherche dans le layout principal. Pour trouver un locataire spécifique parmi 200, l'admin doit aller dans Locataires, paginer, ou filtrer. Il n'existe aucun moyen de chercher "Mohamed Fall" depuis n'importe quelle page.
- **Correctif :** Ajouter dans la topbar une barre de recherche globale avec raccourci clavier (Ctrl+K) qui effectue une recherche unifiée sur biens (référence), locataires (nom) et contrats (référence).

---

**E-05 · Modal locataire-rapide sans état de chargement**
- **Fichier :** [resources/views/admin/contrats/create.blade.php:630-674](resources/views/admin/contrats/create.blade.php#L630)
- **Description :** Dans la modal "Nouveau locataire" du formulaire contrat, le bouton "Créer et sélectionner" ne se désactive pas pendant l'appel `fetch`. L'utilisateur peut cliquer plusieurs fois et créer des doublons. Aucun spinner de chargement.
- **Correctif :**
  ```js
  async function creerLocataire() {
      const btn = document.querySelector('#modal-locataire .btn-gold');
      btn.disabled = true;
      btn.textContent = 'Création...';
      try { /* ... */ } finally {
          btn.disabled = false;
          btn.textContent = 'Créer et sélectionner';
      }
  }
  ```

---

**E-06 · Dashboard admin : graphique "Net propriétaires" affiche les loyers par mois (données incorrectes)**
- **Fichier :** [resources/views/admin/dashboard.blade.php:487-523](resources/views/admin/dashboard.blade.php#L487)
- **Description :** Le graphique intitulé "Net reversé par propriétaire" (`chartProprio`) utilise `@json($loyersParMois->pluck('total'))` — ce sont les loyers globaux par mois, pas par propriétaire. Le titre est donc trompeur.
- **Correctif :** Soit changer le titre en "Évolution mensuelle des loyers", soit calculer réellement le net par propriétaire dans le contrôleur et injecter les vraies données.

---

**E-07 · Relance impayés utilise confirm() natif au lieu de la modale globale**
- **Fichier :** [resources/views/impayes/index.blade.php:222-223](resources/views/impayes/index.blade.php#L222)
- **Description :** `onclick="return confirm('Envoyer une relance email à ce locataire ?')"` — utilise la boîte de dialogue native du navigateur, sans cohérence avec la modale `#g-confirm-overlay` déjà configurée globalement. Même problème sur les boutons de la page Subscription (`onsubmit="return confirm(...)"`, ligne 298).
- **Correctif :** Remplacer par `data-confirm="Envoyer une relance email à {{ $item['contrat']->locataire?->name }} ?" data-confirm-title="Relancer le locataire" data-confirm-ok="Envoyer"` sur le formulaire pour utiliser la modale globale.

---

**E-08 · Profil utilisateur non accessible depuis la sidebar**
- **Fichier :** [resources/views/components/sidebar.blade.php:366-387](resources/views/components/sidebar.blade.php#L366)
- **Description :** Le footer de la sidebar affiche le nom et l'email de l'utilisateur mais ne rend pas ce bloc cliquable. Il n'y a aucun lien vers `/profile`. Pour modifier son profil (mot de passe, email), l'utilisateur doit connaître l'URL.
- **Correctif :** Rendre `.bm-profile` un lien `<a href="{{ route('profile.edit') }}">` avec un tooltip "Mon profil".

---

### 🟡 Moyen

---

**M-01 · Flash messages disparaissent en 5 secondes (trop rapide)**
- **Fichier :** [resources/views/layouts/app.blade.php:344-349](resources/views/layouts/app.blade.php#L344)
- **Description :** `setTimeout(5000)` — 5 secondes est insuffisant pour lire un message d'erreur fiscal complexe (ex : "Le BRS 5% n'a pas pu être calculé car le NINEA du locataire est absent"). Sur mobile, l'utilisateur peut rater le message.
- **Correctif :** Augmenter à 8000ms (8s) pour les messages `flash-error` et `flash-warning`. Laisser 5s pour les succès.

---

**M-02 · États vides incohérents entre les vues**
- **Fichiers :** [resources/views/proprietaire/dashboard.blade.php:201-203](resources/views/proprietaire/dashboard.blade.php#L201), [resources/views/locataire/dashboard.blade.php:303-305](resources/views/locataire/dashboard.blade.php#L303)
- **Description :** Certaines vues utilisent le composant `<x-empty-state>` (biens/index), d'autres ont des `<div style="text-align:center;color:#9ca3af">` inline. Le dashboard propriétaire affiche "Aucun bien associé à votre compte." sans action suggérée. La vue locataire affiche "Aucun paiement enregistré pour l'instant." sans CTA non plus.
- **Correctif :** Standardiser sur `<x-empty-state>` dans toutes les listes et tableaux. Ajouter un CTA contextuel (ex : "Contacter votre agence" pour le locataire sans contrat).

---

**M-03 · Tableau des paiements locataire sans filtre par période**
- **Fichier :** [resources/views/locataire/paiements.blade.php](resources/views/locataire/paiements.blade.php)
- **Description :** La vue des quittances du locataire n'a aucun filtre. Si un locataire est là depuis 3 ans (36 paiements), retrouver la quittance de décembre 2023 nécessite de paginer.
- **Correctif :** Ajouter un filtre `<select name="annee">` pour filtrer par année. 10 lignes de code PHP + 1 `GET` parameter.

---

**M-04 · Formulaire création utilisateur : scrollTo() redéfini**
- **Fichier :** [resources/views/users/create.blade.php:613](resources/views/users/create.blade.php#L613)
- **Description :** `function scrollTo(id)` redéfinit `window.scrollTo`. Certains navigateurs peuvent se comporter de façon inattendue. Les boutons de navigation latérale dans ce formulaire peuvent ne pas fonctionner.
- **Correctif :** Renommer en `scrollToSection(id)` et mettre à jour tous les `onclick`.

---

**M-05 · Confirmation d'abonnement avec prix hardcodé dans la vue**
- **Fichier :** [resources/views/subscription/index.blade.php:298](resources/views/subscription/index.blade.php#L298)
- **Description :** `return confirm('Confirmer l\'abonnement {{ $p['label'] }} à {{ $p['total'] }} FCFA ?')` — le prix est interpolé dans un `confirm()` natif. Si les prix changent côté serveur mais pas côté vue, l'utilisateur voit un prix incorrect avant de payer.
- **Correctif :** Utiliser la modale globale avec `data-confirm` et `data-confirm-title`. Les prix sont correctement injectés depuis PHP.

---

**M-06 · Navigation impayés : mois futur accessible en URL directe**
- **Fichier :** [resources/views/impayes/index.blade.php:79-87](resources/views/impayes/index.blade.php#L79)
- **Description :** La flèche "mois suivant" est masquée si le mois est futur, mais rien n'empêche l'accès via URL directe (`?mois=12&annee=2026`). Le contrôleur doit valider côté serveur.
- **Correctif :** Dans `ImpayeController::index()`, rejeter les périodes futures avec une redirection vers le mois courant.

---

**M-07 · Labels de la topbar en mixte français/anglais**
- **Fichier :** [resources/views/layouts/app.blade.php:222](resources/views/layouts/app.blade.php#L222)
- **Description :** `$header ?? 'Dashboard'` — le fallback "Dashboard" est en anglais. La topbar peut donc afficher "Votre agence › Dashboard" sur certaines pages.
- **Correctif :** Remplacer par `$header ?? 'Tableau de bord'`.

---

**M-08 · Selector de biens dans le formulaire contrat non filtré par "disponible"**
- **Fichier :** [resources/views/admin/contrats/create.blade.php:81-93](resources/views/admin/contrats/create.blade.php#L81)
- **Description :** Le `<select>` des biens affiche "Sélectionner un bien disponible" mais liste potentiellement tous les biens incluant les biens déjà loués, en travaux ou archivés. Un admin peut créer un contrat sur un bien déjà occupé.
- **Correctif :** Dans le contrôleur, filtrer `->where('statut', 'disponible')`. Ajouter le statut visiblement dans chaque `<option>` pour que l'admin puisse voir si un bien est disponible ou non.

---

**M-09 · Graphiques du dashboard sans état vide ni message "données insuffisantes"**
- **Fichier :** [resources/views/admin/dashboard.blade.php:399-548](resources/views/admin/dashboard.blade.php#L399)
- **Description :** Quand une nouvelle agence s'inscrit (zéro biens, zéro paiements), les graphiques Chart.js affichent des axes vides ou des courbes plates sans message explicatif. L'impression est celle d'un bug.
- **Correctif :** Ajouter dans le JS une condition : si toutes les données sont à 0, remplacer le canvas par un message "Ajoutez vos premiers biens pour voir les statistiques."

---

**M-10 · Tooltip CSS défini mais peu utilisé dans les formulaires**
- **Fichier :** [resources/views/layouts/app.blade.php:163-167](resources/views/layouts/app.blade.php#L163)
- **Description :** Le système `.tip-icon[data-tip]` est implémenté dans le layout mais n'est utilisé que dans la section fiscale des contrats. Des champs complexes comme "NINEA", "TOM", "BRS", "Indexation annuelle", "Loyer nu" n'ont pas de tooltip alors qu'ils déroulent des novices.
- **Correctif :** Ajouter `<i class="tip-icon" data-tip="...">?</i>` systématiquement sur : NINEA, TOM, BRS, Loyer nu, Indexation annuelle, RCCM.

---

### 🟢 Faible

---

**F-01 · Bouton "Choisir" générique sur la page abonnement**
- **Fichier :** [resources/views/subscription/index.blade.php:301](resources/views/subscription/index.blade.php#L301)
- **Description :** Tous les CTA s'appellent "Choisir" sans différenciation. En termes d'accessibilité (screen readers), 4 boutons "Choisir" sans contexte distinct est problématique.
- **Correctif :** Remplacer par `aria-label="S'abonner au plan {{ $p['label'] }}"` et optionnellement le texte par "Choisir {{ $p['label'] }}".

---

**F-02 · Avatar utilisateur dans la sidebar : initiale unique**
- **Fichier :** [resources/views/components/sidebar.blade.php:369](resources/views/components/sidebar.blade.php#L369)
- **Description :** `strtoupper(substr($user->name ?? 'U', 0, 1))` — affiche seulement 1 caractère. Pour "Amadou Diallo", on affiche "A". Le risque de collision avec d'autres utilisateurs "A..." est élevé.
- **Correctif :** Utiliser 2 caractères (initiales prénom + nom) : `strtoupper(substr($parts[0] ?? 'U', 0, 1) . substr($parts[1] ?? '', 0, 1))`.

---

**F-03 · Fil d'Ariane topbar non clickable sur toutes les pages**
- **Fichier :** [resources/views/layouts/app.blade.php:220](resources/views/layouts/app.blade.php#L220)
- **Description :** Le breadcrumb affiche "AgencyName › Page" mais la page actuelle (en `<strong>`) n'est jamais clickable et le séparateur "›" n'est pas un vrai séparateur ARIA. Pour les formulaires imbriqués (contrats/create), il n'y a qu'un niveau alors qu'il pourrait y en avoir 2 (Contrats → Créer).
- **Correctif :** Ajouter `aria-label="Fil d'ariane"` sur le nav breadcrumb. Pour les pages create/edit, permettre l'injection d'un breadcrumb multi-niveaux via un slot.

---

**F-04 · Émojis dans les formulaires utilisateur (style potentiellement inadapté)**
- **Fichier :** [resources/views/users/create.blade.php:309](resources/views/users/create.blade.php#L309)
- **Description :** `👨 Homme` / `👩 Femme` dans les radio buttons, `🏢 Entreprise`, `👤 Particulier`, etc. Les émojis peuvent s'afficher différemment selon l'OS (Windows 10 vs Android) et semblent informels pour un SaaS professionnel destiné à des agences.
- **Correctif :** Remplacer les émojis par des icônes SVG cohérentes avec le reste de l'interface pour maintenir le registre professionnel.

---

**F-05 · Page 403 manquante ou non personnalisée**
- **Fichier :** [resources/views/errors/403.blade.php](resources/views/errors/403.blade.php)
- **Description :** La page 403 existe mais n'a pas été vérifiée comme étant cohérente avec la 404. Elle devrait guider l'utilisateur vers son dashboard selon son rôle, pas vers la page d'accueil publique.
- **Correctif :** Dans errors/403.blade.php, si `auth()->check()`, afficher un lien vers `route('dashboard')` plutôt que vers `url('/')`.

---

## Victoires rapides (< 30 min chacune)

| # | Action | Fichier | Temps estimé |
|---|--------|---------|-------------|
| **VR-1** | Corriger le bug de condition dans app.blade.php ligne 240 | [layouts/app.blade.php:240](resources/views/layouts/app.blade.php#L240) | 2 min |
| **VR-2** | Ajouter la route Bailleurs dans `$navAdmin` de la sidebar | [components/sidebar.blade.php:14](resources/views/components/sidebar.blade.php#L14) | 5 min |
| **VR-3** | Remplacer `confirm()` natif des impayés et abonnements par `data-confirm` | [impayes/index.blade.php:222](resources/views/impayes/index.blade.php#L222) + [subscription/index.blade.php:298](resources/views/subscription/index.blade.php#L298) | 10 min |
| **VR-4** | Changer le fallback breadcrumb "Dashboard" → "Tableau de bord" | [layouts/app.blade.php:222](resources/views/layouts/app.blade.php#L222) | 1 min |
| **VR-5** | Renommer `scrollTo()` en `scrollToSection()` dans users/create | [users/create.blade.php:613](resources/views/users/create.blade.php#L613) | 5 min |

---

## Dette UX (problèmes structurels à planifier)

### D-01 · Responsive Design — Sprint dédié (3-5 jours)
L'absence totale de responsive est un problème structurel. Il ne s'agit pas d'ajouter quelques media queries : la sidebar fixe, les grilles CSS fixes et l'absence de viewport-adaptive layouts nécessitent une refonte du layout principal. Recommandation : adopter une approche mobile-first avec la sidebar qui se transforme en drawer sur mobile, et convertir toutes les grilles en `grid-template-columns: repeat(auto-fit, minmax(MIN, 1fr))`.

### D-02 · Système de design unifié
Il n'existe pas de fichier CSS global partagé. Chaque vue Blade redéfinit ses propres `.card`, `.dt`, `.kpi`, etc. dans des `<style>` inline. Résultat : 3 couleurs de bouton, 2 thèmes de fond, des tailles de texte légèrement différentes d'une page à l'autre. Recommandation : extraire un `app.css` partagé avec les variables CSS et les composants communs, et vider les `<style>` redondants dans chaque vue.

### D-03 · Recherche globale et filtres avancés
L'application gère des centaines de biens, locataires et contrats. Sans recherche globale ni filtres croisés (ex : "biens disponibles à Dakar, appartement, > 200k"), l'efficacité opérationnelle des agences est limitée. À planifier : barre de recherche globale (Cmd+K), filtres persistants en URL sur les pages index.

### D-04 · Parcours propriétaire sécurisé
Les routes de la nav propriétaire pointent vers des routes admin. Il faut créer des contrôleurs et routes dédiés au rôle propriétaire avec une portée restreinte aux données personnelles. Ce chantier touche le routeur, les middlewares, les contrôleurs et les vues.

### D-05 · Notifications en temps réel
Les locataires et propriétaires n'ont aucun mécanisme de notification (email ou in-app) quand un paiement est enregistré, quand un contrat approche de son terme, ou quand une relance est émise. L'intégration de Laravel Notifications (email + éventuellement push PWA) est à planifier.

### D-06 · Formulaire de contrat — Mode wizard ou brouillon
Le formulaire de création de contrat est le formulaire le plus complexe et le plus long de l'application. Sans sauvegarde automatique ni mode wizard, une erreur serveur oblige à tout ressaisir. Planifier un mécanisme de brouillon (sauvegarde partielle en session ou base de données).

---

## Cartographie des parcours par rôle

### Admin agence
| Action clé | Nb de clics | Problème |
|------------|-------------|---------|
| Créer un contrat | 2 clics | OK |
| Enregistrer un paiement | 2 clics | OK |
| Accéder au portefeuille bailleur | ∞ | Route absente de la nav 🔴 |
| Modifier un contrat existant | 3 clics | Contrats → liste → clic sur contrat → Modifier |
| Générer un relevé PDF bailleur | ∞ | Idem, route non accessible 🔴 |
| Voir les bilans fiscaux | 2 clics | OK |

### Propriétaire
| Action clé | Nb de clics | Problème |
|------------|-------------|---------|
| Voir ses paiements reçus | 1 clic | Dashboard uniquement |
| Télécharger une quittance | 2 clics | OK |
| Voir le détail d'un bien | 2 clics | Pointe vers admin 🟠 |
| Voir ses déclarations fiscales | ∞ | Aucune route propriétaire dédiée |

### Locataire
| Action clé | Nb de clics | Problème |
|------------|-------------|---------|
| Voir son prochain loyer | 1 clic | Dashboard, très visible ✅ |
| Télécharger quittance mensuelle | 2 clics | OK |
| Contacter l'agence | 2 clics | WhatsApp intégré ✅ |
| Savoir si son loyer est en retard | 1 clic | Dashboard, bannière verte ✅ |
| Filtrer ses quittances par année | ∞ | Pas de filtre 🟡 |

### SuperAdmin
| Action clé | Nb de clics | Problème |
|------------|-------------|---------|
| Créer une agence | 2 clics | OK |
| Activer un abonnement | 3 clics | OK |
| Rechercher une agence | ∞ | Pas de recherche 🟡 |
| Filtrer le journal d'activité par agence | ∞ | Pas de filtre 🟡 |

---

## Fonctionnalités présentes côté back mais sans interface

| Fonctionnalité | Contrôleur / Route | Statut |
|---------------|-------------------|--------|
| Portefeuille bailleurs (relevé PDF) | `admin.bailleurs.index` / `admin.bailleurs.show` | Route existe, pas dans nav 🔴 |
| Export CSV paiements | `admin.paiements.export-csv` | Route existe, non exposée dans les vues |
| Bail formel PDF | `admin.contrats.bail-formel-pdf` | Non exposé dans la vue show contrat |
| Rapport financier Export PDF | `admin.rapports.financier.export-pdf` | Exposé uniquement depuis le dashboard admin ✅ |
| États trimestriels CSV | `admin.etats-trimestriels.csv` | Non exposé dans la vue index |

---

*Rapport généré automatiquement par audit UX — Bimothèque Immo*
