@extends('layouts.superadmin')

@section('title', 'Vérification à deux facteurs')

@section('content')
<div class="max-w-[440px] mx-auto">
    <div class="text-[12px] font-semibold tracking-[0.12em] uppercase text-gold">Sécurité du compte</div>
    <h1 class="font-display font-medium text-[30px] text-ink mt-1 mb-2">Vérification requise</h1>
    <p class="text-[14px] text-muted leading-relaxed mb-6">
        Saisissez le code à 6 chiffres de votre application d'authentification, ou l'un de vos
        codes de secours si vous n'avez pas accès à votre téléphone.
    </p>

    <div class="bg-white border border-line rounded-xl p-8">
        <form method="POST" action="{{ route('superadmin.2fa.verify') }}" class="space-y-4">
            @csrf
            <div>
                <label for="code" class="block text-[13px] font-semibold text-ink mb-1.5">Code d'authentification</label>
                <input id="code" name="code" type="text" inputmode="text" autocomplete="one-time-code"
                       required autofocus
                       class="w-full text-center text-[18px] tracking-[0.25em] font-mono rounded-lg border border-line px-4 py-3 text-ink focus:border-teal focus:outline-none @error('code') border-error @enderror"
                       placeholder="000000">
                @error('code')
                    <p class="text-[12.5px] text-error mt-1.5">{{ $message }}</p>
                @enderror
            </div>

            <button type="submit"
                    class="w-full bg-gold text-white font-semibold text-[14px] rounded-lg px-4 py-3 hover:bg-gold-deep transition-colors">
                Vérifier
            </button>
        </form>
    </div>

    <form method="POST" action="{{ route('logout') }}" class="mt-4 text-center">
        @csrf
        <button type="submit" class="text-[13px] text-muted hover:text-ink font-medium">Se déconnecter</button>
    </form>
</div>
@endsection
