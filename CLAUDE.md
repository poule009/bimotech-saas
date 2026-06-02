# CLAUDE.md — Bimothèque Immo

---

## RÈGLE N°1 — Vérification après chaque brique

**Après chaque composant, vue ou bloc posé, dans cet ordre :**

1. `php artisan view:cache` → zéro erreur Blade
2. `php -l <fichier.php>` → zéro erreur syntaxe PHP
3. `npm run build` → zéro erreur de compilation Tailwind
4. Vérifier mobile (375px) : layout, lisibilité, tap targets ≥ 48px
5. Vérifier tablette (768px) : mini sidebar visible, grilles 2 col
6. Vérifier desktop (1024px) : sidebar complète, données denses
7. Palette : zéro couleur hors palette (sauf exception documentée)
8. Polices : uniquement `font-display` (Syne) et `font-body` (DM Sans)
9. `var(--ac)` utilisé partout où la couleur agence intervient
10. Zéro `<style>` inline dans la vue migrée — tout en Tailwind

**Si une vérification échoue → corriger avant de continuer. Toujours.**

---

## Contexte produit

- **Produit :** Bimothèque Immo — SaaS B2B gestion immobilière
- **Marché :** Agences immobilières au Sénégal
- **Utilisateur principal :** Directeur/gestionnaire d'agence — mobile Android 375px
- **Fonctionnalités :** Baux, quittances, paiements, locataires, bailleurs, fiscalité (TVA 18%, BRS 5%, CFPB, loi 81-18)

---

## Stack technique

