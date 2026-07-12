@extends('layouts.app')

@php
    $estLoc     = $user->isLocataire();
    $profil     = $estLoc ? null : $user->proprietaire;
    $entreprise = (bool) ($profil?->est_personne_morale_is);
    $fmt        = fn ($n) => number_format((float) $n, 0, ',', ' ');
    $modeLabels = ['especes'=>'Espèces','virement'=>'Virement bancaire','wave'=>'Wave','orange_money'=>'Orange Money','free_money'=>'Free Money','cheque'=>'Chèque','mobile_money'=>'Mobile Money'];
    $statutLabels = ['loue'=>['Loué','bg-green/10 text-green'],'disponible'=>['Vacant','bg-gold/15 text-gold'],'en_travaux'=>['En travaux','bg-error/10 text-error']];
@endphp

@section('title', $user->name)
@section('page-title', $estLoc ? 'Fiche locataire' : 'Fiche propriétaire')
@section('page-subtitle')
    <a href="{{ $estLoc ? route('admin.users.locataires') : route('admin.users.proprietaires') }}" class="text-teal font-semibold hover:underline">{{ $estLoc ? 'Locataires' : 'Propriétaires' }}</a>
    <span class="text-muted"> / {{ $user->name }}</span>
@endsection

@section('content')
@if($estLoc)
    @include('users._show-locataire')
