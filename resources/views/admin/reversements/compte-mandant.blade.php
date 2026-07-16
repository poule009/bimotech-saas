@extends('layouts.app')

@php
    use Illuminate\Support\Facades\Storage;
    $fmt   = fn ($n) => number_format((float) $n, 0, ',', ' ');
    $solde = (float) $compteGlobal['solde_restant'];
    $init  = mb_strtoupper(mb_substr($proprietaire->name, 0, 2));
    // Dépenses liées à un bien (portées par les paiements de la période)
    $depensesBien = collect($compte['paiements'])->flatMap(fn ($p) => $p->depenses)->sortByDesc('date_depense');

    // Décomposition du bandeau qui tombe TOUJOURS juste : net total − déjà reversés = solde.
    //   (le net total intègre déjà loyers + caution − commission − BRS − dépenses)
    $reversementsFaits = (float) $compteGlobal['reversements_effectues'];
    $breakdown = $fmt($compteGlobal['net_du']) . ' F net total dû';
    if ($reversementsFaits > 0.5) $breakdown .= ' − ' . $fmt($reversementsFaits) . ' F déjà reversés';
@endphp

@section('title', 'Compte — ' . $proprietaire->name)
@section('page-title', 'Compte propriétaire')
@section('page-subtitle')
    <a href="{{ route('admin.comptabilite.index') }}" class="text-teal font-semibold hover:underline">Comptabilité</a>
    <span class="text-muted"> / {{ $proprietaire->name }}</span>
@endsection