- Laravel 11 (bootstrap/app.php — pas de Kernel.php)
- Blade templates (pas de Vue/React/Livewire)
- **Tailwind CSS v3** + **Alpine.js v3**
- Vite (`npm run dev` / `npm run build`)
- Polices : Syne + DM Sans via Google Fonts
- Icônes : SVG inline uniquement (pas de librairie d'icônes externe)
- Pas de jQuery, pas d'autres CSS frameworks

### CRITIQUE — @vite() absent du layout actuel

Le layout `layouts/app.blade.php` ne charge pas encore Tailwind via Vite.
**La Phase 1 de la migration doit ajouter dans le `<head>` du layout :**

```blade
@vite(['resources/css/app.css', 'resources/js/app.js'])
```

Sans cette ligne, aucune classe Tailwind ne fonctionnera en production.
Pendant le développement : lancer `npm run dev` en parallèle.

---

## Couleur agence dynamique — `var(--ac)`

```css
var(--ac)    /* couleur hex de l'agence ex: #C9A84C */
var(--ac-r)  /* composante R (0-255) */
var(--ac-g)  /* composante G (0-255) */
var(--ac-b)  /* composante B (0-255) */
```

Injectée dans `layouts/app.blade.php` (déjà en place, ne pas modifier) :
```blade
@php
    $agencyColor = auth()->user()?->agency?->couleur_primaire ?? '#c9a84c';
    $hex = ltrim($agencyColor, '#');
    $cr  = hexdec(substr($hex, 0, 2));
    $cg  = hexdec(substr($hex, 2, 2));
    $cb  = hexdec(substr($hex, 4, 2));
@endphp
<style>
    :root { --ac: {{ $agencyColor }}; --ac-r: {{ $cr }}; --ac-g: {{ $cg }}; --ac-b: {{ $cb }}; }
</style>
```

**Règles absolues :**
- Utiliser `bg-[var(--ac)]`, `text-[var(--ac)]`, `border-[var(--ac)]` en Tailwind
- Pour les opacités : `style="background: rgba(var(--ac-r),var(--ac-g),var(--ac-b),0.1)"`
- Ne jamais remplacer par une couleur fixe
- Bouton submit de tout formulaire → `bg-[var(--ac)]`
- Nav item actif sidebar → `bg-[var(--ac)]/10 text-[var(--ac)]`

---

## Palette de couleurs — STRICTE

### Tokens Tailwind (définis dans tailwind.config.js)

| Token | Hex | Usage |
|-------|-----|-------|
| `bimo-bg` | `#F7F5F0` | Fond général des pages |
| `bimo-bg2` | `#F0EDE6` | Headers de cards, zones secondaires |
| `bimo-surface` | `#FFFFFF` | Cards, modals, inputs |
| `bimo-navy` | `#1B4F6B` | Navbar, sidebar, boutons primaires, textes forts |
| `bimo-navy-dk` | `#163F56` | Hover états navy |
| `bimo-gold` | `#C9A84C` | CTA, valeurs positives, highlights |
| `bimo-red` | `#EF4444` | Impayés uniquement |

### Opacités en Tailwind — valeurs autorisées

Tailwind v3 accepte ces opacités avec `/` : `5 10 20 25 30 40 50 60 70 75 80 90 95`

```
text-bimo-navy/50    ✅  →  rgba(27,79,107,0.5)
bg-bimo-gold/10      ✅  →  rgba(201,168,76,0.1)
border-bimo-navy/20  ✅  →  rgba(27,79,107,0.2)
bg-bimo-gold/[7%]    ✅  →  valeur arbitraire (utiliser [] si hors liste)
bg-bimo-gold/7       ❌  →  invalide sans []
```

### Exception couleur — Rouge impayés

`bimo-red` (`#EF4444`) — UNIQUEMENT pour impayés et alertes critiques.

### Règle absolue

- ❌ Pas de vert (sauf icône WhatsApp `#25D366`)
- ❌ Pas de bleu vif, violet, orange, rose
- ❌ Pas de gradient (sauf PDF/quittances)
- ❌ Pas de `style="color:#..."` hardcodé
- ✅ Positif → `bimo-gold`
- ✅ Critique → `bimo-red`
- ✅ Tout le reste → nuances `bimo-navy`

---

## Typographie

### Chargement polices (dans layouts/app.blade.php)

```html
<link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
```

### Classes Tailwind

| Usage | Classe |
|-------|--------|
| Titre de page, valeur KPI | `font-display font-extrabold` |
| Sous-titre, section | `font-display font-bold` |
| Label navigation | `font-display font-semibold` |
| Corps de texte | `font-body font-normal` |
| Texte secondaire | `font-body font-light` |
| Label, badge, cap | `font-body font-medium` |

### Règles

- Jamais Inter, Roboto, Arial ou autre police
- Taille minimum : `text-[10px]`
- Caps : `uppercase tracking-widest`
- Titres : `tracking-tight leading-tight`
- Corps : `leading-relaxed`

---

## Breakpoints — 3 interfaces distinctes

**Mobile-first : écrire d'abord sans préfixe (mobile), puis `md:` (tablette), puis `lg:` (desktop).**

| Breakpoint | Tailwind | Écran | Interface |
|-----------|----------|-------|-----------|
| Default | — | ≤ 767px | App mobile native |
| `md:` | 768px+ | Tablette | Mini sidebar + contenu |
| `lg:` | 1024px+ | Desktop | Interface pro complète |

### Mobile — App native
- Bottom nav fixe `fixed bottom-0` (pas de sidebar)
- Contenu plein largeur `w-full px-4`
- Cards à la place des tableaux
- FAB pour l'action principale `fixed bottom-20 right-4`
- Tap targets `min-h-[48px]`
- Espacement `p-4`

### Tablette (md:)
- Mini sidebar icônes `md:fixed md:left-0 md:w-16`
- Contenu décalé `md:ml-16`
- Grilles 2 colonnes `md:grid-cols-2`
- Tableaux simplifiés (quelques colonnes)
- Espacement `md:p-6`

### Desktop (lg:)
- Sidebar complète `lg:w-64`
- Contenu décalé `lg:ml-64`
- KPIs en 4 colonnes `lg:grid-cols-4`
- Tableaux complets toutes colonnes
- Hover states riches
- Espacement `lg:p-8`

---

## Composants — Classes Tailwind exactes

### Layout global

```
Mobile  : topbar sticky top + bottom nav fixed bottom + contenu pb-20
Tablette: mini sidebar fixed left w-16 + contenu ml-16
Desktop : sidebar fixed left w-64 + contenu ml-64
```

### Topbar mobile

```html
<header class="sticky top-0 z-40 flex items-center justify-between h-14 px-4 bg-bimo-navy lg:hidden">
  <!-- Logo -->
  <span class="font-display font-extrabold text-white">Bimothèque <span class="text-bimo-gold">Immo</span></span>
  <!-- Avatar -->
  <div class="w-[34px] h-[34px] rounded-[9px] bg-[var(--ac)] flex items-center justify-center font-display font-bold text-white text-sm">
    {{ initiales }}
  </div>
</header>
```

### Sidebar desktop

```html
<aside class="hidden lg:flex fixed left-0 top-0 h-full w-64 bg-bimo-navy flex-col z-40">
  <!-- Nav item actif -->
  <a class="flex items-center gap-3 px-3 py-2.5 rounded-xl bg-white/10 text-bimo-gold font-display font-semibold text-sm">
  <!-- Nav item inactif -->
  <a class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-white/60 hover:text-white hover:bg-white/5 transition-colors duration-150 font-body text-sm">
</aside>
```

### Mini sidebar tablette

```html
<aside class="hidden md:flex lg:hidden fixed left-0 top-0 h-full w-16 bg-bimo-navy flex-col items-center py-4 gap-2 z-40">
  <!-- Icône active -->
  <a class="w-10 h-10 rounded-xl bg-white/10 flex items-center justify-center text-bimo-gold">
  <!-- Icône inactive -->
  <a class="w-10 h-10 rounded-xl flex items-center justify-center text-white/50 hover:text-white hover:bg-white/5 transition-colors duration-150">
</aside>
```

### Bottom nav mobile

```html
<nav class="fixed bottom-0 left-0 right-0 h-16 bg-bimo-navy flex items-center justify-around px-2 z-40 lg:hidden">
  <!-- Item actif -->
  <a class="flex flex-col items-center gap-1 text-bimo-gold">
    <svg class="w-5 h-5">...</svg>
    <span class="font-display font-semibold text-[10px] uppercase tracking-widest">Label</span>
  </a>
  <!-- Item inactif -->
  <a class="flex flex-col items-center gap-1 text-white/50 hover:text-white transition-colors duration-150">
</nav>
```

### FAB (Floating Action Button)

```html
{{-- bottom-[72px] = bottom nav (64px) + 8px de marge --}}
<button class="fixed bottom-[72px] right-4 z-50 w-[52px] h-[52px] rounded-[14px] bg-bimo-gold text-bimo-navy flex items-center justify-center shadow-[0_8px_24px_rgba(201,168,76,0.4)] font-extrabold text-2xl lg:hidden">
  +
</button>
```

### Card KPI

```html
<!-- Standard -->
<div class="bg-white rounded-[14px] border border-bimo-navy/10 p-4">
  <p class="font-body font-medium text-[9.5px] uppercase tracking-widest text-bimo-navy/50 mb-2">Label</p>
  <p class="font-display font-extrabold text-xl text-bimo-navy">Valeur</p>
  <p class="font-body font-light text-[10.5px] text-bimo-navy/50 mt-1">Sous-texte</p>
</div>

<!-- Highlighted (revenus) -->
<div class="bg-bimo-gold/[8%] rounded-[14px] border border-bimo-gold/25 p-4">
  <p class="font-body font-medium text-[9.5px] uppercase tracking-widest text-bimo-gold/70 mb-2">Label</p>
  <p class="font-display font-extrabold text-xl text-bimo-gold">Valeur</p>
</div>
```

### Boutons

```html
<!-- Primaire navy -->
<button class="inline-flex items-center gap-2 bg-bimo-navy hover:bg-bimo-navy-dk text-white font-display font-bold text-sm px-5 py-3 rounded-[10px] transition-colors duration-150">

<!-- CTA gold -->
<button class="inline-flex items-center gap-2 bg-bimo-gold hover:brightness-90 text-bimo-navy font-display font-bold text-sm px-5 py-3 rounded-[10px] shadow-[0_4px_16px_rgba(201,168,76,0.3)] transition-all duration-150">

<!-- Ghost -->
<button class="inline-flex items-center gap-2 bg-transparent text-bimo-navy/60 hover:text-bimo-navy border border-bimo-navy/15 hover:border-bimo-navy/30 font-body text-sm px-5 py-3 rounded-[10px] transition-all duration-150">

<!-- Danger -->
<button class="inline-flex items-center gap-2 bg-bimo-red/10 text-bimo-red hover:bg-bimo-red/20 border border-bimo-red/20 font-body text-sm px-5 py-3 rounded-[10px] transition-all duration-150">
```

### Inputs & Labels

```html
<div class="space-y-1.5">
  <label class="block font-body font-medium text-sm text-bimo-navy">
    Label <span class="text-bimo-red">*</span>
    <span class="font-light text-bimo-navy/40 ml-1">(optionnel)</span>
  </label>
  <input class="w-full px-4 py-3 rounded-[10px] bg-white border border-bimo-navy/20 font-body text-sm text-bimo-navy placeholder:text-bimo-navy/35 focus:outline-none focus:border-bimo-gold focus:ring-2 focus:ring-bimo-gold/15 transition-all duration-150">
  <!-- Erreur -->
  <p class="font-body text-xs text-bimo-red">Message d'erreur</p>
</div>
```

### Select

```html
<select class="w-full px-4 py-3 rounded-[10px] bg-white border border-bimo-navy/20 font-body text-sm text-bimo-navy focus:outline-none focus:border-bimo-gold focus:ring-2 focus:ring-bimo-gold/15 transition-all duration-150 appearance-none cursor-pointer">
```

### Badges

```html
<!-- Validé / positif -->
<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-body font-medium bg-bimo-gold/10 border border-bimo-gold/20 text-bimo-gold">

<!-- Impayé / erreur -->
<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-body font-medium bg-bimo-red/10 border border-bimo-red/20 text-bimo-red">

<!-- Neutre -->
<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-body font-medium bg-bimo-navy/10 border border-bimo-navy/15 text-bimo-navy/70">
```

### Flash messages (session Laravel)

**Les flash messages success/warning/error sont déjà gérés dans `layouts/app.blade.php`.**
Ne pas les re-implémenter dans les vues. Ils s'affichent automatiquement.

Pour les erreurs de validation inline dans un formulaire (en plus du flash global) :
```html
{{-- Erreur sur un champ spécifique --}}
@error('nom_champ')
<p class="mt-1 font-body text-xs text-bimo-red">{{ $message }}</p>
@enderror

{{-- Input en état d'erreur --}}
<input class="... @error('nom_champ') border-bimo-red focus:border-bimo-red focus:ring-bimo-red/15 @enderror">
```

### Tableaux (desktop uniquement)

```html
<!-- Wrapper avec fallback mobile cards -->
<div class="hidden md:block overflow-x-auto">
  <table class="w-full text-sm">
    <thead>
      <tr class="border-b border-bimo-navy/10 bg-bimo-bg2">
        <th class="px-4 py-3 text-left font-body font-medium text-[10px] uppercase tracking-widest text-bimo-navy/50">Col</th>
      </tr>
    </thead>
    <tbody class="divide-y divide-bimo-navy/[5%]">
      <tr class="hover:bg-bimo-bg transition-colors duration-100">
        <td class="px-4 py-3.5 font-body text-bimo-navy">Valeur</td>
      </tr>
    </tbody>
  </table>
</div>
<!-- Cards mobile (toujours présent sur mobile) -->
<div class="md:hidden space-y-3">
  <div class="bg-white rounded-[14px] border border-bimo-navy/10 p-4">...</div>
</div>
```

---

## Alpine.js — Patterns autorisés

**Alpine.js est le seul outil JS pour les interactions UI. Pas de JS vanilla inline.**

### Dropdown / menu

```html
<div x-data="{ open: false }" class="relative">
  <button @click="open = !open" @click.outside="open = false">Trigger</button>
  <div x-show="open" x-transition:enter="transition duration-150"
       x-transition:enter-start="opacity-0 translate-y-1"
       x-transition:enter-end="opacity-100 translate-y-0"
       class="absolute right-0 top-full mt-1 bg-white border border-bimo-navy/10 rounded-[12px] shadow-lg z-50 min-w-[160px]">
    ...
  </div>
</div>
```

### Modal

```html
<div x-data="{ open: false }">
  <button @click="open = true">Ouvrir</button>
  <div x-show="open" x-transition class="fixed inset-0 z-50 flex items-center justify-center p-4">
    <div class="absolute inset-0 bg-bimo-navy/40 backdrop-blur-sm" @click="open = false"></div>
    <div class="relative bg-white rounded-[20px] w-full max-w-md shadow-xl p-6">
      ...
    </div>
  </div>
</div>
```

### Sidebar toggle mobile

```html
<div x-data="{ sidebarOpen: false }">
  <button @click="sidebarOpen = true">☰</button>
  <!-- Overlay -->
  <div x-show="sidebarOpen" @click="sidebarOpen = false" class="fixed inset-0 bg-bimo-navy/40 z-30 lg:hidden"></div>
  <!-- Sidebar -->
  <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
         class="fixed left-0 top-0 h-full w-64 bg-bimo-navy z-40 transition-transform duration-250 lg:translate-x-0">
    ...
  </aside>
</div>
```

### Tabs

```html
<div x-data="{ tab: 'mois' }">
  <div class="flex gap-1 bg-bimo-bg2 p-1 rounded-[10px]">
    <button @click="tab = 'mois'" :class="tab === 'mois' ? 'bg-white text-bimo-navy shadow-sm' : 'text-bimo-navy/50'"
            class="flex-1 py-1.5 px-3 rounded-[8px] font-body font-medium text-sm transition-all duration-150">
      Ce mois
    </button>
  </div>
  <div x-show="tab === 'mois'">...</div>
</div>
```

---

## Structure d'une vue migrée

**Template de référence — toujours suivre cette structure :**

```blade
@extends('layouts.app')

{{-- Titre dans le breadcrumb topbar --}}
@section('header', 'Nom de la section')

@section('content')
{{-- ZÉRO <style> ici — tout en Tailwind --}}
{{-- Les flash messages sont déjà gérés par le layout — NE PAS les re-ajouter --}}

<div class="space-y-4 md:space-y-6">

  {{-- En-tête de page --}}
  <div class="flex items-start justify-between gap-4">
    <div>
      <h1 class="font-display font-extrabold text-xl md:text-2xl text-bimo-navy tracking-tight leading-tight">
        Titre de la page
      </h1>
      <p class="font-body text-sm text-bimo-navy/50 mt-1">Sous-titre ou description</p>
    </div>
    {{-- Bouton action principale --}}
    <a href="{{ route('...') }}"
       class="flex-shrink-0 inline-flex items-center gap-2 bg-[var(--ac)] text-white font-display font-bold text-sm px-4 py-2.5 rounded-[10px] transition-opacity duration-150 hover:opacity-90">
      <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">...</svg>
      <span class="hidden sm:inline">Action</span>
    </a>
  </div>

  {{-- Contenu principal --}}
  <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-3 md:gap-4">
    ...
  </div>

</div>
@endsection
```

### Structure d'un formulaire

```blade
@section('content')
<div class="max-w-2xl mx-auto">
  <form method="POST" action="{{ route('...') }}" class="space-y-6">
    @csrf
    {{-- Pour PUT/PATCH : --}}
    @method('PUT')

    {{-- Groupe de champs --}}
    <div class="bg-white rounded-[14px] border border-bimo-navy/10 p-5 space-y-4">
      <h2 class="font-display font-bold text-base text-bimo-navy">Section</h2>

      <div class="space-y-1.5">
        <label for="champ" class="block font-body font-medium text-sm text-bimo-navy">
          Label <span class="text-bimo-red">*</span>
        </label>
        <input id="champ" name="champ" type="text"
               value="{{ old('champ') }}"
               class="w-full px-4 py-3 rounded-[10px] bg-white border font-body text-sm text-bimo-navy
                      placeholder:text-bimo-navy/35 focus:outline-none focus:ring-2 transition-all duration-150
                      @error('champ') border-bimo-red focus:border-bimo-red focus:ring-bimo-red/15
                      @else border-bimo-navy/20 focus:border-bimo-gold focus:ring-bimo-gold/15 @enderror">
        @error('champ')
        <p class="font-body text-xs text-bimo-red">{{ $message }}</p>
        @enderror
      </div>
    </div>

    {{-- Barre de soumission sticky --}}
    <div class="sticky bottom-0 bg-white/95 backdrop-blur-sm border-t border-bimo-navy/10 px-0 py-3 flex justify-end gap-3">
      <a href="{{ route('...') }}"
         class="inline-flex items-center px-5 py-2.5 rounded-[10px] border border-bimo-navy/15 font-body text-sm text-bimo-navy/60 hover:text-bimo-navy hover:border-bimo-navy/30 transition-all duration-150">
        Annuler
      </a>
      <button type="submit"
              class="inline-flex items-center gap-2 px-5 py-2.5 rounded-[10px] bg-[var(--ac)] text-white font-display font-bold text-sm hover:opacity-90 transition-opacity duration-150">
        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
        Enregistrer
      </button>
    </div>
  </form>
</div>
@endsection
```

### Slots disponibles dans le layout

```blade
{{-- Titre dans le breadcrumb (topbar desktop) --}}
@section('header', 'Nom de la page')

{{-- Scripts spécifiques à la vue --}}
@push('scripts')
<script>...</script>
@endpush

{{-- Styles spécifiques (à éviter — préférer Tailwind) --}}
@push('styles')
<style>...</style>
@endpush
```

---

## Icônes — Règles

- **SVG inline uniquement** — pas de Font Awesome, pas d'Heroicons npm
- Taille standard : `w-4 h-4` (petite), `w-5 h-5` (moyenne), `w-6 h-6` (grande)
- Couleur via `currentColor` (héritée du texte parent) ou classe Tailwind explicite
- `stroke-width="1.5"` pour les icônes outline (style général de l'app)
- `viewBox="0 0 24 24"` standard

---

## Règles absolues

### À faire
- Mobile-first : sans préfixe → `md:` → `lg:`
- `p-4` mobile, `md:p-6` tablette, `lg:p-8` desktop
- `rounded-[14px]` cards, `rounded-[10px]` boutons/inputs, `rounded-full` badges
- Transitions `duration-150` sur tous les états interactifs
- Tap targets `min-h-[48px]` sur mobile
- `var(--ac)` pour le bouton submit principal de chaque formulaire

### À ne pas faire
- ❌ `<style>` inline dans une vue migrée
- ❌ Classes `.bm-*` mélangées avec Tailwind (vue migrée = 100% Tailwind)
- ❌ `@apply` dans app.css
- ❌ Couleur hardcodée en `style=""`
- ❌ Tableaux sans version mobile cards
- ❌ Taille < `text-[10px]`
- ❌ Border-radius < `rounded-lg` (< 8px)
- ❌ Animation > `duration-300`
- ❌ Autre police que `font-display` et `font-body`

---

## Ordre de migration

### Phase 1 — Foundation (obligatoire en premier)
1. `tailwind.config.js`
2. `resources/css/app.css`
3. `resources/views/layouts/app.blade.php`
4. `resources/views/layouts/guest.blade.php`

### Phase 2 — Vues critiques
5. `auth/login.blade.php`
6. `admin/dashboard.blade.php`
7. `paiements/index.blade.php`
8. `bailleurs/index.blade.php`

### Phase 3 — Biens & contrats
9. `biens/index.blade.php`
10. `biens/create.blade.php`
11. `biens/edit.blade.php`
12. `biens/show.blade.php`
13. `admin/contrats/index.blade.php`
14. `admin/contrats/create.blade.php`
15. `admin/contrats/show.blade.php`
16. `admin/contrats/edit.blade.php`

### Phase 4 — Utilisateurs & finances
17. `users/create.blade.php`
18. `users/edit.blade.php`
19. `users/show.blade.php`
20. `users/proprietaires.blade.php`
21. `users/locataires.blade.php`
22. `paiements/create.blade.php`
23. `paiements/show.blade.php`
24. `impayes/index.blade.php`

### Phase 5 — Secondaires
25. `immeubles/` (4 vues)
26. `bailleurs/show.blade.php`
27. `rapports/financier.blade.php`
28. `admin/agency-settings.blade.php`
29. `profile/edit.blade.php`
30. `locataire/` (3 vues)
31. `proprietaire/dashboard.blade.php`

### Phase 6 — Admin & superadmin
32. `superadmin/` (4 vues)
33. `admin/` fiscalité, bilans, TVA (6 vues)
34. `subscription/` (3 vues)
35. `errors/` (404, 403, 500)
36. `activity-logs/index.blade.php`
37. Supprimer `bimotech.css` seulement ici

### Phase 7 — Marketing public
38. `contact.blade.php`
39. `demo.blade.php`
40. `mentions-legales.blade.php`

### NE PAS TOUCHER
- `portail/` — design system public indépendant
- `*/pdf/` — documents impression, règles différentes
- `emails/` — templates email
- `sitemap.blade.php`
- `users/create.blade.php` sections locataire (fiscal BRS) — logique métier complexe, ne pas casser

---

## Points de vigilance métier

- `montant_encaisse` absent de `StorePaiementRequest::rules()` → calculé par `FiscalService`
- Global scope `agency_id` absent sur `Bien` → toujours filtrer manuellement
- Module fiscal contrôlé par `config('features.fiscalite')` → ne jamais toucher aux calculs
- `bimotech.css` reste actif jusqu'à Phase 6 complète — ne pas supprimer avant
- Validation email propriétaire optionnelle (nullable) — password required_with:email
