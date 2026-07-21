@extends('layouts.vitrine-agence')

@php
    use Illuminate\Support\Str;

    // Prix formaté (FCFA / mois — plateforme locative).
    $prix = fn ($b) => number_format((float) $b->loyer_mensuel, 0, ',', ' ');

    // Photo d'ambiance de la section « types » : une couverture déjà chargée,
    // différente du hero quand c'est possible (aucune image externe inventée).
    $photoTypes = $catalogue->firstWhere(fn ($b) => $b->photo_couverture && $b->id !== ($heroBien->id ?? null))?->photo_couverture
        ?? $heroBien?->photo_couverture;

    // Icônes par type de bien — inline, pas de dépendance externe.
    $iconeType = [
        'appartement' => '<path d="M3 21h18M6 21V7l6-4 6 4v14M10 21v-4h4v4"/>',
        'villa'       => '<path d="M3 10.5 12 3l9 7.5M5 9.5V21h14V9.5M9 21v-6h6v6"/>',
        'studio'      => '<rect x="4" y="5" width="16" height="14" rx="2"/><path d="M4 12h16"/>',
        'bureau'      => '<rect x="4" y="3" width="16" height="18" rx="1.5"/><path d="M8 7h2M14 7h2M8 11h2M14 11h2M8 15h8"/>',
        'commerce'    => '<path d="M3 9h18l-1.5-5H4.5L3 9zM5 9v11h14V9M9 20v-6h6v6"/>',
        'terrain'     => '<path d="M3 19h18M6 19l3-9 3 5 2-3 4 7"/>',
    ];
@endphp

@section('content')

{{-- ══════════════ HERO PLEINE LARGEUR ══════════════ --}}
<div class="hero">
    @if($heroBien?->photo_couverture)
        <div class="hero-bg"><img src="{{ $heroBien->photo_couverture->url }}" alt="" aria-hidden="true"></div>
    @endif
    <div class="hero-veil"></div>

    <div class="wrap hero-inner">
        <div class="hero-eyebrow">{{ $stats['annees'] }} an{{ $stats['annees'] > 1 ? 's' : '' }} à votre service</div>
        <h1 class="hero-title">{{ $agence->name }}</h1>
        <p class="hero-desc">{{ $agence->slogan ?: 'Villas, appartements et terrains sélectionnés — gérés directement par notre équipe, jusqu\'à la remise des clés.' }}</p>
        <div class="hero-metrics">
            <div>
                <div class="hm-num">{{ $stats['annees'] }} an{{ $stats['annees'] > 1 ? 's' : '' }}</div>
                <div class="hm-label">d'expérience</div>
            </div>
            <div>
                <div class="hm-num">{{ $stats['geres_total'] }}</div>
                <div class="hm-label">bien{{ $stats['geres_total'] > 1 ? 's' : '' }} géré{{ $stats['geres_total'] > 1 ? 's' : '' }}</div>
            </div>
            <div>
                <div class="hm-num">{{ $stats['disponibles'] }}</div>
                <div class="hm-label">disponible{{ $stats['disponibles'] > 1 ? 's' : '' }}</div>
            </div>
        </div>
    </div>

    @if($heroBien)
        <a class="hero-featured" href="{{ route('vitrine.bien', [$agence->slug, $heroBien->slug]) }}">
            <div class="hf-label">À la une</div>
            <div class="hf-title">{{ Str::limit($heroBien->titre_fallback, 34) }}</div>
            <div class="hf-sub">{{ $heroBien->quartier }}</div>
            <div class="hf-price">{{ $prix($heroBien) }} FCFA / mois</div>
        </a>
    @endif
</div>

{{-- ══════════════ BARRE DE RECHERCHE ══════════════ --}}
<div class="wrap search-zone">
    <div class="search-tab">Location</div>
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

