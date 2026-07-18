@extends('layouts.superadmin')

@section('title', 'Inviter un collaborateur')

@section('content')
<div class="max-w-[720px] mx-auto">

    {{-- En-tête --}}
    <div class="mb-6">
        <a href="{{ route('superadmin.equipe.index') }}" class="text-[13px] font-semibold text-teal hover:underline inline-flex items-center gap-1.5 mb-3">← Équipe interne</a>
        <div class="text-[12px] font-semibold tracking-[0.12em] uppercase text-gold">Gestion plateforme</div>
        <h1 class="font-display font-medium text-[30px] text-ink mt-1">Inviter un collaborateur</h1>
        <p class="text-[14.5px] text-muted mt-1.5">Crée un compte Super Admin à accès restreint. Il ne verra que les agences que vous lui attribuerez.</p>
    </div>

    @if($errors->any())
        <div class="mb-5 rounded-lg bg-error/10 border border-error/25 px-4 py-3 text-[13px] text-error">
            Merci de corriger les champs signalés ci-dessous.
        </div>
    @endif

    <form method="POST" action="{{ route('superadmin.equipe.store') }}">
        @csrf

        <div class="f-card mb-5" x-data="passwordToggle">
            <div class="f-card-title">Compte collaborateur</div>
            <p class="f-card-sub">Il se connectera avec cet email et définira son propre mot de passe à la première connexion.</p>

            <div class="grid sm:grid-cols-2 gap-4">
                <div>
                    <label class="f-label">Nom complet</label>
                    <input type="text" name="name" value="{{ old('name') }}" placeholder="Israel Ndassouba" class="f-input @error('name') f-input-error @enderror">
                    @error('name')<p class="mt-1.5 text-[12px] text-error">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="f-label">Email</label>
                    <input type="email" name="email" value="{{ old('email') }}" placeholder="collaborateur@bimo-tech.sn" class="f-input @error('email') f-input-error @enderror">
                    @error('email')<p class="mt-1.5 text-[12px] text-error">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="f-label flex items-center justify-between">
                        <span>Mot de passe temporaire</span>
                        <button type="button" x-on:click="toggle" x-text="label" class="text-[11.5px] font-semibold text-teal"></button>
                    </label>
                    <input x-bind:type="type" name="password" placeholder="Min. 8 caractères" class="f-input @error('password') f-input-error @enderror">
                    @error('password')<p class="mt-1.5 text-[12px] text-error">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="f-label">Confirmer le mot de passe</label>
                    <input x-bind:type="type" name="password_confirmation" placeholder="Répéter le mot de passe" class="f-input">
                </div>
            </div>
        </div>

        <div class="f-card mb-5">
            <div class="f-card-title">Rémunération</div>
            <p class="f-card-sub">Taux de commission convenu — appliqué au MRR des agences qu'il apporte. Modifiable plus tard.</p>

            <div class="max-w-[220px]">
                <label class="f-label">Taux de commission (%)</label>
                <input type="number" step="0.01" min="0" max="100" name="taux_commission" value="{{ old('taux_commission', '35') }}" placeholder="35" class="f-input @error('taux_commission') f-input-error @enderror">
                @error('taux_commission')<p class="mt-1.5 text-[12px] text-error">{{ $message }}</p>@enderror
            </div>

            <div class="mt-4 bg-paper border border-line rounded-[10px] px-4 py-3 text-[12.5px] text-muted leading-relaxed flex gap-2">
                <svg class="w-4 h-4 text-teal shrink-0 mt-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M12 16v-4M12 8h.01"/></svg>
                <span>Par défaut, il pourra <strong class="text-ink">voir ses agences</strong> et <strong class="text-ink">impersonner</strong> dans son périmètre. La facturation et les règles fiscales restent désactivées — ajustez ses permissions depuis l'écran Équipe interne.</span>
            </div>
        </div>

        <div class="flex items-center justify-end gap-3">
            <a href="{{ route('superadmin.equipe.index') }}" class="px-5 py-3 rounded-[11px] border-[1.5px] border-line text-[14px] font-bold text-ink hover:border-teal">Annuler</a>
            <button type="submit" class="bg-teal hover:bg-teal-deep text-white px-6 py-3 rounded-[11px] text-[14px] font-bold">Créer le compte</button>
        </div>
    </form>

</div>
@endsection
