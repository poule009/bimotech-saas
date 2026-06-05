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
<body class="font-body bg-bimo-bg antialiased min-h-screen grid md:grid-cols-2">

{{-- Panneau gauche brand --}}
<div class="hidden md:flex flex-col justify-between bg-bimo-navy p-12 relative overflow-hidden">
    <div class="absolute inset-0 opacity-[0.03]"
         style="background-image:linear-gradient(rgba(255,255,255,1) 1px,transparent 1px),linear-gradient(90deg,rgba(255,255,255,1) 1px,transparent 1px);background-size:48px 48px;pointer-events:none"></div>
    <a href="{{ url('/') }}" class="relative z-10">
        <img src="/images/logo.jpeg" alt="BiMO-tech Immo" class="h-10 w-auto">
    </a>
    <div class="relative z-10">
        <h2 class="font-display font-extrabold text-[clamp(22px,2.5vw,32px)] text-white tracking-tight leading-tight mb-5">
            Votre agence en ligne<br>en <em class="not-italic text-bimo-gold">moins de 10 min</em>
        </h2>
        <p class="font-body font-light text-sm text-white/50 leading-relaxed max-w-xs mb-7">Rejoignez les agences sénégalaises qui gèrent leur activité avec BimoTech.</p>
        <div class="flex flex-col gap-3">
            @foreach([['Conformité fiscale incluse','TVA 18%, NINEA, loi 81-18, TOM — automatiquement.'],['Quittances PDF légales','Générées et archivées automatiquement.'],['Essai gratuit 30 jours','Aucune carte bancaire requise.']] as [$t,$d])
            <div class="flex items-start gap-3">
                <div class="w-5 h-5 rounded-[5px] bg-bimo-gold/15 flex items-center justify-center flex-shrink-0 mt-0.5">
                    <svg class="w-3 h-3 text-bimo-gold" viewBox="0 0 12 12" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="2,6 5,9 10,3"/></svg>
                </div>
                <div>
                    <div class="font-body font-semibold text-sm text-white mb-0.5">{{ $t }}</div>
                    <div class="font-body text-xs text-white/40">{{ $d }}</div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    <div class="relative z-10 font-body text-sm text-white/40">Déjà un compte ? <a href="{{ route('login') }}" class="text-bimo-gold hover:text-white transition-colors duration-150">Se connecter →</a></div>
</div>

