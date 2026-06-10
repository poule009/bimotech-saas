# Diagnostic UX — Bimothèque Immo (couverture étendue)

> Réalisé le **10 juin 2026**. Persona de référence : **directeur d'agence sur Android 375px** (cf. CLAUDE.md).
> **Couverture : les 90 vues applicatives** (hors `pdf/`, `emails/`, `portail/`, pages marketing publiques).
> **Méthode :** (1) mesures automatisées sur les 90 vues — couleurs, typo, tableaux, devise, accessibilité, cibles tactiles ; (2) lecture approfondie d'au moins un écran par catégorie (navigation, dashboard, formulaire de saisie, liste, comptabilité, portail locataire, portail propriétaire). Références `fichier:ligne` cliquables.
> **Limites assumées :** je ne vois pas le rendu pixel ni ne teste avec de vrais utilisateurs. Frictions **argumentées et priorisées**, à confronter à 3-5 vrais directeurs d'agence.

---

## Score UX global : **7.0 / 10**

| Dimension | Score | Verdict |
|-----------|:-----:|---------|
| 🔔 Feedback & état système | **8.5** | Le point le plus solide. Vraiment soigné. |
| 🎯 Parcours critiques (efficacité) | **8.0** | Saisie paiement et onboarding excellents. |
| ✍️ Microcopy & contenu | **8.0** | Clair, pédagogique, bon français, états vides distingués. |
| 🎨 Cohérence visuelle & design system | **7.0** | Système tokenisé fort, mais incohérences à l'échelle (devise, typo, rebrand gris). |
| 🧭 Architecture de l'info & navigation | **7.0** | Bien structurée mais dense ; trous sur mobile. |
| 📱 Mobile / responsive | **6.5** | PWA propre, mais cibles tactiles < 48px et action #1 trop loin. |
| ♿ Accessibilité (WCAG) | **4.5** | Le maillon faible, confirmé à l'échelle (84/90 vues sans ARIA). |

**En une phrase :** une UX **mature et réfléchie** dans ses parcours phares (onboarding guidé, décompte fiscal live, anti-doublon, WhatsApp pré-rempli), mais qui paie une **dette transverse** sur trois axes mesurables : ergonomie tactile mobile, accessibilité, et cohérence de détail (devise/typo).

---

## 📊 Mesures à l'échelle (toutes les vues)

Ces chiffres portent sur les **90 vues applicatives**, pas un échantillon :

| Mesure | Constat | Lecture |
|--------|---------|---------|
| **Accessibilité** | **84 / 90 vues** sans *aucun* `aria-*` / `role` / `sr-only` | 93 % des écrans muets pour les lecteurs d'écran |
| **Cibles tactiles** | **127× `h-8` (32px)** + **76× `h-7` (28px)** d'éléments interactifs | Sous le minimum 48px → taps imprécis sur mobile |
| **Typo minuscule** | **77× `text-[9px]`** + **2× `text-[8px]`** | Sous le plancher documenté (`9.5px`) → lisibilité |
| **Devise** | **237× « F »**, **105× « FCFA »**, **3× « CFA »** | Incohérence massive, pas isolée |
| **Tables non responsive** | **1 réellement cassée** (`components/fiscal/decompte-paiement`) ; les autres scrollent en `overflow-x-auto` | *(je corrige ici mon estimation initiale : le risque est bien plus faible que « 10 tables »)* |
| **Couleurs hex en dur** | **19 vues** avec `style="…#hex…"` | Surtout halos décoratifs `#C9A84C` (or périmé) |
| **Classe invalide `tracking-widests`** | **8 vues** | Typo → letter-spacing silencieusement ignoré |
| **États vides** | **72 vues** en couvrent un | Très bonne couverture |

---

## 🟠 Frictions IMPORTANTES

### UX-1 — L'action #1 quotidienne (« enregistrer un paiement ») est trop loin sur mobile
**Effort : Faible.** Aucun FAB dans toute l'app (0 occurrence) alors que `CLAUDE.md` en spécifie un (`bottom-[72px] right-4`). Pour saisir un paiement : bottom-nav **Paiements** → liste → bouton « Nouveau ». 2-3 taps + scroll pour l'acte le plus fréquent. → Ajouter le FAB prévu, pointant vers `admin.paiements.create`.

