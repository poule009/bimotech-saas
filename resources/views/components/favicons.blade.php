{{-- Favicons Bimmo (application web). Le SVG vectoriel est la source principale
     (support navigateurs modernes) ; apple-touch-icon en PNG pour les raccourcis
     d'écran d'accueil iOS. NB : public/favicon.ico est encore l'ancien placeholder
     (à régénérer depuis bimmo-logo.svg avec un outil d'export). --}}
<link rel="icon" href="{{ asset('icons/bimmo-logo.svg') }}" type="image/svg+xml">
<link rel="apple-touch-icon" href="{{ asset('icons/bimmo-logo.png') }}">
