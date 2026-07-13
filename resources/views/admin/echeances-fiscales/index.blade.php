@extends('layouts.app')

@section('title', 'Fiscalité')
@section('page-title', 'Fiscalité')
@section('page-subtitle')<span class="text-muted">Toutes les échéances fiscales de vos propriétaires, biens et de l'agence</span>@endsection

@php
    // Catégorie de filtre (= data-type de la ligne, doit matcher une chip).
    $categorie = fn (string $t) => match (true) {
        str_starts_with($t, 'brs')      => 'brs',
        str_starts_with($t, 'cgf')      => 'cgf',
        $t === 'cfpb_teom'              => 'cfpb',
        $t === 'droit_enregistrement'  => 'denreg',
        str_ends_with($t, '_agence')   => 'agence',
        default                         => $t, // tva, irpp
    };

    // Familles d'icônes (§6) — 4 SVG max, cohérence avant tout.
    $icones = [
        'declaration' => '<path d="M4 20h16M5 20V10l7-5 7 5v10" stroke-width="1.6" stroke-linejoin="round"/><path d="M9 20v-6h6v6" stroke-width="1.6"/>',
        'recu'        => '<path d="M4 4h16v4H4V4Z" stroke-width="1.6" stroke-linejoin="round"/><path d="M5 8v11a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V8" stroke-width="1.6"/><path d="M9 13h6M9 17h6" stroke-width="1.6" stroke-linecap="round"/>',
        'document'    => '<path d="M6 3h9l5 5v13a1 1 0 0 1-1 1H6a1 1 0 0 1-1-1V4a1 1 0 0 1 1-1Z" stroke-width="1.6" stroke-linejoin="round"/><path d="M9 12h6M9 16h6" stroke-width="1.6" stroke-linecap="round"/>',
        'immeubles'   => '<path d="M4 21V6l6-3v18M14 21V10l6-2v13" stroke-width="1.6" stroke-linejoin="round"/><path d="M4 21h16" stroke-width="1.6" stroke-linecap="round"/>',
    ];
    $familleDe = fn (string $t) => match (true) {
        str_ends_with($t, '_agence')  => 'immeubles',
        $t === 'cfpb_teom'            => 'recu',
        $t === 'droit_enregistrement' => 'document',
        default                        => 'declaration',
    };

    // Badge de fiabilité.
    $badgeDe = fn (?string $s) => match ($s) {
        'estimation_structurelle', 'perimetre_partiel', 'estimation' => ['Estimation', 'bg-gold-soft/50 text-gold'],
        'confirme' => ['Fiable', 'bg-green/10 text-green'],
        default    => null,
    };

    // Texte relatif ("dans X jours" / "il y a X jours").
    $relatif = function (?string $dateStr) use ($today) {
        if (! $dateStr) return null;
        $d = \Illuminate\Support\Carbon::parse($dateStr)->startOfDay();
        $j = (int) $today->copy()->startOfDay()->diffInDays($d, false);
        if ($j < 0)  return 'il y a ' . abs($j) . ' jour' . (abs($j) > 1 ? 's' : '');
        if ($j === 0) return "aujourd'hui";
        return 'dans ' . $j . ' jour' . ($j > 1 ? 's' : '');
    };

    $fmt = fn ($n) => number_format((float) $n, 0, ',', ' ');

    $meta = [
        'late'  => ['titre' => 'En retard',              'pill' => 'bg-error', 'bord' => 'border-l-error'],
        'soon'  => ['titre' => 'Dans les 7 jours',       'pill' => 'bg-gold',  'bord' => 'border-l-gold'],
        'month' => ['titre' => 'Dans les 30 jours',      'pill' => 'bg-green', 'bord' => 'border-l-green'],
        'later' => ['titre' => 'Plus tard cette année',  'pill' => 'bg-muted', 'bord' => 'border-l-muted'],
    ];

    $chips = [
        'all' => 'Toutes', 'tva' => 'TVA', 'brs' => 'BRS', 'irpp' => 'IRPP',
        'cgf' => 'CGF', 'cfpb' => 'CFPB · TEOM', 'denreg' => "Droits d'enregistrement", 'agence' => 'Agence (IS · CEL)',
    ];

    $nbEntites = collect($echeances)->reject(fn ($e) => str_ends_with($e['type'], '_agence'))->count();
@endphp