@else
<div class="max-w-[1100px]" x-data="tabs">

    @if(session('success'))
        <div class="mb-5 rounded-lg bg-green/10 border border-green/25 px-4 py-3 text-[13px] text-green">{{ session('success') }}</div>
    @endif

    {{-- En-tête profil --}}
    <div class="bg-white border border-line rounded-xl p-6 md:p-7 flex flex-col sm:flex-row sm:items-center gap-5 mb-5">
        <div @class([
            'w-[68px] h-[68px] rounded-2xl flex items-center justify-center font-bold text-[22px] shrink-0',
            'bg-gold text-teal-deep' => $entreprise,
            'bg-teal text-paper' => ! $entreprise,
        ])>{{ mb_strtoupper(mb_substr($user->name, 0, 2)) }}</div>

        <div class="min-w-0 flex-1">
            <h2 class="font-display font-semibold text-[25px] mb-1.5">{{ $user->name }}</h2>
            <div class="flex flex-wrap items-center gap-x-4 gap-y-1.5 text-[13.5px] text-muted">
                @if($user->telephone)<span class="inline-flex items-center gap-1.5"><x-icon name="phone" size="13" /> {{ $user->telephone }}</span>@endif
                @if($user->email)<span class="inline-flex items-center gap-1.5"><x-icon name="mail" size="13" /> {{ $user->email }}</span>@endif
                @if($entreprise)
                    <span class="text-[12px] font-bold px-3 py-1 rounded-full bg-gold/15 text-gold">Entreprise</span>
                @else
                    <span class="text-[12px] font-bold px-3 py-1 rounded-full bg-green/10 text-green">Particulier</span>
                @endif
            </div>
        </div>

        <div class="flex items-center gap-2.5 shrink-0">
            <a href="{{ route('admin.users.edit', $user) }}" class="px-5 py-3 rounded-[10px] border-[1.5px] border-line bg-white text-ink text-[14px] font-bold hover:border-teal transition-colors">Modifier</a>
            @if(Route::has('admin.biens.create'))
                <a href="{{ route('admin.biens.create') }}" class="px-5 py-3 rounded-[10px] bg-teal text-paper text-[14px] font-bold hover:bg-teal-deep transition-colors whitespace-nowrap">+ Ajouter un bien</a>
            @endif
        </div>
    </div>

    {{-- Résumé financier --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-white border border-line rounded-xl p-4">
            <div class="text-[11.5px] uppercase tracking-wide text-muted font-bold mb-1.5">Biens</div>
            <div class="font-display font-semibold text-[20px]">{{ $stats['nb_biens'] ?? 0 }} <span class="text-[13px] text-muted font-body">· {{ $stats['nb_biens_loues'] ?? 0 }} loués</span></div>
        </div>
        <div class="bg-white border border-line rounded-xl p-4">
            <div class="text-[11.5px] uppercase tracking-wide text-muted font-bold mb-1.5">Loyers encaissés</div>
            <div class="font-display font-semibold text-[20px] text-gold">{{ $fmt($stats['total_loyers'] ?? 0) }} <span class="text-[13px] font-body text-muted">F</span></div>
        </div>
        <div class="bg-white border border-line rounded-xl p-4">
            <div class="text-[11.5px] uppercase tracking-wide text-muted font-bold mb-1.5">Net à reverser</div>
            <div class="font-display font-semibold text-[20px]">{{ $fmt($stats['total_net'] ?? 0) }} <span class="text-[13px] font-body text-muted">F</span></div>
        </div>
        <div class="bg-white border border-line rounded-xl p-4">
            <div class="text-[11.5px] uppercase tracking-wide text-muted font-bold mb-1.5">Paiements</div>
            <div class="font-display font-semibold text-[20px]">{{ $stats['nb_paiements'] ?? 0 }}</div>
        </div>
    </div>

    {{-- Onglets --}}
    <div class="flex gap-2 border-b-2 border-line mb-5">
        <button type="button" x-on:click="showInfo" x-bind:class="infoTabClass" class="px-4 py-3 text-[14.5px] font-bold border-b-[3px] -mb-0.5 transition-colors">Informations</button>
        <button type="button" x-on:click="showBiens" x-bind:class="biensTabClass" class="px-4 py-3 text-[14.5px] font-bold border-b-[3px] -mb-0.5 transition-colors">Biens liés · {{ $stats['nb_biens'] ?? 0 }}</button>
        <button type="button" x-on:click="showDocs" x-bind:class="docsTabClass" class="px-4 py-3 text-[14.5px] font-bold border-b-[3px] -mb-0.5 transition-colors">Documents</button>
    </div>

    {{-- Onglet Informations --}}
    <div x-show="isInfo">
        <div class="f-card mb-5">
            <h3 class="f-card-title mb-4">Coordonnées</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div><div class="text-[11.5px] uppercase tracking-wide text-muted font-bold mb-1">Téléphone</div><div class="text-[15px] font-semibold">{{ $user->telephone ?? '—' }}</div></div>
                <div><div class="text-[11.5px] uppercase tracking-wide text-muted font-bold mb-1">Email</div><div class="text-[15px] font-semibold">{{ $user->email ?? '—' }}</div></div>
                <div><div class="text-[11.5px] uppercase tracking-wide text-muted font-bold mb-1">Adresse</div><div class="text-[15px] font-semibold">{{ $user->adresse ?? $profil?->ville ?? '—' }}</div></div>
                <div><div class="text-[11.5px] uppercase tracking-wide text-muted font-bold mb-1">{{ $entreprise ? 'NINEA' : 'N° CNI' }}</div><div class="text-[15px] font-semibold">{{ ($entreprise ? $profil?->ninea : $profil?->cni) ?? '—' }}</div></div>
            </div>
        </div>
        <div class="f-card">
            <h3 class="f-card-title mb-4">Versement des loyers</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div><div class="text-[11.5px] uppercase tracking-wide text-muted font-bold mb-1">Mode préféré</div><div class="text-[15px] font-semibold">{{ $modeLabels[$profil?->mode_paiement_prefere] ?? 'Virement bancaire' }}</div></div>
                <div>
                    <div class="text-[11.5px] uppercase tracking-wide text-muted font-bold mb-1">Régime fiscal (TVA)</div>
                    @if($profil?->assujetti_tva)
                        <div class="text-[15px] font-semibold text-green">Assujetti à la TVA</div>
                    @else
                        <div class="text-[15px] font-semibold">Non assujetti à la TVA</div>
                        <div class="mt-1 inline-flex items-center gap-1.5 rounded-md bg-gold/15 text-teal-deep px-2 py-1 text-[11.5px] font-semibold">
                            <x-icon name="alert-triangle" size="13" /> Statut TVA non confirmé — à vérifier
                        </div>
                        <p class="text-[11.5px] text-muted mt-1">Tant que ce n'est pas confirmé, aucune TVA n'est facturée sur ses loyers (sécurité : pas de TVA indue). Modifiez la fiche pour confirmer l'assujettissement.</p>
                    @endif
                </div>
                <div>
                    <div class="text-[11.5px] uppercase tracking-wide text-muted font-bold mb-1">Retenue à la source (BRS)</div>
                    @if($profil?->est_personne_morale_is)
                        <div class="text-[15px] font-semibold">Non applicable</div>
                        <p class="text-[11.5px] text-muted mt-1">Personne morale (IS) — pas de retenue à la source sur ses loyers.</p>
                    @elseif($profil?->brs_dispense)
                        <div class="text-[15px] font-semibold text-gold">Dispensé de BRS</div>
                        <p class="text-[11.5px] text-muted mt-1">Aucune retenue de 5% n'est prélevée (dispense DGID). Décochez la dispense sur la fiche pour rétablir la retenue.</p>
                    @else
                        <div class="text-[15px] font-semibold text-green">Soumis à la BRS (5%)</div>
                        <p class="text-[11.5px] text-muted mt-1">Bailleur personne physique : 5% retenus sur les loyers ≥ 150 000 F et reversés à la DGID.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Onglet Biens --}}
    <div x-show="isBiens" x-cloak>
        @if($biens->isEmpty())
            <div class="bg-white border border-line rounded-xl py-14 text-center text-muted text-[14px]">Aucun bien rattaché à ce propriétaire.</div>
        @else
            <div class="space-y-3">
                @foreach($biens as $bien)
                    @php [$stLabel, $stClass] = $statutLabels[$bien->statut] ?? [ucfirst($bien->statut), 'bg-paper-dim text-muted']; @endphp
                    <a href="{{ Route::has('admin.biens.show') ? route('admin.biens.show', $bien) : '#' }}"
                       class="flex items-center gap-4 p-4 border border-line rounded-xl bg-white hover:border-teal transition-colors">
                        <span class="w-[50px] h-[50px] rounded-[11px] bg-teal text-paper flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 21h18M5 21V7l7-4 7 4v14M9 21v-6h6v6"/></svg>
                        </span>
                        <div class="min-w-0 flex-1">
                            <div class="font-bold text-[15px] truncate">{{ $bien->reference }}</div>
                            <div class="text-[12.5px] text-muted truncate">{{ $bien->ville }}{{ $bien->type ? ' · '.ucfirst($bien->type) : '' }}</div>
                        </div>
                        <span class="text-[12px] font-bold px-3 py-1.5 rounded-full {{ $stClass }} shrink-0">{{ $stLabel }}</span>
                    </a>
                @endforeach
            </div>
            <div class="mt-5">{{ $biens->links() }}</div>
        @endif
    </div>

    {{-- Onglet Documents --}}
    <div x-show="isDocs" x-cloak>
        @if($profil?->piece_identite_path)
            <div class="f-card flex items-center gap-4">
                <div class="w-11 h-11 rounded-[10px] bg-teal/10 text-teal flex items-center justify-center shrink-0">
                    <x-icon name="file-text" size="20" />
                </div>
                <div class="min-w-0 flex-1">
                    <div class="text-[14.5px] font-bold">Pièce d'identité</div>
                    <div class="text-[12.5px] text-muted truncate">{{ basename($profil->piece_identite_path) }}</div>
                </div>
                <a href="{{ Storage::disk('public')->url($profil->piece_identite_path) }}" target="_blank" rel="noopener"
                   class="px-4 py-2.5 rounded-[10px] border-[1.5px] border-line bg-white text-ink text-[13.5px] font-bold hover:border-teal transition-colors shrink-0">Ouvrir</a>
            </div>
        @else
            <div class="bg-white border border-line rounded-xl py-14 text-center text-muted text-[14px]">
                Aucun document. Les pièces ajoutées depuis les biens ou contrats de ce propriétaire apparaîtront ici.
            </div>
        @endif
    </div>
</div>
@endif
@endsection
