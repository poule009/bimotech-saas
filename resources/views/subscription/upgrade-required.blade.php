@extends('layouts.app')
@section('title', 'Fonctionnalité non disponible')
@section('breadcrumb', 'Upgrade requis')

@section('content')
@php
    $plans = config('plans');
    $hierarchy = $plans['hierarchy'];

    // Plan courant de l'agence
    $subscription    = auth()->user()?->agency?->subscription;
    $niveauBrut      = $subscription?->plan_niveau ?? 'legacy';
    $niveauEffectif  = $plans['niveau_effectif'][$niveauBrut] ?? 'starter';
    $labelActuel     = $plans['labels'][$niveauBrut] ?? 'Starter';

    // Plan supérieur requis (transmis par le middleware ou par défaut le suivant)
    $niveauRequis    = $requiredPlan ?? null;
    if (! $niveauRequis) {
        $posActuelle  = array_search($niveauEffectif, $hierarchy);
        $niveauRequis = $hierarchy[min($posActuelle + 1, count($hierarchy) - 1)];
    }
    $labelRequis = $plans['labels'][$niveauRequis] ?? ucfirst($niveauRequis);

    // Avantages à afficher pour le plan requis (repris de subscription/index)
    $avantagesParPlan = [
        'pro'    => ["Gestion d'immeubles", "Rapports financiers PDF", "Relevés propriétaires PDF", "Export CSV & Import Excel", "Contrats formels PDF", "Recherche globale"],
        'agence' => ["Fiscalité BRS / TVA", "Bilans fiscaux DGID", "États BRS trimestriels", "Logs d'activité complets", "Support prioritaire"],
    ];
    $avantages = $avantagesParPlan[$niveauRequis] ?? [];

    $tarifsNiveau = \App\Models\Subscription::TARIFS;
    $mensuel = $tarifsNiveau[$niveauRequis]['mensuel'] ?? null;
    $annuel  = $tarifsNiveau[$niveauRequis]['annuel']  ?? null;
@endphp

<style>
.upg-wrap {
    max-width: 560px;
    margin: 60px auto;
    padding: 0 16px 80px;
    text-align: center;
}

/* Icône cadenas central */
.upg-lock-circle {
    width: 72px; height: 72px;
    border-radius: 50%;
    background: rgba(201,169,110,.1);
    border: 2px solid rgba(201,169,110,.3);
    display: flex; align-items: center; justify-content: center;
    margin: 0 auto 28px;
}
.upg-lock-circle svg {
    width: 32px; height: 32px;
    color: #C9A96E;
}

.upg-plan-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    background: rgba(201,169,110,.1);
    border: 1px solid rgba(201,169,110,.25);
    border-radius: 6px;
    padding: 4px 12px;
    font-size: 11px;
    font-weight: 700;
    color: #C9A96E;
    letter-spacing: .5px;
    text-transform: uppercase;
    margin-bottom: 20px;
}

.upg-title {
    font-size: 22px;
    font-weight: 700;
    color: #e6edf3;
    margin-bottom: 10px;
    line-height: 1.3;
}
.upg-subtitle {
    font-size: 14px;
    color: #8b949e;
    line-height: 1.6;
    margin-bottom: 32px;
}

/* Card avantages */
.upg-card {
    background: rgba(255,255,255,.04);
    border: 1px solid rgba(255,255,255,.07);
    border-radius: 14px;
    padding: 24px 28px;
    text-align: left;
    margin-bottom: 28px;
}
.upg-card-title {
    font-size: 13px;
    font-weight: 700;
    color: #C9A96E;
    text-transform: uppercase;
    letter-spacing: .8px;
    margin-bottom: 16px;
}
.upg-feature-list {
    list-style: none;
    padding: 0; margin: 0;
    display: flex;
    flex-direction: column;
    gap: 10px;
}
.upg-feature-list li {
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 13.5px;
    color: #c9d1d9;
}
.upg-feature-list li svg {
    width: 16px; height: 16px;
    flex-shrink: 0;
    color: #C9A96E;
}

