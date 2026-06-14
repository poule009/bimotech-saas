# Plan 360° — Refonte UX/UI & Design System

**Produit :** Bimothèque Immo — SaaS B2B de gestion locative (Sénégal)
**Date :** juin 2026
**Source :** audit instrumenté (Chrome piloté : contrastes WCAG calculés, cibles tactiles mesurées, sémantique, poids réseau, parcours réels).

---

## 0. Objectif

Atteindre un niveau d'exécution international **tout en concevant nativement pour le terrain sénégalais** — sans importer les hypothèses occidentales (connexion rapide, grand écran, contraste subtil, densité). Le but n'est pas un « SaaS pro simplifié », mais un produit **conçu pour la façon dont les agences sénégalaises travaillent réellement**.

---

## 1. Principes directeurs

| # | Principe | Implication concrète |
|---|----------|----------------------|
| P-1 | **Contraste élevé, jamais subtil** | Soleil + écrans bas de gamme → tout texte utile ≥ 4.5:1 |
| P-2 | **Grosses cibles au pouce** | Usage mobile à une main → interactif ≥ 48px |
| P-3 | **Léger & tolérant au réseau** | 3G/coupures → pages courtes, images lazy + miniatures |
| P-4 | **Task-first, pas dashboard-first** | Guider (« quoi faire ce mois ») > étaler des chiffres |
| P-5 | **Natif mobile-money + WhatsApp** | Wave/OM/Free, relances WhatsApp en 1 tap |
| P-6 | **Langage simple** | Français clair ; wolof sur écrans clés (v2) |
| P-7 | **Confiance & conformité locale** | TVA 18 %, BRS, loi 81-18, DGID, quittances conformes = fossé concurrentiel |
| P-8 | **Un système, pas des écrans** | 1 composant par besoin, décliné — zéro réinvention par page |

> Règle d'or : **importer l'exigence occidentale, pas ses hypothèses.**

---

## 2. État des lieux (mesuré)

**Atouts à préserver :** « routine du mois » (task-first exemplaire), login, logique mobile-app (bottom-nav/FAB), localisation (FCFA/Wave/OM, fiscalité SN), formulaire d'encaissement avec décompte fiscal live, modale de confirmation destructive.

**Problèmes confirmés (chiffrés) :**
- **Accessibilité** : textes sous AA (badges 3.98, liens 3.07, labels caps 4.4) ; cibles 36px (< 48px) — 35 à 81 par page ; titres = `H1` seul (pas de `h2/h3`) ; 4–5 champs sans `<label>` par page.
- **Responsive** : palier « tablette » annoncé mais **non implémenté** (768px = layout mobile).
- **Marque** : 4 noms (bee / BiMO-Tech Immo / BIMO-Tech Immo / Bimothèque Immo).
- **Système visuel** : 3 traitements de carte différents ; rouge en aplat surutilisé (dilue le signal impayé) ; KPI sans hiérarchie ; data-viz décorative (sans axes/valeurs).
- **IA** : 30 liens de nav par page ; sections « argent » qui se recouvrent (Caisse/Compta/Trésorerie/Bilan/Fiscalité).
- **Perf** : 420–680 KB/page, 0 image aujourd'hui (bon) → **risque** dès l'activation des photos.
- **Bug** : jours d'échéance non arrondis (`19.133…j`).

**Baseline à suivre (KPIs) :**
- Contraste : ~3–4.4 (cible ≥ 4.5)
- Cibles < 40px : 35–81/page (cible 0 sur mobile)
- Poids dashboard : 683 KB (cible < 400 KB hors photos)
- Noms de marque : 4 (cible 1)
- Liens de nav : 30 (cible ≤ ~18, ≤ 5 sections)

---

## 3. Chantiers (workstreams)

