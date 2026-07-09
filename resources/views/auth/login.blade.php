@extends('layouts.guest')

@section('title', 'Connexion')

@section('content')
    <div class="mb-[30px]">
        <h2 class="font-display font-semibold text-[26px] mb-1.5">Content de vous revoir</h2>
        <p class="text-[13.5px] text-muted">Connectez-vous pour accéder à votre agence.</p>
    </div>

    {{-- Message de session (ex. lien de réinitialisation envoyé) --}}
    @if (session('status'))
        <div class="mb-5 rounded bg-green/10 border border-green/25 px-4 py-3 text-[13px] text-green">
            {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}" novalidate>
        @csrf

        {{-- Email --}}
        <div class="mb-[18px]">
            <label for="email" class="auth-field-label">Adresse email</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}"
                   required autofocus autocomplete="username" placeholder="vous@agence.sn"
                   class="auth-input @error('email') auth-input-error @enderror">
            @error('email')
                <p class="field-error">{{ $message }}</p>
            @enderror
        </div>

        {{-- Mot de passe --}}
        <div class="mb-[18px]">
            <label for="password" class="auth-field-label">Mot de passe</label>
            <div class="relative" x-data="passwordToggle">
                <input id="password" type="password" name="password"
                       required autocomplete="current-password" placeholder="••••••••"
                       x-bind:type="type"
                       class="auth-input pr-20 @error('password') auth-input-error @enderror">
                <button type="button" x-on:click="toggle" x-text="label"
                        class="absolute right-3 top-1/2 -translate-y-1/2 text-[13px] text-muted bg-transparent border-0 cursor-pointer">Afficher</button>
            </div>
            @error('password')
                <p class="field-error">{{ $message }}</p>
            @enderror
        </div>

        {{-- Se souvenir + mot de passe oublié --}}
        <div class="flex items-center justify-between mb-[26px] text-[13px]">
            <label class="flex items-center gap-2 text-muted cursor-pointer">
                <input type="checkbox" name="remember" class="w-[15px] h-[15px] accent-teal">
                Se souvenir de moi
            </label>
            @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}" class="auth-link">Mot de passe oublié ?</a>
            @endif
        </div>

        <button type="submit" class="btn-primary">Se connecter</button>
    </form>

    <div class="flex items-center gap-3.5 my-[22px] text-xs text-muted">
        <span class="flex-1 h-px bg-line"></span> ou <span class="flex-1 h-px bg-line"></span>
    </div>

    <a href="{{ route('auth.google') }}" class="btn-google">
        <svg width="16" height="16" viewBox="0 0 18 18"><path fill="#4285F4" d="M17.64 9.2c0-.64-.06-1.25-.16-1.84H9v3.48h4.84a4.14 4.14 0 01-1.8 2.72v2.26h2.92C16.6 14.06 17.64 11.86 17.64 9.2z"/><path fill="#34A853" d="M9 18c2.43 0 4.47-.8 5.96-2.18l-2.92-2.26c-.8.54-1.83.86-3.04.86-2.34 0-4.32-1.58-5.03-3.7H.96v2.33A9 9 0 009 18z"/><path fill="#FBBC05" d="M3.97 10.72A5.4 5.4 0 013.68 9c0-.6.1-1.18.29-1.72V4.95H.96A9 9 0 000 9c0 1.45.35 2.83.96 4.05l3.01-2.33z"/><path fill="#EA4335" d="M9 3.58c1.32 0 2.5.45 3.44 1.35l2.58-2.58C13.46.9 11.43 0 9 0A9 9 0 00.96 4.95l3.01 2.33C4.68 5.16 6.66 3.58 9 3.58z"/></svg>
        Continuer avec Google
    </a>

    <div class="text-center mt-[26px] text-[13px] text-muted">
        Pas encore de compte ?
        <a class="auth-link" href="{{ route('agency.register') }}">Créer une agence</a>
    </div>
@endsection
