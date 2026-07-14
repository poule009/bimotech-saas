{{-- Verrou de marque « logo + nom » Bimmo (usage : navbar, sidebar, connexion,
     footer vitrine, en-têtes de documents — tout contexte à espace large).
     Le logo et le nom se dimensionnent en `em` : régler la taille via font-size
     (classe utilitaire text-[..] ou style) sur cet élément racine.

     tone :
       - "brand" (défaut) : « Bi » teal (#1B3A3F) — fonds CLAIRS.
       - "paper"          : « Bi » en paper (#F7F3EA) — fonds SOMBRES (teal).
     « mmo » reste doré (#B8892B) et le logo suit le même tone (voir x-logo). --}}
@props(['tone' => 'brand'])
@php
    $bi = $tone === 'paper' ? '#F7F3EA' : '#1B3A3F';
@endphp
<span {{ $attributes->merge(['class' => 'brand-lockup']) }}
      style="display:inline-flex; align-items:center; gap:0.42em; line-height:1;">
    <x-logo :tone="$tone" style="width:3.6em; height:3.6em; flex:none;" />
    <span style="font-family:'Fraunces',serif; font-weight:600; letter-spacing:0.005em; color:{{ $bi }};"
    >Bi<span style="color:#B8892B;">mmo</span></span>
</span>
