{{-- Logo « Le Seuil » (marque Bimmo) : anneau + porte dorée franchissant un seuil.
     Marque figurative SEULE (sans texte). Pour le verrou logo + nom, voir x-brand.
     Ratio strict 1:1 — ne jamais déformer (contrainte non négociable du brief).

     tone :
       - "brand" (défaut) : anneau/seuil teal foncé (#1B3A3F) — fonds CLAIRS (paper, blanc).
       - "paper"          : anneau/seuil en paper (#F7F3EA) — fonds SOMBRES (sidebar/écran
                            de connexion teal). La porte reste dorée dans les deux cas.
     Source vectorielle : public/icons/bimmo-logo.svg --}}
@props(['tone' => 'brand'])
@php
    $ink = $tone === 'paper' ? '#F7F3EA' : '#1B3A3F';
@endphp
<svg {{ $attributes->merge(['class' => 'logo-mark']) }}
     viewBox="115 130 170 170" fill="none" xmlns="http://www.w3.org/2000/svg"
     role="img" aria-label="Bimmo">
    <path d="M274.047 204.596C276.625 222.642 272.344 241.048 262.031 256.256C251.719 271.463 236.108 282.389 218.218 286.921C200.329 291.452 181.434 289.267 165.187 280.788C148.941 272.309 136.498 258.138 130.267 241.018C124.036 223.898 124.459 205.045 131.454 188.106C138.449 171.168 151.519 157.348 168.136 149.321C184.753 141.293 203.735 139.629 221.41 144.649C239.085 149.67 254.196 161.018 263.82 176.499"
          stroke="{{ $ink }}" stroke-width="10" stroke-linecap="round"/>
    <path d="M218.333 183H181.667C179.642 183 178 184.134 178 185.533V256.467C178 257.866 179.642 259 181.667 259H218.333C220.358 259 222 257.866 222 256.467V185.533C222 184.134 220.358 183 218.333 183Z"
          fill="#B8892B"/>
    <path d="M213.903 230.468C215.506 230.723 217.02 229.584 217.283 227.925C217.546 226.267 216.46 224.715 214.857 224.461C213.253 224.206 211.74 225.345 211.477 227.004C211.214 228.663 212.3 230.214 213.903 230.468Z"
          fill="#1B3A3F"/>
    <line x1="145" y1="258.5" x2="255" y2="258.5" stroke="{{ $ink }}"/>
</svg>