/* Prix */
.upg-price {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 20px;
    margin-bottom: 28px;
    flex-wrap: wrap;
}
.upg-price-item {
    text-align: center;
}
.upg-price-amount {
    font-size: 20px;
    font-weight: 800;
    color: #e6edf3;
    line-height: 1;
}
.upg-price-period {
    font-size: 11px;
    color: #484f58;
    margin-top: 3px;
}
.upg-price-sep {
    width: 1px;
    height: 36px;
    background: rgba(255,255,255,.1);
}

/* Boutons */
.upg-btn-primary {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    background: #D42B2B;
    color: #fff;
    font-size: 14px;
    font-weight: 700;
    padding: 13px 32px;
    border-radius: 10px;
    text-decoration: none;
    border: none;
    cursor: pointer;
    transition: background .15s, transform .1s;
    width: 100%;
    margin-bottom: 10px;
}
.upg-btn-primary:hover {
    background: #b52222;
    transform: translateY(-1px);
    color: #fff;
    text-decoration: none;
}
.upg-btn-secondary {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    color: #8b949e;
    font-size: 13px;
    text-decoration: none;
    padding: 8px;
    transition: color .15s;
}
.upg-btn-secondary:hover {
    color: #c9d1d9;
    text-decoration: none;
}
.upg-btn-secondary svg {
    width: 14px; height: 14px;
    flex-shrink: 0;
}
</style>

<div class="upg-wrap">

    {{-- Icône cadenas --}}
    <div class="upg-lock-circle">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
            <path d="M7 11V7a5 5 0 0110 0v4"/>
        </svg>
    </div>

    {{-- Badge plan requis --}}
    <div class="upg-plan-badge">
        <svg style="width:12px;height:12px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>
        </svg>
        Plan {{ $labelRequis }}
    </div>

    <h1 class="upg-title">Fonctionnalité réservée au Plan {{ $labelRequis }}</h1>
    <p class="upg-subtitle">
        Votre abonnement actuel (<strong style="color:#e6edf3">{{ $labelActuel }}</strong>) ne donne pas accès à cette section.
        Passez au Plan {{ $labelRequis }} pour en profiter.
    </p>

    {{-- Avantages du plan requis --}}
    @if(count($avantages) > 0)
    <div class="upg-card">
        <div class="upg-card-title">Ce que vous débloquez avec le Plan {{ $labelRequis }}</div>
        <ul class="upg-feature-list">
            @foreach($avantages as $avantage)
                <li>
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="20 6 9 17 4 12"/>
                    </svg>
                    {{ $avantage }}
                </li>
            @endforeach
        </ul>
    </div>
    @endif

    {{-- Prix --}}
    @if($mensuel || $annuel)
    <div class="upg-price">
        @if($mensuel)
        <div class="upg-price-item">
            <div class="upg-price-amount">{{ number_format($mensuel, 0, ',', ' ') }} <span style="font-size:13px;font-weight:500;color:#8b949e">FCFA</span></div>
            <div class="upg-price-period">/ mois</div>
        </div>
        @endif
        @if($mensuel && $annuel)
        <div class="upg-price-sep"></div>
        @endif
        @if($annuel)
        <div class="upg-price-item">
            <div class="upg-price-amount">{{ number_format($annuel, 0, ',', ' ') }} <span style="font-size:13px;font-weight:500;color:#8b949e">FCFA</span></div>
            <div class="upg-price-period">/ an <span style="color:#C9A96E;font-size:10px">— économisez 2 mois</span></div>
        </div>
        @endif
    </div>
    @endif

    {{-- CTA --}}
    <a href="{{ route('subscription.index') }}" class="upg-btn-primary">
        <svg style="width:16px;height:16px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M5 12h14M12 5l7 7-7 7"/>
        </svg>
        Upgrader mon abonnement
    </a>

    <a href="{{ url()->previous(route('admin.dashboard')) }}" class="upg-btn-secondary">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M19 12H5M12 19l-7-7 7-7"/>
        </svg>
        Retour
    </a>

</div>
@endsection
