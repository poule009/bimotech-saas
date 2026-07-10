@extends('layouts.app')

@php
    $fmt = fn ($n) => number_format((float) $n, 0, ',', ' ');
@endphp

@section('title', 'Comptabilité')
@section('page-title', 'Comptabilité')
@section('page-subtitle', "Deux flux d'argent séparés : celui des propriétaires (en transit) et celui de l'agence.")

@section('content')
<div class="max-w-[1180px]" x-data="comptaTabs" x-cloak>

    @if(session('success'))
        <div class="mb-5 rounded-lg bg-green/10 border border-green/25 px-4 py-3 text-[13px] text-green">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="mb-5 rounded-lg bg-error/10 border border-error/25 px-4 py-3 text-[13px] text-error">{{ session('error') }}</div>
    @endif
    @if(session('info'))
        <div class="mb-5 rounded-lg bg-teal/10 border border-teal/25 px-4 py-3 text-[13px] text-teal">{{ session('info') }}</div>
    @endif

    {{-- Onglets --}}
    <div class="flex gap-1 border-b-2 border-line mb-6">
        <button type="button" x-on:click="showProprietaires" x-bind:class="proprietairesTabClass" class="px-5 py-3 text-[14.5px] font-bold border-b-[3px] -mb-0.5 transition-colors">Propriétaires</button>
        <button type="button" x-on:click="showAgence" x-bind:class="agenceTabClass" class="px-5 py-3 text-[14.5px] font-bold border-b-[3px] -mb-0.5 transition-colors">Agence</button>
        <button type="button" x-on:click="showVerification" x-bind:class="verificationTabClass" class="px-5 py-3 text-[14.5px] font-bold border-b-[3px] -mb-0.5 transition-colors">Vérification</button>
    </div>

    {{-- ════════════ ONGLET PROPRIÉTAIRES ════════════ --}}
    <div x-show="isProprietaires">
        <p class="text-[13px] text-muted mb-4">Solde <strong class="text-ink">en cours</strong> de chaque propriétaire — mis à jour en temps réel, pas seulement le 1er du mois.</p>

        {{-- Recherche propriétaire (serveur) --}}
        <form method="GET" class="mb-4 flex items-center gap-2.5">
            <div class="flex items-center gap-2.5 bg-white border-[1.5px] border-line rounded-[11px] px-4 py-2.5 max-w-[340px] flex-1 focus-within:border-teal transition-colors">
                <svg class="w-[18px] h-[18px] text-muted shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="7"/><path d="m21 21-4.3-4.3"/></svg>
                <input type="text" name="q" value="{{ $q }}" placeholder="Rechercher un propriétaire…" class="flex-1 bg-transparent outline-none text-[14px] text-ink placeholder:text-muted">
            </div>
            @if($q !== '')
                <a href="{{ route('admin.comptabilite.index') }}" class="text-[13px] font-bold text-muted hover:text-teal transition-colors">Effacer</a>
            @endif
        </form>

        @if($lignesProprietaires->isEmpty())
            <div class="bg-white border border-line rounded-2xl py-16 text-center">
                @if($q !== '')
                    <div class="text-[15px] font-semibold mb-1">Aucun propriétaire trouvé</div>
                    <div class="text-[13px] text-muted">Rien ne correspond à « {{ $q }} ».</div>
                @else
                    <div class="text-[15px] font-semibold mb-1">Aucun mouvement propriétaire</div>
                    <div class="text-[13px] text-muted">Les soldes apparaîtront dès que des loyers seront encaissés.</div>
                @endif
            </div>
        @else
            <div class="bg-white border border-line rounded-2xl overflow-hidden">
                <table class="w-full border-collapse">
                    <thead>
                        <tr class="bg-paper-dim border-b border-line">
                            <th class="text-left text-[12px] uppercase tracking-wide text-muted font-bold px-6 py-4">Propriétaire</th>
                            <th class="text-left text-[12px] uppercase tracking-wide text-muted font-bold px-6 py-4">Loyers ce mois</th>
                            <th class="text-left text-[12px] uppercase tracking-wide text-muted font-bold px-6 py-4">Solde en cours</th>
                            <th class="px-6 py-4"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($lignesProprietaires as $ligne)
                            @php
                                $prop  = $ligne['proprietaire'];
                                $solde = $ligne['solde'];
                                $init  = mb_strtoupper(mb_substr($prop->name, 0, 2));
                            @endphp
                            <tr class="border-b border-paper-dim last:border-0 hover:bg-paper/60 transition-colors">
                                <td class="px-6 py-4">
                                    <a href="{{ route('admin.reversements.compte-mandant', $prop) }}" class="flex items-center gap-3.5 group">
                                        <span class="w-[42px] h-[42px] rounded-[11px] bg-teal text-paper flex items-center justify-center text-[14px] font-bold shrink-0">{{ $init }}</span>
                                        <span class="min-w-0">
                                            <span class="block font-bold text-[15px] group-hover:text-teal transition-colors">{{ $prop->name }}</span>
                                            <span class="block text-[12.5px] text-muted">{{ $ligne['nb_biens'] }} bien{{ $ligne['nb_biens'] > 1 ? 's' : '' }}</span>
                                        </span>
                                    </a>
                                </td>
                                <td class="px-6 py-4 text-[14.5px]">{{ $fmt($ligne['loyers_mois']) }} F</td>
                                <td class="px-6 py-4">
                                    @if($solde > 0.5)
                                        <span class="font-bold text-[15.5px] text-green">{{ $fmt($solde) }} F</span>
                                        <span class="ml-1.5 inline-block text-[11px] font-bold px-2.5 py-1 rounded-full bg-green/10 text-green">À reverser</span>
                                    @elseif($solde < -0.5)
                                        <span class="font-bold text-[15.5px] text-gold">−{{ $fmt(abs($solde)) }} F</span>
                                        <span class="ml-1.5 inline-block text-[11px] font-bold px-2.5 py-1 rounded-full bg-gold/15 text-gold">Avance agence</span>
                                    @else
                                        <span class="font-bold text-[15.5px] text-muted">0 F</span>
                                        <span class="ml-1.5 inline-block text-[11px] font-bold px-2.5 py-1 rounded-full bg-paper-dim text-muted">À jour</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <a href="{{ route('admin.reversements.compte-mandant', $prop) }}" class="text-muted hover:text-teal transition-colors">›</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    {{-- ════════════ ONGLET AGENCE ════════════ --}}
    <div x-show="isAgence" x-cloak>
        {{-- KPIs --}}
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
            <div class="bg-white border border-line rounded-xl p-5">
                <div class="text-[12px] text-muted font-bold mb-2">Revenu ce mois</div>
                <div class="font-display font-semibold text-[24px] text-green">{{ $fmt($revenuAgence) }} <span class="text-[14px] font-body text-muted">F</span></div>
                <div class="text-[11.5px] text-muted mt-1">Commissions {{ $agenceAssujettieTva ? 'HT' : 'encaissées' }}</div>
                @if($agenceAssujettieTva && $tvaCollectee > 0)
                    <div class="text-[11px] text-amber mt-1.5 pt-1.5 border-t border-paper-dim">+ {{ $fmt($tvaCollectee) }} F TVA collectée <span class="text-muted">(à reverser à la DGID)</span></div>
                @endif
            </div>
            <div class="bg-white border border-line rounded-xl p-5">
                <div class="text-[12px] text-muted font-bold mb-2">Dépenses ce mois</div>
                <div class="font-display font-semibold text-[24px] text-error">{{ $fmt($depensesAgence) }} <span class="text-[14px] font-body text-muted">F</span></div>
                <div class="text-[11.5px] text-muted mt-1">Fixes + occasionnelles</div>
            </div>
            <div class="bg-white border border-line rounded-xl p-5">
                <div class="text-[12px] text-muted font-bold mb-2">Bénéfice net</div>
                <div class="font-display font-semibold text-[24px]">{{ $fmt($beneficeNet) }} <span class="text-[14px] font-body text-muted">F</span></div>
                <div class="text-[11.5px] text-muted mt-1">Revenu − dépenses</div>
            </div>
        </div>

        {{-- Dépenses fixes --}}
        <div class="f-card mb-5" x-data="collapsible">
            <div class="flex items-start justify-between gap-3 mb-1">
                <div>
                    <h3 class="f-card-title">Dépenses fixes</h3>
                    <p class="f-card-sub">Récurrentes — pas besoin de les ressaisir chaque mois.</p>
                </div>
                <div class="flex items-center gap-2 shrink-0">
                    @if($modelesReportables > 0)
                        <form method="POST" action="{{ route('admin.charges-agence.reporter') }}">
                            @csrf
                            <button type="submit" class="text-[12.5px] font-bold px-3.5 py-2 rounded-lg border-[1.5px] border-teal text-teal bg-white hover:bg-paper transition-colors">↻ Reporter sur ce mois ({{ $modelesReportables }})</button>
                        </form>
                    @endif
                    <button type="button" x-on:click="toggle" class="text-[12.5px] font-bold px-3.5 py-2 rounded-lg bg-teal text-paper hover:bg-teal-deep transition-colors">+ Ajouter</button>
                </div>
            </div>

            {{-- Formulaire fixe --}}
            <div x-show="open" x-cloak class="mt-4 p-4 rounded-xl bg-paper border border-line">
                <form method="POST" action="{{ route('admin.charges-agence.store') }}" class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    @csrf
                    <input type="hidden" name="recurrente" value="1">
                    <div class="sm:col-span-2">
                        <label class="f-label" for="fx-libelle">Libellé</label>
                        <input class="f-input" id="fx-libelle" name="libelle" type="text" placeholder="Ex. Loyer bureau" required>
                    </div>
                    <div>
                        <label class="f-label" for="fx-categorie">Catégorie</label>
                        <select class="f-select" id="fx-categorie" name="categorie" required>
                            @foreach($categoriesAgence as $key => $label)
                                <option value="{{ $key }}" @selected($key === 'loyer_bureau')>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="f-label" for="fx-montant">Montant (FCFA)</label>
                        <input class="f-input" id="fx-montant" name="montant" type="number" min="0" step="1" placeholder="120000" required>
                    </div>
                    <div>
                        <label class="f-label" for="fx-date">Date</label>
                        <input class="f-input" id="fx-date" name="date_charge" type="date" value="{{ now()->toDateString() }}" required>
                    </div>
                    <div class="flex items-end">
                        <button type="submit" class="btn-primary w-full">Enregistrer la charge fixe</button>
                    </div>
                </form>
            </div>

            {{-- Liste fixes --}}
            <div class="mt-4">
                @forelse($chargesFixes as $c)
                    <div class="flex items-center gap-3.5 py-3 border-b border-paper-dim last:border-0">
                        <span class="w-9 h-9 rounded-[9px] bg-error/10 text-error flex items-center justify-center text-[13px] font-bold shrink-0">{{ mb_strtoupper(mb_substr($c->categorie_libelle, 0, 1)) }}</span>
                        <div class="min-w-0">
                            <div class="font-semibold text-[14px] truncate">{{ $c->libelle }}</div>
                            <div class="text-[12px] text-muted">{{ $c->categorie_libelle }} · {{ optional($c->date_charge)->locale('fr')->isoFormat('D MMM Y') }}</div>
                        </div>
                        <div class="ml-auto font-bold text-[14.5px] text-error whitespace-nowrap">−{{ $fmt($c->montant) }} F</div>
                        <form method="POST" action="{{ route('admin.charges-agence.destroy', $c) }}" x-data="confirmForm" x-on:submit="submit" data-confirm="Supprimer cette charge fixe ?">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-muted hover:text-error transition-colors text-[16px] px-1" aria-label="Supprimer">×</button>
                        </form>
                    </div>
                @empty
                    <div class="py-6 text-center text-[13px] text-muted">Aucune charge fixe ce mois. Ajoutez-en une, elle sera reportable les mois suivants.</div>
                @endforelse
            </div>
        </div>

        {{-- Dépenses occasionnelles --}}
        <div class="f-card" x-data="collapsible">
            <div class="flex items-start justify-between gap-3 mb-1">
                <div>
                    <h3 class="f-card-title">Dépenses occasionnelles</h3>
                    <p class="f-card-sub">Ponctuelles — ajoutées au cas par cas.</p>
                </div>
                <button type="button" x-on:click="toggle" class="text-[12.5px] font-bold px-3.5 py-2 rounded-lg bg-teal text-paper hover:bg-teal-deep transition-colors shrink-0">+ Ajouter</button>
            </div>

            <div x-show="open" x-cloak class="mt-4 p-4 rounded-xl bg-paper border border-line">
                <form method="POST" action="{{ route('admin.charges-agence.store') }}" class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    @csrf
                    <input type="hidden" name="recurrente" value="0">
                    <div class="sm:col-span-2">
                        <label class="f-label" for="oc-libelle">Libellé</label>
                        <input class="f-input" id="oc-libelle" name="libelle" type="text" placeholder="Ex. Impression brochures" required>
                    </div>
                    <div>
                        <label class="f-label" for="oc-categorie">Catégorie</label>
                        <select class="f-select" id="oc-categorie" name="categorie" required>
                            @foreach($categoriesAgence as $key => $label)
                                <option value="{{ $key }}" @selected($key === 'autre')>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="f-label" for="oc-montant">Montant (FCFA)</label>
                        <input class="f-input" id="oc-montant" name="montant" type="number" min="0" step="1" placeholder="45000" required>
                    </div>
                    <div>
                        <label class="f-label" for="oc-date">Date</label>
                        <input class="f-input" id="oc-date" name="date_charge" type="date" value="{{ now()->toDateString() }}" required>
                    </div>
                    <div class="flex items-end">
                        <button type="submit" class="btn-primary w-full">Enregistrer la dépense</button>
                    </div>
                </form>
            </div>

            <div class="mt-4">
                @forelse($chargesOccasionnelles as $c)
                    <div class="flex items-center gap-3.5 py-3 border-b border-paper-dim last:border-0">
                        <span class="w-9 h-9 rounded-[9px] bg-error/10 text-error flex items-center justify-center text-[13px] font-bold shrink-0">{{ mb_strtoupper(mb_substr($c->categorie_libelle, 0, 1)) }}</span>
                        <div class="min-w-0">
                            <div class="font-semibold text-[14px] truncate">{{ $c->libelle }}</div>
                            <div class="text-[12px] text-muted">{{ $c->categorie_libelle }} · {{ optional($c->date_charge)->locale('fr')->isoFormat('D MMM Y') }}</div>
                        </div>
                        <div class="ml-auto font-bold text-[14.5px] text-error whitespace-nowrap">−{{ $fmt($c->montant) }} F</div>
                        <form method="POST" action="{{ route('admin.charges-agence.destroy', $c) }}" x-data="confirmForm" x-on:submit="submit" data-confirm="Supprimer cette dépense ?">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-muted hover:text-error transition-colors text-[16px] px-1" aria-label="Supprimer">×</button>
                        </form>
                    </div>
                @empty
                    <div class="py-6 text-center text-[13px] text-muted">Aucune dépense occasionnelle ce mois.</div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- ════════════ ONGLET VÉRIFICATION ════════════ --}}
    <div x-show="isVerification" x-cloak>
        <div class="max-w-[600px] mx-auto bg-white border border-line rounded-2xl p-8 md:p-10 text-center" x-data="verification" data-theorique="{{ $soldeTheorique }}">
            <h3 class="font-display font-semibold text-[19px] mb-2">Contrôle de caisse</h3>
            <p class="text-[13.5px] text-muted mb-7 max-w-[420px] mx-auto">Comparez l'argent des tiers que vous devez détenir au solde réel de votre compte de gestion.</p>

            {{-- Décomposition transparente du solde théorique --}}
            <div class="max-w-[380px] mx-auto mb-7 text-left bg-paper rounded-xl border border-line p-4">
                <div class="flex items-center justify-between py-1.5">
                    <span class="text-[13px] text-muted">Dû aux propriétaires</span>
                    <span class="text-[14px] font-bold text-green">{{ $fmt($duProprietaires) }} F</span>
                </div>
                @if($avancesAgence > 0)
                    <div class="flex items-center justify-between py-1.5 border-t border-paper-dim">
                        <span class="text-[13px] text-muted">Avances de l'agence <span class="text-[11px]">(à récupérer)</span></span>
                        <span class="text-[14px] font-bold text-gold">− {{ $fmt($avancesAgence) }} F</span>
                    </div>
                @endif
                <div class="flex items-center justify-between py-2 mt-1 border-t-2 border-ink">
                    <span class="text-[13.5px] font-bold">Solde théorique à détenir</span>
                    <span class="font-display font-semibold text-[18px]">{{ $fmt($soldeTheorique) }} F</span>
                </div>
            </div>

            <div class="flex flex-col sm:flex-row gap-2.5 justify-center items-stretch sm:items-center max-w-[420px] mx-auto">
                <input type="text" inputmode="numeric" x-model="reel" placeholder="Montant réel sur le compte" class="f-input text-center font-bold flex-1">
            </div>

            {{-- Résultat --}}
            <div x-show="showOk" x-cloak class="mt-6 inline-flex items-center gap-2.5 px-5 py-3 rounded-xl bg-green/10 text-green font-bold text-[14px]">
                <span class="text-[18px]">✓</span> Comptes équilibrés
            </div>
            <div x-show="showEcart" x-cloak class="mt-6 inline-flex items-center gap-2.5 px-5 py-3 rounded-xl bg-error/10 text-error font-bold text-[14px]">
                <span class="text-[18px]">⚠</span> <span x-text="ecartLabel"></span> : <span x-text="ecartAbs"></span>
            </div>
        </div>
    </div>

</div>
@endsection
