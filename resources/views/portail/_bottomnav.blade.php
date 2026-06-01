<style>
:root{--bleu:#0D1B26;--gris:#8E9BAA;--gris-bord:#E8E4DE}
.bnav{position:fixed;bottom:0;left:0;right:0;z-index:300;height:64px;padding-bottom:env(safe-area-inset-bottom,0px);background:rgba(242,237,230,.97);backdrop-filter:blur(10px);border-top:1px solid var(--gris-bord);display:flex;align-items:stretch}
.bni{flex:1;display:flex;flex-direction:column;align-items:center;justify-content:center;gap:3px;text-decoration:none;color:var(--gris);font-size:10px;font-weight:600;transition:color .15s;padding:8px 0}
.bni svg{width:22px;height:22px;stroke:currentColor;fill:none;stroke-width:2;stroke-linecap:round;stroke-linejoin:round}
.bni-lbl{font-size:10px}
.bni.active{color:var(--bleu)}
@media(min-width:768px){.bnav{display:none}}
</style>
@php
  $navHome    = request()->routeIs('portail.home');
  $navSearch  = request()->routeIs('portail.index', 'portail.show', 'portail.quartier');
  $navAgences = request()->routeIs('portail.agence');
@endphp
<nav class="bnav" aria-label="Navigation principale">

  <a href="{{ route('portail.home') }}"
     class="bni{{ $navHome ? ' active' : '' }}"
     @if($navHome)aria-current="page"@endif>
    <svg viewBox="0 0 24 24"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
    <span class="bni-lbl">Accueil</span>
  </a>

  <a href="{{ route('portail.index') }}"
     class="bni{{ $navSearch ? ' active' : '' }}"
     @if($navSearch)aria-current="page"@endif>
    <svg viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
    <span class="bni-lbl">Recherche</span>
  </a>

  <a href="{{ route('portail.index') }}"
     class="bni{{ $navAgences ? ' active' : '' }}"
     @if($navAgences)aria-current="page"@endif>
    <svg viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87"/><path d="M16 3.13a4 4 0 010 7.75"/></svg>
    <span class="bni-lbl">Agences</span>
  </a>

  <a href="#" class="bni" aria-label="Favoris (bientôt disponible)">
    <svg viewBox="0 0 24 24"><path d="M20.84 4.61a5.5 5.5 0 00-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 00-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 000-7.78z"/></svg>
    <span class="bni-lbl">Favoris</span>
  </a>

</nav>
