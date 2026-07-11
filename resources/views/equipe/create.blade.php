@extends('layouts.app')

@php
    use App\Support\TeamAccess;

    $tag = [
        'none' => ['Aucun accès', 'bg-error/10 text-error'],
        'view' => ['Consultation', 'bg-amber/15 text-amber'],
        'full' => ['Modification', 'bg-green/12 text-green'],
    ];
    $initial = old('preset_role', 'secretaire');
@endphp

@section('title', 'Inviter un membre')
@section('page-title', 'Inviter un membre')

@section('content')
<div class="max-w-[720px]" x-data="equipeRolePicker" data-initial="{{ $initial }}">

    <div class="mb-6">
        <a href="{{ route('admin.equipe.index') }}" class="text-[13px] font-bold text-teal hover:underline flex items-center gap-1.5">← Mon équipe</a>
    </div>

    <form method="POST" action="{{ route('admin.equipe.store') }}">
        @csrf

        {{-- Coordonnées --}}
        <div class="f-card mb-5">
            <div class="f-card-title">Coordonnées</div>
            <p class="f-card-sub">Chaque membre reçoit son propre identifiant — jamais de mot de passe partagé.</p>

            <div class="grid sm:grid-cols-2 gap-4">
                <div>
                    <label class="f-label">Nom complet</label>
                    <input type="text" name="name" value="{{ old('name') }}" placeholder="Prénom et nom" class="f-input @error('name') f-input-error @enderror">
                    @error('name')<p class="mt-1.5 text-[12px] text-error">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="f-label">Téléphone</label>
                    <input type="text" name="telephone" value="{{ old('telephone') }}" placeholder="+221 77 xxx xx xx" class="f-input @error('telephone') f-input-error @enderror">
                    @error('telephone')<p class="mt-1.5 text-[12px] text-error">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="f-label">Email</label>
                    <input type="email" name="email" value="{{ old('email') }}" placeholder="nom@agence.sn" class="f-input @error('email') f-input-error @enderror">
                    @error('email')<p class="mt-1.5 text-[12px] text-error">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="f-label">Mot de passe temporaire</label>
                    <input type="text" name="password" value="{{ old('password') }}" placeholder="À changer à la 1ʳᵉ connexion" class="f-input @error('password') f-input-error @enderror">
                    @error('password')<p class="mt-1.5 text-[12px] text-error">{{ $message }}</p>@enderror
                </div>
            </div>

            <div class="mt-4 bg-paper border border-line rounded-[10px] px-4 py-3 text-[12.5px] text-muted leading-relaxed flex gap-2">
                <x-icon name="info" size="15" class="text-teal shrink-0 mt-0.5" />
                <span>Cette personne se connectera avec cet <strong class="text-ink">email</strong> et le mot de passe temporaire, sur l'écran de connexion habituel. Elle devra en choisir un nouveau dès sa première connexion.</span>
            </div>
        </div>

        {{-- Rôle de départ --}}
        <div class="f-card mb-5">
            <div class="f-card-title">Rôle de départ</div>
            <p class="f-card-sub">Un point de départ — vous pourrez ajuster chaque accès précisément juste après l'invitation.</p>

            <input type="hidden" name="preset_role" value="{{ $initial }}" x-model="preset">

            <div class="grid sm:grid-cols-3 gap-3.5">
                <button type="button" data-preset="administrateur" x-on:click="pick" x-bind:class="adminCardClass" class="text-left border-2 rounded-[13px] p-4 transition">
                    <x-icon name="star" size="20" class="text-gold mb-2" />
                    <div class="font-bold text-[14.5px] mb-1">Administrateur</div>
                    <div class="text-[12px] text-muted leading-snug">Accès complet à tout, y compris la comptabilité et la gestion de l'équipe.</div>
                </button>
                <button type="button" data-preset="secretaire" x-on:click="pick" x-bind:class="secretaireCardClass" class="text-left border-2 rounded-[13px] p-4 transition">
                    <x-icon name="file-text" size="20" class="text-teal mb-2" />
                    <div class="font-bold text-[14.5px] mb-1">Secrétaire</div>
                    <div class="text-[12px] text-muted leading-snug">Gestion quotidienne — biens, locataires, contrats. Comptabilité fermée par défaut.</div>
                </button>
                <button type="button" data-preset="personnalise" x-on:click="pick" x-bind:class="customCardClass" class="text-left border-2 rounded-[13px] p-4 transition">
                    <x-icon name="cpu" size="20" class="text-ink/60 mb-2" />
                    <div class="font-bold text-[14.5px] mb-1">Personnalisé</div>
                    <div class="text-[12px] text-muted leading-snug">Vous choisissez chaque accès un par un, sans modèle prédéfini.</div>
                </button>
            </div>
            @error('preset_role')<p class="mt-2 text-[12px] text-error">{{ $message }}</p>@enderror

            {{-- Aperçu des accès (un bloc par preset, visible selon la sélection) --}}
            <div class="bg-paper border border-line rounded-xl p-5 mt-5">
                <div class="text-[12.5px] font-bold text-teal-deep mb-3">Aperçu des accès — modifiable après l'invitation</div>
                @foreach(TeamAccess::PRESETS as $presetKey => $niveaux)
                    <div @if($presetKey === 'administrateur') x-show="showAdmin" @elseif($presetKey === 'secretaire') x-show="showSecretaire" @else x-show="showCustom" @endif x-cloak>
                        @foreach(TeamAccess::MODULES as $modKey => $mod)
                            @php $lvl = $niveaux[$modKey] ?? 'none'; $t = $tag[$lvl]; @endphp
                            <div class="flex items-center justify-between py-2 border-b border-paper-dim last:border-0 text-[13px]">
                                <span class="flex items-center gap-2">{{ $mod['label'] }}@if($mod['sensitive'])<x-icon name="alert-triangle" size="12" class="text-gold" />@endif</span>
                                <span class="text-[11px] font-bold px-2.5 py-1 rounded-full {{ $t[1] }}">{{ $t[0] }}</span>
                            </div>
                        @endforeach
                    </div>
                @endforeach
            </div>
        </div>

        <div class="flex items-center justify-end gap-3">
            <a href="{{ route('admin.equipe.index') }}" class="px-5 py-3 rounded-[11px] border-[1.5px] border-line text-[14px] font-bold text-ink hover:border-teal">Annuler</a>
            <button type="submit" class="bg-teal hover:bg-teal-deep text-white px-6 py-3 rounded-[11px] text-[14px] font-bold">Envoyer l'invitation</button>
        </div>
    </form>

</div>
@endsection
