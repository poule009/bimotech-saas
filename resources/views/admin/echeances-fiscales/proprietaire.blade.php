@extends('layouts.app')

@section('title', 'Fiscalité — ' . $proprietaire->name)
@section('page-title', 'Fiscalité')
@section('page-subtitle')<span class="text-muted">Toutes les taxes de ce propriétaire, avec le détail de chaque calcul.</span>@endsection

@php
    $fmt = fn ($n) => number_format((float) $n, 0, ',', ' ');

    $initiales = function (string $nom): string {
        $mots = preg_split('/\s+/', trim($nom));
        $a = mb_substr($mots[0] ?? '', 0, 1);
        $b = count($mots) > 1 ? mb_substr(end($mots), 0, 1) : '';
        return mb_strtoupper($a . $b) ?: '?';
    };

    // Code court affiché dans la pastille de la carte.
    $codeTaxe = fn (string $t) => match (true) {
        $t === 'tva'                          => 'TVA',
        str_starts_with($t, 'brs')            => 'BRS',
        $t === 'irpp'                         => 'IRPP',
        str_starts_with($t, 'cgf')            => 'CGF',
        $t === 'cfpb_teom'                    => 'CFPB',
        $t === 'droit_enregistrement'         => 'DE',
        default                               => 'FISC',
    };

    // Badge fiabilité par taxe.
    $badgeDe = fn (?string $s) => match ($s) {
        'confirme' => ['Fiable',     'bg-green/10 text-green'],
        null       => ['Fiable',     'bg-green/10 text-green'],
        default    => ['Estimation', 'bg-gold-soft/50 text-gold'],
    };

    $relatif = function (?string $dateStr) use ($today) {
        if (! $dateStr) return null;
        $d = \Illuminate\Support\Carbon::parse($dateStr)->startOfDay();
        $j = (int) $today->copy()->startOfDay()->diffInDays($d, false);
        if ($j < 0)  return 'en retard de ' . abs($j) . ' jour' . (abs($j) > 1 ? 's' : '');
        if ($j === 0) return "aujourd'hui";
        return 'dans ' . $j . ' jour' . ($j > 1 ? 's' : '');
    };
@endphp

