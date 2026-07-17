@extends('layouts.app')

@section('title', 'Fiscalité')
@section('page-title', 'Fiscalité')
@section('page-subtitle')<span class="text-muted">La situation fiscale de chaque propriétaire, en un coup d'œil.</span>@endsection

@php
    $fmt = fn ($n) => number_format((float) $n, 0, ',', ' ');

    // Initiales d'après le nom (2 lettres max) pour l'avatar.
    $initiales = function (string $nom): string {
        $mots = preg_split('/\s+/', trim($nom));
        $a = mb_substr($mots[0] ?? '', 0, 1);
        $b = count($mots) > 1 ? mb_substr(end($mots), 0, 1) : '';
        return mb_strtoupper($a . $b) ?: '?';
    };
@endphp

@section('content')
<div class="max-w-[980px] mx-auto">

    {{-- ── Tuiles de résumé (global) ── --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="rounded-xl p-5 {{ $resume['nb_retard'] > 0 ? 'bg-error text-paper' : 'bg-white border border-line' }}">
            <div class="text-[11px] uppercase tracking-wide font-semibold {{ $resume['nb_retard'] > 0 ? 'text-paper/70' : 'text-muted' }} mb-1.5">En retard</div>
            <div class="font-display font-semibold text-[24px] {{ $resume['nb_retard'] > 0 ? 'text-paper' : 'text-teal' }}">
                {{ $resume['nb_retard'] }} <span class="text-[12px] font-medium {{ $resume['nb_retard'] > 0 ? 'text-paper/75' : 'text-muted' }}">échéance{{ $resume['nb_retard'] > 1 ? 's' : '' }}</span>
            </div>
            @if($resume['nb_retard'] > 0)
                <div class="text-[11px] text-paper/70 mt-1">{{ $fmt($resume['somme_retard']) }} FCFA à régulariser</div>
            @endif
        </div>
        <div class="rounded-xl p-5 bg-white border border-line">
            <div class="text-[11px] uppercase tracking-wide font-semibold text-muted mb-1.5">Dans les 7 jours</div>
            <div class="font-display font-semibold text-[22px] text-teal">{{ $fmt($resume['somme_7j']) }}<span class="text-[12px] font-medium text-muted ml-1">FCFA</span></div>
        </div>
        <div class="rounded-xl p-5 bg-white border border-line">
            <div class="text-[11px] uppercase tracking-wide font-semibold text-muted mb-1.5">Dans les 30 jours</div>
            <div class="font-display font-semibold text-[22px] text-teal">{{ $fmt($resume['somme_30j']) }}<span class="text-[12px] font-medium text-muted ml-1">FCFA</span></div>
        </div>
        <div class="rounded-xl p-5 bg-white border border-line">
            <div class="text-[11px] uppercase tracking-wide font-semibold text-muted mb-1.5">Total année {{ $today->year }} (estimé)</div>
            <div class="font-display font-semibold text-[22px] text-teal">{{ $fmt($resume['total_annee']) }}<span class="text-[12px] font-medium text-muted ml-1">FCFA</span></div>
        </div>
    </div>

    @if($proprietaires->isEmpty())
        {{-- État vide global (agence neuve / aucun propriétaire avec bien) --}}
        <div class="bg-white border border-dashed border-line rounded-2xl text-center py-20 px-8">
            <div class="w-14 h-14 rounded-full bg-green/10 flex items-center justify-center mx-auto mb-4">
                <svg class="w-6 h-6 text-green" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><path d="M4 4h16v4H4V4Z" stroke-linejoin="round"/><path d="M5 8v11a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V8"/><path d="M9 13h6M9 17h4" stroke-linecap="round"/></svg>
            </div>
            <h3 class="font-display font-semibold text-[17px] text-teal mb-1.5">Aucun propriétaire à afficher</h3>
            <p class="text-[13px] text-muted max-w-[380px] mx-auto leading-relaxed">Dès qu'un propriétaire aura un bien enregistré, sa situation fiscale apparaîtra ici automatiquement — rien à configurer de votre côté.</p>
        </div>
    @else
        <div x-data="fiscaliteProprietaires">
            {{-- ── Recherche ── --}}
            <div class="flex items-center gap-2 bg-white border border-line rounded-[9px] px-3.5 py-2.5 mb-4 max-w-[340px]">
                <svg class="w-[15px] h-[15px] text-muted shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3" stroke-linecap="round"/></svg>
                <input type="text" x-model="q" x-on:input="filter" placeholder="Rechercher un propriétaire…"
                       class="border-none outline-none bg-transparent text-[13.5px] w-full placeholder:text-muted">
            </div>

            {{-- ── Liste de cartes propriétaire ── --}}
            <div class="flex flex-col gap-2.5">
                @foreach($proprietaires as $p)
                    @php
                        $chip = match ($p['statut']) {
                            'late'     => ['En retard',                          'bg-error/10 text-error',  'bg-error'],
                            'upcoming' => ['Échéance J-' . $p['jours'],           'bg-gold-soft/40 text-gold', 'bg-gold'],
                            default    => ['À jour',                             'bg-green/10 text-green',  'bg-green'],
                        };
                    @endphp
                    <a href="{{ route('admin.echeances-fiscales.proprietaire', $p['id']) }}"
                       data-owner data-name="{{ mb_strtolower($p['name']) }}"
                       class="group bg-white border border-line rounded-xl px-5 py-4 flex items-center gap-4 transition-colors hover:border-gold-soft hover:shadow-sm">
                        <div class="w-11 h-11 rounded-[10px] bg-teal text-gold-soft flex items-center justify-center font-display font-semibold text-[16px] shrink-0">{{ $initiales($p['name']) }}</div>
                        <div class="flex-1 min-w-0">
                            <div class="font-semibold text-[15px] text-ink truncate">{{ $p['name'] }}</div>
                            <div class="text-[12px] text-muted mt-0.5">
                                {{ $p['nb_biens'] }} bien{{ $p['nb_biens'] > 1 ? 's' : '' }}
                                @if($p['nb_retard'] > 0)
                                    — <span class="text-error font-medium">{{ $p['nb_retard'] }} en retard</span>
                                @elseif($p['nb_a_venir'] > 0)
                                    — {{ $p['nb_a_venir'] }} échéance{{ $p['nb_a_venir'] > 1 ? 's' : '' }} à venir
                                @else
                                    — à jour
                                @endif
                            </div>
                        </div>
                        <span class="inline-flex items-center gap-1.5 text-[11px] font-semibold px-2.5 py-1 rounded-full whitespace-nowrap {{ $chip[1] }}">
                            <span class="w-1.5 h-1.5 rounded-full {{ $chip[2] }}"></span>{{ $chip[0] }}
                        </span>
                        <div class="text-right min-w-[110px] hidden sm:block">
                            <div class="font-mono font-semibold text-[14px] text-ink">{{ $fmt($p['montant_30j']) }} FCFA</div>
                            <div class="text-[11px] text-muted mt-0.5">{{ $p['montant_30j'] > 0 ? 'dû sous 30 jours' : 'rien à venir' }}</div>
                        </div>
                        <svg class="w-4 h-4 text-muted shrink-0 group-hover:text-teal transition-colors" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 6l6 6-6 6" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </a>
                @endforeach
            </div>

            {{-- État vide de recherche --}}
            <div data-empty-search class="hidden text-center bg-white border border-dashed border-line rounded-2xl py-12 px-8 mt-2.5">
                <div class="w-[52px] h-[52px] rounded-full bg-green/10 flex items-center justify-center mx-auto mb-4">
                    <svg class="w-5 h-5 text-green" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><circle cx="11" cy="11" r="7"/><path d="M20 20l-3.5-3.5" stroke-linecap="round"/></svg>
                </div>
                <h3 class="font-display font-semibold text-[16px] text-teal mb-1">Aucun propriétaire trouvé</h3>
                <p class="text-[13px] text-muted">Aucun nom ne correspond à votre recherche.</p>
            </div>
        </div>

        {{-- Légende --}}
        <div class="flex items-start gap-2.5 text-[12px] text-muted bg-white border border-line rounded-xl px-4 py-3 mt-3 leading-relaxed">
            <svg class="w-3.5 h-3.5 mt-0.5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><circle cx="12" cy="12" r="9"/><path d="M12 8v5M12 16h.01" stroke-linecap="round"/></svg>
            <span>Cliquez sur un propriétaire pour voir toutes ses taxes et le détail de chaque calcul. Le montant affiché correspond à ce qui est dû dans les 30 prochains jours.</span>
        </div>
    @endif
</div>
@endsection
