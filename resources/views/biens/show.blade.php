@extends('layouts.app')
@section('header', $bien->reference . ' — ' . $bien->type_label)

@section('content')
<div class="space-y-4 md:space-y-5">

    {{-- Breadcrumb --}}
    <div class="flex items-center gap-2 font-body text-sm text-bimo-navy/40">
        <a href="{{ route('admin.biens.index') }}" class="hover:text-bimo-navy transition-colors duration-150">Biens</a>
        <svg class="w-3 h-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
        <span class="text-bimo-navy font-medium">{{ $bien->reference }}</span>
    </div>

    {{-- Actions --}}
    <div class="flex flex-wrap gap-2">
        <a href="{{ route('admin.biens.edit', $bien) }}"
           class="inline-flex items-center gap-2 px-4 py-2.5 bg-bimo-navy text-white
                  font-display font-bold text-sm rounded-[10px] hover:bg-bimo-navy-dk transition-colors duration-150">
            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
            Modifier
        </a>

        @if(!$bien->contratActif)
        <a href="{{ route('admin.contrats.create', ['bien_id' => $bien->id]) }}"
           class="inline-flex items-center gap-2 px-4 py-2.5 bg-bimo-gold/10 border border-bimo-gold/25 text-bimo-gold
                  font-display font-bold text-sm rounded-[10px] hover:bg-bimo-gold/20 transition-all duration-150">
            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><line x1="12" y1="11" x2="12" y2="17"/><line x1="9" y1="14" x2="15" y2="14"/></svg>
            Créer un contrat
        </a>
        @endif

        @if($bien->contratActif)
        <a href="{{ route('admin.paiements.create', ['contrat_id' => $bien->contratActif->id]) }}"
           class="inline-flex items-center gap-2 px-4 py-2.5 bg-bimo-gold/10 border border-bimo-gold/25 text-bimo-gold
                  font-display font-bold text-sm rounded-[10px] hover:bg-bimo-gold/20 transition-all duration-150">
            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6"/></svg>
            Enregistrer un paiement
        </a>
        <a href="{{ route('admin.contrats.show', $bien->contratActif) }}"
           class="inline-flex items-center gap-2 px-4 py-2.5 bg-white border border-bimo-navy/15 text-bimo-navy/60
                  font-body text-sm rounded-[10px] hover:text-bimo-navy hover:border-bimo-navy/30 transition-all duration-150">
            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
            Voir le contrat
        </a>
        @endif

        <a href="{{ route('admin.biens.index') }}"
           class="inline-flex items-center gap-2 px-4 py-2.5 bg-white border border-bimo-navy/15 text-bimo-navy/60
                  font-body text-sm rounded-[10px] hover:text-bimo-navy hover:border-bimo-navy/30 transition-all duration-150">
            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg>
            Retour
        </a>

        @if(!$bien->contratActif)
        <form method="POST" action="{{ route('admin.biens.destroy', $bien) }}"
              data-confirm="Le bien {{ $bien->reference }} sera archivé. Cette action est irréversible."
              data-confirm-title="Archiver ce bien ?"
              data-confirm-ok="Oui, archiver">
            @csrf @method('DELETE')
            <button type="submit"
                    class="inline-flex items-center gap-2 px-4 py-2.5 bg-bimo-red/10 border border-bimo-red/20 text-bimo-red
                           font-body text-sm rounded-[10px] hover:bg-bimo-red/20 transition-all duration-150">
                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6"/></svg>
                Archiver
            </button>
        </form>
        @endif
    </div>

    {{-- Alerte portail incomplet --}}
    @if(!empty($raisonsAbsence))
    <div class="flex items-start gap-3 bg-bimo-gold/[8%] border border-bimo-gold/25 rounded-[12px] px-4 py-3">
        <svg class="w-4 h-4 text-bimo-gold flex-shrink-0 mt-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
        </svg>
        <div>
            <div class="font-body font-semibold text-sm text-bimo-gold mb-0.5">Ce bien n'est pas affiché sur le portail public</div>
            <div class="font-body text-xs text-bimo-gold/80">
                Il manque : <strong>{{ implode(', ', $raisonsAbsence) }}</strong>.
                <a href="{{ route('admin.biens.edit', $bien) }}"
                   class="font-semibold ml-1 hover:text-bimo-navy transition-colors duration-150">
                    Compléter la fiche →
                </a>
            </div>
        </div>
    </div>
    @endif

    {{-- Hero card sombre --}}
    <div class="bg-bimo-navy rounded-[14px] p-5 md:p-7 flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4 relative overflow-hidden">
        <div class="absolute top-0 right-0 w-48 h-48 rounded-full opacity-[0.06]"
             style="background: radial-gradient(circle, #C9A84C 0%, transparent 70%); transform: translate(30%, -30%)">
        </div>
        <div class="relative z-10">
            <div class="font-body font-medium text-[10px] uppercase tracking-widest text-white/30 mb-2">
                {{ $bien->reference }}
            </div>
            <div class="font-display font-extrabold text-xl text-white leading-tight mb-1">
                {{ $bien->titre ?? $bien->type_label }}
                @if($bien->meuble)
                <span class="font-body font-normal text-sm text-bimo-gold/70 ml-2">· Meublé</span>
                @endif
            </div>
            @if($bien->titre)
            <div class="font-body text-sm text-white/40 mb-1">{{ $bien->type_label }}</div>
            @endif
            <div class="font-body text-sm text-white/50 mb-3">
                {{ $bien->adresse }}
                @if($bien->quartier) · {{ $bien->quartier }} @endif
                @if($bien->commune) · {{ $bien->commune }} @endif
                · {{ $bien->ville }}
            </div>
            <div class="flex items-center gap-3 flex-wrap">
                @php
                    $badgeClass = match($bien->statut) {
                        'disponible' => 'bg-bimo-gold/10 border-bimo-gold/25 text-bimo-gold',
                        'loue'       => 'bg-bimo-navy-dk/50 border-white/10 text-white/70',
                        'en_travaux' => 'bg-white/5 border-white/10 text-white/50',
                        default      => 'bg-white/5 border-white/10 text-white/30',
                    };
                @endphp
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full border text-xs font-body font-medium {{ $badgeClass }}">
                    <span class="w-1.5 h-1.5 rounded-full bg-current"></span>
                    {{ $bien->statut_label }}
                </span>
                @if($bien->surface_m2)
                <span class="font-body text-xs text-white/40">{{ $bien->surface_m2 }} m²</span>
                @endif
                @if($bien->nombre_pieces)
                <span class="font-body text-xs text-white/40">{{ $bien->nombre_pieces }} pièces</span>
                @endif
            </div>
        </div>
        <div class="relative z-10 text-right flex-shrink-0">
            <div class="font-body font-medium text-[9px] uppercase tracking-widest text-bimo-gold/50 mb-1">Loyer mensuel</div>
            <div class="font-display font-extrabold text-3xl text-bimo-gold leading-none">
                {{ number_format($bien->loyer_mensuel, 0, ',', ' ') }}
                <span class="font-body font-normal text-base text-bimo-gold/40">F</span>
            </div>
            <div class="font-body text-xs text-white/30 mt-1">Commission {{ $bien->taux_commission ?? 10 }}%</div>
        </div>
    </div>

    {{-- KPIs --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
        <div class="bg-bimo-gold/[8%] rounded-[14px] border border-bimo-gold/25 p-4">
            <div class="font-body font-medium text-[9.5px] uppercase tracking-widest text-bimo-gold/70 mb-1">Loyer mensuel</div>
            <div class="font-display font-extrabold text-xl text-bimo-gold leading-none">
                {{ number_format($bien->loyer_mensuel, 0, ',', ' ') }}
                <span class="font-body font-normal text-sm text-bimo-gold/50">F</span>
            </div>
            <div class="font-body text-[10.5px] text-bimo-gold/60 mt-1.5">FCFA / mois</div>
        </div>
        <div class="bg-white rounded-[14px] border border-bimo-navy/10 p-4">
            <div class="font-body font-medium text-[9.5px] uppercase tracking-widest text-bimo-navy/50 mb-1">Net propriétaire</div>
            <div class="font-display font-extrabold text-xl text-bimo-navy leading-none">
                {{ number_format($bien->loyer_mensuel * (1 - ($bien->taux_commission ?? 10) / 100 * 1.18), 0, ',', ' ') }}
                <span class="font-body font-normal text-sm text-bimo-navy/40">F</span>
            </div>
            <div class="font-body text-[10.5px] text-bimo-navy/40 mt-1.5">Après commission TTC</div>
        </div>
        <div class="bg-white rounded-[14px] border border-bimo-navy/10 p-4">
            <div class="font-body font-medium text-[9.5px] uppercase tracking-widest text-bimo-navy/50 mb-1">Contrats</div>
            <div class="font-display font-extrabold text-xl text-bimo-navy leading-none">
                {{ $bien->contrats->count() }}
            </div>
            <div class="font-body text-[10.5px] text-bimo-navy/40 mt-1.5">
                {{ $bien->contratActif ? '1 actif' : 'Aucun actif' }}
            </div>
        </div>
    </div>

    {{-- Grid principale --}}
    <div class="grid grid-cols-1 lg:grid-cols-[1fr_300px] gap-5 items-start">

        {{-- COLONNE GAUCHE --}}
        <div class="space-y-4">

            {{-- Photos --}}
            @if($bien->photos->count() > 0)
            @php $principale = $bien->photos->firstWhere('est_principale', true) ?? $bien->photos->first(); @endphp
            <div class="bg-white rounded-[14px] border border-bimo-navy/10 overflow-hidden">
                <div class="flex items-center justify-between px-5 py-4 border-b border-bimo-navy/[5%] bg-bimo-bg2">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-[8px] bg-bimo-navy/5 flex items-center justify-center flex-shrink-0">
                            <svg class="w-4 h-4 text-bimo-navy/40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
                        </div>
                        <span class="font-display font-bold text-sm text-bimo-navy">
                            Photos <span class="font-body font-normal text-bimo-navy/40 text-xs">({{ $bien->photos->count() }})</span>
                        </span>
                    </div>
                    <a href="{{ route('admin.biens.edit', $bien) }}"
                       class="font-body text-xs text-bimo-navy/40 hover:text-bimo-gold transition-colors duration-150">
                        Gérer les photos →
                    </a>
                </div>
                <div class="px-5 py-5">
                    <div class="h-64 md:h-72 bg-bimo-bg2 rounded-[10px] overflow-hidden mb-3">
                        <img src="{{ asset('storage/'.$principale->chemin) }}"
                             id="photo-principale" alt="Photo principale"
                             class="w-full h-full object-cover">
                    </div>
                    @if($bien->photos->count() > 1)
                    <div class="flex gap-2 flex-wrap">
                        @foreach($bien->photos as $photo)
                        <div class="w-[72px] h-14 rounded-[8px] overflow-hidden cursor-pointer border-2 transition-all duration-150 flex-shrink-0
                                    {{ $photo->id === $principale->id ? 'border-bimo-gold' : 'border-transparent hover:border-bimo-gold/50' }}"
                             onclick="changerPhoto('{{ asset('storage/'.$photo->chemin) }}', this)">
                            <img src="{{ asset('storage/'.$photo->chemin) }}" alt=""
                                 class="w-full h-full object-cover">
                        </div>
                        @endforeach
                    </div>
                    @endif
                </div>
            </div>
            @endif

            {{-- Informations --}}
            <div class="bg-white rounded-[14px] border border-bimo-navy/10 overflow-hidden">
                <div class="flex items-center gap-3 px-5 py-4 border-b border-bimo-navy/[5%] bg-bimo-bg2">
                    <div class="w-8 h-8 rounded-[8px] bg-bimo-navy/10 flex items-center justify-center flex-shrink-0">
                        <svg class="w-4 h-4 text-bimo-navy/50" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                    </div>
                    <span class="font-display font-bold text-sm text-bimo-navy">Informations</span>
                </div>
                <div class="px-5 py-5">
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-4 mb-5">
                        @php
                            $infos = [
                                ['Type',      $bien->type_label],
                                ['Surface',   $bien->surface_m2 ? $bien->surface_m2.' m²' : '—'],
                                ['Pièces',    $bien->nombre_pieces ?? '—'],
                                ['Chambres',  $bien->nombre_chambres ?? '—'],
                                ['SDB',       $bien->nombre_sdb ?? '—'],
                                ['Étage',     $bien->etage ?? '—'],
                                ['Meublé',    $bien->meuble ? 'Oui' : 'Non'],
                                ['Parking',   $bien->parking ? 'Oui' : 'Non'],
                                ['Climatisé', $bien->climatise ? 'Oui' : 'Non'],
                                ['Statut',    $bien->statut_label],
                                ['Référence', $bien->reference],
                            ];
                        @endphp
                        @foreach($infos as [$lbl, $val])
                        <div>
                            <div class="font-body font-medium text-[10px] uppercase tracking-widest text-bimo-navy/40 mb-1">{{ $lbl }}</div>
                            <div class="font-body font-medium text-sm text-bimo-navy">{{ $val }}</div>
                        </div>
                        @endforeach
                    </div>

                    {{-- Localisation --}}
                    <div class="pt-4 border-t border-bimo-navy/[5%]">
                        <div class="font-body font-medium text-[10px] uppercase tracking-widest text-bimo-navy/40 mb-3">Localisation</div>
                        <div class="grid grid-cols-2 gap-4">
                            @foreach([['Adresse', $bien->adresse], ['Quartier', $bien->quartier ?? '—'], ['Commune', $bien->commune ?? '—'], ['Ville', $bien->ville]] as [$lbl, $val])
                            <div>
                                <div class="font-body font-medium text-[10px] uppercase tracking-widest text-bimo-navy/40 mb-1">{{ $lbl }}</div>
                                <div class="font-body text-sm text-bimo-navy">{{ $val }}</div>
                            </div>
                            @endforeach
                        </div>
                    </div>

                    {{-- Aménités --}}
                    @if(!empty($bien->amenites))
                    <div class="pt-4 border-t border-bimo-navy/[5%] mt-4">
                        <div class="font-body font-medium text-[10px] uppercase tracking-widest text-bimo-navy/40 mb-3">Commodités</div>
                        <div class="flex flex-wrap gap-2">
                            @foreach($bien->amenites as $item)
                            <span class="inline-flex items-center px-3 py-1 rounded-full border border-bimo-navy/10 bg-bimo-bg font-body text-xs text-bimo-navy/70">
                                {{ $item }}
                            </span>
                            @endforeach
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Contrat actif --}}
            @if($bien->contratActif)
            <div class="bg-white rounded-[14px] border border-bimo-navy/10 overflow-hidden">
                <div class="flex items-center justify-between px-5 py-4 border-b border-bimo-navy/[5%] bg-bimo-bg2">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-[8px] bg-bimo-gold/15 flex items-center justify-center flex-shrink-0">
                            <svg class="w-4 h-4 text-bimo-gold" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                        </div>
                        <span class="font-display font-bold text-sm text-bimo-navy">Contrat actif</span>
                    </div>
                    <a href="{{ route('admin.contrats.show', $bien->contratActif) }}"
                       class="font-body text-xs text-bimo-navy/40 hover:text-bimo-gold transition-colors duration-150">
                        Voir le contrat →
                    </a>
                </div>
                <div class="px-5 py-5 grid grid-cols-2 md:grid-cols-3 gap-4">
                    <div>
                        <div class="font-body font-medium text-[10px] uppercase tracking-widest text-bimo-navy/40 mb-1">Locataire</div>
                        <div class="font-body font-medium text-sm text-bimo-navy">{{ $bien->contratActif->locataire?->name ?? '—' }}</div>
                        <div class="font-body text-xs text-bimo-navy/50">{{ $bien->contratActif->locataire?->telephone ?? '' }}</div>
                    </div>
                    <div>
                        <div class="font-body font-medium text-[10px] uppercase tracking-widest text-bimo-navy/40 mb-1">Loyer contractuel</div>
                        <div class="font-display font-bold text-sm text-bimo-gold">
                            {{ number_format($bien->contratActif->loyer_contractuel, 0, ',', ' ') }} F
                        </div>
                    </div>
                    <div>
                        <div class="font-body font-medium text-[10px] uppercase tracking-widest text-bimo-navy/40 mb-1">Période</div>
                        <div class="font-body text-sm text-bimo-navy">{{ $bien->contratActif->date_debut?->format('d/m/Y') }}</div>
                        <div class="font-body text-xs text-bimo-navy/50">
                            {{ $bien->contratActif->date_fin?->format('d/m/Y') ?? 'Contrat ouvert' }}
                        </div>
                    </div>
                    <div>
                        <div class="font-body font-medium text-[10px] uppercase tracking-widest text-bimo-navy/40 mb-1">Type bail</div>
                        <div class="font-body text-sm text-bimo-navy">
                            {{ \App\Models\Contrat::TYPES_BAIL[$bien->contratActif->type_bail] ?? $bien->contratActif->type_bail }}
                        </div>
                    </div>
                    <div>
                        <div class="font-body font-medium text-[10px] uppercase tracking-widest text-bimo-navy/40 mb-1">Référence bail</div>
                        <div class="font-body text-xs text-bimo-navy">
                            {{ $bien->contratActif->reference_bail ?? 'BAIL-'.$bien->contratActif->id }}
                        </div>
                    </div>
                    <div>
                        <div class="font-body font-medium text-[10px] uppercase tracking-widest text-bimo-navy/40 mb-1">Caution</div>
                        <div class="font-body text-sm text-bimo-navy">{{ number_format($bien->contratActif->caution, 0, ',', ' ') }} F</div>
                    </div>
                </div>
            </div>
            @endif

            {{-- Derniers paiements --}}
            <div class="bg-white rounded-[14px] border border-bimo-navy/10 overflow-hidden">
                <div class="flex items-center justify-between px-5 py-4 border-b border-bimo-navy/[5%] bg-bimo-bg2">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-[8px] bg-bimo-navy/5 flex items-center justify-center flex-shrink-0">
                            <svg class="w-4 h-4 text-bimo-navy/40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="1" y="4" width="22" height="16" rx="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>
                        </div>
                        <span class="font-display font-bold text-sm text-bimo-navy">Derniers paiements</span>
                    </div>
                    @if($bien->contratActif)
                    <a href="{{ route('admin.paiements.create', ['contrat_id' => $bien->contratActif->id]) }}"
                       class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-bimo-gold/10 border border-bimo-gold/20 rounded-[7px]
                              font-body font-medium text-xs text-bimo-gold hover:bg-bimo-gold/20 transition-all duration-150">
                        + Paiement
                    </a>
                    @endif
                </div>

                @if($paiements->isEmpty())
                <div class="px-5 py-10 text-center font-body text-sm text-bimo-navy/30">
                    @if($bien->contratActif) Aucun paiement enregistré pour ce contrat.
                    @else Aucun contrat actif sur ce bien. @endif
                </div>
                @else

                {{-- Mobile cards --}}
                <div class="md:hidden divide-y divide-bimo-navy/[5%]">
                    @foreach($paiements as $p)
                    <div class="px-5 py-3.5 flex items-center justify-between gap-3">
                        <div>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-display font-semibold bg-bimo-gold/10 border border-bimo-gold/20 text-bimo-gold">
                                {{ \Carbon\Carbon::parse($p->periode)->translatedFormat('M Y') }}
                            </span>
                            <div class="font-body text-xs text-bimo-navy/40 mt-1">
                                {{ $p->date_paiement ? \Carbon\Carbon::parse($p->date_paiement)->format('d/m/Y') : '—' }}
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="font-display font-bold text-sm text-bimo-gold">{{ number_format($p->montant_encaisse, 0, ',', ' ') }} F</div>
                            <div class="font-body text-xs text-bimo-navy/40 mt-0.5">Net: {{ number_format($p->net_a_verser_proprietaire ?? $p->net_proprietaire ?? 0, 0, ',', ' ') }} F</div>
                        </div>
                    </div>
                    @endforeach
                </div>

                {{-- Desktop table --}}
                <div class="hidden md:block overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-bimo-navy/[5%] bg-bimo-bg">
                                <th class="px-5 py-3 text-left font-body font-medium text-[10px] uppercase tracking-widest text-bimo-navy/40">Période</th>
                                <th class="px-5 py-3 text-left font-body font-medium text-[10px] uppercase tracking-widest text-bimo-navy/40">Date</th>
                                <th class="px-5 py-3 text-right font-body font-medium text-[10px] uppercase tracking-widest text-bimo-navy/40">Montant</th>
                                <th class="px-5 py-3 text-right font-body font-medium text-[10px] uppercase tracking-widest text-bimo-navy/40">Net proprio</th>
                                <th class="px-5 py-3 text-left font-body font-medium text-[10px] uppercase tracking-widest text-bimo-navy/40">Mode</th>
                                <th class="px-5 py-3 text-center font-body font-medium text-[10px] uppercase tracking-widest text-bimo-navy/40">PDF</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-bimo-navy/[5%]">
                            @foreach($paiements as $p)
                            <tr class="hover:bg-bimo-bg transition-colors duration-100">
                                <td class="px-5 py-3.5">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-display font-semibold bg-bimo-gold/10 border border-bimo-gold/20 text-bimo-gold">
                                        {{ \Carbon\Carbon::parse($p->periode)->translatedFormat('M Y') }}
                                    </span>
                                </td>
                                <td class="px-5 py-3.5 font-body text-xs text-bimo-navy/50">
                                    {{ $p->date_paiement ? \Carbon\Carbon::parse($p->date_paiement)->format('d/m/Y') : '—' }}
                                </td>
                                <td class="px-5 py-3.5 text-right font-display font-bold text-sm text-bimo-gold">
                                    {{ number_format($p->montant_encaisse, 0, ',', ' ') }} F
                                </td>
                                <td class="px-5 py-3.5 text-right font-body font-semibold text-sm text-bimo-navy">
                                    {{ number_format($p->net_a_verser_proprietaire ?? $p->net_proprietaire ?? 0, 0, ',', ' ') }} F
                                </td>
                                <td class="px-5 py-3.5 font-body text-xs text-bimo-navy/50">
                                    {{ \App\Http\Controllers\PaiementController::MODES_PAIEMENT[$p->mode_paiement] ?? $p->mode_paiement }}
                                </td>
                                <td class="px-5 py-3.5 text-center">
                                    <a href="{{ route('admin.paiements.pdf', $p) }}" target="_blank"
                                       class="w-7 h-7 inline-flex items-center justify-center border border-bimo-navy/10 rounded-[6px]
                                              text-bimo-navy/40 hover:text-bimo-gold hover:border-bimo-gold/30 transition-all duration-150">
                                        <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @endif
            </div>

            {{-- Description --}}
            @if($bien->description)
            <div class="bg-white rounded-[14px] border border-bimo-navy/10 overflow-hidden">
                <div class="flex items-center gap-3 px-5 py-4 border-b border-bimo-navy/[5%] bg-bimo-bg2">
                    <div class="w-8 h-8 rounded-[8px] bg-bimo-navy/5 flex items-center justify-center flex-shrink-0">
                        <svg class="w-4 h-4 text-bimo-navy/40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
                    </div>
                    <span class="font-display font-bold text-sm text-bimo-navy">Description</span>
                </div>
                <div class="px-5 py-5">
                    <p class="font-body text-sm text-bimo-navy/70 leading-relaxed">{{ $bien->description }}</p>
                </div>
            </div>
            @endif

        </div>{{-- fin colonne gauche --}}

        {{-- COLONNE DROITE --}}
        <div class="lg:sticky lg:top-6 space-y-4">

            {{-- Propriétaire --}}
            <div class="bg-bimo-navy rounded-[14px] overflow-hidden">
                <div class="px-5 py-4 border-b border-white/[7%]">
                    <div class="font-display font-bold text-sm text-white">Propriétaire</div>
                </div>
                <div class="px-5 py-4">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-10 h-10 rounded-full flex items-center justify-center flex-shrink-0
                                    font-display font-bold text-sm text-bimo-gold
                                    bg-bimo-gold/15 border border-bimo-gold/30">
                            {{ mb_strtoupper(mb_substr($bien->proprietaire?->name ?? 'P', 0, 2)) }}
                        </div>
                        <div>
                            <div class="font-body font-semibold text-sm text-white">{{ $bien->proprietaire?->name ?? '—' }}</div>
                            <div class="font-body text-xs text-white/35">{{ $bien->proprietaire?->email ?? '' }}</div>
                        </div>
                    </div>
                    @if($bien->proprietaire?->telephone)
                    <div class="flex items-center justify-between py-2 border-t border-white/[6%]">
                        <span class="font-body text-xs text-white/40">Téléphone</span>
                        <span class="font-body text-xs text-white/70">{{ $bien->proprietaire->telephone }}</span>
                    </div>
                    @endif
                    @if($bien->proprietaire)
                    <a href="{{ route('admin.users.show', $bien->proprietaire) }}"
                       class="flex items-center justify-center gap-2 mt-3 px-4 py-2.5
                              border border-white/10 rounded-[9px]
                              font-body text-xs text-bimo-gold hover:text-white hover:border-white/20
                              transition-all duration-150">
                        Voir le profil →
                    </a>
                    @endif
                </div>
            </div>

            {{-- Récapitulatif --}}
            <div class="bg-bimo-navy rounded-[14px] overflow-hidden">
                <div class="px-5 py-4 border-b border-white/[7%]">
                    <div class="font-display font-bold text-sm text-white">Récapitulatif</div>
                </div>
                <div class="px-5 py-2 divide-y divide-white/[6%]">
                    @php
                        $sideRows = [
                            ['Référence',   $bien->reference, 'font-display font-semibold text-xs'],
                            ['Loyer',        number_format($bien->loyer_mensuel, 0, ',', ' ') . ' F', 'text-bimo-gold font-semibold'],
                            ['Commission',   ($bien->taux_commission ?? 10) . ' %', ''],
                            ['Net proprio',  number_format($bien->loyer_mensuel * (1 - ($bien->taux_commission ?? 10) / 100 * 1.18), 0, ',', ' ') . ' F', 'text-white font-semibold'],
                            ['Statut',       $bien->statut_label, ''],
                            ['Surface',      $bien->surface_m2 ? $bien->surface_m2.' m²' : '—', ''],
                            ['Pièces',       $bien->nombre_pieces ?? '—', ''],
                            ['Meublé',       $bien->meuble ? 'Oui' : 'Non', ''],
                            ['Ajouté le',    $bien->created_at?->format('d/m/Y') ?? '—', ''],
                        ];
                    @endphp
                    @foreach($sideRows as [$lbl, $val, $valClass])
                    <div class="flex items-center justify-between py-2.5">
                        <span class="font-body text-xs text-white/40">{{ $lbl }}</span>
                        <span class="font-body text-xs text-white/70 {{ $valClass }}">{{ $val }}</span>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- Historique contrats --}}
            @if($bien->contrats->count() > 0)
            <div class="bg-bimo-navy rounded-[14px] overflow-hidden">
                <div class="px-5 py-4 border-b border-white/[7%]">
                    <div class="font-display font-bold text-sm text-white">Historique contrats</div>
                </div>
                <div class="px-5 py-2 divide-y divide-white/[6%]">
                    @foreach($bien->contrats->take(5) as $c)
                    <div class="py-3">
                        <div class="flex items-center justify-between mb-1">
                            <span class="font-body font-medium text-sm text-white/80">{{ $c->locataire?->name ?? '—' }}</span>
                            <span class="font-body text-[10px] font-semibold
                                         {{ $c->statut === 'actif' ? 'text-bimo-gold' : ($c->statut === 'resilié' ? 'text-bimo-red/70' : 'text-white/30') }}">
                                {{ \App\Models\Contrat::STATUTS[$c->statut] ?? $c->statut }}
                            </span>
                        </div>
                        <div class="font-body text-xs text-white/30 mb-1">
                            {{ $c->date_debut?->format('d/m/Y') }} → {{ $c->date_fin?->format('d/m/Y') ?? 'En cours' }}
                        </div>
                        <a href="{{ route('admin.contrats.show', $c) }}"
                           class="font-body text-xs text-bimo-gold hover:text-white transition-colors duration-150">
                            Voir →
                        </a>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

        </div>

    </div>

</div>

@push('scripts')
<script>
function changerPhoto(url, thumb) {
    document.getElementById('photo-principale').src = url;
    document.querySelectorAll('[onclick*="changerPhoto"]').forEach(t => {
        t.className = t.className.replace('border-bimo-gold', 'border-transparent');
    });
    thumb.className = thumb.className.replace('border-transparent', 'border-bimo-gold').replace('hover:border-bimo-gold/50', '');
}
</script>
@endpush

@endsection
