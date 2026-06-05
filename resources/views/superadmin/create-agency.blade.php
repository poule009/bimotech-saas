@extends('layouts.app')
@section('header', 'Nouvelle agence')

@section('content')
<div class="max-w-2xl">

    <nav class="flex items-center gap-2 font-body text-sm text-bimo-text/40 mb-5">
        <a href="{{ route('superadmin.dashboard') }}" class="hover:text-bimo-text transition-colors duration-150">Agences</a>
        <svg class="w-3 h-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
        <span class="text-bimo-text font-semibold">Nouvelle agence</span>
    </nav>

    @if($errors->any())
    <div class="flex items-start gap-3 bg-bimo-red/[5%] border border-bimo-red/20 rounded-[10px] px-4 py-3 mb-5">
        <svg class="w-4 h-4 text-bimo-red flex-shrink-0 mt-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
        <div class="font-body text-sm text-bimo-red">
            <strong>Veuillez corriger les erreurs :</strong>
            <ul class="mt-1 list-disc pl-4">
                @foreach($errors->all() as $error)<li class="text-xs">{{ $error }}</li>@endforeach
            </ul>
        </div>
    </div>
    @endif

    <form method="POST" action="{{ route('superadmin.agencies.store') }}" class="space-y-4">
        @csrf

        {{-- Infos agence --}}
        <div class="bg-white rounded-[14px] border border-bimo-navy/10 overflow-hidden">
            <div class="flex items-center gap-3 px-5 py-4 border-b border-bimo-navy/[5%] bg-bimo-bg2">
                <div class="w-8 h-8 rounded-[8px] bg-bimo-gold/15 flex items-center justify-center flex-shrink-0">
                    <svg class="w-4 h-4 text-bimo-gold" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                </div>
                <span class="font-display font-bold text-sm text-bimo-text">Informations de l'agence</span>
            </div>
            <div class="px-5 py-5">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="space-y-1.5">
                        <label class="block font-body font-medium text-sm text-bimo-text" for="agency_name">Nom de l'agence <span class="text-bimo-red">*</span></label>
                        <input type="text" id="agency_name" name="agency_name" value="{{ old('agency_name') }}" required placeholder="Ex : Agence Immobilière Dakar"
                               class="w-full px-4 py-3 rounded-[10px] bg-white border font-body text-sm text-bimo-text placeholder:text-bimo-text/35 focus:outline-none focus:ring-2 transition-all duration-150 @error('agency_name') border-bimo-red focus:border-bimo-red focus:ring-bimo-red/15 @else border-bimo-navy/20 focus:border-bimo-gold focus:ring-bimo-gold/15 @enderror">
                        @error('agency_name')<p class="font-body text-xs text-bimo-red">{{ $message }}</p>@enderror
                    </div>
                    <div class="space-y-1.5">
                        <label class="block font-body font-medium text-sm text-bimo-text" for="agency_email">Email de l'agence <span class="text-bimo-red">*</span></label>
                        <input type="email" id="agency_email" name="agency_email" value="{{ old('agency_email') }}" required placeholder="contact@agence.sn"
                               class="w-full px-4 py-3 rounded-[10px] bg-white border font-body text-sm text-bimo-text placeholder:text-bimo-text/35 focus:outline-none focus:ring-2 transition-all duration-150 @error('agency_email') border-bimo-red focus:border-bimo-red focus:ring-bimo-red/15 @else border-bimo-navy/20 focus:border-bimo-gold focus:ring-bimo-gold/15 @enderror">
                        @error('agency_email')<p class="font-body text-xs text-bimo-red">{{ $message }}</p>@enderror
                    </div>
                    <div class="space-y-1.5">
                        <label class="block font-body font-medium text-sm text-bimo-text" for="agency_telephone">Téléphone <span class="font-light text-bimo-text/40">(optionnel)</span></label>
                        <input type="text" id="agency_telephone" name="agency_telephone" value="{{ old('agency_telephone') }}" placeholder="+221 77 000 00 00"
                               class="w-full px-4 py-3 rounded-[10px] bg-white border border-bimo-navy/20 font-body text-sm text-bimo-text placeholder:text-bimo-text/35 focus:outline-none focus:border-bimo-gold focus:ring-2 focus:ring-bimo-gold/15 transition-all duration-150">
                    </div>
                    <div class="space-y-1.5">
                        <label class="block font-body font-medium text-sm text-bimo-text" for="agency_adresse">Adresse <span class="font-light text-bimo-text/40">(optionnel)</span></label>
                        <input type="text" id="agency_adresse" name="agency_adresse" value="{{ old('agency_adresse') }}" placeholder="Rue 10, Dakar"
                               class="w-full px-4 py-3 rounded-[10px] bg-white border border-bimo-navy/20 font-body text-sm text-bimo-text placeholder:text-bimo-text/35 focus:outline-none focus:border-bimo-gold focus:ring-2 focus:ring-bimo-gold/15 transition-all duration-150">
                    </div>
                </div>
                <div class="mt-4 flex items-start gap-2 bg-bimo-gold/[6%] border border-bimo-gold/20 rounded-[8px] px-4 py-3">
                    <svg class="w-4 h-4 text-bimo-gold flex-shrink-0 mt-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                    <p class="font-body text-xs text-bimo-gold">Un abonnement <strong>essai gratuit de 30 jours</strong> sera automatiquement créé.</p>
                </div>
            </div>
        </div>

        {{-- Compte admin --}}
        <div class="bg-white rounded-[14px] border border-bimo-navy/10 overflow-hidden">
            <div class="flex items-center gap-3 px-5 py-4 border-b border-bimo-navy/[5%] bg-bimo-bg2">
                <div class="w-8 h-8 rounded-[8px] bg-bimo-navy/10 flex items-center justify-center flex-shrink-0">
                    <svg class="w-4 h-4 text-bimo-text/50" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                </div>
                <span class="font-display font-bold text-sm text-bimo-text">Compte administrateur de l'agence</span>
            </div>
            <div class="px-5 py-5">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="space-y-1.5">
                        <label class="block font-body font-medium text-sm text-bimo-text" for="admin_name">Nom complet <span class="text-bimo-red">*</span></label>
                        <input type="text" id="admin_name" name="admin_name" value="{{ old('admin_name') }}" required placeholder="Prénom Nom"
                               class="w-full px-4 py-3 rounded-[10px] bg-white border font-body text-sm text-bimo-text placeholder:text-bimo-text/35 focus:outline-none focus:ring-2 transition-all duration-150 @error('admin_name') border-bimo-red focus:border-bimo-red focus:ring-bimo-red/15 @else border-bimo-navy/20 focus:border-bimo-gold focus:ring-bimo-gold/15 @enderror">
                        @error('admin_name')<p class="font-body text-xs text-bimo-red">{{ $message }}</p>@enderror
                    </div>
                    <div class="space-y-1.5">
                        <label class="block font-body font-medium text-sm text-bimo-text" for="admin_email">Email de connexion <span class="text-bimo-red">*</span></label>
                        <input type="email" id="admin_email" name="admin_email" value="{{ old('admin_email') }}" required placeholder="admin@agence.sn"
                               class="w-full px-4 py-3 rounded-[10px] bg-white border font-body text-sm text-bimo-text placeholder:text-bimo-text/35 focus:outline-none focus:ring-2 transition-all duration-150 @error('admin_email') border-bimo-red focus:border-bimo-red focus:ring-bimo-red/15 @else border-bimo-navy/20 focus:border-bimo-gold focus:ring-bimo-gold/15 @enderror">
                        @error('admin_email')<p class="font-body text-xs text-bimo-red">{{ $message }}</p>@enderror
                    </div>
                    <div class="space-y-1.5">
                        <label class="block font-body font-medium text-sm text-bimo-text" for="admin_password">Mot de passe <span class="text-bimo-red">*</span></label>
                        <input type="password" id="admin_password" name="admin_password" required placeholder="Minimum 8 caractères"
                               class="w-full px-4 py-3 rounded-[10px] bg-white border font-body text-sm text-bimo-text placeholder:text-bimo-text/35 focus:outline-none focus:ring-2 transition-all duration-150 @error('admin_password') border-bimo-red focus:border-bimo-red focus:ring-bimo-red/15 @else border-bimo-navy/20 focus:border-bimo-gold focus:ring-bimo-gold/15 @enderror">
                        <p class="font-body text-xs text-bimo-text/40 mt-1">L'admin recevra ce mot de passe par email.</p>
                        @error('admin_password')<p class="font-body text-xs text-bimo-red">{{ $message }}</p>@enderror
                    </div>
                    <div class="space-y-1.5">
                        <label class="block font-body font-medium text-sm text-bimo-text" for="admin_password_confirmation">Confirmer le mot de passe <span class="text-bimo-red">*</span></label>
                        <input type="password" id="admin_password_confirmation" name="admin_password_confirmation" required placeholder="Répétez le mot de passe"
                               class="w-full px-4 py-3 rounded-[10px] bg-white border border-bimo-navy/20 font-body text-sm text-bimo-text placeholder:text-bimo-text/35 focus:outline-none focus:border-bimo-gold focus:ring-2 focus:ring-bimo-gold/15 transition-all duration-150">
                    </div>
                </div>
                <div class="mt-4 flex items-start gap-2 bg-bimo-navy/[4%] border border-bimo-navy/10 rounded-[8px] px-4 py-3">
                    <svg class="w-4 h-4 text-bimo-text/40 flex-shrink-0 mt-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                    <p class="font-body text-xs text-bimo-text/50">Un email de bienvenue avec les identifiants sera envoyé. <strong>Vous resterez connecté en tant que Super Admin.</strong></p>
                </div>
            </div>
        </div>

        <div class="flex items-center gap-3">
            <button type="submit"
                    class="inline-flex items-center gap-2 px-5 py-2.5 bg-[var(--ac)] text-white font-display font-bold text-sm rounded-[10px] hover:opacity-90 transition-opacity duration-150">
                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/></svg>
                Créer l'agence
            </button>
            <a href="{{ route('superadmin.dashboard') }}"
               class="inline-flex items-center px-5 py-2.5 rounded-[10px] border border-bimo-navy/15 font-body text-sm text-bimo-text/60 hover:text-bimo-text hover:border-bimo-navy/30 transition-all duration-150">
                Annuler
            </a>
        </div>

    </form>
</div>
@endsection
