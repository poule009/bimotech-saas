# CLAUDE.md — bee

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

- **Produit :** bee — SaaS B2B gestion immobilière (marque unique : « bee »)
- **Marché :** Agences immobilières au Sénégal
- **Utilisateur principal :** Directeur/gestionnaire d'agence. Travaille **majoritairement sur ordinateur (desktop)**, et aussi sur mobile Android 375px.
- **Priorité d'interface :** Le **desktop est aussi important, voire plus, que le mobile.** Le code reste mobile-first (cf. Breakpoints) mais le rendu `lg:` ne doit jamais être négligé : tables denses lisibles, survol nettement visible, actions claires à la souris.
- **Fonctionnalités :** Baux, quittances, paiements, locataires, bailleurs, fiscalité (TVA 18%, BRS 5%, CFPB, loi 81-18)

---

## Stack technique

- Laravel 12 (bootstrap/app.php — pas de Kernel.php)
- Blade templates (pas de Vue/React/Livewire)
- **Tailwind CSS v3** + **Alpine.js v3**
- Vite (`npm run dev` / `npm run build`)
- Polices : Syne + DM Sans via Google Fonts
- Icônes : SVG inline uniquement (pas de librairie d'icônes externe)
- Pas de jQuery, pas d'autres CSS frameworks

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
| `bimo-bg` | `#F4F4F5` | Fond général des pages |
| `bimo-bg2` | `#E9E9EA` | Headers de cards, zones secondaires |
| `bimo-surface` | `#FFFFFF` | Cards, modals, inputs |
| `bimo-navy` | `#7B1E3A` | Navbar, sidebar, boutons primaires, textes forts (bordeaux) |
| `bimo-navy-dk` | `#641630` | Hover états navy |
| `bimo-text` | `#111111` | Texte principal sur fond clair |
| `bimo-gold` | `#6B7280` | CTA, valeurs positives, highlights (gris ardoise) |
| `bimo-red` | `#EF4444` | Impayés uniquement |

> ⚠️ **Rebrand appliqué.** `bimo-navy` est désormais un **bordeaux** (`#7B1E3A`) et `bimo-gold` un **gris ardoise** (`#6B7280`). Les noms de tokens (« navy », « gold ») sont **historiques** et conservés pour ne pas casser les vues existantes. Source de vérité = `tailwind.config.js`.

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

### Contrastes WCAG 2.1 — Ratios minimum

| Type de texte | Ratio AA minimum | Ratio AAA |
|---------------|-----------------|-----------|
| Texte normal (< 18pt) | **4.5:1** | 7:1 |
| Texte large (≥ 18pt ou ≥ 14pt bold) | **3:1** | 4.5:1 |
| Composants UI (bordures, icônes) | **3:1** | — |

**Règles pour ce projet :**
- `text-bimo-navy/50` sur fond blanc → ratio ~3.2:1 — **sous le seuil AA** pour du texte normal. Utiliser `/70` minimum pour les labels importants, réserver `/50` aux métadonnées secondaires (caps uppercase < 11px uniquement).
- `text-[9.5px]` labels KPI — accepté comme exception documentée (caps uppercase, contexte dense), mais ne jamais descendre en dessous.
- `text-bimo-gold/70` sur fond blanc → ratio ~2.8:1 — **exception documentée** pour les labels caps de cards highlighted uniquement.

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
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
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

> `font-display` et `font-body` pointent toutes les deux vers **Poppins** — seul le grammage les différencie.

### Règles

- Police unique : **Poppins** (grammages 300 à 800)
- Jamais Inter, Roboto, Arial, Syne, DM Sans ou autre police
- Taille minimum : `text-[10px]`
- Caps : `uppercase tracking-widest`
- Titres : `tracking-tight leading-tight`
- Corps : `leading-relaxed`

---

## Breakpoints — 2 paliers d'interface

**Mobile-first : écrire d'abord sans préfixe (mobile), puis `md:` (mise en page), puis `lg:` (desktop).**

> ⚠️ **Mobile-first ≠ desktop secondaire.** Les patrons d'agence sont **majoritairement sur ordinateur** : le rendu `lg:` est au moins aussi important que le mobile. « Mobile-first » décrit l'ordre d'écriture du CSS, **pas** la priorité d'expérience. Soigner les tables desktop autant que les cards mobile (survol visible, densité maîtrisée, menu `⋮` quand >4 actions par ligne).

> ✅ **Décision juin 2026 — 2 paliers de navigation seulement.** Mobile (< 1024px, bottom-nav) et Desktop (≥ 1024px, sidebar). **Pas de chrome tablette dédié** : entre 768 et 1023px, on sert le chrome mobile. `md:` ne sert qu'à la **mise en page** (grilles/espacement), jamais à une mini-sidebar.

| Breakpoint | Tailwind | Écran | Chrome |
|-----------|----------|-------|--------|
| Default | — | < 1024px | App mobile (bottom-nav, cards, FAB) |
| `md:` | 768px+ | Tablette | **Mise en page seule** (grilles 2 col, espacement) — chrome mobile conservé |
| `lg:` | 1024px+ | Desktop | Interface pro complète (sidebar) |

### Mobile — App native
- Bottom nav fixe `fixed bottom-0` (pas de sidebar)
- Contenu plein largeur `w-full px-4`
- Cards à la place des tableaux
- FAB pour l'action principale `fixed bottom-20 right-4`
- Tap targets `min-h-[48px]`
- Espacement `p-4`

### Tablette (768–1023px) — pas de chrome dédié
- **Chrome mobile conservé** (bottom-nav, pas de sidebar) — décision juin 2026, 2 paliers.
- `md:` sert uniquement à la **mise en page** :
  - Grilles 2 colonnes `md:grid-cols-2`
  - Espacement `md:p-6`
- ❌ Ne pas créer de mini-sidebar `md:w-16` (non implémentée, retirée du périmètre).

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
  <span class="font-display font-extrabold text-white">bee</span>
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

## Sécurité — règles non négociables

### Règles de base Laravel (toujours)

**XSS — `{{ }}` par défaut, jamais `{!! !!}` sans raison documentée**
```blade
{{ $variable }}      ✅ — échappe automatiquement
{!! $variable !!}    ❌ — uniquement si HTML de confiance ET commentaire qui explique pourquoi
```

**CSRF — `@csrf` dans chaque formulaire sans exception**
```blade
<form method="POST">
    @csrf   {{-- obligatoire --}}
```

**Mass assignment — toujours passer par un FormRequest**
```php
// ❌ Jamais
Bien::create($request->all());

// ✅ Toujours
Bien::create($request->validated());
// ou un FormRequest explicite avec rules()
```

**SQL injection — Eloquent/query builder uniquement, jamais de raw avec input**
```php
// ❌ Jamais
DB::select("SELECT * FROM biens WHERE quartier = '$quartier'");

// ✅ Toujours
Bien::where('quartier', $quartier)->get();
```

**Logs — jamais de données sensibles**
```php
// ❌ Jamais logger
Log::info('Paiement', ['montant' => $montant, 'token' => $token]);

// ✅ Logger uniquement les IDs et statuts
Log::info('Paiement créé', ['paiement_id' => $paiement->id]);
```

---

### Règles spécifiques à ce SaaS

**Multi-tenancy — vérifier agency_id avant chaque action**

Chaque contrôleur admin doit s'assurer que la ressource appartient à l'agence courante. Ne jamais faire confiance à l'ID dans l'URL seul :

```php
// ❌ Dangereux — un admin peut accéder aux biens d'une autre agence
$bien = Bien::findOrFail($id);

// ✅ Correct — scope forcé à l'agence courante
$bien = Bien::where('agency_id', auth()->user()->agency_id)->findOrFail($id);
// ou via policy : $this->authorize('view', $bien);
```

**IDOR — toujours vérifier la propriété de la ressource**

Règle : tout `findOrFail($id)` dans un contrôleur admin doit être suivi d'une vérification d'appartenance ou d'une policy. Un admin d'agence A ne doit jamais pouvoir lire, modifier ou supprimer une ressource de l'agence B.

**Policy — préférer `denyAsNotFound()` pour les ressources privées**

Quand une ressource existe mais n'appartient pas à l'agence courante, retourner 404 plutôt que 403 pour ne pas révéler l'existence de la ressource :

```php
// ❌ Révèle que la ressource existe mais est interdite
return Response::deny('Ce bien n\'appartient pas à votre agence.');

// ✅ Ne révèle pas l'existence — plus sûr en multi-tenant
return Response::denyAsNotFound();
```

Réservé aux ressources que l'utilisateur ne devrait pas savoir qu'elles existent (biens, contrats, paiements d'autres agences). Garder `deny()` avec message pour les erreurs métier légitimes (ex : contrat actif ne peut pas être supprimé).

**Escalade de rôle — jamais permettre l'auto-modification de rôle**

```php
// ❌ Jamais dans une mise à jour de profil
$user->update($request->validated()); // si 'role' est dans validated()

// ✅ Exclure explicitement le rôle des champs modifiables par l'utilisateur lui-même
$user->update($request->safe()->except(['role', 'agency_id']));
```

**Fiscalité = responsabilité légale**

Les calculs TVA 18%, BRS 5%, CFPB sont des obligations légales sénégalaises. Une erreur n'est pas un bug UI, c'est un problème juridique. Règle absolue :
- Zéro modification de `FiscalService` sans validation métier explicite du client
- Zéro arrondi manuel sur les montants fiscaux — laisser `FiscalService` gérer
- Zéro contournement de `config('features.fiscalite')`

**Superadmin — compte unique, protection maximale**

- Le 2FA est obligatoire et déjà enforced par le middleware `require2fa`
- Jamais de seeder qui crée un superadmin avec un mot de passe par défaut (`password`, `admin123`, etc.) en production
- Jamais d'endpoint qui permet de créer un second superadmin depuis l'interface

**Impersonation — traçabilité obligatoire**

Quand un superadmin impersonne un admin et effectue une action, l'`ActivityLog` doit enregistrer l'ID du superadmin réel (dans `session('impersonating_id')`), pas seulement l'ID de l'admin impersonné. Sans ça, impossible d'auditer qui a vraiment fait quoi.

**Abonnements expirés — vérification serveur, pas seulement UI**

Les badges et locks dans la nav sont de l'UI — ils peuvent être contournés. Toute feature payante doit être vérifiée côté contrôleur ou middleware :

```php
// ❌ Insuffisant — vérification UI seulement (dans la vue)
@if($canAccess('immeubles'))

// ✅ Vérification serveur en plus (dans le contrôleur)
abort_unless($canAccess('immeubles'), 403, 'Fonctionnalité non disponible sur votre plan.');
```

---

## Architecture & Code — règles spécifiques au projet

### Subscription — passer par les méthodes du modèle

Jamais de `update()` direct sur le statut ou les dates d'un abonnement. Le modèle `Subscription` expose des méthodes dédiées qui encapsulent la logique métier :

```php
// ❌ Jamais
$subscription->update(['statut' => 'actif', 'date_fin_abonnement' => $date]);

// ✅ Toujours
$subscription->activer($plan, $dateDebut, $dateFin);
$subscription->estEnEssai();
$subscription->estActif();
$subscription->joursRestantsEssai();
```

### ActivityLog — logger toutes les actions CRUD sur les ressources clés

Toute création, modification ou suppression sur `Bien`, `Contrat`, `Paiement`, `User`, `Agency` doit être tracée. Sans ça, l'audit est impossible et la fonctionnalité "Activité" du superadmin est inutile.

```php
// Après chaque action significative dans un contrôleur
activity()->on($bien)->log('Bien créé');
activity()->on($contrat)->log('Contrat résilié');
```

Si un contrôleur existant loggue déjà — vérifier le pattern utilisé et le reproduire à l'identique.

### FiscalService — seule source de vérité des calculs

Zéro calcul de TVA, BRS, CFPB ou montant net en dehors de `FiscalService`. Même pour un calcul "simple" en apparence.

```php
// ❌ Jamais calculer manuellement
$tva = $loyer * 0.18;
$montant_net = $loyer - $tva;

// ✅ Toujours déléguer
$resultat = app(FiscalService::class)->calculer($contrat, $montant);
```

### N+1 queries — eager load obligatoire

Les chaînes `Agency → Users → Biens → Contrats → Paiements` génèrent des N+1 dès qu'on itère. Toujours eager load les relations utilisées dans la vue ou le calcul :

```php
// ❌ N+1 — 1 requête par agence pour charger subscription
$agences = Agency::all();
// Dans la vue : $agence->subscription->statut  → N requêtes

// ✅ Eager load
$agences = Agency::with('subscription', 'users')->get();

// ✅ Pour les listes avec biens + contrats
$biens = Bien::with(['contratActif', 'proprietaire', 'photos'])->get();
```

### Soft deletes — 6 modèles concernés

Les modèles suivants utilisent `SoftDeletes` : **`Bien`, `User`, `Contrat`, `Locataire`, `Proprietaire`, `Immeuble`**.

Les modèles suivants **n'en ont pas** : `Agency`, `Subscription`, `Paiement`, `ActivityLog`.

Conséquences :
```php
// Sur un modèle avec SoftDeletes
Bien::find($id)           // exclut automatiquement les supprimés ✅
Bien::withTrashed()       // inclut les supprimés (portail, archives)
Bien::onlyTrashed()       // uniquement les supprimés

// Vérifier avant toute action
if ($bien->trashed()) { abort(404); }

// Sur un modèle SANS SoftDeletes
Paiement::find($id)       // suppression définitive — pas de withTrashed()
```

Ne jamais appeler `withTrashed()` sur `Agency`, `Subscription` ou `Paiement` — la méthode n'existe pas.

---

## Rôles & Plans

### Rôles utilisateur

| Rôle | Accès | Notes |
|------|-------|-------|
| `superadmin` | Plateforme entière | Pas d'agence — `auth()->user()->agency` est **null** |
| `admin` | Son agence uniquement | Créé automatiquement à la création d'agence |
| `locataire` | Portail locataire | Vues `locataire/` uniquement |
| `proprietaire` | Portail propriétaire | Vues `proprietaire/` uniquement |

**Middleware superadmin :** `auth → verified → isSuperAdmin → require2fa`
**Middleware agence :** `auth → verified → isStaff`

La sidebar et la bottom nav s'adaptent automatiquement selon `isSuperAdmin()` dans `layouts/app.blade.php`. Ne pas dupliquer cette logique dans les vues.

### Plans d'abonnement

Hiérarchie : `starter` < `pro` < `agence`

```php
$canAccess('nom_feature')    // true si le plan actuel permet l'accès
$planRequired('nom_feature') // retourne 'Pro' / 'Agence' / null si starter
config('plans.features.X')   // plan minimum requis pour la feature X
```

Les badges plan dans la nav sont calculés automatiquement — ne pas les hardcoder.

### Zones à ne pas toucher

| Zone | Raison |
|------|--------|
| `portail/` | Design system public indépendant — polices et règles différentes |
| `*/pdf/` | Documents impression — gradients autorisés, CSS propre |
| `emails/` | Templates email — pas de Tailwind |
| `sitemap.blade.php` | Fichier XML déguisé en Blade |
| `FiscalService` | TVA 18%, BRS 5%, CFPB, loi 81-18 — ne jamais modifier les calculs |
| `bimotech.css` | Encore actif — ne supprimer qu'après migration 100% des vues |
| `users/create.blade.php` sections BRS | Logique fiscale locataire complexe — ne pas casser |

---

## Pièges connus — ne pas répéter ces erreurs

### Subscription — noms de champs exacts

```php
// ✅ Correct
$sub->date_fin_essai         $sub->date_debut_essai
$sub->date_fin_abonnement    $sub->date_debut_abonnement
$sub->statut                 // 'essai' | 'actif' | 'expiré' | 'suspendu'

// ❌ Ces propriétés n'existent PAS — erreur silencieuse (retourne null)
$sub->essai_fin    $sub->abonnement_fin
```

### Bien — pas de global scope agency_id

`Bien` n'a **pas** de global scope `agency_id`. Toujours filtrer manuellement :

```php
Bien::where('agency_id', $agencyId)->...    // admin agence
Bien::portail()->...                         // portail public (visible_portail + statut)
Bien::withoutGlobalScope(AgencyScope::class) // cross-agency (superadmin, rapports)
```

### Superadmin — agency est null

`auth()->user()->agency` retourne `null` pour le superadmin.
Toujours vérifier `isSuperAdmin()` avant d'accéder à `->agency` ou `->agency_id`.

### montant_encaisse — ne pas mettre dans les inputs

Absent de `StorePaiementRequest::rules()` — calculé automatiquement par `FiscalService`.
Ne jamais l'ajouter dans un formulaire, une validation ou une requête.

### Email propriétaire — nullable

`email` du propriétaire est `nullable`. `password` est `required_with:email`.
Ne pas rendre l'email obligatoire — casse la création de propriétaire sans compte.

### Flash messages — déjà dans le layout

`success`, `warning`, `error` et erreurs de validation sont gérés dans `layouts/app.blade.php`.
Ne jamais les ré-implémenter dans une vue — ils s'affichent automatiquement.

### Impersonation — auth() retourne l'utilisateur impersonné

Quand actif, `auth()->user()` retourne l'utilisateur impersonné, pas le superadmin.
Détecter l'état avec `session('impersonating_id')`.
La bannière d'impersonation est déjà dans `layouts/app.blade.php`.

### config('features.fiscalite')

Le module fiscal est contrôlé par ce flag feature.
Ne jamais modifier les calculs dans `FiscalService` — règles légales sénégalaises.