@section('content')
<div class="max-w-[900px]">

    @if(session('success'))
        <div class="mb-5 rounded-lg bg-green/10 border border-green/25 px-4 py-3 text-[13px] text-green">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="mb-5 rounded-lg bg-error/10 border border-error/25 px-4 py-3 text-[13px] text-error">{{ session('error') }}</div>
    @endif
    @if($errors->any())
        <div class="mb-5 rounded-lg bg-error/10 border border-error/25 px-4 py-3 text-[13px] text-error">
            @foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach
        </div>
    @endif

    {{-- En-tête profil --}}
    <div class="bg-white border border-line rounded-2xl p-6 flex items-center gap-4 mb-4">
        <span class="w-14 h-14 rounded-[14px] bg-teal text-paper flex items-center justify-center text-[19px] font-bold shrink-0">{{ $init }}</span>
        <div class="min-w-0">
            <h2 class="font-display font-semibold text-[22px]">{{ $proprietaire->name }}</h2>
            <div class="text-[13px] text-muted">{{ $compteGlobal['nb_biens'] }} bien{{ $compteGlobal['nb_biens'] > 1 ? 's' : '' }} en gestion</div>
        </div>
        <a href="{{ route('admin.reversements.releve-pdf', ['proprietaire' => $proprietaire, 'periode' => $periode]) }}" class="ml-auto inline-flex items-center gap-2 px-4 py-2.5 rounded-[10px] border-[1.5px] border-teal text-teal bg-white text-[13.5px] font-bold hover:bg-paper transition-colors shrink-0"><x-icon name="file-text" size="15" /> Relevé PDF</a>
    </div>

    {{-- Bandeau solde --}}
    @if($solde > 0.5)
        <div class="bg-green text-white rounded-2xl p-6 md:px-8 mb-5" x-data="collapsible">
            <div class="flex flex-col sm:flex-row sm:items-center gap-4">
                <div class="w-[52px] h-[52px] rounded-[14px] bg-white/15 flex items-center justify-center shrink-0"><x-icon name="wallet" size="24" /></div>
                <div class="flex-1 min-w-0">
                    <div class="text-[11.5px] uppercase tracking-wide font-bold opacity-85 mb-1">Solde en cours à reverser</div>
                    <div class="font-display font-semibold text-[26px]">{{ $fmt($solde) }} F CFA</div>
                    <div class="text-[12.5px] opacity-90 mt-1">{{ $breakdown }}</div>
                </div>
                <button type="button" x-on:click="toggle" class="bg-white text-green px-5 py-3 rounded-[10px] text-[13.5px] font-bold hover:opacity-90 transition-opacity shrink-0 whitespace-nowrap">Marquer reversé</button>
            </div>

            {{-- Formulaire reversement --}}
            <div x-show="open" x-cloak class="mt-5 pt-5 border-t border-white/20">
                <form method="POST" action="{{ route('admin.reversements.store') }}" class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    @csrf
                    <input type="hidden" name="proprietaire_id" value="{{ $proprietaire->id }}">
                    <div>
                        <label class="block text-[12px] font-bold mb-1.5 opacity-90" for="rev-montant">Montant reversé (FCFA)</label>
                        <input class="w-full px-3.5 py-2.5 rounded-[9px] text-ink text-[14px] font-bold border-0" id="rev-montant" name="montant" type="number" min="1" step="1" value="{{ (int) $solde }}" required>
                    </div>
                    <div>
                        <label class="block text-[12px] font-bold mb-1.5 opacity-90" for="rev-mode">Mode</label>
                        <select class="w-full px-3.5 py-2.5 rounded-[9px] text-ink text-[14px] border-0" id="rev-mode" name="mode_paiement" required>
                            @foreach($modesPaiement as $key => $label)<option value="{{ $key }}">{{ $label }}</option>@endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-[12px] font-bold mb-1.5 opacity-90" for="rev-date">Date</label>
                        <input class="w-full px-3.5 py-2.5 rounded-[9px] text-ink text-[14px] border-0" id="rev-date" name="date_reversement" type="date" value="{{ now()->toDateString() }}" required>
                    </div>
                    <div>
                        <label class="block text-[12px] font-bold mb-1.5 opacity-90" for="rev-ref">Référence (facultatif)</label>
                        <input class="w-full px-3.5 py-2.5 rounded-[9px] text-ink text-[14px] border-0" id="rev-ref" name="reference" type="text" placeholder="N° transfert">
                    </div>
                    <div class="sm:col-span-2">
                        <button type="submit" class="w-full bg-teal-deep text-paper py-3 rounded-[10px] text-[14px] font-bold hover:opacity-90 transition-opacity">Confirmer le reversement de {{ $fmt($solde) }} F</button>
                    </div>
                </form>
            </div>
        </div>
    @elseif($solde < -0.5)
        <div class="bg-gold text-teal-deep rounded-2xl p-6 md:px-8 mb-5 flex flex-col sm:flex-row sm:items-center gap-4">
            <div class="w-[52px] h-[52px] rounded-[14px] bg-teal-deep/15 flex items-center justify-center text-[24px] shrink-0">↩</div>
            <div class="flex-1 min-w-0">
                <div class="text-[11.5px] uppercase tracking-wide font-bold opacity-80 mb-1">Avance de l'agence</div>
                <div class="font-display font-semibold text-[26px]">{{ $fmt(abs($solde)) }} F CFA</div>
                <div class="text-[12.5px] text-teal-deep/80 mt-1">Les dépenses dépassent les loyers encaissés. À récupérer sur les prochains loyers — aucun reversement dû.</div>
            </div>
        </div>
    @else
        <div class="bg-paper-dim text-ink rounded-2xl p-6 md:px-8 mb-5 flex items-center gap-4">
            <div class="w-[52px] h-[52px] rounded-[14px] bg-white flex items-center justify-center text-green shrink-0"><x-icon name="check" size="24" /></div>
            <div>
                <div class="font-display font-semibold text-[18px]">Compte à jour</div>
                <div class="text-[12.5px] text-muted mt-0.5">Aucun montant à reverser pour l'instant.</div>
            </div>
        </div>
    @endif

    {{-- Sélecteur de période --}}
    @if($periodes->isNotEmpty())
        <form method="GET" class="mb-5 flex items-center gap-2.5">
            <label class="text-[12.5px] text-muted font-semibold" for="periode">Détail du mois :</label>
            <select class="f-select max-w-[220px]" id="periode" name="periode" onchange="this.form.submit()">
                <option value="">Tout (solde global)</option>
                @foreach($periodes as $p)
                    <option value="{{ $p }}" @selected($periode === $p)>{{ \Carbon\Carbon::parse($p . '-01')->locale('fr')->isoFormat('MMMM Y') }}</option>
                @endforeach
            </select>
        </form>
    @endif

    {{-- Loyers encaissés --}}
    <div class="f-card mb-4">
        <h3 class="f-card-title mb-3">Loyers encaissés{{ $periode ? ' — ' . \Carbon\Carbon::parse($periode . '-01')->locale('fr')->isoFormat('MMMM Y') : '' }}</h3>
        @forelse($compte['paiements'] as $p)
            <div class="flex items-center gap-3.5 py-3 border-b border-paper-dim last:border-0">
                <span class="w-9 h-9 rounded-[9px] bg-green/10 text-green flex items-center justify-center text-[14px] shrink-0">↑</span>
                <div class="min-w-0">
                    <div class="font-semibold text-[14px] truncate">{{ $p->contrat?->bien?->titre ?: ('Bien ' . $p->contrat?->bien?->reference) }}</div>
                    <div class="text-[12px] text-muted">Payé le {{ optional($p->date_paiement)->locale('fr')->isoFormat('D/MM/Y') }}</div>
                </div>
                <div class="ml-auto font-bold text-[14.5px] text-green whitespace-nowrap">+{{ $fmt($p->montant_encaisse) }} F</div>
            </div>
        @empty
            <div class="py-6 text-center text-[13px] text-muted">Aucun loyer encaissé sur la période.</div>
        @endforelse

        @if(($compte['caution_incluse'] ?? 0) > 0.5)
            <div class="flex items-center gap-3.5 py-3 border-t border-paper-dim">
                <span class="w-9 h-9 rounded-[9px] bg-green/10 text-green flex items-center justify-center text-[13px] font-bold shrink-0">C</span>
                <div class="min-w-0">
                    <div class="font-semibold text-[14px]">Caution reçue</div>
                    <div class="text-[12px] text-muted">À remettre au propriétaire</div>
                </div>
                <div class="ml-auto font-bold text-[14.5px] text-green whitespace-nowrap">+{{ $fmt($compte['caution_incluse']) }} F</div>
            </div>
        @endif
    </div>

    {{-- Commission agence --}}
    <div class="f-card mb-4">
        <h3 class="f-card-title mb-3">Commission de l'agence</h3>
        <div class="flex items-center gap-3.5 py-2">
            <span class="w-9 h-9 rounded-[9px] bg-paper-dim text-muted flex items-center justify-center text-[14px] font-bold shrink-0">%</span>
            <div class="text-[13.5px] text-muted">Commission prélevée (taux fixé par bien)</div>
            <div class="ml-auto font-bold text-[14.5px] text-error whitespace-nowrap">−{{ $fmt($compte['commissions_deduites']) }} F</div>
        </div>
        @if($compte['brs_retenu'] > 0)
            <div class="flex items-center gap-3.5 py-2 border-t border-paper-dim">
                <span class="w-9 h-9 rounded-[9px] bg-paper-dim text-muted flex items-center justify-center text-[13px] font-bold shrink-0">B</span>
                <div class="text-[13.5px] text-muted">BRS retenu</div>
                <div class="ml-auto font-bold text-[14.5px] text-error whitespace-nowrap">−{{ $fmt($compte['brs_retenu']) }} F</div>
            </div>
        @endif
    </div>

    {{-- Dépenses avancées --}}
    <div class="f-card mb-4" x-data="depenseProprioForm">
        <div class="flex items-start justify-between gap-3 mb-1">
            <div>
                <h3 class="f-card-title">Dépenses avancées</h3>
                <p class="f-card-sub">Justificatif obligatoire pour toute dépense déduite de l'argent du propriétaire.</p>
            </div>
            <button type="button" x-on:click="toggle" class="text-[12.5px] font-bold px-3.5 py-2 rounded-lg bg-teal text-paper hover:bg-teal-deep transition-colors shrink-0">+ Ajouter une dépense</button>
        </div>

        {{-- Formulaire dépense --}}
        <div x-show="show" x-cloak class="mt-4 p-4 rounded-xl bg-paper border border-line">
            <form method="POST" action="{{ route('admin.comptabilite.depenses.store', $proprietaire) }}" enctype="multipart/form-data" class="space-y-3">
                @csrf
                <input type="hidden" name="type" x-bind:value="typeValue">

                {{-- Fourche --}}
                <div class="flex bg-paper-dim rounded-full p-1">
                    <button type="button" x-on:click="setBien" x-bind:class="bienTabClass" class="flex-1 text-[12.5px] font-bold py-2 rounded-full transition-colors">Liée à un bien</button>
                    <button type="button" x-on:click="setDirect" x-bind:class="directTabClass" class="flex-1 text-[12.5px] font-bold py-2 rounded-full transition-colors">Directement au propriétaire</button>
                </div>

                <div x-show="isBien" x-cloak>
                    <label class="f-label" for="dep-bien">Bien concerné</label>
                    <select class="f-select" id="dep-bien" name="bien_id" x-bind:disabled="isDirect">
                        @forelse($biensImputables as $b)
                            <option value="{{ $b->id }}">{{ $b->titre ?: ('Bien ' . $b->reference) }}</option>
                        @empty
                            <option value="">Aucun bien avec loyer encaissé</option>
                        @endforelse
                    </select>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="f-label" for="dep-libelle">Description</label>
                        <input class="f-input" id="dep-libelle" name="libelle" type="text" placeholder="Ex. Réparation plomberie" required>
                    </div>
                    <div>
                        <label class="f-label" for="dep-montant">Montant (FCFA)</label>
                        <input class="f-input" id="dep-montant" name="montant" type="number" min="1" step="1" placeholder="65000" required>
                    </div>
                    <div>
                        <label class="f-label" for="dep-cat">Catégorie</label>
                        <select class="f-select" id="dep-cat" name="categorie" required>
                            @foreach($categoriesProprio as $key => $label)<option value="{{ $key }}">{{ $label }}</option>@endforeach
                        </select>
                    </div>
                    <div>
                        <label class="f-label" for="dep-date">Date</label>
                        <input class="f-input" id="dep-date" name="date_depense" type="date" value="{{ now()->toDateString() }}" required>
                    </div>
                </div>

                {{-- Justificatif OBLIGATOIRE (traité en rouge, exprès) --}}
                <div>
                    <label class="f-label text-error" for="dep-justif">Justificatif <span class="font-bold">*obligatoire</span></label>
                    <input class="block w-full text-[13px] text-muted border-[1.5px] border-dashed border-error rounded-[10px] p-3 bg-error/5 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:bg-error file:text-white file:text-[12px] file:font-bold file:cursor-pointer" id="dep-justif" name="justificatif" type="file" accept="image/*,application/pdf" required>
                    <p class="text-[11px] text-error/80 mt-1">Photo de facture ou reçu — requis pour toute dépense déduite de l'argent d'un propriétaire.</p>
                </div>

                <button type="submit" class="btn-primary w-full">Enregistrer la dépense</button>
            </form>
        </div>

        {{-- Liste des dépenses (bien + directes) --}}
        <div class="mt-4">
            @php $aDesDepenses = $depensesBien->isNotEmpty() || $depensesDirectes->isNotEmpty(); @endphp
            @if(! $aDesDepenses)
                <div class="py-6 text-center text-[13px] text-muted">Aucune dépense avancée.</div>
            @else
                @foreach($depensesBien as $d)
                    @include('admin.reversements._depense-row', ['d' => $d, 'contexte' => $d->paiement?->contrat?->bien?->titre ?: 'Bien'])
                @endforeach
                @foreach($depensesDirectes as $d)
                    @include('admin.reversements._depense-row', ['d' => $d, 'contexte' => 'Direct au propriétaire'])
                @endforeach
            @endif

            <div class="flex items-center justify-between pt-4 mt-3 border-t-2 border-ink">
                <div class="font-bold text-[14.5px]">Net à reverser{{ $periode ? ' (mois)' : '' }} — total</div>
                <div class="font-display font-bold text-[19px]">{{ $fmt($compte['net_du']) }} F</div>
            </div>
            @if(($compte['reversements_effectues'] ?? 0) > 0.5)
                <div class="flex items-center justify-between pt-2">
                    <div class="text-[13.5px] text-muted">Déjà reversé</div>
                    <div class="font-bold text-[14.5px] text-error whitespace-nowrap">−{{ $fmt($compte['reversements_effectues']) }} F</div>
                </div>
                <div class="flex items-center justify-between pt-2 mt-1 border-t border-paper-dim">
                    <div class="font-bold text-[14.5px]">Solde restant à reverser</div>
                    <div class="font-display font-bold text-[19px] text-gold">{{ $fmt($compte['solde_restant']) }} F</div>
                </div>
            @endif
        </div>
    </div>

    {{-- Historique des relevés --}}
    <div class="f-card">
        <h3 class="f-card-title mb-3">Historique des reversements</h3>
        @forelse($reversements as $r)
            <div class="flex items-center gap-3.5 py-3 border-b border-paper-dim last:border-0">
                <div class="font-bold text-[13.5px] w-[130px] shrink-0">{{ optional($r->date_reversement)->locale('fr')->isoFormat('D MMM Y') }}</div>
                <div class="text-[13px] text-muted">{{ $r->mode_paiement_libelle }}{{ $r->reference ? ' · ' . $r->reference : '' }}</div>
                <div class="ml-auto font-bold text-[14px] whitespace-nowrap">{{ $fmt($r->montant) }} F</div>
            </div>
        @empty
            <div class="py-6 text-center text-[13px] text-muted">Aucun reversement enregistré.</div>
        @endforelse
    </div>

</div>
@endsection
