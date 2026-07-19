@extends('layouts.vitrine-agence')

@section('meta_title', $bien->titre_fallback . ' — ' . $agence->name)
@section('meta_description', $bien->description ? \Illuminate\Support\Str::limit($bien->description, 160) : ($bien->type_label . ' à ' . $bien->quartier . ' — ' . $agence->name))

@php
    use Illuminate\Support\Str;

    $prix = number_format((float) $bien->loyer_mensuel, 0, ',', ' ');

    // Grille complète des specs — source unique partagée avec la liste.
    $specs = $bien->specsVitrine();

    $lienBien = route('vitrine.bien', [$agence->slug, $bien->slug]);
@endphp

@section('content')
<div class="wrap detail-top">
    <a class="back-btn" href="{{ route('vitrine.home', $agence->slug) }}#catalogue">← Retour au catalogue</a>

    {{-- ══════════ GALERIE ══════════ --}}
    @if($photos->isNotEmpty())
        <div class="gallery">
            <div class="gallery-main"><img id="gallery-main-img" src="{{ $photos->first()->url }}" alt="{{ $bien->titre_fallback }}"></div>
            <div class="gallery-side">
                @php $side = $photos->slice(1, 2)->values(); $reste = max(0, $photos->count() - 3); @endphp
                @for($i = 0; $i < 2; $i++)
                    @if(isset($side[$i]))
                        <button type="button" class="gallery-thumb" data-full="{{ $side[$i]->url }}">
                            <img src="{{ $side[$i]->url }}" alt="">
                            @if($i === 1 && $reste > 0)<div class="gallery-more">+{{ $reste }} photo{{ $reste > 1 ? 's' : '' }}</div>@endif
                        </button>
                    @else
                        <div class="gallery-thumb"><img src="{{ $photos->first()->url }}" alt=""></div>
                    @endif
                @endfor
            </div>
        </div>
    @endif

    <div class="detail-grid">
        <div>
            <div class="detail-kicker">{{ $bien->type_label }} · Location</div>
            <h1 class="detail-title">{{ $bien->titre_fallback }}</h1>
            <div class="detail-loc">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                <span>{{ $bien->quartier }}{{ $bien->ville ? ', ' . $bien->ville : '' }}</span>
            </div>

            @if(count($specs))
                <div class="detail-specs">
                    @foreach($specs as $s)
                        <div class="ds-item"><div class="ds-num">{{ $s[0] }}</div><div class="ds-label">{{ $s[1] }}</div></div>
                    @endforeach
                </div>
            @endif

            @if($bien->description)
                <div class="detail-desc-title">Description</div>
                <p class="detail-desc">{{ $bien->description }}</p>
            @endif

            <div class="detail-desc-title">Localisation</div>
            <div class="map-block">
                <div class="map-pin">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                </div>
                <div class="map-note">{{ $bien->quartier }}{{ $bien->ville ? ', ' . $bien->ville : '' }} — adresse exacte communiquée après contact</div>
            </div>
        </div>

        {{-- ══════════ CARTE PRIX + CONTACT ══════════ --}}
        <div class="detail-side">
            <div class="price-card">
                <div class="price-card-amount">{{ $prix }} FCFA <span>/ mois</span></div>
                <div class="price-card-sub">Hors caution — modalités communiquées par l'agence</div>

                @if($whatsappUrl)
                    <a class="contact-btn whatsapp" href="{{ $whatsappUrl }}" target="_blank" rel="noopener">
                        <svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C6.48 2 2 6.48 2 12c0 1.85.5 3.58 1.35 5.07L2 22l5.07-1.32A9.94 9.94 0 0012 22c5.52 0 10-4.48 10-10S17.52 2 12 2z"/></svg>
                        Contacter au sujet de ce bien
                    </a>
                @endif

                <button type="button" class="contact-btn share" id="copy-link-btn" data-url="{{ $lienBien }}">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 8a3 3 0 1 0-2.83-4H15a3 3 0 0 0 .05 2.06L8.09 9.26a3 3 0 1 0 0 5.48l6.96 3.2a3 3 0 1 0 .87-1.83l-6.96-3.2a3 3 0 0 0 0-1.82l6.96-3.2c.32.35.72.6 1.18.75"/></svg>
                    <span class="copy-label">Copier le lien de ce bien</span>
                </button>
                <div class="copy-feedback" id="copy-feedback" aria-live="polite"></div>
            </div>

            <div class="agency-card">
                @if($agence->logo_path)
                    <img class="agency-card-logo" src="{{ \Illuminate\Support\Facades\Storage::url($agence->logo_path) }}" alt="{{ $agence->name }}">
                @endif
                <div>
                    <div class="agency-card-name">{{ $agence->name }}</div>
                    <div class="agency-card-sub">Gestion directe par l'agence</div>
                </div>
            </div>
        </div>
    </div>

    {{-- ══════════ À VOIR AUSSI ══════════ --}}
    @if($autres->isNotEmpty())
        <section style="padding:80px 0 40px;">
            <div class="sec-eyebrow">À voir aussi</div>
            <div class="sec-title">Autres biens de l'agence</div>
            <div class="listing-grid">
                @foreach($autres as $b)
                    <a class="listing-card" href="{{ route('vitrine.bien', [$agence->slug, $b->slug]) }}">
                        <div class="listing-media">
                            @if($b->photo_couverture)<img src="{{ $b->photo_couverture->url }}" alt="{{ $b->titre_fallback }}">@endif
                            <span class="listing-type">{{ $b->type_label }}</span>
                        </div>
                        <div class="listing-body">
                            <div class="listing-loc">{{ $b->quartier }}{{ $b->ville ? ', ' . $b->ville : '' }}</div>
                            <div class="listing-title">{{ Str::limit($b->titre_fallback, 42) }}</div>
                            <div class="listing-footer">
                                <div class="listing-price">{{ number_format((float) $b->loyer_mensuel, 0, ',', ' ') }} <span>FCFA / mois</span></div>
                                <div class="listing-arrow">→</div>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        </section>
    @endif
</div>
@endsection
