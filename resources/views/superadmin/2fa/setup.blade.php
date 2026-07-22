@extends('layouts.superadmin')

@section('title', 'Activer la double authentification')

@section('content')
<div class="max-w-[560px] mx-auto">
    <div class="text-[12px] font-semibold tracking-[0.12em] uppercase text-gold">Sécurité du compte</div>
    <h1 class="font-display font-medium text-[30px] text-ink mt-1 mb-2">Double authentification</h1>
    <p class="text-[14px] text-muted leading-relaxed mb-6">
        Obligatoire pour les comptes Super Admin. Scannez le QR code avec une application
        d'authentification (Google Authenticator, Authy, 1Password…), puis saisissez le code à 6 chiffres.
    </p>

    <div class="bg-white border border-line rounded-xl p-8">
        {{-- QR code (SVG rendu côté serveur) --}}
        <div class="flex justify-center mb-6">
            <div class="p-3 bg-white border border-line rounded-lg w-[212px] h-[212px] flex items-center justify-center">
                {!! $qrCode !!}
            </div>
        </div>

        {{-- Clé de secours en clair (saisie manuelle si le QR est illisible) --}}
        <div class="text-center mb-6">
            <div class="text-[12px] text-muted mb-1">Ou saisissez cette clé manuellement :</div>
            <code class="inline-block text-[14px] font-mono tracking-[0.15em] text-ink bg-paper-dim rounded-lg px-3 py-2 break-all">{{ $secret }}</code>
        </div>

        <form method="POST" action="{{ route('superadmin.2fa.confirm') }}" class="space-y-4">
            @csrf
            <div>
                <label for="code" class="block text-[13px] font-semibold text-ink mb-1.5">Code de vérification</label>
                <input id="code" name="code" type="text" inputmode="numeric" autocomplete="one-time-code"
                       maxlength="6" pattern="[0-9]{6}" required autofocus
                       class="w-full text-center text-[20px] tracking-[0.4em] font-mono rounded-lg border border-line px-4 py-3 text-ink focus:border-teal focus:outline-none @error('code') border-error @enderror"
                       placeholder="000000">
                @error('code')
                    <p class="text-[12.5px] text-error mt-1.5">{{ $message }}</p>
                @enderror
            </div>

            <button type="submit"
                    class="w-full bg-gold text-white font-semibold text-[14px] rounded-lg px-4 py-3 hover:bg-gold-deep transition-colors">
                Activer la double authentification
            </button>
        </form>
    </div>
</div>
@endsection
