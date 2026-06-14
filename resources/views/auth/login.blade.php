<!DOCTYPE html>
<html lang="fr" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Connexion — bee</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet"
          href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap"
          media="print" onload="this.media='all'">
    <noscript>
        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap">
    </noscript>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full bg-bimo-bg font-body antialiased">

<div class="min-h-screen flex">

    {{-- ═══ PANNEAU GAUCHE — Brand (desktop uniquement) ═══ --}}
    <div class="hidden lg:flex lg:w-1/2 xl:w-[55%] bg-bimo-navy flex-col justify-between p-12 relative overflow-hidden">

        {{-- Déco grille --}}
        <div class="absolute inset-0 opacity-[0.03]"
             style="background-image: linear-gradient(rgba(255,255,255,1) 1px, transparent 1px), linear-gradient(90deg, rgba(255,255,255,1) 1px, transparent 1px); background-size: 48px 48px;">
        </div>
        {{-- Halo gold --}}
        <div class="absolute bottom-0 right-0 w-96 h-96 rounded-full opacity-10"
             style="background: radial-gradient(circle, #C9A84C 0%, transparent 70%); transform: translate(30%, 30%)">
        </div>

        {{-- Logo --}}
        <a href="{{ url('/') }}" class="relative z-10 inline-flex">
            <x-bee-logo variant="white" size="md" />
        </a>

        {{-- Accroche centrale --}}
        <div class="relative z-10">
            <h1 class="font-display font-extrabold text-white leading-tight tracking-tight mb-4"
                style="font-size: clamp(28px, 3.5vw, 42px)">
                Gérez votre agence<br>
                <span class="text-bimo-gold">comme un pro</span>
            </h1>
            <p class="font-body font-light text-white/50 text-base leading-relaxed max-w-sm">
                Biens, contrats, paiements, quittances — tout centralisé.<br>
                Conforme TVA 18%, NINEA et loi 81-18 Sénégal.
            </p>
        </div>

        {{-- Stats --}}
        <div class="relative z-10 flex items-center gap-10">
            <div>
                <div class="font-display font-extrabold text-bimo-gold text-3xl">150+</div>
                <div class="font-body text-xs text-white/40 mt-1 uppercase tracking-widest">Biens gérés</div>
            </div>
            <div class="w-px h-10 bg-white/10"></div>
            <div>
                <div class="font-display font-extrabold text-bimo-gold text-3xl">12</div>
                <div class="font-body text-xs text-white/40 mt-1 uppercase tracking-widest">Agences actives</div>
            </div>
            <div class="w-px h-10 bg-white/10"></div>
            <div>
                <div class="font-display font-extrabold text-bimo-gold text-3xl">98%</div>
                <div class="font-body text-xs text-white/40 mt-1 uppercase tracking-widest">Recouvrement</div>
            </div>
        </div>
    </div>

    {{-- ═══ PANNEAU DROIT — Formulaire ═══ --}}
    <div class="flex-1 flex items-center justify-center px-6 py-12 bg-white overflow-y-auto">
        <div class="w-full max-w-[380px]">

            {{-- Logo mobile uniquement --}}
            <div class="flex justify-center mb-8 lg:hidden">
                <x-bee-logo variant="navy" size="md" />
            </div>

            {{-- Titre --}}
            <h2 class="font-display font-extrabold text-bimo-text text-2xl tracking-tight mb-1">
                Connexion
            </h2>
            <p class="font-body text-sm text-bimo-text/50 mb-8">
                Pas encore de compte ?
                <a href="{{ route('register') }}"
                   class="text-bimo-gold font-medium hover:text-bimo-text transition-colors duration-150">
                    Créer une agence gratuitement
                </a>
            </p>

            {{-- Erreurs --}}
            @if($errors->any())
            <div class="flex items-start gap-3 bg-bimo-red/[5%] border border-bimo-red/20 rounded-[10px] px-4 py-3 mb-5"
                 role="alert">
                <svg class="w-4 h-4 text-bimo-red flex-shrink-0 mt-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
                </svg>
                <div class="font-body text-sm text-bimo-red">
                    @foreach($errors->all() as $error)
                    <p>{{ $error }}</p>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Status --}}
            @if(session('status'))
            <div class="flex items-center gap-3 bg-bimo-gold/[8%] border border-bimo-gold/25 rounded-[10px] px-4 py-3 mb-5"
                 role="status">
                <svg class="w-4 h-4 text-bimo-gold flex-shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <polyline points="20 6 9 17 4 12"/>
                </svg>
                <p class="font-body text-sm text-bimo-gold">{{ session('status') }}</p>
            </div>
            @endif

            {{-- Formulaire --}}
            <form method="POST" action="{{ route('login') }}" id="login-form" novalidate
                  x-data="loginForm" @submit="start">
                @csrf

                {{-- Email --}}
                <div class="mb-4">
                    <label for="email" class="block font-body font-medium text-sm text-bimo-text mb-1.5">
                        Adresse email
                    </label>
                    <input type="email" id="email" name="email"
                           value="{{ old('email') }}"
                           placeholder="votre@agence.sn"
                           autocomplete="email" autofocus
                           aria-required="true"
                           class="w-full px-4 py-3 rounded-[10px] bg-white border font-body text-sm text-bimo-text
                                  placeholder:text-bimo-text/30 focus:outline-none focus:ring-2 transition-all duration-150
                                  @error('email') border-bimo-red focus:border-bimo-red focus:ring-bimo-red/15
                                  @else border-bimo-navy/20 focus:border-bimo-gold focus:ring-bimo-gold/15 @enderror">
                    @error('email')
                    <p class="mt-1 font-body text-xs text-bimo-red" role="alert">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Mot de passe --}}
                <div class="mb-5" x-data="passwordToggle">
                    <label for="password" class="block font-body font-medium text-sm text-bimo-text mb-1.5">
                        Mot de passe
                    </label>
                    <div class="relative">
                        <input :type="type"
                               id="password" name="password"
                               placeholder="••••••••"
                               autocomplete="current-password"
                               aria-required="true"
                               class="w-full pl-4 pr-11 py-3 rounded-[10px] bg-white border font-body text-sm text-bimo-text
                                      placeholder:text-bimo-text/30 focus:outline-none focus:ring-2 transition-all duration-150
                                      @error('password') border-bimo-red focus:border-bimo-red focus:ring-bimo-red/15
                                      @else border-bimo-navy/20 focus:border-bimo-gold focus:ring-bimo-gold/15 @enderror">
                        <button type="button"
                                @click="toggle"
                                :aria-label="label"
                                class="absolute right-3 top-1/2 -translate-y-1/2 p-1 text-bimo-text/30 hover:text-bimo-text/60 transition-colors duration-150">
                            <svg x-show="hidden" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>
                            </svg>
                            <svg x-show="show" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="display:none">
                                <path d="M17.94 17.94A10.07 10.07 0 0112 20c-7 0-11-8-11-8a18.45 18.45 0 015.06-5.94M9.9 4.24A9.12 9.12 0 0112 4c7 0 11 8 11 8a18.5 18.5 0 01-2.16 3.19m-6.72-1.07a3 3 0 11-4.24-4.24"/>
                                <line x1="1" y1="1" x2="23" y2="23"/>
                            </svg>
                        </button>
                    </div>
                    @error('password')
                    <p class="mt-1 font-body text-xs text-bimo-red" role="alert">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Se souvenir + mot de passe oublié --}}
                <div class="flex items-center justify-between mb-6">
                    <label class="flex items-center gap-2 cursor-pointer select-none">
                        <input type="checkbox" name="remember"
                               {{ old('remember') ? 'checked' : '' }}
                               class="w-4 h-4 rounded border-bimo-navy/20 text-bimo-gold cursor-pointer
                                      focus:ring-bimo-gold/20 focus:ring-2">
                        <span class="font-body text-sm text-bimo-text/60">Se souvenir de moi</span>
                    </label>
                    @if(Route::has('password.request'))
                    <a href="{{ route('password.request') }}"
                       class="font-body text-sm text-bimo-gold hover:text-bimo-text transition-colors duration-150">
                        Mot de passe oublié ?
                    </a>
                    @endif
                </div>

                {{-- Bouton connexion --}}
                <button type="submit"
                        :disabled="loading"
                        class="w-full flex items-center justify-center gap-2 px-6 py-3.5 rounded-[10px]
                               bg-bimo-navy hover:bg-bimo-navy-dk text-white
                               font-display font-bold text-sm
                               transition-all duration-150
                               disabled:opacity-60 disabled:cursor-not-allowed">
                    <svg x-show="loading" class="w-4 h-4 animate-spin" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="display:none">
                        <path d="M21 12a9 9 0 11-6.219-8.56"/>
                    </svg>
                    <span x-text="label">Se connecter</span>
                </button>
            </form>

            {{-- Divider --}}
            <div class="flex items-center gap-3 my-6">
                <div class="flex-1 h-px bg-bimo-navy/10"></div>
                <span class="font-body text-xs text-bimo-text/30">ou</span>
                <div class="flex-1 h-px bg-bimo-navy/10"></div>
            </div>

            {{-- Google OAuth --}}
            <a href="{{ route('auth.google') }}"
               class="flex items-center justify-center gap-3 w-full px-5 py-3 rounded-[10px]
                      border border-bimo-navy/15 hover:border-bimo-navy/30 hover:bg-bimo-bg
                      font-body font-medium text-sm text-bimo-text
                      transition-all duration-150 mb-6">
                <svg class="w-[18px] h-[18px] flex-shrink-0" viewBox="0 0 48 48">
                    <path fill="#EA4335" d="M24 9.5c3.54 0 6.71 1.22 9.21 3.6l6.85-6.85C35.9 2.38 30.47 0 24 0 14.62 0 6.51 5.38 2.56 13.22l7.98 6.19C12.43 13.72 17.74 9.5 24 9.5z"/>
                    <path fill="#4285F4" d="M46.98 24.55c0-1.57-.15-3.09-.38-4.55H24v9.02h12.94c-.58 2.96-2.26 5.48-4.78 7.18l7.73 6c4.51-4.18 7.09-10.36 7.09-17.65z"/>
                    <path fill="#FBBC05" d="M10.53 28.59c-.48-1.45-.76-2.99-.76-4.59s.27-3.14.76-4.59l-7.98-6.19C.92 16.46 0 20.12 0 24c0 3.88.92 7.54 2.56 10.78l7.97-6.19z"/>
                    <path fill="#34A853" d="M24 48c6.48 0 11.93-2.13 15.89-5.81l-7.73-6c-2.18 1.48-4.97 2.31-8.16 2.31-6.26 0-11.57-4.22-13.47-9.91l-7.98 6.19C6.51 42.62 14.62 48 24 48z"/>
                </svg>
                Continuer avec Google
            </a>

            {{-- Lien inscription --}}
            <p class="text-center font-body text-sm text-bimo-text/50">
                Nouvelle agence ?
                <a href="{{ route('register') }}"
                   class="text-bimo-gold font-medium hover:text-bimo-text transition-colors duration-150">
                    Créer un compte gratuit →
                </a>
            </p>

        </div>
    </div>

</div>

</body>
</html>
