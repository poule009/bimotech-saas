@extends('layouts.app')
@section('header', 'Mon profil')

@section('content')
<div class="max-w-2xl space-y-5">

    {{-- Informations profil --}}
    <div class="bg-white rounded-[14px] border border-bimo-navy/10 overflow-hidden">
        <div class="flex items-center gap-3 px-5 py-4 border-b border-bimo-navy/[5%] bg-bimo-bg2">
            <div class="w-8 h-8 rounded-[8px] bg-bimo-gold/15 flex items-center justify-center flex-shrink-0">
                <svg class="w-4 h-4 text-bimo-gold" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
            </div>
            <div>
                <div class="font-display font-bold text-sm text-bimo-navy">Informations du profil</div>
                <div class="font-body text-xs text-bimo-navy/40">Mettez à jour votre nom et votre adresse email.</div>
            </div>
        </div>
        <div class="px-5 py-5">
            @include('profile.partials.update-profile-information-form')
        </div>
    </div>

    {{-- Mot de passe --}}
    <div class="bg-white rounded-[14px] border border-bimo-navy/10 overflow-hidden">
        <div class="flex items-center gap-3 px-5 py-4 border-b border-bimo-navy/[5%] bg-bimo-bg2">
            <div class="w-8 h-8 rounded-[8px] bg-bimo-navy/10 flex items-center justify-center flex-shrink-0">
                <svg class="w-4 h-4 text-bimo-navy/50" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0110 0v4"/></svg>
            </div>
            <div>
                <div class="font-display font-bold text-sm text-bimo-navy">Mot de passe</div>
                <div class="font-body text-xs text-bimo-navy/40">Utilisez un mot de passe long et aléatoire pour rester sécurisé.</div>
            </div>
        </div>
        <div class="px-5 py-5">
            @include('profile.partials.update-password-form')
        </div>
    </div>

    {{-- Supprimer le compte --}}
    <div class="bg-white rounded-[14px] border border-bimo-red/20 overflow-hidden">
        <div class="flex items-center gap-3 px-5 py-4 border-b border-bimo-red/10 bg-bimo-red/[3%]">
            <div class="w-8 h-8 rounded-[8px] bg-bimo-red/10 flex items-center justify-center flex-shrink-0">
                <svg class="w-4 h-4 text-bimo-red" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6"/></svg>
            </div>
            <div>
                <div class="font-display font-bold text-sm text-bimo-red">Supprimer le compte</div>
                <div class="font-body text-xs text-bimo-red/60">Une fois supprimé, toutes les données seront perdues définitivement.</div>
            </div>
        </div>
        <div class="px-5 py-5">
            @include('profile.partials.delete-user-form')
        </div>
    </div>

</div>
@endsection
