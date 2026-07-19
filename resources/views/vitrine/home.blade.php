@extends('layouts.vitrine-agence')

@php
    use Illuminate\Support\Str;

    // Prix formaté (FCFA / mois — plateforme locative).
    $prix = fn ($b) => number_format((float) $b->loyer_mensuel, 0, ',', ' ');
    // Specs : source unique Bien::specsVitrine(), tronquée pour les cartes.
@endphp

@section('content')

{{-- ══════════════ HERO ══════════════ --}}
<div class="hero">
    <div class="wrap">
        <div class="hero-grid">
            <div>
                <div class="hero-eyebrow">{{ $stats['annees'] }} an{{ $stats['annees'] > 1 ? 's' : '' }} à votre service</div>
                <h1 class="hero-title">{{ $agence->name }}</h1>
                <p class="hero-desc">{{ $agence->slogan ?: 'Villas, appartements et terrains sélectionnés — gérés directement par notre équipe.' }}</p>
                <div class="hero-metrics">
                    <div><div class="hm-num">{{ $stats['annees'] }} an{{ $stats['annees'] > 1 ? 's' : '' }}</div><div class="hm-label">d'expérience</div></div>
                    <div><div class="hm-num">{{ $stats['geres_total'] }}</div><div class="hm-label">bien{{ $stats['geres_total'] > 1 ? 's' : '' }} géré{{ $stats['geres_total'] > 1 ? 's' : '' }}</div></div>
                    <div><div class="hm-num">{{ $stats['disponibles'] }}</div><div class="hm-label">disponible{{ $stats['disponibles'] > 1 ? 's' : '' }}</div></div>
                </div>
            </div>
            @if($heroBien && $heroBien->photo_couverture)
                <a class="hero-photo" href="{{ route('vitrine.bien', [$agence->slug, $heroBien->slug]) }}">
                    <img src="{{ $heroBien->photo_couverture->url }}" alt="{{ $heroBien->titre_fallback }}">
                    <svg class="hero-arc" viewBox="0 0 70 70" fill="none"><path d="M2 68 A66 66 0 0 1 68 2" stroke="#E8CD97" stroke-width="2" stroke-linecap="round"/><circle cx="68" cy="2" r="3" fill="#E8CD97"/></svg>
                    <div class="hero-tag">
                        <div>
                            <div class="hero-tag-title">{{ Str::limit($heroBien->titre_fallback, 28) }}</div>
                            <div class="hero-tag-sub">{{ $heroBien->quartier }}</div>
                        </div>
                        <div class="hero-tag-price">{{ $prix($heroBien) }}<br>FCFA/mois</div>
                    </div>
                </a>
            @endif
        </div>
    </div>

    {{-- Barre de recherche → catalogue filtré --}}
    <div class="wrap">
        <form class="searchbar" method="GET" action="{{ route('vitrine.home', $agence->slug) }}">
            <div class="search-field">
                <div class="sf-label">Quartier</div>
                <div class="sf-value">
                    <select name="quartier">
                        <option value="">Tous les quartiers</option>
                        @foreach($quartiers as $q)
                            <option value="{{ $q['nom'] }}" @selected(request('quartier') === $q['nom'])>{{ $q['nom'] }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="search-field">
                <div class="sf-label">Type de bien</div>
                <div class="sf-value">
                    <select name="type">
                        <option value="">Tous types</option>
                        @foreach(\App\Models\Bien::TYPES as $val => $label)
                            <option value="{{ $val }}" @selected($typeActif === $val)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="search-field">
                <div class="sf-label">Budget max (mensuel)</div>
                <div class="sf-value">
                    <select name="budget">
                        <option value="">Sans limite</option>
                        @foreach([150000, 250000, 400000, 700000, 1500000] as $seuil)
                            <option value="{{ $seuil }}" @selected((int) request('budget') === $seuil)>{{ number_format($seuil, 0, ',', ' ') }} FCFA</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <button class="search-btn" type="submit">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
                Rechercher
            </button>
        </form>
    </div>
</div>

{{-- ══════════════ QUARTIERS ══════════════ --}}
@if($quartiers->isNotEmpty())
    <section id="quartiers">
        <div class="wrap">
            <div class="sec-eyebrow">Explorer</div>
            <div class="sec-title">Par quartier</div>
            <div class="nb-row">
                @foreach($quartiers as $q)
                    <a class="nb-item" href="{{ route('vitrine.home', [$agence->slug, 'quartier' => $q['nom']]) }}#catalogue">
                        <div class="nb-photo">
                            @if($q['photo'])<img src="{{ $q['photo']->url }}" alt="{{ $q['nom'] }}">@endif
                        </div>
                        <div class="nb-name">{{ $q['nom'] }}</div>
                        <div class="nb-count">{{ $q['nb'] }} bien{{ $q['nb'] > 1 ? 's' : '' }}</div>
                    </a>
                @endforeach
            </div>
        </div>
    </section>
@endif

{{-- ══════════════ BIENS EN VEDETTE ══════════════ --}}
@if($vedettes->isNotEmpty())
    <section class="featured" id="biens">
        <div class="wrap">
            <div class="sec-eyebrow">Sélection du moment</div>
            <div class="sec-title">Biens en vedette</div>
            @foreach($vedettes as $i => $b)
                <a class="feat-row {{ $i % 2 === 1 ? 'reverse' : '' }}" href="{{ route('vitrine.bien', [$agence->slug, $b->slug]) }}">
                    <div class="feat-media">
                        @if($b->photo_couverture)<img src="{{ $b->photo_couverture->url }}" alt="{{ $b->titre_fallback }}">@endif
                        @if($b->est_en_vedette)<span class="feat-badge">Coup de cœur</span>@endif
                    </div>
                    <div class="feat-info">
                        <div class="feat-kicker">{{ $b->type_label }} · Location</div>
                        <div class="feat-title">{{ $b->titre_fallback }}</div>
                        <div class="feat-specs">
                            @foreach(array_slice($b->specsVitrine(), 0, 3) as $s)<span>{{ $s[0] }} {{ $s[1] }}</span>@endforeach
                        </div>
                        @if($b->description)<p class="feat-desc">{{ Str::limit($b->description, 220) }}</p>@endif
                        <div class="feat-bottom">
                            <div class="feat-price">{{ $prix($b) }} FCFA <span>/ mois</span></div>
                            <div class="feat-link">→</div>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>
    </section>
@endif

{{-- ══════════════ CATALOGUE ══════════════ --}}
<section id="catalogue">
    <div class="wrap">
        <div class="cat-head">
            <div>
                <div class="sec-eyebrow">Le catalogue</div>
                <div class="sec-title" style="margin-bottom:0;">Tous nos biens disponibles</div>
            </div>
            @if($typesPresents->isNotEmpty())
                <div class="filters">
                    <a class="filter-pill {{ ! $typeActif ? 'active' : '' }}" href="{{ route('vitrine.home', $agence->slug) }}#catalogue">Tous</a>
                    @foreach($typesPresents as $t)
                        <a class="filter-pill {{ $typeActif === $t['type'] ? 'active' : '' }}" href="{{ route('vitrine.home', [$agence->slug, 'type' => $t['type']]) }}#catalogue">{{ $t['label'] }}</a>
                    @endforeach
                </div>
            @endif
        </div>

        @if($catalogue->isNotEmpty())
            <div class="cat-count">{{ $totalCatalogue }} bien{{ $totalCatalogue > 1 ? 's' : '' }} {{ $typeActif || request('quartier') || request('budget') ? 'correspondant' . ($totalCatalogue > 1 ? 's' : '') : 'disponible' . ($totalCatalogue > 1 ? 's' : '') }}</div>
            <div class="listing-grid">
                @foreach($catalogue as $b)
                    <a class="listing-card" href="{{ route('vitrine.bien', [$agence->slug, $b->slug]) }}">
                        <div class="listing-media">
                            @if($b->photo_couverture)<img src="{{ $b->photo_couverture->url }}" alt="{{ $b->titre_fallback }}">@endif
                            <span class="listing-type">{{ $b->type_label }}</span>
                        </div>
                        <div class="listing-body">
                            <div class="listing-loc">{{ $b->quartier }}{{ $b->ville ? ', ' . $b->ville : '' }}</div>
                            <div class="listing-title">{{ Str::limit($b->titre_fallback, 42) }}</div>
                            <div class="listing-specs">
                                @foreach(array_slice($b->specsVitrine(), 0, 2) as $s)<span>{{ $s[0] }} {{ $s[1] }}</span>@endforeach
                            </div>
                            <div class="listing-footer">
                                <div class="listing-price">{{ $prix($b) }} <span>FCFA / mois</span></div>
                                <div class="listing-arrow">→</div>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>

            @if($lienVoirPlus)
                <div class="show-more">
                    <a class="btn-outline" href="{{ $lienVoirPlus }}">Voir plus de biens ({{ $resteAAfficher }} restant{{ $resteAAfficher > 1 ? 's' : '' }})</a>
                </div>
            @endif
        @else
            <div class="empty-state">
                <div class="empty-icon">
                    <svg width="34" height="34" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><path d="M9 22V12h6v10"/></svg>
                </div>
                @if($totalDisponibles === 0)
                    <div class="empty-title">Aucun bien disponible pour l'instant</div>
                    <p class="empty-text">De nouveaux biens seront bientôt publiés. Revenez prochainement, ou contactez l'agence pour être prévenu dès qu'un bien correspond à votre recherche.</p>
                @else
                    <div class="empty-title">Aucun bien ne correspond à ce filtre</div>
                    <p class="empty-text">Essayez un autre quartier ou un autre type de bien. <a href="{{ route('vitrine.home', $agence->slug) }}#catalogue" style="color:var(--teal); font-weight:600;">Voir tous les biens →</a></p>
                @endif
            </div>
        @endif
    </div>
</section>

{{-- ══════════════ CONFIANCE ══════════════ --}}
<div class="trust">
    <div class="wrap">
        <div class="trust-inner">
            <div class="trust-quote">« Chaque bien que nous proposons, nous le gérons <span>nous-mêmes</span> — jusqu'à la remise des clés. »</div>
            <div class="trust-grid">
                <div class="trust-item"><div class="trust-num">{{ $stats['annees'] }} an{{ $stats['annees'] > 1 ? 's' : '' }}</div><div class="trust-label">à votre service</div></div>
                <div class="trust-item"><div class="trust-num">{{ $stats['geres_total'] }}</div><div class="trust-label">bien{{ $stats['geres_total'] > 1 ? 's' : '' }} géré{{ $stats['geres_total'] > 1 ? 's' : '' }} par l'agence</div></div>
                <div class="trust-item"><div class="trust-num">{{ $quartiers->count() }}</div><div class="trust-label">quartier{{ $quartiers->count() > 1 ? 's' : '' }} couvert{{ $quartiers->count() > 1 ? 's' : '' }}</div></div>
            </div>
        </div>
    </div>
</div>

{{-- ══════════════ ALERTES WHATSAPP ══════════════ --}}
@if($whatsappUrl)
    <section>
        <div class="wrap">
            <div class="alerts-box">
                <div class="alerts-title">Ne manquez pas <em>le prochain bien</em></div>
                <p class="alerts-desc">Contactez {{ $agence->name }} sur WhatsApp pour être prévenu dès qu'un bien correspondant à vos critères devient disponible.</p>
                <a class="alerts-cta" href="{{ $whatsappUrl }}" target="_blank" rel="noopener">
                    <svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C6.48 2 2 6.48 2 12c0 1.85.5 3.58 1.35 5.07L2 22l5.07-1.32A9.94 9.94 0 0012 22c5.52 0 10-4.48 10-10S17.52 2 12 2z"/></svg>
                    Contacter sur WhatsApp
                </a>
            </div>
        </div>
    </section>
@endif

@endsection