{{-- ══════════════ BIENS EN VEDETTE ══════════════ --}}
@if($vedettes->isNotEmpty())
    <section id="biens">
        <div class="wrap">
            <div class="sec-head">
                <div class="sec-eyebrow">Sélection du moment</div>
                <div class="sec-title">Biens en vedette</div>
                <p class="sec-desc">Les biens que notre équipe met en avant ce mois-ci, disponibles immédiatement.</p>
            </div>
            <div class="listing-grid">
                @foreach($vedettes as $b)
                    <a class="listing-card" href="{{ route('vitrine.bien', [$agence->slug, $b->slug]) }}">
                        <div class="listing-media">
                            @if($b->photo_couverture)<img src="{{ $b->photo_couverture->url }}" alt="{{ $b->titre_fallback }}">@endif
                            <span class="listing-type">{{ $b->type_label }}</span>
                            @if($b->est_en_vedette)<span class="listing-flag">Coup de cœur</span>@endif
                            <span class="listing-tag-price">{{ $prix($b) }} FCFA</span>
                        </div>
                        <div class="listing-body">
                            <div class="listing-loc">{{ $b->quartier }}{{ $b->ville ? ', ' . $b->ville : '' }}</div>
                            <div class="listing-title">{{ Str::limit($b->titre_fallback, 42) }}</div>
                            <div class="listing-specs">
                                @foreach(array_slice($b->specsVitrine(), 0, 3) as $s)<span>{{ $s[0] }} {{ $s[1] }}</span>@endforeach
                            </div>
                            <div class="listing-footer">
                                <div class="listing-price">{{ $prix($b) }} <span>FCFA / mois</span></div>
                                <div class="listing-arrow">Voir le bien →</div>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    </section>
@endif

{{-- ══════════════ CATALOGUE ══════════════ --}}
<section class="catalogue" id="catalogue">
    <div class="wrap">
        <div class="sec-head">
            <div class="sec-eyebrow">Le catalogue</div>
            <div class="sec-title">Tous nos biens disponibles</div>
            <p class="sec-desc">Parcourez l'ensemble de nos annonces et cliquez sur un bien pour en découvrir le détail.</p>
        </div>

        <div class="cat-head">
            <div class="cat-count">
                @if($catalogue->isNotEmpty())
                    @php
                        $pluriel = $totalCatalogue > 1 ? 's' : '';
                        $filtre  = $typeActif || request('quartier') || request('budget');
                    @endphp
                    {{ $totalCatalogue . ' bien' . $pluriel . ' ' . ($filtre ? 'correspondant' : 'disponible') . $pluriel }}
                @endif
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
            <div class="listing-grid">
                @foreach($catalogue as $b)
                    <a class="listing-card" href="{{ route('vitrine.bien', [$agence->slug, $b->slug]) }}">
                        <div class="listing-media">
                            @if($b->photo_couverture)<img src="{{ $b->photo_couverture->url }}" alt="{{ $b->titre_fallback }}">@endif
                            <span class="listing-type">{{ $b->type_label }}</span>
                            <span class="listing-tag-price">{{ $prix($b) }} FCFA</span>
                        </div>
                        <div class="listing-body">
                            <div class="listing-loc">{{ $b->quartier }}{{ $b->ville ? ', ' . $b->ville : '' }}</div>
                            <div class="listing-title">{{ Str::limit($b->titre_fallback, 42) }}</div>
                            <div class="listing-specs">
                                @foreach(array_slice($b->specsVitrine(), 0, 3) as $s)<span>{{ $s[0] }} {{ $s[1] }}</span>@endforeach
                            </div>
                            <div class="listing-footer">
                                <div class="listing-price">{{ $prix($b) }} <span>FCFA / mois</span></div>
                                <div class="listing-arrow">Voir le bien →</div>
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

{{-- ══════════════ QUE RECHERCHEZ-VOUS ══════════════ --}}
@if($typesPresents->isNotEmpty())
    <section id="types">
        <div class="wrap">
            <div class="types-split">
                <div>
                    <div class="sec-eyebrow">Catégories</div>
                    <div class="sec-title">Que recherchez-vous ?</div>
                    <p class="sec-desc" style="margin-bottom:32px;">Choisissez le type de bien qui correspond à votre projet — nous n'affichons que les catégories réellement disponibles.</p>
                    <div class="types-grid">
                        @foreach($typesPresents->take(4) as $t)
                            <a class="type-card" href="{{ route('vitrine.home', [$agence->slug, 'type' => $t['type']]) }}#catalogue">
                                <span class="type-icon">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round">
                                        {!! $iconeType[$t['type']] ?? $iconeType['appartement'] !!}
                                    </svg>
                                </span>
                                <span>
                                    <span class="type-name">{{ $t['label'] }}</span>
                                    <span class="type-count">{{ $t['nb'] }} bien{{ $t['nb'] > 1 ? 's' : '' }} disponible{{ $t['nb'] > 1 ? 's' : '' }}</span>
                                </span>
                            </a>
                        @endforeach
                    </div>
                </div>
                @if($photoTypes)
                    <div class="types-photo"><img src="{{ $photoTypes->url }}" alt="" aria-hidden="true"></div>
                @endif
            </div>
        </div>
    </section>
