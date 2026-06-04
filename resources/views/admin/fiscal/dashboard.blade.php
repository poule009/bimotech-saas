@extends('layouts.app')
@section('header', 'Fiscalité')

@section('content')
<div class="space-y-4 md:space-y-6">

    {{-- En-tête --}}
    <div class="flex items-start justify-between gap-4">
        <div>
            <h1 class="font-display font-extrabold text-xl md:text-2xl text-bimo-navy tracking-tight leading-tight">Fiscalité</h1>
            <p class="font-body text-sm text-bimo-navy/50 mt-1">Année {{ $annee }} — Vue d'ensemble</p>
        </div>
        <div class="flex items-center gap-2 flex-wrap">
            <a href="{{ route('admin.fiscal.simulation') }}"
               class="inline-flex items-center gap-2 bg-bimo-navy hover:bg-bimo-navy-dk text-white font-display font-bold text-sm px-4 py-2.5 rounded-[10px] transition-colors duration-150">
                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="16"/><line x1="8" y1="12" x2="16" y2="12"/></svg>
                <span class="hidden sm:inline">Simulation</span>
            </a>
            <a href="{{ route('admin.bilans-fiscaux.index') }}"
               class="inline-flex items-center gap-2 bg-[var(--ac)] text-white font-display font-bold text-sm px-4 py-2.5 rounded-[10px] hover:opacity-90 transition-opacity duration-150">
                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                <span class="hidden sm:inline">Bilans</span>
            </a>
        </div>
    </div>

    {{-- Alertes --}}
    @if($declarationsEnRetard > 0 || $nbBilansManquants > 0 || $contratsExpirant > 0)
    <div class="space-y-2">
        @if($declarationsEnRetard > 0)
        <div class="flex items-center gap-3 bg-bimo-red/[5%] border border-bimo-red/20 rounded-[12px] px-4 py-3">
            <svg class="w-4 h-4 text-bimo-red flex-shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
            <p class="font-body text-sm text-bimo-red"><span class="font-semibold">{{ $declarationsEnRetard }} déclaration(s) TVA en retard</span> — à déposer urgemment.</p>
            <a href="{{ route('admin.tva-agence.index') }}" class="ml-auto font-body text-xs text-bimo-red underline">Voir</a>
        </div>
        @endif
        @if($nbBilansManquants > 0)
        <div class="flex items-center gap-3 bg-bimo-gold/[5%] border border-bimo-gold/25 rounded-[12px] px-4 py-3">
            <svg class="w-4 h-4 text-bimo-gold flex-shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
            <p class="font-body text-sm text-bimo-navy"><span class="font-semibold">{{ $nbBilansManquants }} bilan(s) non calculé(s)</span> pour {{ $annee }}.</p>
            <a href="{{ route('admin.bilans-fiscaux.index') }}" class="ml-auto font-body text-xs text-bimo-navy/60 underline">Calculer</a>
        </div>
        @endif
        @if($contratsExpirant > 0)
        <div class="flex items-center gap-3 bg-bimo-navy/[3%] border border-bimo-navy/10 rounded-[12px] px-4 py-3">
            <svg class="w-4 h-4 text-bimo-navy/50 flex-shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
            <p class="font-body text-sm text-bimo-navy"><span class="font-semibold">{{ $contratsExpirant }} contrat(s)</span> expirent dans 30 jours.</p>
        </div>
        @endif
    </div>
    @endif

    {{-- KPIs --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
        <div class="bg-white rounded-[14px] border border-bimo-navy/10 p-4">
            <p class="font-body font-medium text-[9.5px] uppercase tracking-widest text-bimo-navy/50 mb-2">IRPP total estimé</p>
            <p class="font-display font-extrabold text-xl text-bimo-navy">{{ number_format($kpiIrpp, 0, ',', ' ') }}</p>
            <p class="font-body font-light text-[10.5px] text-bimo-navy/40 mt-1">FCFA — {{ $annee }}</p>
        </div>
        <div class="bg-white rounded-[14px] border border-bimo-navy/10 p-4">
            <p class="font-body font-medium text-[9.5px] uppercase tracking-widest text-bimo-navy/50 mb-2">BRS retenu total</p>
            <p class="font-display font-extrabold text-xl text-bimo-navy">{{ number_format($kpiBrs, 0, ',', ' ') }}</p>
            <p class="font-body font-light text-[10.5px] text-bimo-navy/40 mt-1">FCFA à verser DGI</p>
        </div>
        <div class="bg-bimo-gold/[8%] rounded-[14px] border border-bimo-gold/25 p-4">
            <p class="font-body font-medium text-[9.5px] uppercase tracking-widest text-bimo-gold/70 mb-2">Économie CGF potentielle</p>
            <p class="font-display font-extrabold text-xl text-bimo-gold">{{ number_format($kpiEconomie, 0, ',', ' ') }}</p>
            <p class="font-body font-light text-[10.5px] text-bimo-gold/60 mt-1">FCFA pour vos clients</p>
        </div>
        <div class="bg-white rounded-[14px] border border-bimo-navy/10 p-4">
            <p class="font-body font-medium text-[9.5px] uppercase tracking-widest text-bimo-navy/50 mb-2">Bilans calculés</p>
            <p class="font-display font-extrabold text-xl text-bimo-navy">{{ $nbBilansCalcules }}<span class="text-bimo-navy/30 text-sm font-body font-normal">/{{ $nbProprietaires }}</span></p>
            <p class="font-body font-light text-[10.5px] text-bimo-navy/40 mt-1">Propriétaires {{ $annee }}</p>
        </div>
    </div>

    {{-- TVA mois courant --}}
    @if($tvaMois)
    <div class="bg-white rounded-[14px] border border-bimo-navy/10 p-5 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <p class="font-body font-medium text-[10px] uppercase tracking-widest text-bimo-navy/40 mb-1">TVA — {{ $tvaMois->periode_label }}</p>
            <p class="font-display font-bold text-lg text-bimo-navy">{{ number_format($tvaMois->tva_nette_due, 0, ',', ' ') }} <span class="font-body font-normal text-sm text-bimo-navy/40">FCFA due</span></p>
            @if($tvaMois->est_en_retard)
            <span class="inline-flex items-center mt-1 px-2 py-0.5 rounded-full text-[11px] font-body font-medium bg-bimo-red/10 border border-bimo-red/20 text-bimo-red">En retard</span>
            @else
            <span class="inline-flex items-center mt-1 px-2 py-0.5 rounded-full text-[11px] font-body font-medium bg-bimo-navy/10 border border-bimo-navy/15 text-bimo-navy/70">{{ ucfirst($tvaMois->statut) }}</span>
            @endif
        </div>
        <a href="{{ route('admin.tva-agence.show', [now()->year, now()->month]) }}"
           class="inline-flex items-center gap-2 px-4 py-2 border border-bimo-navy/15 rounded-[9px] font-body text-sm text-bimo-navy/60 hover:border-bimo-navy/30 hover:text-bimo-navy transition-all duration-150 flex-shrink-0">
            Voir la déclaration
        </a>
    </div>
    @endif

    {{-- Raccourcis modules --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
        <a href="{{ route('admin.bilans-fiscaux.index') }}"
           class="bg-white rounded-[14px] border border-bimo-navy/10 p-4 flex flex-col gap-2 hover:border-bimo-navy/25 transition-colors duration-150">
            <svg class="w-5 h-5 text-bimo-navy/40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="9" y1="15" x2="15" y2="15"/><line x1="12" y1="12" x2="12" y2="18"/></svg>
            <p class="font-display font-bold text-sm text-bimo-navy">Bilans IRPP</p>
            <p class="font-body text-[10.5px] text-bimo-navy/40">Par propriétaire</p>
        </a>
        <a href="{{ route('admin.tva-agence.index') }}"
           class="bg-white rounded-[14px] border border-bimo-navy/10 p-4 flex flex-col gap-2 hover:border-bimo-navy/25 transition-colors duration-150">
            <svg class="w-5 h-5 text-bimo-navy/40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="1" y="4" width="22" height="16" rx="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>
            <p class="font-display font-bold text-sm text-bimo-navy">TVA mensuelle</p>
            <p class="font-body text-[10.5px] text-bimo-navy/40">Déclarations DGI</p>
        </a>
        <a href="{{ route('admin.etats-trimestriels.index') }}"
           class="bg-white rounded-[14px] border border-bimo-navy/10 p-4 flex flex-col gap-2 hover:border-bimo-navy/25 transition-colors duration-150">
            <svg class="w-5 h-5 text-bimo-navy/40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
            <p class="font-display font-bold text-sm text-bimo-navy">États BRS</p>
            <p class="font-body text-[10.5px] text-bimo-navy/40">Trimestriels</p>
        </a>
        <a href="{{ route('admin.echeances-fiscales.index') }}"
           class="bg-white rounded-[14px] border border-bimo-navy/10 p-4 flex flex-col gap-2 hover:border-bimo-navy/25 transition-colors duration-150">
            <svg class="w-5 h-5 text-bimo-navy/40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
            <p class="font-display font-bold text-sm text-bimo-navy">Échéances</p>
            <p class="font-body text-[10.5px] text-bimo-navy/40">Calendrier DGI</p>
        </a>
    </div>

    {{-- Tableau propriétaires --}}
    <div class="bg-white rounded-[14px] border border-bimo-navy/10 overflow-hidden">
        <div class="px-5 py-4 border-b border-bimo-navy/[5%] bg-bimo-bg2 flex items-center justify-between">
            <span class="font-display font-bold text-sm text-bimo-navy">Situation fiscale {{ $annee }}</span>
            <span class="font-body text-xs text-bimo-navy/40">{{ $nbBilansCalcules }}/{{ $nbProprietaires }} calculés</span>
        </div>

        {{-- Mobile --}}
        <div class="md:hidden divide-y divide-bimo-navy/[5%]">
            @forelse($proprietaires as $prop)
            <div class="px-5 py-4">
                <div class="flex items-center justify-between gap-2 mb-2">
                    <p class="font-body font-medium text-sm text-bimo-navy">{{ $prop->name }}</p>
                    @if($prop->bilan_calcule)
                        @if($prop->regime === 'cgf')
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-body font-medium bg-bimo-gold/10 border border-bimo-gold/20 text-bimo-gold">CGF</span>
                        @else
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-body font-medium bg-bimo-navy/10 border border-bimo-navy/15 text-bimo-navy/70">IRPP</span>
                        @endif
                    @else
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-body font-medium bg-bimo-navy/[5%] border border-bimo-navy/10 text-bimo-navy/30">Non calculé</span>
                    @endif
                </div>
                <div class="flex items-center gap-2">
                    <a href="{{ route('admin.bilans-fiscaux.show', $prop) }}" class="font-body text-xs text-bimo-navy/50 underline">Voir le bilan</a>
                </div>
            </div>
            @empty
            <div class="px-5 py-10 text-center font-body text-sm text-bimo-navy/30">Aucun propriétaire.</div>
            @endforelse
        </div>

        {{-- Desktop --}}
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-bimo-navy/[5%] bg-bimo-bg">
                        <th class="px-5 py-3 text-left font-body font-medium text-[10px] uppercase tracking-widest text-bimo-navy/40">Propriétaire</th>
                        <th class="px-5 py-3 text-right font-body font-medium text-[10px] uppercase tracking-widest text-bimo-navy/40">Revenus bruts</th>
                        <th class="px-5 py-3 text-right font-body font-medium text-[10px] uppercase tracking-widest text-bimo-navy/40">IRPP estimé</th>
                        <th class="px-5 py-3 text-right font-body font-medium text-[10px] uppercase tracking-widest text-bimo-navy/40">CGF</th>
                        <th class="px-5 py-3 text-center font-body font-medium text-[10px] uppercase tracking-widest text-bimo-navy/40">Régime conseillé</th>
                        <th class="px-5 py-3 text-center font-body font-medium text-[10px] uppercase tracking-widest text-bimo-navy/40">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-bimo-navy/[5%]">
                    @forelse($proprietaires as $prop)
                    <tr class="hover:bg-bimo-bg transition-colors duration-100">
                        <td class="px-5 py-3.5 font-body font-medium text-sm text-bimo-navy">{{ $prop->name }}</td>
                        <td class="px-5 py-3.5 text-right font-display font-semibold text-sm text-bimo-navy/70">{{ $prop->bilan_calcule ? number_format($prop->revenus, 0, ',', ' ').' F' : '—' }}</td>
                        <td class="px-5 py-3.5 text-right font-display font-semibold text-sm text-bimo-navy">{{ $prop->bilan_calcule ? number_format($prop->irpp, 0, ',', ' ').' F' : '—' }}</td>
                        <td class="px-5 py-3.5 text-right font-display font-semibold text-sm text-bimo-gold">{{ $prop->bilan_calcule && $prop->cgf > 0 ? number_format($prop->cgf, 0, ',', ' ').' F' : '—' }}</td>
                        <td class="px-5 py-3.5 text-center">
                            @if($prop->bilan_calcule)
                                @if($prop->regime === 'cgf')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-body font-medium bg-bimo-gold/10 border border-bimo-gold/20 text-bimo-gold">CGF recommandé</span>
                                @elseif($prop->regime === 'hors_cgf')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-body font-medium bg-bimo-navy/10 border border-bimo-navy/15 text-bimo-navy/70">IRPP (hors CGF)</span>
                                @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-body font-medium bg-bimo-navy/10 border border-bimo-navy/15 text-bimo-navy/70">IRPP</span>
                                @endif
                            @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-body font-medium bg-bimo-navy/[5%] border border-bimo-navy/10 text-bimo-navy/30">Non calculé</span>
                            @endif
                        </td>
                        <td class="px-5 py-3.5 text-center">
                            <div class="flex items-center justify-center gap-2">
                                <a href="{{ route('admin.bilans-fiscaux.show', $prop) }}" class="inline-flex items-center px-3 py-1 border border-bimo-navy/15 rounded-[7px] font-body text-xs text-bimo-navy/60 hover:border-bimo-navy/30 hover:text-bimo-navy transition-all duration-150">Bilan</a>
                                @if($prop->bilan_calcule)
                                <a href="{{ route('admin.bilans-fiscaux.fiche-transparente', $prop) }}" class="inline-flex items-center px-3 py-1 border border-bimo-gold/25 rounded-[7px] font-body text-xs text-bimo-gold hover:bg-bimo-gold/10 transition-all duration-150">Fiche PDF</a>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="px-5 py-10 text-center font-body text-sm text-bimo-navy/30">Aucun propriétaire.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
