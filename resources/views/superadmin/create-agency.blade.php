@extends('layouts.superadmin')

@section('title', 'Ajouter une agence')

@section('content')
<div class="max-w-[820px] mx-auto">

    {{-- En-tête --}}
    <div class="mb-6">
        <a href="{{ route('superadmin.agencies.index') }}" class="text-[13px] font-semibold text-teal hover:underline inline-flex items-center gap-1.5 mb-3">← Agences</a>
        <div class="text-[12px] font-semibold tracking-[0.12em] uppercase text-gold">Gestion plateforme</div>
        <h1 class="font-display font-medium text-[30px] text-ink mt-1">Ajouter une agence</h1>
        <p class="text-[14.5px] text-muted mt-1.5">Onboarding manuel — crée l'agence, son compte administrateur et un essai gratuit de 30 jours.</p>
    </div>

    @if($errors->any())
        <div class="mb-5 rounded-lg bg-error/10 border border-error/25 px-4 py-3 text-[13px] text-error">
            {{ $errors->first('general') ?: 'Merci de corriger les champs signalés ci-dessous.' }}
        </div>
    @endif

    <form method="POST" action="{{ route('superadmin.agencies.store') }}">
        @csrf

        {{-- ─────────── Informations de l'agence ─────────── --}}
        <div class="f-card mb-5">
            <div class="f-card-title">Informations de l'agence</div>
            <p class="f-card-sub">Coordonnées de l'agence cliente. L'adresse et le téléphone sont facultatifs.</p>

            <div class="grid sm:grid-cols-2 gap-4">
                <div>
                    <label class="f-label">Nom de l'agence</label>
                    <input type="text" name="agency_name" value="{{ old('agency_name') }}" placeholder="Immo Teranga" class="f-input @error('agency_name') f-input-error @enderror">
                    @error('agency_name')<p class="mt-1.5 text-[12px] text-error">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="f-label">Email de l'agence</label>
                    <input type="email" name="agency_email" value="{{ old('agency_email') }}" placeholder="contact@agence.sn" class="f-input @error('agency_email') f-input-error @enderror">
                    @error('agency_email')<p class="mt-1.5 text-[12px] text-error">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="f-label">Téléphone <span class="text-muted font-normal">(optionnel)</span></label>
                    <input type="text" name="agency_telephone" value="{{ old('agency_telephone') }}" placeholder="+221 33 800 00 00" class="f-input @error('agency_telephone') f-input-error @enderror">
                    @error('agency_telephone')<p class="mt-1.5 text-[12px] text-error">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="f-label">Adresse <span class="text-muted font-normal">(optionnel)</span></label>
                    <input type="text" name="agency_adresse" value="{{ old('agency_adresse') }}" placeholder="Dakar, Almadies" class="f-input @error('agency_adresse') f-input-error @enderror">
                    @error('agency_adresse')<p class="mt-1.5 text-[12px] text-error">{{ $message }}</p>@enderror
                </div>
            </div>
        </div>

        {{-- ─────────── Compte administrateur ─────────── --}}
        <div class="f-card mb-5" x-data="passwordToggle">
            <div class="f-card-title">Compte administrateur</div>
            <p class="f-card-sub">Le directeur de l'agence. Il recevra un email de bienvenue et se connectera avec cet email.</p>

            <div class="grid sm:grid-cols-2 gap-4">
                <div>
                    <label class="f-label">Nom complet</label>
                    <input type="text" name="admin_name" value="{{ old('admin_name') }}" placeholder="Moussa Diop" class="f-input @error('admin_name') f-input-error @enderror">
                    @error('admin_name')<p class="mt-1.5 text-[12px] text-error">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="f-label">Email</label>
                    <input type="email" name="admin_email" value="{{ old('admin_email') }}" placeholder="directeur@agence.sn" class="f-input @error('admin_email') f-input-error @enderror">
                    @error('admin_email')<p class="mt-1.5 text-[12px] text-error">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="f-label flex items-center justify-between">
                        <span>Mot de passe</span>
                        <button type="button" x-on:click="toggle" x-text="label" class="text-[11.5px] font-semibold text-teal"></button>
                    </label>
                    <input x-bind:type="type" name="admin_password" placeholder="Min. 8 caractères" class="f-input @error('admin_password') f-input-error @enderror">
                    @error('admin_password')<p class="mt-1.5 text-[12px] text-error">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="f-label">Confirmer le mot de passe</label>
                    <input x-bind:type="type" name="admin_password_confirmation" placeholder="Répéter le mot de passe" class="f-input">
                </div>
            </div>

            <div class="mt-4 bg-paper border border-line rounded-[10px] px-4 py-3 text-[12.5px] text-muted leading-relaxed flex gap-2">
                <svg class="w-4 h-4 text-teal shrink-0 mt-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M12 16v-4M12 8h.01"/></svg>
                <span>Un <strong class="text-ink">essai gratuit de 30 jours</strong> est ouvert automatiquement. Vous pourrez activer un plan payant depuis la fiche de l'agence.</span>
            </div>
        </div>

        <div class="flex items-center justify-end gap-3">
            <a href="{{ route('superadmin.agencies.index') }}" class="px-5 py-3 rounded-[11px] border-[1.5px] border-line text-[14px] font-bold text-ink hover:border-teal">Annuler</a>
            <button type="submit" class="bg-teal hover:bg-teal-deep text-white px-6 py-3 rounded-[11px] text-[14px] font-bold">Créer l'agence</button>
        </div>
    </form>

</div>
@endsection
