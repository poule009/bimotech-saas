@extends('layouts.guest')

@section('title', 'Nouveau mot de passe')

@section('content')
    <div class="mb-[30px]">
        <h2 class="font-display font-semibold text-[26px] mb-1.5">Nouveau mot de passe</h2>
        <p class="text-[13.5px] text-muted">Choisissez un mot de passe pour votre compte.</p>
    </div>

    {{-- Erreurs de jeton / email invalide ou expiré --}}
    @error('email')
        <div class="mb-6 rounded bg-error/10 border border-error/25 px-4 py-3 text-[13px] text-error">
            {{ $message }}
        </div>
    @enderror

    <form method="POST" action="{{ route('password.store') }}" novalidate>
        @csrf

        {{-- Jeton + email transmis par le lien reçu par email (champs cachés) --}}
        <input type="hidden" name="token" value="{{ $request->route('token') }}">
        <input type="hidden" name="email" value="{{ old('email', $request->email) }}">

        {{-- Nouveau mot de passe --}}
        <div class="mb-[18px]">
            <label for="password" class="auth-field-label">Nouveau mot de passe</label>
            <div class="relative" x-data="passwordToggle">
                <input id="password" type="password" name="password"
                       required autocomplete="new-password" placeholder="8 caractères min."
                       x-bind:type="type"
                       class="auth-input pr-20 @error('password') auth-input-error @enderror">
                <button type="button" x-on:click="toggle" x-text="label"
                        class="absolute right-3 top-1/2 -translate-y-1/2 text-[13px] text-muted bg-transparent border-0 cursor-pointer">Afficher</button>
            </div>
            @error('password')
                <p class="field-error">{{ $message }}</p>
            @enderror
        </div>

        {{-- Confirmation --}}
        <div class="mb-[26px]">
            <label for="password_confirmation" class="auth-field-label">Confirmer le mot de passe</label>
            <div class="relative" x-data="passwordToggle">
                <input id="password_confirmation" type="password" name="password_confirmation"
                       required autocomplete="new-password" placeholder="••••••••"
                       x-bind:type="type"
                       class="auth-input pr-20">
                <button type="button" x-on:click="toggle" x-text="label"
                        class="absolute right-3 top-1/2 -translate-y-1/2 text-[13px] text-muted bg-transparent border-0 cursor-pointer">Afficher</button>
            </div>
        </div>

        <button type="submit" class="btn-primary">Réinitialiser le mot de passe</button>
    </form>

    <div class="text-center mt-[26px] text-[13px] text-muted">
        <a class="auth-link" href="{{ route('login') }}">← Retour à la connexion</a>
    </div>
@endsection
