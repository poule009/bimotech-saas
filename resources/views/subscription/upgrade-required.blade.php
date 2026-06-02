@extends('layouts.app')
@section('header', 'Upgrade requis')

@section('content')

@php
    $plans = config('plans');
    $hierarchy = $plans['hierarchy'];
    $subscription    = auth()->user()?->agency?->subscription;
    $niveauBrut      = $subscription?->plan_niveau ?? 'legacy';
    $niveauEffectif  = $plans['niveau_effectif'][$niveauBrut] ?? 'starter';
    $labelActuel     = $plans['labels'][$niveauBrut] ?? 'Starter';
    $niveauRequis    = $requiredPlan ?? null;
    if (!$niveauRequis) {
        $posActuelle  = array_search($niveauEffectif, $hierarchy);
        $niveauRequis = $hierarchy[min($posActuelle + 1, count($hierarchy) - 1)];
    }
    $labelRequis = $plans['labels'][$niveauRequis] ?? ucfirst($niveauRequis);
    $avantagesParPlan = [
        'pro'    => ["Gestion d'immeubles", "Rapports financiers PDF", "Relevés propriétaires PDF", "Export CSV & Import Excel", "Contrats formels PDF", "Recherche globale"],
        'agence' => ["Fiscalité BRS / TVA", "Bilans fiscaux DGID", "États BRS trimestriels", "Logs d'activité complets", "Support prioritaire"],
    ];
    $avantages = $avantagesParPlan[$niveauRequis] ?? [];
    $tarifsNiveau = \App\Models\Subscription::TARIFS;
    $mensuel = $tarifsNiveau[$niveauRequis]['mensuel'] ?? null;
    $annuel  = $tarifsNiveau[$niveauRequis]['annuel']  ?? null;
@endphp

<div class="max-w-lg mx-auto py-10 text-center">

    {{-- Icône --}}
    <div class="w-18 h-18 rounded-full bg-bimo-gold/10 border-2 border-bimo-gold/30 flex items-center justify-center mx-auto mb-7" style="width:72px;height:72px">
        <svg class="w-8 h-8 text-bimo-gold" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0110 0v4"/></svg>
    </div>

    {{-- Badge plan requis --}}
    <div class="inline-flex items-center gap-1.5 bg-bimo-gold/10 border border-bimo-gold/25 rounded-[6px] px-3 py-1.5 font-body font-bold text-xs uppercase tracking-widest text-bimo-gold mb-5">
        <svg class="w-3 h-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
        Plan {{ $labelRequis }}
    </div>

    <h1 class="font-display font-extrabold text-2xl text-bimo-navy tracking-tight mb-3">Fonctionnalité réservée au Plan {{ $labelRequis }}</h1>
    <p class="font-body text-sm text-bimo-navy/60 leading-relaxed mb-8">
        Votre abonnement actuel (<strong class="text-bimo-navy">{{ $labelActuel }}</strong>) ne donne pas accès à cette section.
        Passez au Plan {{ $labelRequis }} pour en profiter.
    </p>

    {{-- Avantages --}}
    @if(count($avantages) > 0)
    <div class="bg-bimo-gold/[6%] border border-bimo-gold/20 rounded-[14px] p-6 text-left mb-7">
        <div class="font-body font-medium text-[10px] uppercase tracking-widest text-bimo-gold/70 mb-4">Ce que vous débloquez avec le Plan {{ $labelRequis }}</div>
        <ul class="space-y-3">
            @foreach($avantages as $avantage)
            <li class="flex items-center gap-3">
                <svg class="w-4 h-4 text-bimo-gold flex-shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                <span class="font-body text-sm text-bimo-navy/80">{{ $avantage }}</span>
            </li>
            @endforeach
        </ul>
    </div>
    @endif

    {{-- Prix --}}
    @if($mensuel || $annuel)
    <div class="flex items-center justify-center gap-8 mb-8">
        @if($mensuel)
        <div class="text-center">
            <div class="font-display font-extrabold text-2xl text-bimo-navy">{{ number_format($mensuel,0,',','') }}<span class="font-body text-sm text-bimo-navy/40 ml-1">FCFA</span></div>
            <div class="font-body text-xs text-bimo-navy/40 mt-1">/ mois</div>
        </div>
        @endif
        @if($mensuel && $annuel)<div class="w-px h-10 bg-bimo-navy/10"></div>@endif
        @if($annuel)
        <div class="text-center">
            <div class="font-display font-extrabold text-2xl text-bimo-navy">{{ number_format($annuel,0,',','') }}<span class="font-body text-sm text-bimo-navy/40 ml-1">FCFA</span></div>
            <div class="font-body text-xs text-bimo-navy/40 mt-1">/ an <span class="text-bimo-gold text-[10px]">— 2 mois offerts</span></div>
        </div>
        @endif
    </div>
    @endif

    {{-- CTA --}}
    <a href="{{ route('subscription.index') }}"
       class="flex items-center justify-center gap-2 w-full px-6 py-3.5 bg-[var(--ac)] text-white font-display font-bold text-sm rounded-[10px] hover:opacity-90 transition-opacity duration-150 mb-3">
        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
        Upgrader mon abonnement
    </a>

    <a href="{{ url()->previous(route('admin.dashboard')) }}"
       class="flex items-center justify-center gap-2 w-full px-6 py-3 font-body text-sm text-bimo-navy/50 hover:text-bimo-navy transition-colors duration-150">
        <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
        Retour
    </a>

</div>
@endsection
