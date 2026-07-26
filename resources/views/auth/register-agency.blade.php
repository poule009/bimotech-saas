@extends('layouts.guest')

@section('title', 'Créer une agence')

@section('content')
    <div class="mb-[30px]">
        <h2 class="font-display font-semibold text-[26px] mb-1.5">Créez votre agence</h2>
        <p class="text-[13.5px] text-muted">Quelques informations suffisent pour démarrer.</p>
    </div>

    @error('general')
        <div class="mb-5 rounded bg-error/10 border border-error/25 px-4 py-3 text-[13px] text-error">
            {{ $message }}
        </div>
    @enderror

    <form method="POST" action="{{ route('agency.register.store') }}" novalidate>
        @csrf

        {{-- Nom de l'agence --}}
        <div class="mb-[18px]">
            <label for="agency_name" class="auth-field-label">Nom de l'agence</label>
            <input id="agency_name" type="text" name="agency_name" value="{{ old('agency_name') }}"
                   required autofocus placeholder="Ex. Teranga Immobilier"
                   class="auth-input @error('agency_name') auth-input-error @enderror">
            @error('agency_name')
                <p class="field-error">{{ $message }}</p>
            @enderror
        </div>

        {{-- Pays — choix explicite, jamais déduit : il commande le régime fiscal
             et les mentions légales des documents émis. Pas d'option présélectionnée. --}}
        <div class="mb-[18px]">
            <label for="pays" class="auth-field-label">Pays de l'agence</label>
            <select id="pays" name="pays" required
                    class="auth-input @error('pays') auth-input-error @enderror">
                <option value="" disabled @selected(! old('pays'))>Choisissez votre pays</option>
                @foreach ($paysDisponibles as $code => $nom)
                    <option value="{{ $code }}" @selected(old('pays') === $code)>{{ $nom }}</option>
                @endforeach
            </select>
            @error('pays')
                <p class="field-error">{{ $message }}</p>
            @enderror
        </div>

        {{-- Nom de l'administrateur --}}
        <div class="mb-[18px]">
            <label for="admin_name" class="auth-field-label">Nom de l'administrateur</label>
            <input id="admin_name" type="text" name="admin_name" value="{{ old('admin_name') }}"
                   required placeholder="Prénom et nom"
                   class="auth-input @error('admin_name') auth-input-error @enderror">
            @error('admin_name')
                <p class="field-error">{{ $message }}</p>
            @enderror
        </div>

        {{-- Email --}}
        <div class="mb-[18px]">
            <label for="admin_email" class="auth-field-label">Email</label>
            <input id="admin_email" type="email" name="admin_email" value="{{ old('admin_email') }}"
                   required autocomplete="username" placeholder="vous@agence.sn"
                   class="auth-input @error('admin_email') auth-input-error @enderror">
            @error('admin_email')
                <p class="field-error">{{ $message }}</p>
            @enderror
        </div>

        {{-- Mot de passe + confirmation --}}
        <div class="mb-[18px] grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label for="admin_password" class="auth-field-label">Mot de passe</label>
                <div class="relative" x-data="passwordToggle">
                    <input id="admin_password" type="password" name="admin_password"
                           required autocomplete="new-password" placeholder="8 caractères min."
                           x-bind:type="type"
                           class="auth-input pr-20 @error('admin_password') auth-input-error @enderror">
                    <button type="button" x-on:click="toggle" x-text="label"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-[13px] text-muted bg-transparent border-0 cursor-pointer">Afficher</button>
                </div>
            </div>
            <div>
                <label for="admin_password_confirmation" class="auth-field-label">Confirmer</label>
                <div class="relative" x-data="passwordToggle">
                    <input id="admin_password_confirmation" type="password" name="admin_password_confirmation"
                           required autocomplete="new-password" placeholder="••••••••"
                           x-bind:type="type"
                           class="auth-input pr-20">
                    <button type="button" x-on:click="toggle" x-text="label"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-[13px] text-muted bg-transparent border-0 cursor-pointer">Afficher</button>
                </div>
            </div>
        </div>
        @error('admin_password')
            <p class="field-error -mt-2 mb-2">{{ $message }}</p>
        @enderror

        {{-- CGU --}}
        <label class="flex items-start gap-2.5 text-[12.5px] text-muted mb-[26px] leading-[1.5] cursor-pointer">
            <input type="checkbox" name="cgu" value="1" @checked(old('cgu'))
                   class="mt-0.5 w-[15px] h-[15px] accent-teal shrink-0">
            <span>
                J'accepte les <a class="auth-link" href="{{ route('mentions-legales') }}" target="_blank">conditions d'utilisation</a>
                et la <a class="auth-link" href="{{ route('confidentialite') }}" target="_blank">politique de confidentialité</a>.
            </span>
        </label>
        @error('cgu')
            <p class="field-error -mt-5 mb-4">{{ $message }}</p>
        @enderror

        <button type="submit" class="btn-primary">Créer mon agence</button>
    </form>

    <div class="flex items-center gap-3.5 my-[22px] text-xs text-muted">
        <span class="flex-1 h-px bg-line"></span> ou <span class="flex-1 h-px bg-line"></span>
    </div>

    <a href="{{ route('auth.google') }}" class="btn-google">
        <svg width="16" height="16" viewBox="0 0 18 18"><path fill="#4285F4" d="M17.64 9.2c0-.64-.06-1.25-.16-1.84H9v3.48h4.84a4.14 4.14 0 01-1.8 2.72v2.26h2.92C16.6 14.06 17.64 11.86 17.64 9.2z"/><path fill="#34A853" d="M9 18c2.43 0 4.47-.8 5.96-2.18l-2.92-2.26c-.8.54-1.83.86-3.04.86-2.34 0-4.32-1.58-5.03-3.7H.96v2.33A9 9 0 009 18z"/><path fill="#FBBC05" d="M3.97 10.72A5.4 5.4 0 013.68 9c0-.6.1-1.18.29-1.72V4.95H.96A9 9 0 000 9c0 1.45.35 2.83.96 4.05l3.01-2.33z"/><path fill="#EA4335" d="M9 3.58c1.32 0 2.5.45 3.44 1.35l2.58-2.58C13.46.9 11.43 0 9 0A9 9 0 00.96 4.95l3.01 2.33C4.68 5.16 6.66 3.58 9 3.58z"/></svg>
        Continuer avec Google
    </a>

    <div class="text-center mt-[26px] text-[13px] text-muted">
        Déjà un compte ?
        <a class="auth-link" href="{{ route('login') }}">Se connecter</a>
    </div>
@endsection
