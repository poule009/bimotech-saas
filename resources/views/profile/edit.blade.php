@extends('layouts.app')

@php
    $initiales = collect(explode(' ', trim($user->name)))
        ->filter()->take(2)->map(fn ($p) => mb_strtoupper(mb_substr($p, 0, 1)))->implode('');

    $badgeAcces = [
        'none' => ['Aucun accès',  'bg-error/10 text-error'],
        'view' => ['Consultation', 'bg-amber/15 text-amber'],
        'full' => ['Modification', 'bg-green/12 text-green'],
    ];
@endphp

@section('title', 'Mon profil')
@section('page-title', 'Mon profil')

@section('content')
@php $ongletInitial = ($errors->hasBag('updatePassword') || $errors->hasBag('setPassword')) ? 'securite' : null; @endphp
<div class="max-w-[760px]" x-data="profilTabs" data-initial="{{ $ongletInitial }}">

    @if(session('status'))
        @php $msg = ['profile-updated' => 'Profil mis à jour.', 'password-updated' => 'Mot de passe mis à jour.', 'notifications-updated' => 'Préférences enregistrées.'][session('status')] ?? null; @endphp
        @if($msg)
            <div class="mb-5 rounded-lg bg-green/10 border border-green/25 px-4 py-3 text-[13px] text-green flex items-center gap-2"><x-icon name="check-circle" size="16" /> {{ $msg }}</div>
        @endif
    @endif
    @if(session('error'))
        <div class="mb-5 rounded-lg bg-error/10 border border-error/25 px-4 py-3 text-[13px] text-error flex items-center gap-2"><x-icon name="alert-triangle" size="16" /> {{ session('error') }}</div>
    @endif

    {{-- Bandeau profil --}}
    <div class="flex items-center gap-4 bg-white border border-line rounded-2xl px-6 py-5 mb-6">
        <div class="w-16 h-16 rounded-2xl bg-teal text-white flex items-center justify-center text-[22px] font-bold shrink-0">{{ $initiales }}</div>
        <div>
            <h2 class="font-display font-semibold text-[19px]">{{ $user->name }}</h2>
            <span class="inline-block mt-1 text-[11.5px] font-bold px-3 py-1 rounded-full bg-gold/15 text-gold">{{ $roleLabel }}</span>
        </div>
    </div>

    {{-- Onglets --}}
    <div class="flex gap-1.5 border-b-2 border-line mb-6">
        <button type="button" x-on:click="showIdentite"      x-bind:class="identiteClass"      class="px-4 py-3 text-[14px] font-bold border-b-[3px] -mb-0.5 transition-colors">Identité</button>
        <button type="button" x-on:click="showSecurite"      x-bind:class="securiteClass"      class="px-4 py-3 text-[14px] font-bold border-b-[3px] -mb-0.5 transition-colors">Sécurité</button>
        <button type="button" x-on:click="showNotifications" x-bind:class="notificationsClass" class="px-4 py-3 text-[14px] font-bold border-b-[3px] -mb-0.5 transition-colors">Notifications</button>
        @if($showAcces)
            <button type="button" x-on:click="showAcces"     x-bind:class="accesClass"         class="px-4 py-3 text-[14px] font-bold border-b-[3px] -mb-0.5 transition-colors">Mes accès</button>
        @endif
    </div>

    {{-- ─────────── IDENTITÉ ─────────── --}}
    <div x-show="isIdentite">
        <div class="f-card">
            <div class="f-card-title">Informations personnelles</div>
            <p class="f-card-sub">Ces informations vous concernent, pas l'agence dans son ensemble.</p>

            <form method="POST" action="{{ route('profile.update') }}">
                @csrf @method('PATCH')
                <div class="mb-[18px]">
                    <label class="f-label">Nom complet</label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" class="f-input @error('name') f-input-error @enderror">
                    @error('name')<p class="mt-1.5 text-[12px] text-error">{{ $message }}</p>@enderror
                </div>
                <div class="grid sm:grid-cols-2 gap-4">
                    <div>
                        <label class="f-label">Téléphone</label>
                        <input type="text" name="telephone" value="{{ old('telephone', $user->telephone) }}" placeholder="+221 77 xxx xx xx" class="f-input @error('telephone') f-input-error @enderror">
                        @error('telephone')<p class="mt-1.5 text-[12px] text-error">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="f-label">Email</label>
                        <input type="email" name="email" value="{{ old('email', $user->email) }}" class="f-input @error('email') f-input-error @enderror">
                        @error('email')<p class="mt-1.5 text-[12px] text-error">{{ $message }}</p>@enderror
                    </div>
                </div>
                <div class="flex justify-end mt-5">
                    <button type="submit" class="bg-teal hover:bg-teal-deep text-white px-6 py-3 rounded-[10px] text-[14px] font-bold">Enregistrer</button>
                </div>
            </form>
        </div>
    </div>

    {{-- ─────────── SÉCURITÉ ─────────── --}}
    <div x-show="isSecurite" x-cloak>
        <div class="f-card">
            @if($estGoogle)
                {{-- Compte Google : pas de mot de passe connu → on en définit un (la session
                     authentifiée via Google suffit à prouver l'identité, pas d'ancien requis). --}}
                <div class="f-card-title">Définir un mot de passe</div>
                <p class="f-card-sub">Votre compte utilise la connexion Google. Définissez un mot de passe pour aussi pouvoir vous connecter par email.</p>

                <div class="mb-5 rounded-[10px] bg-teal/8 border border-teal/20 px-4 py-3 text-[12.5px] text-teal-deep flex gap-2">
                    <x-icon name="info" size="15" class="text-teal shrink-0 mt-0.5" />
                    <span>Compte lié à Google — aucun mot de passe actuel à saisir.</span>
                </div>

                <form method="POST" action="{{ route('password.set') }}">
                    @csrf @method('PUT')
                    <div class="grid sm:grid-cols-2 gap-4">
                        <div>
                            <label class="f-label">Nouveau mot de passe</label>
                            <input type="password" name="password" autocomplete="new-password" placeholder="8 caractères min." class="f-input @error('password', 'setPassword') f-input-error @enderror">
                            @error('password', 'setPassword')<p class="mt-1.5 text-[12px] text-error">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="f-label">Confirmer</label>
                            <input type="password" name="password_confirmation" autocomplete="new-password" class="f-input">
                        </div>
                    </div>
                    <div class="flex justify-end mt-5">
                        <button type="submit" class="bg-teal hover:bg-teal-deep text-white px-6 py-3 rounded-[10px] text-[14px] font-bold">Définir le mot de passe</button>
                    </div>
                </form>
            @else
                <div class="f-card-title">Changer le mot de passe</div>
                <p class="f-card-sub">Utilisez un mot de passe que vous n'employez pour aucun autre service.</p>

                <form method="POST" action="{{ route('password.update') }}">
                    @csrf @method('PUT')
                    <div class="mb-[18px]">
                        <label class="f-label">Mot de passe actuel</label>
                        <input type="password" name="current_password" autocomplete="current-password" class="f-input @error('current_password', 'updatePassword') f-input-error @enderror">
                        @error('current_password', 'updatePassword')<p class="mt-1.5 text-[12px] text-error">{{ $message }}</p>@enderror
                    </div>
                    <div class="grid sm:grid-cols-2 gap-4">
                        <div>
                            <label class="f-label">Nouveau mot de passe</label>
                            <input type="password" name="password" autocomplete="new-password" placeholder="8 caractères min." class="f-input @error('password', 'updatePassword') f-input-error @enderror">
                            @error('password', 'updatePassword')<p class="mt-1.5 text-[12px] text-error">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="f-label">Confirmer</label>
                            <input type="password" name="password_confirmation" autocomplete="new-password" class="f-input">
                        </div>
                    </div>
                    <div class="flex justify-end mt-5">
                        <button type="submit" class="bg-teal hover:bg-teal-deep text-white px-6 py-3 rounded-[10px] text-[14px] font-bold">Mettre à jour le mot de passe</button>
                    </div>
                </form>
            @endif
        </div>
    </div>

    {{-- ─────────── NOTIFICATIONS ─────────── --}}
    <div x-show="isNotifications" x-cloak>
        <div class="f-card">
            <div class="f-card-title">Préférences de notification</div>
            <p class="f-card-sub">Personnel — chaque membre de l'équipe peut régler cela différemment. Notifications dans l'application uniquement.</p>

            <form method="POST" action="{{ route('profile.notifications') }}">
                @csrf
                @php
                    $toggles = [
                        ['alerte_retard',   'Alerte à chaque nouveau retard de paiement', "Notification dans l'app dès qu'un loyer passe en retard"],
                        ['rappel_echeance', 'Rappel des contrats bientôt échus',          "7 jours avant l'échéance d'un bail"],
                    ];
                @endphp
                @foreach($toggles as $i => [$key, $titre, $desc])
                    <div class="flex items-center justify-between gap-5 py-4 {{ $i > 0 ? 'border-t border-paper-dim' : '' }}">
                        <div>
                            <div class="text-[14px] font-bold">{{ $titre }}</div>
                            <div class="text-[12.5px] text-muted mt-0.5 leading-snug">{{ $desc }}</div>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer shrink-0">
                            <input type="checkbox" name="{{ $key }}" value="1" class="peer sr-only" @checked($user->notifPref($key))>
                            <div class="w-[42px] h-6 rounded-full bg-line peer-checked:bg-teal transition-colors"></div>
                            <div class="absolute left-[3px] top-[3px] w-[18px] h-[18px] rounded-full bg-white shadow transition-transform peer-checked:translate-x-[18px]"></div>
                        </label>
                    </div>
                @endforeach
                <div class="flex justify-end mt-5">
                    <button type="submit" class="bg-teal hover:bg-teal-deep text-white px-6 py-3 rounded-[10px] text-[14px] font-bold">Enregistrer</button>
                </div>
            </form>
        </div>
    </div>

    {{-- ─────────── MES ACCÈS (lecture seule, personnel d'agence uniquement) ─────────── --}}
    @if($showAcces)
    <div x-show="isAcces" x-cloak>
        <div class="f-card">
            <div class="f-card-title">Mes accès</div>
            <p class="f-card-sub">Ce que vous pouvez voir et faire dans l'application — géré par votre administrateur.</p>

            @if($estAdminComplet)
                <div class="rounded-[14px] px-6 py-5 text-white bg-gradient-to-br from-gold to-teal flex items-center gap-4">
                    <x-icon name="star" size="24" />
                    <div>
                        <div class="font-display font-semibold text-[15px]">Accès complet</div>
                        <div class="text-[12.5px] text-white/90 mt-0.5">Vous avez accès à tous les modules de l'application, sans restriction.</div>
                    </div>
                </div>
            @else
                @foreach($modules as $key => $mod)
                    @php $lvl = $niveaux[$key] ?? 'none'; $b = $badgeAcces[$lvl]; @endphp
                    <div class="flex items-center gap-3.5 py-3 border-b border-paper-dim last:border-0">
                        <span class="w-[34px] h-[34px] rounded-[9px] bg-paper-dim flex items-center justify-center shrink-0"><x-icon :name="$mod['icon']" size="15" /></span>
                        <span class="font-semibold text-[13.5px] flex-1">{{ $mod['label'] }}</span>
                        <span class="text-[11px] font-bold px-3 py-1 rounded-full {{ $b[1] }}">{{ $b[0] }}</span>
                    </div>
                @endforeach
                <div class="text-[12px] text-muted mt-4 text-center">Besoin d'un accès supplémentaire ? Demandez à votre administrateur.</div>
            @endif
        </div>
    </div>
    @endif

</div>
@endsection
