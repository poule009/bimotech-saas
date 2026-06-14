@extends('layouts.app')
@section('header', 'États trimestriels BRS')

@section('content')

<div class="space-y-4 md:space-y-5">

    {{-- En-tête --}}
    <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-3">
        <div>
            <h1 class="font-display font-extrabold text-xl md:text-2xl text-bimo-text tracking-tight">États trimestriels BRS</h1>
            <p class="font-body text-sm text-bimo-text/50 mt-1">Retenues à la source sur loyers — Art. 200 §5 CGI Sénégal — Année {{ $annee }}</p>
        </div>
        <div class="flex-shrink-0 bg-bimo-navy/[4%] border border-bimo-navy/10 rounded-[10px] px-4 py-3 text-right">
            <div class="font-body font-medium text-xs text-bimo-text/50">📋 À déposer au Centre des Services Fiscaux</div>
            <div class="font-body text-xs text-bimo-text/30 mt-0.5">15 Avr · 15 Juil · 15 Oct · 15 Jan N+1</div>
        </div>
    </div>

    {{-- Filtre --}}
    <form method="GET" class="flex items-center gap-3 bg-white rounded-[12px] border border-bimo-navy/10 px-5 py-3.5">
        <span class="font-body font-medium text-xs text-bimo-text/50 whitespace-nowrap">Année fiscale :</span>
        <select name="annee"
                class="px-3 py-2 border border-bimo-navy/15 rounded-[8px] font-body text-sm text-bimo-text bg-bimo-bg focus:outline-none focus:border-bimo-gold cursor-pointer transition-all duration-150">
            @foreach($anneesDisponibles as $a)
            <option value="{{ $a }}" {{ $annee == $a ? 'selected' : '' }}>{{ $a }}</option>
            @endforeach
        </select>
        <button type="submit"
                class="inline-flex items-center px-4 py-2 bg-bimo-navy text-white font-display font-bold text-sm rounded-[9px] hover:bg-bimo-navy-dk transition-colors duration-150">
            Afficher
        </button>
    </form>

    {{-- 4 trimestres --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        @foreach($trimestres as $t)
        @php
            $statut = $t['statut'];
            $borderColor = match($statut) { 'telecharge'=>'border-t-bimo-gold', 'en_cours'=>'border-t-bimo-navy', 'a_deposer','en_retard'=>'border-t-bimo-red', default=>'' };
        @endphp
        <div class="bg-white rounded-[14px] border border-bimo-navy/10 overflow-hidden flex flex-col" style="border-top: 3px solid {{ match($statut) { 'telecharge'=>'var(--ac)', 'en_cours'=>'#1B4F6B', 'a_deposer','en_retard'=>'#A60F1C', default=>'rgba(27,79,107,.1)' } }}">
            <div class="flex items-center justify-between px-5 py-4 border-b border-bimo-navy/[5%] bg-bimo-bg2">
                <div>
                    <div class="font-display font-bold text-base text-bimo-text">{{ $t['label'] }}</div>
                    <div class="font-body text-xs text-bimo-text/40 mt-0.5">{{ $t['mois_label'] }}</div>
                </div>
                @if($statut === 'avenir')
                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-body font-semibold bg-bimo-navy/[5%] text-bimo-text/30">
                    <svg class="w-3 h-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                    À venir
                </span>
                @elseif($statut === 'en_cours')
                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-body font-semibold bg-bimo-navy/10 border border-bimo-navy/15 text-bimo-text/70">
                    <span class="w-1.5 h-1.5 rounded-full bg-bimo-navy/50"></span>En cours
                </span>
                @elseif($statut === 'a_deposer')
                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-body font-semibold bg-bimo-gold/10 border border-bimo-gold/20 text-bimo-gold">
                    <svg class="w-3 h-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                    À déposer
                </span>
                @elseif($statut === 'en_retard')
                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-body font-semibold bg-bimo-red/10 border border-bimo-red/20 text-bimo-red">
                    <svg class="w-3 h-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                    En retard
                </span>
                @else
                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-body font-semibold bg-bimo-navy/10 border border-bimo-navy/15 text-bimo-text/70">
                    <svg class="w-3 h-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
                    Téléchargé
                </span>
                @endif
            </div>

            <div class="px-5 py-4 flex-1">
                <div class="grid grid-cols-2 gap-3 mb-3">
                    <div class="bg-bimo-red/[5%] border border-bimo-red/15 rounded-[9px] p-3">
                        <div class="font-body font-medium text-[9.5px] uppercase tracking-widest text-bimo-red/70 mb-1">BRS total retenu</div>
                        <div class="font-display font-bold text-lg text-bimo-red leading-none">{{ $t['total_brs'] > 0 ? number_format($t['total_brs'],0,',','').' F' : '—' }}</div>
                    </div>
                    <div class="bg-bimo-bg border border-bimo-navy/[8%] rounded-[9px] p-3">
                        <div class="font-body font-medium text-[9.5px] uppercase tracking-widest text-bimo-text/40 mb-1">Bailleurs concernés</div>
                        <div class="font-display font-bold text-lg text-bimo-text leading-none">{{ $t['nb_bailleurs'] }}</div>
                    </div>
                </div>

                <div class="font-body text-xs {{ $statut === 'en_retard' ? 'text-bimo-red font-semibold' : 'text-bimo-text/40' }}">
                    📅 Date limite : {{ $t['date_limite']->translatedFormat('d F Y') }}
                    @if($statut === 'en_retard') — <strong>Dépassée</strong>
                    @elseif($statut === 'a_deposer') @php $j = (int) now()->diffInDays($t['date_limite'], false); @endphp — J-{{ $j }} restant(s)
                    @endif
                </div>

                @if($t['download'])
                <div class="font-body text-[10px] text-bimo-gold mt-1">
                    ✅ Dernier téléchargement : {{ $t['download']->downloaded_at->format('d/m/Y à H:i') }} ({{ strtoupper($t['download']->type) }})
                </div>
                @endif
            </div>

            <div class="px-5 py-3.5 border-t border-bimo-navy/[5%] flex items-center gap-2">
                @if($t['total_brs'] > 0 || $t['nb_bailleurs'] > 0)
                <a href="{{ route('admin.etats-trimestriels.show', [$annee, $t['numero']]) }}"
                   class="inline-flex items-center gap-1.5 px-3 py-1.5 border border-bimo-navy/15 rounded-[7px] font-body text-xs text-bimo-text/60 hover:border-bimo-gold hover:text-bimo-text transition-all duration-150">
                    <svg class="w-3 h-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                    Voir le détail
                </a>
                <a href="{{ route('admin.etats-trimestriels.pdf', [$annee, $t['numero']]) }}" target="_blank"
                   class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-bimo-navy text-white rounded-[7px] font-body text-xs hover:bg-bimo-navy-dk transition-colors duration-150">
                    <svg class="w-3 h-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                    PDF
                </a>
                <a href="{{ route('admin.etats-trimestriels.csv', [$annee, $t['numero']]) }}"
                   class="inline-flex items-center gap-1.5 px-3 py-1.5 border border-bimo-navy/15 rounded-[7px] font-body text-xs text-bimo-text/60 hover:border-bimo-gold hover:text-bimo-text transition-all duration-150">
                    <svg class="w-3 h-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                    CSV
                </a>
                @else
                <span class="font-body text-xs text-bimo-text/30 italic">Aucun paiement BRS sur ce trimestre</span>
                @endif
            </div>
        </div>
        @endforeach
    </div>

    <div class="flex items-start gap-2 bg-bimo-gold/[6%] border border-bimo-gold/20 rounded-[10px] px-4 py-3">
        <svg class="w-4 h-4 text-bimo-gold flex-shrink-0 mt-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
        <p class="font-body text-xs text-bimo-gold/80 leading-relaxed">
            <strong>Art. 200 §5 CGI Sénégal :</strong> Les agences immobilières sont tenues de remettre aux services fiscaux un état trimestriel des versements effectués aux bailleurs <strong>personnes physiques</strong>. Seuls les paiements avec BRS &gt; 0 sont inclus. Les bailleurs personnes morales (IS) sont exclus. <strong>Consultez votre Centre des Services Fiscaux (CSF) pour le dépôt.</strong>
        </p>
    </div>

</div>
@endsection
