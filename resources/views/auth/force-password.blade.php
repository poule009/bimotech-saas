@extends('layouts.guest')

@section('title', 'Nouveau mot de passe')

@section('content')
    <div class="mb-[30px]">
        <h2 class="font-display font-semibold text-[26px] mb-1.5">Choisissez votre mot de passe</h2>
        <p class="text-[13.5px] text-muted">Pour votre sécurité, définissez un nouveau mot de passe personnel avant d'accéder à votre espace.</p>
    </div>

    <form method="POST" action="{{ route($updateRoute ?? 'admin.password.force.update') }}" novalidate>
        @csrf

        <div class="mb-[18px]">
            <label for="password" class="auth-field-label">Nouveau mot de passe</label>
            <div class="relative" x-data="passwordToggle">
                <input id="password" type="password" name="password"
                       required autofocus autocomplete="new-password" placeholder="••••••••"
                       x-bind:type="type"
                       class="auth-input pr-20 @error('password') auth-input-error @enderror">
                <button type="button" x-on:click="toggle" x-text="label"
                        class="absolute right-3 top-1/2 -translate-y-1/2 text-[13px] text-muted bg-transparent border-0 cursor-pointer">Afficher</button>
            </div>
            @error('password')<p class="field-error">{{ $message }}</p>@enderror
            <p class="mt-1.5 text-[12px] text-muted">Au moins 8 caractères, avec majuscule, minuscule, chiffre et symbole.</p>
        </div>

        <div class="mb-[26px]">
            <label for="password_confirmation" class="auth-field-label">Confirmer le mot de passe</label>
            <input id="password_confirmation" type="password" name="password_confirmation"
                   required autocomplete="new-password" placeholder="••••••••" class="auth-input">
        </div>

        <button type="submit" class="btn-primary">Enregistrer et continuer</button>
    </form>

    <form method="POST" action="{{ route('logout') }}" class="text-center mt-[26px] text-[13px] text-muted">
        @csrf
        <button type="submit" class="auth-link bg-transparent border-0 cursor-pointer">Se déconnecter</button>
    </form>
@endsection