@section('content')
<div class="max-w-[860px] mx-auto">

    {{-- Fil d'ariane --}}
    <div class="text-[12.5px] text-muted mb-4">
        <a href="{{ route('admin.echeances-fiscales.index') }}" class="text-teal font-semibold border-b border-gold hover:opacity-80">Fiscalité</a>
        <span class="mx-1.5">/</span>{{ $proprietaire->name }}
    </div>

    {{-- En-tête propriétaire --}}
    <div class="flex items-center gap-4 bg-white border border-line rounded-2xl px-6 py-5 mb-5">
        <div class="w-[52px] h-[52px] rounded-xl bg-teal text-gold-soft flex items-center justify-center font-display font-semibold text-[18px] shrink-0">{{ $initiales($proprietaire->name) }}</div>
        <div class="min-w-0">
            <div class="font-display font-semibold text-[22px] text-teal truncate">{{ $proprietaire->name }}</div>
            <div class="text-[13px] text-muted mt-0.5">
                {{ $nbBiens }} bien{{ $nbBiens > 1 ? 's' : '' }}
                @if($montant30 > 0)
                    — <span class="font-semibold text-ink">{{ $fmt($montant30) }} FCFA</span> dû sous 30 jours
                @else
                    — rien à régler sous 30 jours
                @endif
            </div>
        </div>
    </div>

    {{-- Bandeau d'avertissement DGID (points de calcul à confirmer) --}}
    @if($nbEstimations > 0)
        <div class="flex items-center gap-2.5 bg-gold-soft/25 border border-gold-soft rounded-[10px] px-4 py-3 mb-5 text-[12.5px] text-gold">
            <svg class="w-4 h-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"><path d="M12 9v4M12 17h.01M10.3 3.9 1.8 18a2 2 0 0 0 1.7 3h17a2 2 0 0 0 1.7-3L13.7 3.9a2 2 0 0 0-3.4 0z" stroke-linecap="round" stroke-linejoin="round"/></svg>
            <span><strong>{{ $nbEstimations }} point{{ $nbEstimations > 1 ? 's' : '' }} de calcul</strong> {{ $nbEstimations > 1 ? 'restent' : 'reste' }} à confirmer auprès de la DGID — dépliez les cartes « Estimation » pour voir le détail.</span>
        </div>
    @endif

    <div class="text-[13px] font-bold uppercase tracking-wide text-muted mb-3">Échéances de ce propriétaire</div>

    @if($echeances->isEmpty())
        <div class="bg-white border border-dashed border-line rounded-2xl text-center py-16 px-8">
            <div class="w-12 h-12 rounded-full bg-green/10 flex items-center justify-center mx-auto mb-3">
                <svg class="w-6 h-6 text-green" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><path d="M20 6 9 17l-5-5" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </div>
            <h3 class="font-display font-semibold text-[16px] text-teal mb-1">Aucune échéance à venir</h3>
            <p class="text-[13px] text-muted max-w-[380px] mx-auto leading-relaxed">Ce propriétaire n'a pas d'obligation fiscale dans les douze prochains mois pour les biens gérés dans l'application.</p>
        </div>
    @else
        <div class="flex flex-col gap-3">
            @foreach($echeances as $e)
                @php
                    [$bTxt, $bCls] = $badgeDe($e['statut_calcul'] ?? null);
                    $detail = $e['detail'] ?? null;
                    $bien   = $e['bien'] ?? null;
                    $dateFr = $e['date_limite'] ? \Illuminate\Support\Carbon::parse($e['date_limite'])->locale('fr')->isoFormat('D MMMM Y') : null;
                    $enRetard = $e['date_limite'] && \Illuminate\Support\Carbon::parse($e['date_limite'])->startOfDay()->lt($today);
                @endphp
                <div x-data="taxCard" data-open="{{ $loop->first ? 'true' : 'false' }}"
                     class="bg-white border border-line rounded-xl overflow-hidden">
                    {{-- Tête (cliquable) --}}
                    <button type="button" x-on:click="toggle" class="w-full flex items-center gap-3.5 px-5 py-4 text-left">
                        <span class="w-[38px] h-[38px] rounded-[9px] bg-paper flex items-center justify-center shrink-0 font-bold text-[11px] text-teal">{{ $codeTaxe($e['type']) }}</span>
                        <div class="flex-1 min-w-0">
                            <div class="font-semibold text-[14.5px] text-ink truncate">{{ $e['libelle'] }}</div>
                            <div class="text-[12px] text-muted mt-0.5 truncate">
                                @if($bien){{ $bien }} — @endif
                                @if($dateFr)échéance {{ $dateFr }} <span class="{{ $enRetard ? 'text-error font-medium' : '' }}">({{ $relatif($e['date_limite']) }})</span>@endif
                            </div>
                        </div>
                        <span class="text-[10.5px] font-semibold px-2 py-0.5 rounded-full whitespace-nowrap shrink-0 {{ $bCls }}">{{ $bTxt }}</span>
                        <div class="text-right shrink-0">
                            <div class="font-display font-semibold text-[19px] text-teal leading-none">{{ $fmt($e['montant'] ?? 0) }}</div>
                            <div class="text-[11px] text-muted mt-0.5">FCFA</div>
                        </div>
                        <svg class="w-4 h-4 text-muted shrink-0 transition-transform" x-bind:class="chevClass" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m6 9 6 6 6-6" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </button>

                    {{-- Détail du calcul (déplié) --}}
                    @if($detail)
                        <div x-show="open" x-cloak class="border-t border-paper-dim bg-paper-dim/60 px-5 py-4">
                            @foreach(($detail['lignes'] ?? []) as $ligne)
                                <div class="flex items-center justify-between gap-4 py-2 border-b border-dashed border-line/70 last:border-b-0 text-[13.5px]">
                                    <span class="text-muted">{{ $ligne['label'] }}</span>
                                    <span class="font-mono text-[12.5px] text-teal-deep shrink-0">{{ $ligne['valeur'] }}</span>
                                </div>
                            @endforeach

                            @isset($detail['resultat'])
                                <div class="flex items-center justify-between gap-4 bg-white border border-line rounded-lg px-3.5 py-3 mt-2.5">
                                    <span class="font-semibold text-[13.5px] text-ink">{{ $detail['resultat']['label'] }}</span>
                                    <span class="font-display font-semibold text-[18px] text-teal shrink-0">{{ $fmt($detail['resultat']['montant']) }} FCFA</span>
                                </div>
                            @endisset

                            @if(!empty($detail['note']))
                                <p class="text-[11.5px] text-muted mt-2.5 italic leading-snug">{{ $detail['note'] }}</p>
                            @endif
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    @endif

    {{-- Légende --}}
    <div class="flex items-start gap-2.5 text-[12px] text-muted bg-white border border-line rounded-xl px-4 py-3 mt-4 leading-relaxed">
        <svg class="w-3.5 h-3.5 mt-0.5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><circle cx="12" cy="12" r="9"/><path d="M12 8v5M12 16h.01" stroke-linecap="round"/></svg>
        <span><strong class="text-green">Fiable</strong> : calculé directement depuis vos contrats et paiements. <strong class="text-gold">Estimation</strong> : basé sur le loyer, à recouper avec le montant réel de l'administration fiscale.</span>
    </div>
</div>
@endsection
