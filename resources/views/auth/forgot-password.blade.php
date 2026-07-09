@extends('layouts.guest')

@section('title', 'Mot de passe oublié')

@section('content')
    <div class="mb-[30px]">
        <h2 class="font-display font-semibold text-[26px] mb-1.5">Mot de passe oublié</h2>
        <p class="text-[13.5px] text-muted">Indiquez votre email, on vous envoie un lien de réinitialisation.</p>
    </div>

    {{-- Confirmation « email envoyé » (après soumission réussie) --}}
    @if (session('status'))
        <div class="mb-6 rounded bg-green/10 border border-green/25 px-4 py-3.5 text-[13px] text-green leading-[1.6]">
            <strong class="font-semibold">Email envoyé.</strong>
            Vérifiez votre boîte de réception — le lien est valable 60 minutes.
            Rien reçu ? Pensez à regarder vos spams, ou renvoyez la demande ci-dessous.
        </div>
    @endif

    <form method="POST" action="{{ route('password.email') }}" novalidate>
        @csrf

        <div class="mb-[26px]">
            <label for="email" class="auth-field-label">Adresse email</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}"
                   required autofocus autocomplete="username" placeholder="vous@agence.sn"
                   class="auth-input @error('email') auth-input-error @enderror">
            @error('email')
                <p class="field-error">{{ $message }}</p>
            @enderror
        </div>

        <button type="submit" class="btn-primary">Envoyer le lien</button>
    </form>

    <div class="text-center mt-[26px] text-[13px] text-muted">
        <a class="auth-link" href="{{ route('login') }}">← Retour à la connexion</a>
    </div>
@endsection
