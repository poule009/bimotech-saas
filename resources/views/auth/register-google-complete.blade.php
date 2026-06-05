<!DOCTYPE html>
<html lang="fr" class="h-full">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>Créer votre agence — BimoTech Immo</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Syne:wght@700;800&family=DM+Sans:wght@300;400;500&display=swap" media="print" onload="this.media='all'">
<noscript><link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Syne:wght@700;800&family=DM+Sans:wght@300;400;500&display=swap"></noscript>
@vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-body bg-bimo-bg antialiased min-h-screen flex items-center justify-center px-4 py-8">

<div class="w-full max-w-md">

    {{-- Logo --}}
    <div class="text-center mb-8">
        <a href="{{ url('/') }}" class="inline-flex flex-col items-center gap-2">
            <div class="w-12 h-12 rounded-[12px] bg-bimo-navy flex items-center justify-center">
                <span class="font-display font-extrabold text-bimo-gold text-lg">B</span>
            </div>
            <span class="font-display font-bold text-bimo-text text-lg">
                Bimothèque <span class="text-bimo-gold">Immo</span>
            </span>
        </a>
    </div>

    <div class="bg-white rounded-[16px] border border-bimo-navy/10 p-8 shadow-sm">

        {{-- Icône Google + titre --}}
        <div class="text-center mb-6">
            <div class="w-12 h-12 bg-bimo-navy/[4%] rounded-full flex items-center justify-center mx-auto mb-3">
                <svg width="22" height="22" viewBox="0 0 48 48">
                    <path fill="#EA4335" d="M24 9.5c3.54 0 6.71 1.22 9.21 3.6l6.85-6.85C35.9 2.38 30.47 0 24 0 14.62 0 6.51 5.38 2.56 13.22l7.98 6.19C12.43 13.72 17.74 9.5 24 9.5z"/>
                    <path fill="#4285F4" d="M46.98 24.55c0-1.57-.15-3.09-.38-4.55H24v9.02h12.94c-.58 2.96-2.26 5.48-4.78 7.18l7.73 6c4.51-4.18 7.09-10.36 7.09-17.65z"/>
                    <path fill="#FBBC05" d="M10.53 28.59c-.48-1.45-.76-2.99-.76-4.59s.27-3.14.76-4.59l-7.98-6.19C.92 16.46 0 20.12 0 24c0 3.88.92 7.54 2.56 10.78l7.97-6.19z"/>
                    <path fill="#34A853" d="M24 48c6.48 0 11.93-2.13 15.89-5.81l-7.73-6c-2.18 1.48-4.97 2.31-8.16 2.31-6.26 0-11.57-4.22-13.47-9.91l-7.98 6.19C6.51 42.62 14.62 48 24 48z"/>
                </svg>
            </div>
            <h1 class="font-display font-extrabold text-xl text-bimo-text tracking-tight mb-2">Une dernière étape</h1>
            <p class="font-body text-sm text-bimo-text/50 leading-relaxed">
                Connecté en tant que <strong class="text-bimo-text">{{ $googleName }}</strong><br>
                Donnez un nom à votre agence pour finir.
            </p>
        </div>

        {{-- Erreurs --}}
        @if($errors->any())
        <div class="border-l-[3px] border-bimo-red bg-bimo-red/[5%] border border-bimo-red/20 rounded-[8px] px-4 py-2.5 mb-5">
            @foreach($errors->all() as $error)<p class="font-body text-xs text-bimo-red leading-relaxed">{{ $error }}</p>@endforeach
        </div>
        @endif

        <form method="POST" action="{{ route('agency.register.google.store') }}" class="space-y-4">
            @csrf

            <div class="space-y-1.5">
                <label class="block font-body font-medium text-sm text-bimo-text" for="agency_name">
                    Nom de l'agence <span class="text-bimo-red">*</span>
                </label>
                <input type="text" id="agency_name" name="agency_name" value="{{ old('agency_name') }}"
                       placeholder="Ex : Immobilier Prestige Dakar"
                       required autofocus autocomplete="organization"
                       class="w-full px-4 py-3 rounded-[10px] bg-white border font-body text-sm text-bimo-text placeholder:text-bimo-text/30 focus:outline-none focus:ring-2 transition-all duration-150 @error('agency_name') border-bimo-red focus:border-bimo-red focus:ring-bimo-red/15 @else border-bimo-navy/20 focus:border-bimo-gold focus:ring-bimo-gold/15 @enderror">
                @error('agency_name')<p class="font-body text-xs text-bimo-red">{{ $message }}</p>@enderror
            </div>

            <div class="flex items-start gap-2.5 py-1">
                <input type="checkbox" id="cgu" name="cgu" value="1"
                       class="w-4 h-4 mt-0.5 rounded cursor-pointer accent-bimo-gold flex-shrink-0">
                <label for="cgu" class="font-body text-sm text-bimo-text/60 leading-relaxed cursor-pointer">
                    J'accepte les <a href="#" class="text-bimo-gold hover:underline font-medium">conditions générales d'utilisation</a>
                </label>
            </div>
            @error('cgu')<p class="font-body text-xs text-bimo-red -mt-2">{{ $message }}</p>@enderror

            <button type="submit"
                    class="w-full inline-flex items-center justify-center py-3.5 bg-[var(--ac)] text-white font-display font-bold text-sm rounded-[10px] hover:opacity-90 transition-opacity duration-150 cursor-pointer">
                Créer mon agence gratuitement
            </button>
        </form>

    </div>

    <div class="text-center mt-5">
        <a href="{{ route('agency.register') }}" class="font-body text-sm text-bimo-text/40 hover:text-bimo-text transition-colors duration-150">
            ← Revenir à l'inscription
        </a>
    </div>

    <p class="text-center font-body text-xs text-bimo-text/30 mt-6">
        © {{ date('Y') }} Bimothèque — Conçu au Sénégal 🇸🇳
    </p>
</div>

</body>
</html>
