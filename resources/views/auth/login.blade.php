<x-guest-layout>
    <div class="auth-card">

        <div class="auth-card-title">Connexion</div>
        <div class="auth-card-sub">Accédez à votre espace de gestion</div>

        {{-- Status --}}
        @if (session('status'))
            <div class="form-status">{{ session('status') }}</div>
        @endif

        {{-- Erreur email (agence suspendue, etc.) --}}
        @if ($errors->has('email'))
            <div style="background:#fef2f2;border:1px solid #fecaca;color:#991b1b;font-size:13px;padding:10px 14px;border-radius:10px;margin-bottom:16px;">
                ⚠️ {{ $errors->first('email') }}
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf

            {{-- Email --}}
            <div class="form-group">
                <label class="form-label" for="email">Adresse email</label>
                <input
                    id="email"
                    type="email"
                    name="email"
                    value="{{ old('email') }}"
                    class="form-input"
                    placeholder="votre@email.com"
                    required
                    autofocus
                    autocomplete="username"
                >
                @error('email')
                    <div class="form-error">{{ $message }}</div>
                @enderror
            </div>

            {{-- Mot de passe --}}
            <div class="form-group">
                <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:6px;">
                    <label class="form-label" for="password" style="margin-bottom:0;">Mot de passe</label>
                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" class="auth-link" style="font-size:12px;">
                            Mot de passe oublié ?
                        </a>
                    @endif
                </div>
                <input
                    id="password"
                    type="password"
                    name="password"
                    class="form-input"
                    placeholder="••••••••"
                    required
                    autocomplete="current-password"
                >
                @error('password')
                    <div class="form-error">{{ $message }}</div>
                @enderror
            </div>

            {{-- Se souvenir de moi --}}
            <div class="form-check">
                <input id="remember_me" type="checkbox" name="remember">
                <label for="remember_me">Se souvenir de moi</label>
            </div>

            {{-- Bouton --}}
            <button type="submit" class="btn-auth">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="width:16px;height:16px;">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                </svg>
                Se connecter
            </button>

        </form>

    </div>

    {{-- Lien inscription agence --}}
    <div style="text-align:center;margin-top:16px;">
        <span style="font-size:13px;color:#64748b;">Pas encore de compte ?</span>
        <a href="{{ route('agency.register') }}" class="auth-link" style="margin-left:6px;">
            Créer votre agence gratuitement →
        </a>
    </div>

</x-guest-layout>