@section('content')
<div class="max-w-[980px] mx-auto">

    @if($nbEntites === 0)
        {{-- État vide global (agence neuve / rien à venir) --}}
        <div class="bg-white border border-dashed border-line rounded-2xl text-center py-20 px-8">
            <div class="w-14 h-14 rounded-full bg-green/10 flex items-center justify-center mx-auto mb-4">
                <svg class="w-6 h-6 text-green" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><path d="M4 4h16v4H4V4Z" stroke-linejoin="round"/><path d="M5 8v11a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V8"/><path d="M9 13h6M9 17h4" stroke-linecap="round"/></svg>
            </div>
            <h3 class="font-display font-semibold text-[17px] text-teal mb-1.5">Rien à signaler pour l'instant</h3>
            <p class="text-[13px] text-muted max-w-[380px] mx-auto leading-relaxed">Dès qu'un propriétaire aura un bien loué avec un contrat actif, ses échéances fiscales apparaîtront ici automatiquement — rien à configurer de votre côté.</p>
        </div>
    @else
        {{-- ── Tuiles de résumé ── --}}
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

        <div x-data="fiscaliteCalendrier">
            {{-- ── Filtres (client-side) ── --}}
            <div class="flex flex-wrap gap-2 mb-6">
                @foreach($chips as $key => $label)
                    <button type="button" data-filter="{{ $key }}" x-on:click="apply"
                            class="fisc-chip text-[12px] font-semibold px-3.5 py-1.5 rounded-full border border-line bg-white text-muted transition-colors {{ $key === 'all' ? 'fisc-chip-active' : '' }}">
                        {{ $label }}
                    </button>
                @endforeach
            </div>

            {{-- ── Groupes par urgence ── --}}
            @foreach(['late', 'soon', 'month', 'later'] as $g)
                @if(count($groupes[$g]) > 0)
                    <div data-group class="mb-7">
                        <div class="flex items-center gap-2.5 text-[13px] font-bold uppercase tracking-wide text-muted mb-2.5">
                            <span class="w-2 h-2 rounded-full {{ $meta[$g]['pill'] }}"></span>{{ $meta[$g]['titre'] }}
                        </div>

                        @foreach($groupes[$g] as $e)
                            @php
                                [$bTxt, $bCls] = $badgeDe($e['statut_calcul']) ?? [null, null];
                                $lien = $e['proprietaire_id'] ? route('admin.users.show', $e['proprietaire_id']) : null;
                            @endphp
                            <div data-type="{{ $categorie($e['type']) }}"
                                 class="flex items-center justify-between gap-4 bg-white border border-line border-l-4 {{ $meta[$g]['bord'] }} rounded-[10px] px-4 py-3.5 mb-2">
                                <div class="flex items-center gap-3 min-w-0">
                                    <span class="w-[38px] h-[38px] rounded-[9px] bg-paper flex items-center justify-center shrink-0">
                                        <svg class="w-[18px] h-[18px] text-teal" viewBox="0 0 24 24" fill="none" stroke="currentColor">{!! $icones[$familleDe($e['type'])] !!}</svg>
                                    </span>
                                    <div class="min-w-0">
                                        <div class="text-[14px] font-semibold text-ink truncate">{{ $e['libelle'] }}</div>
                                        <div class="text-[12.5px] text-muted truncate">{{ $e['proprietaire'] ? 'Propriétaire : ' . $e['proprietaire'] : (auth()->user()?->agency?->name ?? 'Agence') . ' — à traiter avec votre comptable' }}</div>
                                    </div>
                                </div>
                                <div class="flex items-center gap-4 shrink-0">
                                    <div class="text-right min-w-[104px]">
                                        @if($e['montant'] === null)
                                            <div class="text-[12px] font-semibold text-muted italic">Voir comptable</div>
                                        @else
                                            <div class="font-display font-semibold text-[15px] text-ink">{{ $fmt($e['montant']) }} FCFA</div>
                                            @if($bTxt)
                                                <span class="inline-flex items-center gap-1 text-[10.5px] font-semibold px-2 py-0.5 rounded-full mt-0.5 {{ $bCls }}"><span class="w-1 h-1 rounded-full bg-current"></span>{{ $bTxt }}</span>
                                            @endif
                                        @endif
                                    </div>
                                    <div class="text-right text-[11.5px] text-muted leading-snug hidden sm:block min-w-[86px]">
                                        <strong class="block text-[13px] text-ink font-semibold">{{ $e['date_limite'] ? \Illuminate\Support\Carbon::parse($e['date_limite'])->locale('fr')->isoFormat('D MMM Y') : 'À confirmer' }}</strong>
                                        {{ $e['date_limite'] ? $relatif($e['date_limite']) : "clôture d'exercice" }}
                                    </div>
                                    @if($lien)
                                        <a href="{{ $lien }}" class="text-[12px] font-semibold text-teal whitespace-nowrap hover:underline">Voir →</a>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            @endforeach

            {{-- État vide de filtre --}}
            <div data-no-results class="hidden text-center bg-white border border-dashed border-line rounded-2xl py-12 px-8">
                <div class="w-[52px] h-[52px] rounded-full bg-green/10 flex items-center justify-center mx-auto mb-4">
                    <svg class="w-5 h-5 text-green" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><circle cx="11" cy="11" r="7"/><path d="M20 20l-3.5-3.5" stroke-linecap="round"/></svg>
                </div>
                <h3 class="font-display font-semibold text-[16px] text-teal mb-1">Aucune échéance de ce type</h3>
                <p class="text-[13px] text-muted">Rien à afficher pour ce filtre en ce moment. Essayez « Toutes » pour tout revoir.</p>
            </div>
        </div>

        {{-- Légende --}}
        <div class="flex items-start gap-2.5 text-[12px] text-muted bg-white border border-line rounded-xl px-4 py-3 mt-3 leading-relaxed">
            <svg class="w-3.5 h-3.5 mt-0.5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><circle cx="12" cy="12" r="9"/><path d="M12 8v5M12 16h.01" stroke-linecap="round"/></svg>
            <span><strong class="text-green">Fiable</strong> : calculé directement depuis vos contrats. <strong class="text-gold">Estimation</strong> : basé sur le loyer, à recouper avec le montant réel de l'administration. Les lignes agence n'affichent jamais de montant — voir votre comptable.</span>
        </div>
    @endif
</div>
@endsection