### A. Marque & identité
- Choisir **un nom + une casse** ; le propager (logo, sidebar, fil d'ariane, `<title>`, emails, PDF).
- Mini-guide de marque (logo, couleurs, ton).

### B. Design system (fondations)
- **Tokens contraste-safe** : revoir les opacités texte (`/40`–`/50` → `/70` pour info utile ; réserver `/50` aux méta < 11px caps).
- **Composant « carte entité » unique** (avatar/initiales + titre + 2-3 méta + 1 action primaire + overflow) → remplace Biens / Propriétaires / Locataires.
- **Hiérarchie KPI** : 1 « chiffre roi » par écran, secondaires atténués.
- **Système de boutons** clarifié (primaire/secondaire/ghost/danger) — déjà cadré dans CLAUDE.md, à appliquer.
- **Rouge = alerte only** : sortir le rouge des panneaux info (cartes proprio, décomptes fiscaux) → blanc + accent fin.
- **États standards** : vide (avec action), chargement (skeleton), erreur, succès.
- `:focus-visible` visible sur tout interactif.

### C. Accessibilité (transversal, P-1/P-2)
- Contraste ≥ 4.5 (≥ 3 large) partout.
- Cibles ≥ 48px sur mobile (nav, icônes d'action, checkbox).
- Sémantique : 1 `h1` + `h2/h3` de section par page.
- `<label>` (ou `aria-label`) sur tout champ, y compris filtres/recherche.

### D. Architecture de l'information
- Fusionner l'« argent » en un espace cohérent ; faire de **« routine du mois » le hub**.
- Réduire la nav (≤ ~18 liens, ≤ 5 sections) ; regrouper le secondaire.
- Recherche globale mise en avant (déjà fonctionnelle).

### E. Responsive — trancher la tablette
- **Option 1** : implémenter le palier tablette (mini-sidebar `md:` w-16).
- **Option 2** : assumer 2 paliers (mobile < 1024 / desktop) et **retirer** le palier tablette de CLAUDE.md.
- Décision produit requise ; aligner code ↔ doc.

### F. Performance & réseau (P-3)
- **Pipeline images** avant activation des photos : `loading="lazy"`, formats légers (WebP), miniatures, ratios fixes (pas de reflow).
- Budget de page : viser < 400 KB hors média.
- Tolérance aux coupures (au minimum : messages clairs, pas de perte de saisie).

### G. Data-viz utile
- Graphes qui **répondent à une question** : valeurs au survol, ligne d'objectif (recouvrement), comparaison N-1, barre accent pour le mois courant.

### H. Parcours clés (optimisation)
- **Encaisser un loyer** (quotidien) : pré-remplissage depuis le contrat, saisie en lot / « répéter le mois dernier ».
- **Relancer un impayé** : WhatsApp 1 tap (ok) + suivi des relances.
- **Reverser au propriétaire** : flux clair, traçable.
- **Quittance** : génération + envoi, conforme.

### I. Localisation avancée
- Français simplifié partout (modèle : routine du mois).
- **Wolof** sur écrans/labels clés (v2).
- Mobile-money & WhatsApp natifs (renforcer).

### J. Microcopy & contenu
- Ton clair, orienté action ; états vides actionnables (« Calculer les bilans → »).
- Messages d'erreur explicites et nommant le champ.

### K. QA visuelle automatisée
- Pérenniser le harness de capture (Chrome piloté) : script qui se connecte (route Dusk), parcourt les écrans, capture desktop+mobile, mesure contraste/cibles. → revue avant chaque release.

### L. Dette / bugs immédiats
- Fix jours décimaux (`(int)`).
- Supprimer les composants Breeze morts (`modal`, `dropdown`) et `sidebar.blade.php`.

---

## 4. Feuille de route par vagues

### Phase 0 — Quick wins « contexte Sénégal » (1 sprint)
*Le cœur du fossé concurrentiel, faible effort.*
- A : nom de marque unifié
- C : passe contraste AA + cibles 48px (mobile) + labels + hiérarchie titres
- L : fix jours décimaux
- E : **décision** tablette (1 ou 2)
> **Sortie :** 0 texte < 4.5 sur écrans clés, 0 cible < 48px mobile, 1 nom, 1 `h1`+sections par page.

### Phase 1 — Fondations design system (1–2 sprints)
- B : tokens contraste-safe, composant carte unique, hiérarchie KPI, rouge=alerte, états standards, focus-visible
> **Sortie :** 1 composant carte partout, 1 chiffre roi/écran, rouge réservé aux alertes.

### Phase 2 — IA & parcours (2 sprints)
- D : fusion « argent » + routine = hub, nav ≤ 18
- H : optimiser encaisser/relancer/reverser/quittance (pré-remplissage, lot)
- E : implémentation tablette si Option 1
> **Sortie :** ≤ 5 sections de nav ; encaisser un loyer en ≤ 3 taps.

### Phase 3 — Perf, data-viz, localisation (continu)
- F : pipeline images + budget page
- G : data-viz utile
- I : wolof écrans clés
- K : QA visuelle automatisée intégrée au flux
> **Sortie :** < 400 KB/page hors média, graphes décisionnels, captures auto en revue.

---

## 5. Definition of Done (par écran)
Un écran est « fait » quand :
- [ ] Contraste ≥ 4.5 (texte) / ≥ 3 (large, UI)
- [ ] Cibles interactives ≥ 48px (mobile)
- [ ] 1 `h1` + sections en `h2/h3` ; champs labellisés
- [ ] Composants du design system uniquement (carte/boutons/badges/états)
- [ ] Rouge réservé aux alertes
- [ ] États vide / chargement / erreur / succès traités
- [ ] Rendu vérifié mobile 375 + desktop (+ tablette si Option 1) via capture
- [ ] Conforme CLAUDE.md (palette, Poppins, `var(--ac)`, Alpine via `Alpine.data()`)

---

## 6. Gouvernance
- Ce plan + CLAUDE.md = sources de vérité. Toute nouvelle vue suit la DoD §5.
- Revue visuelle (captures Chrome) obligatoire avant merge sur les écrans modifiés.
- Décisions produit en attente : **nom de marque**, **palier tablette**, **refonte IA argent**.

---

## 7. Backlog priorisé

| Prio | Item | Chantier | Effort | Critère |
|------|------|----------|--------|---------|
| P1 | Nom de marque unique | A | S | 1 nom partout |
| P1 | Contraste AA | C | M | ≥ 4.5 |
| P1 | Cibles 48px mobile | C | M | 0 < 48px |
| P1 | Fix jours décimaux | L | XS | `(int)` |
| P1 | Hiérarchie titres + labels | C | M | h1+sections, 0 input nu |
| P1 | Décision tablette | E | S | doc=code |
| P2 | Composant carte unique | B | L | 1 composant |
| P2 | Hiérarchie KPI | B | M | 1 chiffre roi |
| P2 | Rouge = alerte only | B | M | 0 aplat info |
| P2 | États standards | B/J | M | vide/erreur/load/succès |
| P2 | IA « argent » + routine hub | D | L | ≤ 5 sections |
| P3 | Parcours encaisser/relancer | H | L | ≤ 3 taps |
| P3 | Pipeline images | F | M | < 400 KB |
| P3 | Data-viz utile | G | M | valeurs + objectif |
| P3 | Wolof écrans clés | I | L | écrans clés bilingues |
| P3 | QA visuelle auto | K | M | captures en revue |
| P3 | Nettoyage code mort | L | S | modal/dropdown/sidebar supprimés |

*Effort : XS < 1 h · S ~½ j · M ~1–2 j · L ~3–5 j.*
