@extends('layouts.app')
@section('header', 'Configurer le 2FA')

@section('content')

<div class="max-w-lg mx-auto space-y-4">

    <div class="bg-white rounded-[14px] border border-bimo-navy/10 overflow-hidden">
        <div class="flex items-center gap-3 px-5 py-4 border-b border-bimo-navy/[5%] bg-bimo-bg2">
            <div class="w-9 h-9 rounded-[10px] bg-bimo-navy/10 flex items-center justify-center flex-shrink-0">
                <svg class="w-4 h-4 text-bimo-text/60" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="5" y="11" width="14" height="10" rx="2"/><path d="M8 11V7a4 4 0 018 0v4"/></svg>
            </div>
            <div>
                <div class="font-display font-bold text-sm text-bimo-text">Configurer l'authentification à deux facteurs</div>
                <div class="font-body text-xs text-bimo-text/40 mt-0.5">Protégez votre compte superadmin avec Google Authenticator ou Authy</div>
            </div>
        </div>
        <div class="px-5 py-6 space-y-5">

            @if($errors->any())
            <div class="flex items-start gap-2 bg-bimo-red/[5%] border border-bimo-red/20 rounded-[10px] px-4 py-3">
                <svg class="w-4 h-4 text-bimo-red flex-shrink-0 mt-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                <span class="font-body text-sm text-bimo-red">{{ $errors->first() }}</span>
            </div>
            @endif

            {{-- Étape 1 --}}
            <div class="flex items-start gap-3">
                <div class="w-6 h-6 rounded-full bg-bimo-navy flex items-center justify-center font-display font-bold text-[11px] text-white flex-shrink-0 mt-0.5">1</div>
                <p class="font-body text-sm text-bimo-text/70 leading-relaxed">Installez <strong class="text-bimo-text">Google Authenticator</strong>, <strong class="text-bimo-text">Authy</strong> ou toute app TOTP compatible sur votre téléphone.</p>
            </div>

            {{-- Étape 2 --}}
            <div class="flex items-start gap-3">
                <div class="w-6 h-6 rounded-full bg-bimo-navy flex items-center justify-center font-display font-bold text-[11px] text-white flex-shrink-0 mt-0.5">2</div>
                <p class="font-body text-sm text-bimo-text/70 leading-relaxed">Scannez ce QR code avec l'application, ou saisissez la clé manuellement.</p>
            </div>

            {{-- QR Code --}}
            <div class="flex justify-center py-4 px-4 bg-bimo-bg rounded-[10px] border border-bimo-navy/[8%]">
                {!! $qrCode !!}
            </div>

            <div>
                <div class="font-body font-medium text-[10px] uppercase tracking-widest text-bimo-text/40 text-center mb-2">Clé de configuration manuelle</div>
                <div class="bg-bimo-bg border border-bimo-navy/[8%] rounded-[8px] px-4 py-3 text-center font-body font-bold text-base text-bimo-text tracking-[3px]" style="font-family:'Courier New',monospace">{{ $secret }}</div>
            </div>

            {{-- Étape 3 --}}
            <div class="flex items-start gap-3">
                <div class="w-6 h-6 rounded-full bg-bimo-navy flex items-center justify-center font-display font-bold text-[11px] text-white flex-shrink-0 mt-0.5">3</div>
                <p class="font-body text-sm text-bimo-text/70 leading-relaxed">Saisissez le code à 6 chiffres affiché dans l'application pour confirmer la configuration.</p>
            </div>

            <form method="POST" action="{{ route('superadmin.2fa.confirm') }}" class="space-y-4">
                @csrf
                <div class="space-y-1.5">
                    <input type="text" name="code"
                           class="w-full px-4 py-4 rounded-[10px] bg-white border text-center font-bold text-2xl tracking-[6px] focus:outline-none focus:ring-2 transition-all duration-150 @error('code') border-bimo-red focus:border-bimo-red focus:ring-bimo-red/15 @else border-bimo-navy/20 focus:border-bimo-gold focus:ring-bimo-gold/15 @enderror"
                           style="font-family:'Courier New',monospace"
                           inputmode="numeric" autocomplete="one-time-code" maxlength="6" placeholder="000000" autofocus>
                    @error('code')
                    <p class="font-body text-xs text-bimo-red text-center">{{ $message }}</p>
                    @enderror
                </div>
                <button type="submit"
                        class="w-full inline-flex items-center justify-center gap-2 px-5 py-3 bg-bimo-navy text-white font-display font-bold text-sm rounded-[10px] hover:bg-bimo-navy-dk transition-colors duration-150 cursor-pointer">
                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                    Confirmer et activer le 2FA
                </button>
            </form>

        </div>
    </div>

</div>
@endsection