@endif

{{-- ══════════════ NOS ENGAGEMENTS ══════════════ --}}
<section id="engagements" style="padding-top:0;">
    <div class="wrap">
        <div class="sec-head">
            <div class="sec-eyebrow">Nos atouts</div>
            <div class="sec-title">Pourquoi passer par nous</div>
            <p class="sec-desc">Un accompagnement direct, sans intermédiaire, de la visite jusqu'à la remise des clés.</p>
        </div>
        <div class="why-grid">
            <div class="why-item">
                <span class="why-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3l8 4v5c0 5-3.4 8.4-8 9-4.6-.6-8-4-8-9V7z"/><path d="m9 12 2 2 4-4"/></svg></span>
                <div class="why-title">Gestion directe</div>
                <p class="why-desc">Chaque bien affiché est géré par l'agence elle-même — pas d'annonce fantôme.</p>
            </div>
            <div class="why-item">
                <span class="why-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2v20M17 6H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg></span>
                <div class="why-title">Prix affichés clairement</div>
                <p class="why-desc">Le loyer mensuel est annoncé sur chaque bien, sans frais cachés en cours de route.</p>
            </div>
            <div class="why-item">
                <span class="why-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg></span>
                <div class="why-title">Ancrage local</div>
                <p class="why-desc">{{ $quartiers->count() }} quartier{{ $quartiers->count() > 1 ? 's' : '' }} couvert{{ $quartiers->count() > 1 ? 's' : '' }} — nous connaissons le terrain et ses réalités.</p>
            </div>
            <div class="why-item">
                <span class="why-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg></span>
                <div class="why-title">Réponse rapide</div>
                <p class="why-desc">Un message WhatsApp suffit pour organiser une visite ou poser vos questions.</p>
            </div>
        </div>
    </div>
</section>

{{-- ══════════════ BANDEAU CONFIANCE ══════════════ --}}
<div class="trust">
    <div class="wrap">
        <div class="trust-inner">
            <div class="trust-quote">« Chaque bien que nous proposons, nous le gérons <span>nous-mêmes</span> — jusqu'à la remise des clés. »</div>
            <div class="trust-grid">
                <div class="trust-item">
                    <div class="trust-num">{{ $stats['annees'] }} an{{ $stats['annees'] > 1 ? 's' : '' }}</div>
                    <div class="trust-label">à votre service</div>
                </div>
                <div class="trust-item">
                    <div class="trust-num">{{ $stats['geres_total'] }}</div>
                    <div class="trust-label">bien{{ $stats['geres_total'] > 1 ? 's' : '' }} géré{{ $stats['geres_total'] > 1 ? 's' : '' }} par l'agence</div>
                </div>
                <div class="trust-item">
                    <div class="trust-num">{{ $quartiers->count() }}</div>
                    <div class="trust-label">quartier{{ $quartiers->count() > 1 ? 's' : '' }} couvert{{ $quartiers->count() > 1 ? 's' : '' }}</div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ══════════════ QUARTIERS ══════════════ --}}
@if($quartiers->isNotEmpty())
    <section id="quartiers">
        <div class="wrap">
            <div class="sec-head">
                <div class="sec-eyebrow">Localisation</div>
                <div class="sec-title">Trouvez un bien dans votre quartier</div>
                <p class="sec-desc">Explorez nos annonces quartier par quartier.</p>
            </div>
            <div class="nb-row">
                @foreach($quartiers as $q)
                    <a class="nb-item" href="{{ route('vitrine.home', [$agence->slug, 'quartier' => $q['nom']]) }}#catalogue">
                        @if($q['photo'])<img src="{{ $q['photo']->url }}" alt="{{ $q['nom'] }}">@endif
                        <span class="nb-veil"></span>
                        <span class="nb-caption">
                            <span class="nb-name">{{ $q['nom'] }}</span>
                            <span class="nb-count">{{ $q['nb'] }} bien{{ $q['nb'] > 1 ? 's' : '' }}</span>
                        </span>
                    </a>
                @endforeach
            </div>
        </div>
    </section>
@endif

{{-- ══════════════ ALERTES WHATSAPP ══════════════ --}}
@if($whatsappUrl)
    <section style="padding-top:0;">
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