### UX-2 — Pas de recherche globale sur mobile
**Effort : Moyen.** La recherche est `hidden lg:flex` ([layouts/app.blade.php:513](resources/views/layouts/app.blade.php#L513)) → invisible sur mobile/tablette. Le persona principal ne peut pas retrouver un locataire/bien vite. → Loupe en topbar mobile ouvrant un overlay plein écran (endpoint `admin.search` déjà là).

### UX-3 — Accessibilité insuffisante, confirmée à l'échelle (WCAG)
**Effort : Moyen-élevé.** **84/90 vues** sans aucun attribut a11y. Les widgets JS vanilla (filtre-select paiement, dropdown profil, recherche, tri colonnes) n'exposent ni rôle ni état. Modale globale sans **focus-trap** ([layouts/app.blade.php:804](resources/views/layouts/app.blade.php#L804)). Cibles tactiles 28-32px (cf. UX-6) = aussi un critère WCAG. Inputs de recherche sans `<label>` lié. → Passe a11y dédiée : `aria-label` sur boutons-icône, focus-trap, labels liés, états ARIA sur widgets.

### UX-4 — L'état actif de navigation est peu visible (effet de bord du rebrand)
**Effort : Faible.** `bimo-gold` est devenu un **gris `#6B7280`** ; l'item actif s'appuie dessus (`text-bimo-gold` sur navy) → affordance « actif » faible, l'utilisateur se repère mal. → Marqueur d'accent `var(--ac)` (vif) ou fond plus contrasté pour l'actif.

### UX-5 — Le décompte fiscal live passe sous la ligne de flottaison sur mobile
**Effort : Faible-moyen.** La pépite de l'écran paiement (calcul commission/TVA/BRS/net en direct) est en `lg:grid-cols-[1fr_320px]` ([paiements/create.blade.php:21](resources/views/paiements/create.blade.php#L21)) → sur mobile, elle atterrit **sous le bouton Enregistrer**. Le directeur saisit à l'aveugle. → Résumé condensé (net à verser) en barre sticky sous le champ montant.

### UX-6 — Cibles tactiles sous le minimum 48px, à l'échelle
**Effort : Moyen.** **127× `h-8` (32px)** et **76× `h-7` (28px)** d'éléments cliquables — typiquement les boutons-icône en bout de ligne de liste (télécharger PDF, copier, supprimer), à **28px**. Sur mobile, taps imprécis et frustrants, surtout pour des actions destructives. `CLAUDE.md` impose pourtant `min-h-[48px]`. → Élargir la zone tactile (padding/`min-h`) des boutons-icône en contexte mobile, même si l'icône reste petite.

---

## 🟡 Frictions MOYENNES

### M1 — Badge compteur d'impayés manquant (structure déjà là, vide)
La bottom-nav « Relances » contient un `<div class="relative">` vide ([layouts/app.blade.php:777](resources/views/layouts/app.blade.php#L777)), manifestement prévu pour un badge. Afficher « ● 5 » est **fort signal / coût quasi nul** — l'info que le directeur veut d'un coup d'œil.

### M2 — Devise incohérente à l'échelle (237 « F » / 105 « FCFA »)
Mélange jusqu'à l'intérieur d'une même vue (le portail locataire affiche « FCFA / mois », « F CFA versés » et « F »). → Une convention unique (recommandé **« FCFA »**), idéalement via un helper Blade `@money()` centralisé.

### M3 — Typo minuscule sous le plancher (77× `text-[9px]`, 2× `text-[8px]`)
`CLAUDE.md` fixe `text-[9.5px]` comme exception plancher (caps denses). 79 occurrences passent en dessous. Sur mobile, pour des directeurs/bailleurs pas toujours jeunes, c'est à la limite du lisible. → Remonter `9px`/`8px` à `≥ 10px` (ou `9.5px` pour les caps).

### M4 — Le sélecteur de contrat est laborieux sur mobile
Pattern « champ recherche qui masque des `<option>` + `<select>` natif » ([paiements/create.blade.php:44](resources/views/paiements/create.blade.php#L44)) : 2 gestes, comportement `option[hidden]` variable selon navigateurs mobiles. → Combobox filtrable inline (Alpine).

### M5 — Pas d'indicateur de chargement sur les appels async
Recherche globale et préchargement fiscal font des `fetch` sans état de chargement. Sur 3G (contexte SN fréquent), l'utilisateur ne sait pas si « ça mouline ». → Mini-spinner / skeleton pendant la requête.

---

## 🟢 Frictions MINEURES

- **Typo `tracking-widests`** (classe Tailwind invalide) dans **8 vues** (`locataire/dashboard:118`, `users/show`, `impayes/index`, `bilans-fiscaux/show`, `import/index`, `bailleurs/show`, `locataire/contrat`, `subscription/index`) → le `letter-spacing` voulu sur ces labels caps n'est jamais appliqué. Corriger en `tracking-widest`.
- **1 table réellement cassée** sur mobile : `components/fiscal/decompte-paiement.blade.php` (ni cards mobile, ni `overflow-x-auto`) → débordement à 375px.
- **Couleur périmée `#C9A84C`** en dur (halos décoratifs de 19 vues + `copyRef()` JS) → remplacer par `var(--ac)` / token.
- **Modale** sans focus-trap complet (cf. UX-3).
- Topbar mobile sans bouton menu (on ouvre via « Menu » en bottom-nav) — acceptable mais contre-intuitif pour qui cherche un hamburger en haut.

---

## ✅ Ce qui est très bien fait (à préserver)

- **Onboarding guidé** ([admin/dashboard.blade.php:94](resources/views/admin/dashboard.blade.php#L94)) : checklist 4 étapes + barre de progression + CTA, dismissible.
- **Décompte fiscal en temps réel** dans la saisie de paiement (commission, TVA, BRS, net recalculés à la sélection du contrat) + **valeurs par défaut intelligentes** (montant pré-rempli, période auto-déduite) + validation inline « ✓ / ⚠ ».
- **Anti-double-soumission** (spinner + disable sur tout submit, [layouts/app.blade.php:884](resources/views/layouts/app.blade.php#L884)) → évite les **doublons de paiement**.
- **Modale de confirmation globale** `data-confirm` (cohérente sur les vues destructives) + **garde « modifications non enregistrées »**.
- **Flash messages** à durées graduées (succès 5 s / alerte 7 s / erreur 12 s).
- **Portails locataire & propriétaire soignés** : état « sans contrat » avec contact agence (`tel:`/`mailto:`), **deep-link WhatsApp pré-rempli** ([locataire/dashboard.blade.php:375](resources/views/locataire/dashboard.blade.php#L375)) — excellent fit marché sénégalais ; tables responsive (cards mobile + table desktop) ; grille biens avec photos `loading="lazy"` + badges statut.
- **États vides distingués** « aucune donnée » vs « aucun résultat filtré » ([biens/index.blade.php:121](resources/views/biens/index.blade.php#L121)) — finition rare.
- **Alertes contextuelles actionnables** (biens invisibles portail, limite de plan) + **badges de plan** (cadenas) pour l'upsell.
- **PWA complète** : manifest, service worker, `theme-color`, `env(safe-area-inset)`.
- **Comptabilité lisible** : compte de résultat revenus/charges/net color-codé (gold/red), CTA « Ajouter une charge » inline, hint « À déclarer à la DGI ».

---

## 🗂️ Couverture par catégorie d'écran

| Catégorie | Écrans lus en profondeur | Verdict |
|-----------|--------------------------|---------|
| Navigation / layout | `layouts/app` (sidebar, bottom-nav, topbar, flash, modale, JS global) | Solide ; dense ; trous mobile (FAB, recherche, état actif) |
| Dashboard admin | `admin/dashboard` | Excellent (onboarding, alertes) |
| Formulaire de saisie | `paiements/create` | Excellent (décompte live) ; décompte sous le fold mobile |
| Liste | `biens/index` + scan `paiements/contrats/impayes/users` | Bon ; pattern table↔cards correct (sauf 1 composant) |
| Comptabilité / fiscal | `comptabilite/compte-resultat` + scan tva/bilans/etats | Lisible ; tables fiscales en scroll horizontal sur mobile |
| Portail locataire | `locataire/dashboard` | Excellent (WhatsApp, contact, responsive) |
| Portail propriétaire | `proprietaire/dashboard` | Solide (KPIs, bilan, grille biens) |
| Transverse (90 vues) | mesures automatisées | a11y faible, cibles tactiles, devise, typo |

---

## 📋 Plan d'action priorisé

| # | Action | Sévérité | Effort | Impact |
|---|--------|:--------:|:------:|--------|
| 1 | **FAB « + paiement »** (dashboard + liste paiements, mobile) | 🟠 UX-1 | 30 min | Très élevé |
| 2 | **Badge compteur impayés** sur bottom-nav (structure déjà là) | 🟡 M1 | 30 min | Élevé / coût nul |
| 3 | **Renforcer l'état actif** de navigation (accent `var(--ac)`) | 🟠 UX-4 | 1 h | Élevé |
| 4 | **Helper devise unique** `@money()` + remplacement « F »→« FCFA » | 🟡 M2 | 2-3 h | Moyen (cohérence) |
| 5 | **Recherche globale mobile** (loupe → overlay) | 🟠 UX-2 | 0,5 j | Élevé |
| 6 | **Décompte fiscal sticky** condensé sur mobile | 🟠 UX-5 | 0,5 j | Élevé |
| 7 | **Élargir les cibles tactiles** des boutons-icône (≥ 44-48px) | 🟠 UX-6 | 0,5 j | Élevé (mobile) |
| 8 | **Passe accessibilité** (aria, focus-trap, labels, contraste) | 🟠 UX-3 | 1-2 j | Élevé |
| 9 | Remonter typo `9px/8px` → `≥10px` + corriger `tracking-widests` | 🟡 M3 | 2-3 h | Moyen |
| 10 | Combobox sélecteur de contrat + spinners async | 🟡 M4/M5 | 0,5 j | Moyen |

**Quick wins (≈ 2 h) :** actions **1, 2, 3** — usage quotidien mobile, coût quasi nul.

---

## Conclusion

Sur l'ensemble des 90 vues, le constat se confirme et se précise : Bimothèque a une **UX de parcours excellente** (saisie paiement, onboarding, portails) posée sur une **dette transverse mesurable** — accessibilité (84/90 vues muettes), ergonomie tactile mobile (cibles 28-32px), et cohérence de détail (devise, typo, état actif). Aucune de ces dettes n'est profonde ; ce sont des passes **systématiques et peu risquées**. Les trois premiers quick wins se font en une demi-journée et améliorent le quotidien mobile plus que n'importe quelle refonte.