{{-- Panneau droit formulaire --}}
<main class="flex items-center justify-center p-8 bg-white overflow-y-auto">
    <div class="w-full max-w-[420px]">
        <h1 class="font-display font-extrabold text-2xl text-bimo-text tracking-tight mb-1">Créer votre agence</h1>
        <p class="font-body text-sm text-bimo-text/50 mb-6">Essai gratuit 30 jours — sans engagement</p>

        {{-- Erreurs --}}
        @if($errors->any())
        <div class="border-l-[3px] border-bimo-red bg-bimo-red/[5%] border border-bimo-red/20 rounded-[8px] px-4 py-2.5 mb-5">
            @foreach($errors->all() as $error)<p class="font-body text-xs text-bimo-red leading-relaxed">{{ $error }}</p>@endforeach
        </div>
        @endif

        {{-- Google --}}
        <a href="{{ route('auth.google') }}"
           class="flex items-center justify-center gap-2.5 w-full px-4 py-3 border border-bimo-navy/15 rounded-[10px] font-body font-semibold text-sm text-bimo-text bg-white hover:bg-bimo-bg hover:border-bimo-navy/25 transition-all duration-150 mb-5">
            <svg width="18" height="18" viewBox="0 0 48 48" class="flex-shrink-0">
                <path fill="#EA4335" d="M24 9.5c3.54 0 6.71 1.22 9.21 3.6l6.85-6.85C35.9 2.38 30.47 0 24 0 14.62 0 6.51 5.38 2.56 13.22l7.98 6.19C12.43 13.72 17.74 9.5 24 9.5z"/>
                <path fill="#4285F4" d="M46.98 24.55c0-1.57-.15-3.09-.38-4.55H24v9.02h12.94c-.58 2.96-2.26 5.48-4.78 7.18l7.73 6c4.51-4.18 7.09-10.36 7.09-17.65z"/>
                <path fill="#FBBC05" d="M10.53 28.59c-.48-1.45-.76-2.99-.76-4.59s.27-3.14.76-4.59l-7.98-6.19C.92 16.46 0 20.12 0 24c0 3.88.92 7.54 2.56 10.78l7.97-6.19z"/>
                <path fill="#34A853" d="M24 48c6.48 0 11.93-2.13 15.89-5.81l-7.73-6c-2.18 1.48-4.97 2.31-8.16 2.31-6.26 0-11.57-4.22-13.47-9.91l-7.98 6.19C6.51 42.62 14.62 48 24 48z"/>
            </svg>
            Continuer avec Google
        </a>

        {{-- Séparateur --}}
        <div class="flex items-center gap-3 mb-5">
            <div class="flex-1 h-px bg-bimo-navy/10"></div>
            <span class="font-body text-xs text-bimo-text/30">ou</span>
            <div class="flex-1 h-px bg-bimo-navy/10"></div>
        </div>

        <form method="POST" action="{{ route('agency.register.store') }}" class="space-y-3">
            @csrf

            <div class="space-y-1.5">
                <label class="block font-body font-medium text-sm text-bimo-text" for="agency_name">Nom de l'agence <span class="text-bimo-red">*</span></label>
                <input type="text" id="agency_name" name="agency_name" value="{{ old('agency_name') }}" placeholder="Ex : Immobilier Prestige Dakar" required autocomplete="organization"
                       class="w-full px-4 py-3 rounded-[10px] bg-white border font-body text-sm text-bimo-text placeholder:text-bimo-text/30 focus:outline-none focus:ring-2 transition-all duration-150 @error('agency_name') border-bimo-red focus:border-bimo-red focus:ring-bimo-red/15 @else border-bimo-navy/20 focus:border-bimo-gold focus:ring-bimo-gold/15 @enderror">
                @error('agency_name')<p class="font-body text-xs text-bimo-red">{{ $message }}</p>@enderror
            </div>

            <div class="space-y-1.5">
                <label class="block font-body font-medium text-sm text-bimo-text" for="admin_name">Votre nom <span class="text-bimo-red">*</span></label>
                <input type="text" id="admin_name" name="admin_name" value="{{ old('admin_name') }}" placeholder="Prénom et Nom" required autocomplete="name"
                       class="w-full px-4 py-3 rounded-[10px] bg-white border font-body text-sm text-bimo-text placeholder:text-bimo-text/30 focus:outline-none focus:ring-2 transition-all duration-150 @error('admin_name') border-bimo-red focus:border-bimo-red focus:ring-bimo-red/15 @else border-bimo-navy/20 focus:border-bimo-gold focus:ring-bimo-gold/15 @enderror">
                @error('admin_name')<p class="font-body text-xs text-bimo-red">{{ $message }}</p>@enderror
            </div>

            <div class="space-y-1.5">
                <label class="block font-body font-medium text-sm text-bimo-text" for="admin_email">Email <span class="text-bimo-red">*</span></label>
                <input type="email" id="admin_email" name="admin_email" value="{{ old('admin_email') }}" placeholder="votre@email.com" required autocomplete="email"
                       class="w-full px-4 py-3 rounded-[10px] bg-white border font-body text-sm text-bimo-text placeholder:text-bimo-text/30 focus:outline-none focus:ring-2 transition-all duration-150 @error('admin_email') border-bimo-red focus:border-bimo-red focus:ring-bimo-red/15 @else border-bimo-navy/20 focus:border-bimo-gold focus:ring-bimo-gold/15 @enderror">
                @error('admin_email')<p class="font-body text-xs text-bimo-red">{{ $message }}</p>@enderror
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div class="space-y-1.5">
                    <label class="block font-body font-medium text-sm text-bimo-text" for="admin_password">Mot de passe <span class="text-bimo-red">*</span></label>
                    <input type="password" id="admin_password" name="admin_password" placeholder="Min. 8 caractères" required autocomplete="new-password"
                           class="w-full px-4 py-3 rounded-[10px] bg-white border font-body text-sm text-bimo-text placeholder:text-bimo-text/30 focus:outline-none focus:ring-2 transition-all duration-150 @error('admin_password') border-bimo-red focus:border-bimo-red focus:ring-bimo-red/15 @else border-bimo-navy/20 focus:border-bimo-gold focus:ring-bimo-gold/15 @enderror">
                    @error('admin_password')<p class="font-body text-xs text-bimo-red">{{ $message }}</p>@enderror
                </div>
                <div class="space-y-1.5">
                    <label class="block font-body font-medium text-sm text-bimo-text" for="admin_password_confirmation">Confirmer <span class="text-bimo-red">*</span></label>
                    <input type="password" id="admin_password_confirmation" name="admin_password_confirmation" placeholder="Répétez" required autocomplete="new-password"
                           class="w-full px-4 py-3 rounded-[10px] bg-white border border-bimo-navy/20 font-body text-sm text-bimo-text placeholder:text-bimo-text/30 focus:outline-none focus:border-bimo-gold focus:ring-2 focus:ring-bimo-gold/15 transition-all duration-150">
                </div>
            </div>

            <div class="flex items-start gap-2.5 py-2">
                <input type="checkbox" id="cgu" name="cgu" value="1"
                       class="w-4 h-4 mt-0.5 rounded cursor-pointer accent-bimo-gold flex-shrink-0">
                <label for="cgu" class="font-body text-sm text-bimo-text/60 leading-relaxed cursor-pointer">
                    J'accepte les <a href="#" class="text-bimo-gold hover:underline font-medium">conditions générales d'utilisation</a>
                </label>
            </div>
            @error('cgu')<p class="font-body text-xs text-bimo-red -mt-1">{{ $message }}</p>@enderror

            <button type="submit"
                    class="w-full inline-flex items-center justify-center py-3.5 bg-[var(--ac)] text-white font-display font-bold text-sm rounded-[10px] hover:opacity-90 transition-opacity duration-150 cursor-pointer">
                Créer mon agence gratuitement
            </button>
        </form>

        <p class="text-center font-body text-sm text-bimo-text/40 mt-5">
            Déjà inscrit ? <a href="{{ route('login') }}" class="text-bimo-gold hover:text-bimo-text font-medium transition-colors duration-150">Se connecter →</a>
        </p>
    </div>
</main>

</body>
</html>
