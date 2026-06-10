@extends('layouts.app')
@section('header', 'Paiements › ' . $paiement->reference_paiement)

@section('content')

@php
    $depenses    = $paiement->depenses ?? collect();
    $totalDep    = (float) $depenses->sum('montant');
    $netBailleur = (float) ($paiement->montant_net_bailleur ?? $paiement->net_a_verser_proprietaire ?? 0);
    $netFinalAff = round($netBailleur - $totalDep, 2);
    $canEditDep  = in_array(auth()->user()?->role, ['admin','superadmin']);
@endphp

<div class="space-y-4 md:space-y-5">

    {{-- Breadcrumb --}}
    <div class="flex items-center gap-2 font-body text-sm text-bimo-text/40">
        <a href="{{ route('admin.paiements.index') }}" class="hover:text-bimo-text transition-colors duration-150">Paiements</a>
        <svg class="w-3 h-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
        <span class="text-bimo-text font-medium">{{ $paiement->reference_paiement }}</span>
    </div>

    {{-- Actions --}}
    <div class="flex flex-wrap gap-2">
        <a href="{{ route('admin.paiements.pdf', $paiement) }}" target="_blank"
           class="inline-flex items-center gap-2 px-4 py-2.5 bg-bimo-navy text-white
                  font-display font-bold text-sm rounded-[10px] hover:bg-bimo-navy-dk transition-colors duration-150">
            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
            Télécharger quittance
        </a>
        <a href="{{ route('admin.contrats.show', $paiement->contrat) }}"
           class="inline-flex items-center gap-2 px-4 py-2.5 bg-white border border-bimo-navy/15 text-bimo-text/60
                  font-body text-sm rounded-[10px] hover:text-bimo-text hover:border-bimo-navy/30 transition-all duration-150">
            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
            Voir le contrat
        </a>
        <a href="{{ route('admin.paiements.index') }}"
           class="inline-flex items-center gap-2 px-4 py-2.5 bg-white border border-bimo-navy/15 text-bimo-text/60
                  font-body text-sm rounded-[10px] hover:text-bimo-text hover:border-bimo-navy/30 transition-all duration-150">
            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg>
            Retour
        </a>
        @if($paiement->statut === 'valide')
        <form method="POST" action="{{ route('admin.paiements.annuler', $paiement) }}"
              data-confirm="Le paiement {{ $paiement->reference_paiement }} sera annulé. Cette action est irréversible."
              data-confirm-title="Annuler ce paiement ?"
              data-confirm-ok="Oui, annuler"
              data-confirm-color="#d97706"
              data-confirm-icon-bg="rgba(217,119,6,0.1)">
            @csrf @method('PATCH')
            <button type="submit"
                    class="inline-flex items-center gap-2 px-4 py-2.5 bg-bimo-red/10 border border-bimo-red/20 text-bimo-red
                           font-body text-sm rounded-[10px] hover:bg-bimo-red/20 transition-all duration-150">
                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
                Annuler
            </button>
        </form>
        @endif
    </div>

    {{-- Hero --}}
    @php
        $heroBadge = match($paiement->statut) {
            'valide' => 'bg-bimo-gold/10 border-bimo-gold/25 text-bimo-gold',
            'annule' => 'bg-bimo-red/10 border-bimo-red/20 text-bimo-red',
            default  => 'bg-bimo-navy/10 border-bimo-navy/15 text-bimo-text/60',
        };
    @endphp
    <div class="bg-bimo-navy rounded-[14px] p-5 md:p-7 flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4 relative overflow-hidden">
        <div class="absolute top-0 right-0 w-48 h-48 rounded-full opacity-[0.06]"
             style="background: radial-gradient(circle, var(--ac) 0%, transparent 70%); transform: translate(30%, -30%)"></div>
        <div class="relative z-10">
            <div class="font-body font-medium text-[10px] uppercase tracking-widest text-white/30 mb-2">{{ $paiement->reference_paiement }}</div>
            <div class="font-display font-extrabold text-xl text-white leading-tight mb-1">
                {{ $paiement->contrat?->bien?->reference }} — {{ $paiement->contrat?->locataire?->name }}
            </div>
            <div class="font-body text-sm text-white/50 mb-3">
                Période : {{ \Carbon\Carbon::parse($paiement->periode)->translatedFormat('F Y') }}
                · Payé le {{ $paiement->date_paiement ? \Carbon\Carbon::parse($paiement->date_paiement)->format('d/m/Y') : '—' }}
            </div>
            <div class="flex items-center gap-3 flex-wrap">
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full border text-xs font-body font-medium {{ $heroBadge }}">
                    <span class="w-1.5 h-1.5 rounded-full bg-current"></span>
                    {{ ucfirst($paiement->statut) }}
                </span>
                <span class="font-body text-xs text-white/40">
                    {{ \App\Http\Controllers\PaiementController::MODES_PAIEMENT[$paiement->mode_paiement] ?? $paiement->mode_paiement }}
                </span>
            </div>
        </div>
        <div class="relative z-10 text-right flex-shrink-0">
            <div class="font-body font-medium text-[9.5px] uppercase tracking-widest text-bimo-gold/50 mb-1">Montant encaissé</div>
            <div class="font-display font-extrabold text-3xl text-bimo-gold leading-none">
                {{ number_format($paiement->montant_encaisse, 0, ',', ' ') }}
                <span class="font-body font-normal text-base text-bimo-gold/40">FCFA</span>
            </div>
        </div>
    </div>

    {{-- Grid principale --}}
    <div class="grid grid-cols-1 lg:grid-cols-[1fr_280px] gap-5 items-start">

        {{-- COLONNE GAUCHE --}}
        <div class="space-y-4">

            {{-- Parties --}}
            <div class="bg-white rounded-[14px] border border-bimo-navy/10 overflow-hidden">
                <div class="flex items-center gap-3 px-5 py-4 border-b border-bimo-navy/[5%] bg-bimo-bg2">
                    <div class="w-8 h-8 rounded-[8px] bg-bimo-gold/15 flex items-center justify-center flex-shrink-0">
                        <svg class="w-4 h-4 text-bimo-gold" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
                    </div>
                    <span class="font-display font-bold text-sm text-bimo-text">Parties</span>
                </div>
                <div class="px-5 py-5 grid grid-cols-2 gap-4">
                    <div>
                        <div class="font-body font-medium text-[10px] uppercase tracking-widest text-bimo-text/40 mb-1">Locataire</div>
                        <div class="font-body font-medium text-sm text-bimo-text">{{ $paiement->contrat?->locataire?->name ?? '—' }}</div>
                        <div class="font-body text-xs text-bimo-text/50">{{ $paiement->contrat?->locataire?->email }}</div>
                        <div class="font-body text-xs text-bimo-text/50">{{ $paiement->contrat?->locataire?->telephone ?? '' }}</div>
                    </div>
                    <div>
                        <div class="font-body font-medium text-[10px] uppercase tracking-widest text-bimo-text/40 mb-1">Propriétaire</div>
                        <div class="font-body font-medium text-sm text-bimo-text">{{ $paiement->contrat?->bien?->proprietaire?->name ?? '—' }}</div>
                        <div class="font-body text-xs text-bimo-text/50">{{ $paiement->contrat?->bien?->proprietaire?->email }}</div>
                        <div class="font-body text-xs text-bimo-text/50">{{ $paiement->contrat?->bien?->proprietaire?->telephone ?? '' }}</div>
                    </div>
                </div>
            </div>

            {{-- Décompte fiscal complet --}}
            @if(config('features.fiscalite'))
            <div class="bg-white rounded-[14px] border border-bimo-navy/10 overflow-hidden">
                <div class="flex items-center gap-3 px-5 py-4 border-b border-bimo-navy/[5%] bg-bimo-bg2">
                    <div class="w-8 h-8 rounded-[8px] bg-bimo-gold/15 flex items-center justify-center flex-shrink-0">
                        <svg class="w-4 h-4 text-bimo-gold" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6"/></svg>
                    </div>
                    <span class="font-display font-bold text-sm text-bimo-text">Décompte fiscal complet</span>
                </div>
                <div>
                    <x-fiscal.decompte-paiement :paiement="$paiement" />
                </div>
            </div>
            @endif

            {{-- Notes --}}
            @if($paiement->notes)
            <div class="bg-white rounded-[14px] border border-bimo-navy/10 overflow-hidden">
                <div class="flex items-center gap-3 px-5 py-4 border-b border-bimo-navy/[5%] bg-bimo-bg2">
                    <div class="w-8 h-8 rounded-[8px] bg-bimo-navy/5 flex items-center justify-center flex-shrink-0">
                        <svg class="w-4 h-4 text-bimo-text/40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/></svg>
                    </div>
                    <span class="font-display font-bold text-sm text-bimo-text">Notes</span>
                </div>
                <div class="px-5 py-5">
                    <p class="font-body text-sm text-bimo-text/70">{{ $paiement->notes }}</p>
                </div>
            </div>
            @endif

            {{-- Dépenses de gestion --}}
            <div class="bg-white rounded-[14px] border border-bimo-navy/10 overflow-hidden" id="card-depenses">
                <div class="flex items-center justify-between px-5 py-4 border-b border-bimo-navy/[5%] bg-bimo-bg2">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-[8px] bg-bimo-red/10 flex items-center justify-center flex-shrink-0">
                            <svg class="w-4 h-4 text-bimo-red" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 14l2-2 4 4"/><path d="M3 6h18M3 12h9m-9 6h5"/></svg>
                        </div>
                        <span class="font-display font-bold text-sm text-bimo-text">Dépenses pour le bailleur</span>
                        @if($totalDep > 0)
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-body font-medium bg-bimo-red/10 border border-bimo-red/20 text-bimo-red">
                            − {{ number_format($totalDep, 0, ',', ' ') }} F
                        </span>
                        @endif
                    </div>
                    @if($canEditDep)
                    <button onclick="toggleDepForm()" id="btn-dep-toggle"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 border border-bimo-navy/15 rounded-[7px]
                                   font-body text-xs text-bimo-text/60 hover:text-bimo-text hover:border-bimo-navy/30 transition-all duration-150">
                        <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                        Ajouter
                    </button>
                    @endif
                </div>

                <div class="px-5 py-4">

                    @if($depenses->isEmpty())
                    <div id="dep-empty" class="text-center py-8 font-body text-sm text-bimo-text/30">
                        <svg class="w-7 h-7 text-bimo-text/15 mx-auto mb-2" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M9 14l2-2 4 4"/><path d="M3 6h18M3 12h9m-9 6h5"/></svg>
                        Aucune dépense enregistrée pour ce mois
                    </div>
                    @else
                    <div id="dep-empty" style="display:none"></div>
                    @endif

                    <div id="dep-list" class="divide-y divide-bimo-navy/[5%]">
                    @foreach($depenses as $dep)
                    <div class="flex items-start gap-3 py-3.5" id="dep-{{ $dep->id }}">
                        <div class="w-8 h-8 rounded-[8px] bg-bimo-red/10 flex items-center justify-center flex-shrink-0 mt-0.5">
                            <svg class="w-3.5 h-3.5 text-bimo-red" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 14l2-2 4 4"/><path d="M3 6h18M3 12h9m-9 6h5"/></svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="font-body font-semibold text-sm text-bimo-text">{{ $dep->libelle }}</div>
                            <div class="flex items-center flex-wrap gap-1.5 mt-0.5">
                                <span class="font-body text-[11px] text-bimo-text/50">{{ $dep->categorie_libelle }}</span>
                                <span class="text-bimo-text/30 text-[10px]">·</span>
                                <span class="font-body text-[11px] text-bimo-text/50">{{ \Carbon\Carbon::parse($dep->date_depense)->format('d/m/Y') }}</span>
                                @if($dep->prestataire)
                                <span class="text-bimo-text/30 text-[10px]">·</span>
                                <span class="font-body text-[11px] text-bimo-text/50">{{ $dep->prestataire }}</span>
                                @endif
                            </div>
                            @if($dep->notes)
                            <div class="font-body text-[11px] text-bimo-text/40 italic mt-0.5">{{ $dep->notes }}</div>
                            @endif
                        </div>
                        <div class="flex items-center gap-2 flex-shrink-0">
                            <span class="font-display font-bold text-sm text-bimo-red">{{ number_format($dep->montant, 0, ',', ' ') }} FCFA</span>
                            @if($canEditDep)
                            <form method="POST"
                                  action="{{ route('admin.paiements.depenses.destroy', [$paiement, $dep]) }}"
                                  data-confirm="Supprimer « {{ $dep->libelle }} » ({{ number_format($dep->montant, 0, ',', ' ') }} F) ?"
                                  data-confirm-title="Supprimer cette dépense ?"
                                  data-confirm-ok="Oui, supprimer">
                                @csrf @method('DELETE')
                                <button type="submit"
                                        class="w-6 h-6 flex items-center justify-center text-bimo-text/25 hover:text-bimo-red transition-colors duration-150"
                                        title="Supprimer">
                                    <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14H6L5 6"/></svg>
                                </button>
                            </form>
                            @endif
                        </div>
                    </div>
                    @endforeach
                    </div>

                    @if($depenses->count() > 1)
                    <div class="flex items-center justify-between py-3 border-t border-bimo-navy/[5%] mt-1">
                        <span class="font-body text-xs text-bimo-text/50">Total dépenses ({{ $depenses->count() }})</span>
                        <span class="font-display font-bold text-sm text-bimo-red">{{ number_format($totalDep, 0, ',', ' ') }} FCFA</span>
                    </div>
                    @endif

                    @if($totalDep > 0)
                    <div class="flex items-center justify-between mt-3 p-3.5 bg-bimo-navy/[4%] border border-bimo-navy/10 rounded-[9px]">
                        <div>
                            <div class="font-body font-medium text-[9.5px] uppercase tracking-widest text-bimo-text/40 mb-0.5">Net à reverser au bailleur</div>
                            <div class="font-body text-[10px] text-bimo-text/30">Après déduction des dépenses</div>
                        </div>
                        <div class="font-display font-bold text-lg text-bimo-text">{{ number_format($netFinalAff, 0, ',', ' ') }} FCFA</div>
                    </div>
                    @endif

                    {{-- Formulaire ajout dépense --}}
                    @if($canEditDep)
                    <div id="dep-form-wrap" style="display:none" class="mt-4 p-5 bg-bimo-bg border border-bimo-navy/10 rounded-[12px]">
                        <div class="font-body font-semibold text-xs uppercase tracking-widest text-bimo-text/50 mb-4">Nouvelle dépense</div>

                        <form method="POST" action="{{ route('admin.paiements.depenses.store', $paiement) }}" id="dep-form" class="space-y-3">
                            @csrf
                            <input type="hidden" name="_dep_form_open" value="1">

                            <div class="space-y-1.5">
                                <label class="block font-body font-medium text-sm text-bimo-text">Libellé <span class="text-bimo-red">*</span></label>
                                <input type="text" name="libelle" value="{{ old('libelle') }}" required placeholder="Ex : Facture plombier Moussa"
                                       class="w-full px-4 py-3 rounded-[10px] bg-white border border-bimo-navy/20 font-body text-sm text-bimo-text
                                              placeholder:text-bimo-text/30 focus:outline-none focus:border-bimo-gold focus:ring-2 focus:ring-bimo-gold/15 transition-all duration-150">
                            </div>

                            <div class="grid grid-cols-2 gap-3">
                                <div class="space-y-1.5">
                                    <label class="block font-body font-medium text-sm text-bimo-text">Montant (FCFA) <span class="text-bimo-red">*</span></label>
                                    <input type="number" name="montant" value="{{ old('montant') }}" required min="1" step="1" placeholder="25000"
                                           class="w-full px-4 py-3 rounded-[10px] bg-white border border-bimo-navy/20 font-body text-sm text-bimo-text
                                                  focus:outline-none focus:border-bimo-gold focus:ring-2 focus:ring-bimo-gold/15 transition-all duration-150">
                                </div>
                                <div class="space-y-1.5">
                                    <label class="block font-body font-medium text-sm text-bimo-text">Catégorie <span class="text-bimo-red">*</span></label>
                                    <select name="categorie" required
                                            class="w-full px-4 py-3 rounded-[10px] bg-white border border-bimo-navy/20 font-body text-sm text-bimo-text cursor-pointer
                                                   focus:outline-none focus:border-bimo-gold focus:ring-2 focus:ring-bimo-gold/15 transition-all duration-150">
                                        <option value="">Choisir…</option>
                                        @foreach(\App\Models\DepenseGestion::CATEGORIES as $key => $label)
                                        <option value="{{ $key }}" {{ old('categorie') === $key ? 'selected' : '' }}>{{ $label }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-3">
                                <div class="space-y-1.5">
                                    <label class="block font-body font-medium text-sm text-bimo-text">Date de la dépense <span class="text-bimo-red">*</span></label>
                                    <input type="date" name="date_depense" value="{{ old('date_depense', now()->format('Y-m-d')) }}" required
                                           class="w-full px-4 py-3 rounded-[10px] bg-white border border-bimo-navy/20 font-body text-sm text-bimo-text
                                                  focus:outline-none focus:border-bimo-gold focus:ring-2 focus:ring-bimo-gold/15 transition-all duration-150">
                                </div>
                                <div class="space-y-1.5">
                                    <label class="block font-body font-medium text-sm text-bimo-text">Prestataire</label>
                                    <input type="text" name="prestataire" value="{{ old('prestataire') }}" placeholder="Ex : Moussa Diallo"
                                           class="w-full px-4 py-3 rounded-[10px] bg-white border border-bimo-navy/20 font-body text-sm text-bimo-text
                                                  placeholder:text-bimo-text/30 focus:outline-none focus:border-bimo-gold focus:ring-2 focus:ring-bimo-gold/15 transition-all duration-150">
                                </div>
                            </div>

                            <div class="space-y-1.5">
                                <label class="block font-body font-medium text-sm text-bimo-text">
                                    Notes <span class="font-normal text-bimo-text/40 text-xs ml-1">(optionnel)</span>
                                </label>
                                <textarea name="notes" rows="2" placeholder="Détails supplémentaires…"
                                          class="w-full px-4 py-3 rounded-[10px] bg-white border border-bimo-navy/20 font-body text-sm text-bimo-text
                                                 placeholder:text-bimo-text/30 focus:outline-none focus:border-bimo-gold focus:ring-2 focus:ring-bimo-gold/15
                                                 transition-all duration-150 resize-y">{{ old('notes') }}</textarea>
                            </div>

                            <div class="flex items-center justify-end gap-3 pt-2">
                                <button type="button" onclick="toggleDepForm()"
                                        class="px-4 py-2 border border-bimo-navy/15 rounded-[8px] font-body text-sm text-bimo-text/60 hover:text-bimo-text transition-all duration-150">
                                    Annuler
                                </button>
                                <button type="submit"
                                        class="inline-flex items-center gap-2 px-4 py-2 bg-[var(--ac)] text-white font-display font-bold text-sm rounded-[8px] hover:opacity-90 transition-opacity duration-150">
                                    <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                                    Enregistrer la dépense
                                </button>
                            </div>
                        </form>
                    </div>
                    @endif

                </div>
            </div>

        </div>{{-- fin colonne gauche --}}

        {{-- COLONNE DROITE --}}
        <div class="lg:sticky lg:top-6">
            <div class="bg-bimo-navy rounded-[14px] overflow-hidden">
                <div class="px-5 py-4 border-b border-white/[7%]">
                    <div class="font-display font-bold text-sm text-white">Résumé fiscal</div>
                </div>
                <div class="px-5 py-2 divide-y divide-white/[6%]">
                    @php
                        $sideRows = [
                            ['Référence',   $paiement->reference_paiement, 'text-xs'],
                            ['Référence bail', $paiement->reference_bail ?? '—', 'text-xs'],
                            ['Période',     \Carbon\Carbon::parse($paiement->periode)->translatedFormat('F Y'), ''],
                            ['Date paiement', $paiement->date_paiement ? \Carbon\Carbon::parse($paiement->date_paiement)->format('d/m/Y') : '—', ''],
                            ['Mode',        \App\Http\Controllers\PaiementController::MODES_PAIEMENT[$paiement->mode_paiement] ?? $paiement->mode_paiement, ''],
                        ];
                    @endphp
                    @foreach($sideRows as [$lbl, $val, $cls])
                    <div class="flex items-center justify-between py-2.5">
                        <span class="font-body text-xs text-white/40">{{ $lbl }}</span>
                        <span class="font-body text-xs text-white/70 {{ $cls }}">{{ $val }}</span>
                    </div>
                    @endforeach

                    <div class="py-3 border-t border-white/[7%]"></div>

                    <div class="flex items-center justify-between py-2.5">
                        <span class="font-body text-xs text-white/40">Loyer encaissé</span>
                        <span class="font-display font-semibold text-sm text-bimo-gold">{{ number_format($paiement->montant_encaisse, 0, ',', ' ') }} FCFA</span>
                    </div>
                    @if(($paiement->frais_agence_ttc ?? 0) > 0)
                    <div class="flex items-center justify-between py-2.5">
                        <span class="font-body text-xs text-white/40">Honoraires TTC</span>
                        <span class="font-body text-xs text-white/70">{{ number_format($paiement->frais_agence_ttc, 0, ',', ' ') }} FCFA</span>
                    </div>
                    @endif
                    @if(($paiement->caution_montant ?? 0) > 0)
                    <div class="flex items-center justify-between py-2.5">
                        <span class="font-body text-xs text-white/40">Caution</span>
                        <span class="font-body text-xs text-white/70">{{ number_format($paiement->caution_montant, 0, ',', ' ') }} FCFA</span>
                    </div>
                    @endif
                    @if(config('features.fiscalite') && ($paiement->brs_amount ?? 0) > 0)
                    <div class="flex items-center justify-between py-2.5">
                        <span class="font-body text-xs text-bimo-red/70">BRS retenu</span>
                        <span class="font-body text-xs text-bimo-red">− {{ number_format($paiement->brs_amount, 0, ',', ' ') }} FCFA</span>
                    </div>
                    @endif
                </div>

                {{-- Net locataire --}}
                <div class="px-5 pb-3">
                    <div class="p-3.5 bg-bimo-gold/10 border border-bimo-gold/20 rounded-[9px] mb-3">
                        <div class="font-body font-medium text-[9.5px] uppercase tracking-widest text-bimo-gold/60 mb-1">Net à payer — Locataire</div>
                        <div class="font-display font-extrabold text-lg text-bimo-gold">
                            {{ number_format($paiement->montant_net_locataire ?? ($paiement->total_encaissement_initial ?? $paiement->montant_encaisse), 0, ',', ' ') }} F
                        </div>
                    </div>

                    {{-- Net bailleur --}}
                    @php $netBrut = (float)($paiement->montant_net_bailleur ?? $paiement->net_a_verser_proprietaire ?? 0); @endphp
                    @if($totalDep > 0)
                    <div class="flex items-center justify-between p-3 bg-bimo-red/10 border border-bimo-red/20 rounded-[8px] mb-2">
                        <span class="font-body text-xs text-bimo-red/70">Dépenses déduites</span>
                        <span class="font-display font-bold text-sm text-bimo-red">− {{ number_format($totalDep, 0, ',', ' ') }} FCFA</span>
                    </div>
                    @endif
                    <div class="p-3.5 rounded-[9px] border {{ $totalDep > 0 ? 'bg-bimo-navy-dk/30 border-white/10' : 'bg-white/[4%] border-white/10' }}">
                        <div class="font-body font-medium text-[9.5px] uppercase tracking-widest text-white/30 mb-1">
                            Net à reverser — Bailleur{{ $totalDep > 0 ? ' (après dépenses)' : '' }}
                        </div>
                        <div class="font-display font-extrabold text-lg text-white">
                            {{ number_format($totalDep > 0 ? $netFinalAff : $netBrut, 0, ',', ' ') }} F
                        </div>
                        @if($totalDep > 0)
                        <div class="font-body text-[10px] text-white/25 mt-0.5">Brut : {{ number_format($netBrut, 0, ',', ' ') }} FCFA</div>
                        @endif
                    </div>

                    @if($paiement->caution_percue > 0)
                    <div class="mt-3 p-3.5 bg-bimo-navy-dk/50 border border-white/10 rounded-[9px]">
                        <div class="font-body font-medium text-[9.5px] uppercase tracking-widest text-white/30 mb-1">Caution perçue (saisie manuelle)</div>
                        <div class="font-display font-bold text-base text-white">{{ number_format($paiement->caution_percue, 0, ',', ' ') }} FCFA</div>
                    </div>
                    @endif

                    <a href="{{ route('admin.paiements.pdf', $paiement) }}" target="_blank"
                       class="flex items-center justify-center gap-2 mt-3 px-4 py-2.5 border border-white/10 rounded-[9px]
                              font-body text-xs text-bimo-gold hover:text-white hover:border-white/20 transition-all duration-150">
                        <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                        Télécharger la quittance PDF
                    </a>
                </div>
            </div>
        </div>

    </div>

</div>

@push('scripts')
<script>
function toggleDepForm() {
    var wrap = document.getElementById('dep-form-wrap');
    var btn  = document.getElementById('btn-dep-toggle');
    if (!wrap) return;
    var open = wrap.style.display === 'none' || wrap.style.display === '';
    wrap.style.display = open ? 'block' : 'none';
    if (btn) btn.innerHTML = open
        ? '<svg style="width:13px;height:13px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="5" y1="12" x2="19" y2="12"/></svg><span class="ml-1">Fermer</span>'
        : '<svg style="width:13px;height:13px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg><span class="ml-1">Ajouter</span>';
    if (open) setTimeout(() => wrap.scrollIntoView({ behavior:'smooth', block:'nearest' }), 60);
}

@if($errors->any())
document.addEventListener('DOMContentLoaded', function() {
    var wrap = document.getElementById('dep-form-wrap');
    if (wrap) { wrap.style.display = 'block'; wrap.scrollIntoView({ behavior:'smooth', block:'nearest' }); }
});
@endif
</script>
@endpush

@endsection
