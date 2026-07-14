@extends('layouts.vitrine')

@section('meta_title', 'Tarifs — Bimmo')
@section('meta_description', 'Starter, Pro ou Agence — 30 jours d\'essai gratuit, sans carte bancaire. Les plans se distinguent par les limites d\'usage, pas par les fonctionnalités.')

@php
    $inscriptionUrl = route('agency.register');

    // Limites d'usage réelles — source unique : config/plans.php (logique produit :
    // gating par USAGE, pas par fonctionnalité — features => [] neutralise check.feature).
    $unites = config('plans.nb_unites_max');   // ['starter'=>15,'pro'=>50,'agence'=>null,...]
    $admins = config('plans.nb_admins_max');   // ['starter'=>2,'pro'=>5,'agence'=>null,...]
    $ill = fn ($v) => $v === null ? 'Illimité' : (string) $v;

    // Toutes les fonctionnalités sont incluses dans TOUS les plans (décision produit).
    $fonctionnalites = [
        'Tableau de bord', 'Biens & logements', 'Immeubles', 'Contrats de bail',
        'Paiements & encaissements', 'Suivi des impayés', 'Quittances PDF',
        'Rapports PDF', 'Import Excel', 'Relevés propriétaires',
        'Déclarations & échéances DGID', 'Bilans fiscaux', "Journal d'activité",
    ];
    $check = '<svg class="compare-check" width="17" height="17" viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="10" stroke="#2D5F4C" stroke-width="1.5"/><path d="M8 12.5l2.5 2.5L16 9" stroke="#2D5F4C" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>';
@endphp

@section('content')

<section class="section" style="background:var(--paper-light); padding-top:150px;">
    <div class="wrap">
        <div class="section-head reveal" style="max-width:680px; margin-bottom:44px;">
            <div class="eyebrow">Tarifs</div>
            <h2>Un tarif simple, qui grandit avec votre agence</h2>
            <p>30 jours d'essai gratuit, sans carte bancaire. Toutes les fonctionnalités sont incluses dans chaque plan — vous ne payez que pour la taille de votre portefeuille.</p>
        </div>

        {{-- Cartes de tarifs (identiques à l'accueil) --}}
        <div class="pricing-grid reveal">
            <div class="price-card">
                <div class="price-tier">Starter</div>
                <div class="price-amount">25 000<span class="u">FCFA / mois</span></div>
                <p class="price-desc">Jusqu'à {{ $ill($unites['starter'] ?? 15) }} unités — Dashboard, biens, contrats, paiements, impayés, quittances PDF.</p>
                <a href="{{ $inscriptionUrl }}" class="btn btn-ghost-ink">Démarrer gratuitement</a>
            </div>
            <div class="price-card featured">
                <div class="price-badge">★ Recommandé</div>
                <div class="price-tier">Pro</div>
                <div class="price-amount">50 000<span class="u">FCFA / mois</span></div>
                <p class="price-desc">Jusqu'à {{ $ill($unites['pro'] ?? 50) }} unités — tout Starter, plus immeubles, rapports PDF, import Excel, relevés propriétaires.</p>
                <a href="{{ $inscriptionUrl }}" class="btn btn-gold">Démarrer — 30 jours gratuits</a>
            </div>
            <div class="price-card">
                <div class="price-tier">Agence</div>
                <div class="price-amount">90 000<span class="u">FCFA / mois</span></div>
                <p class="price-desc">Unités illimitées — tout Pro, plus déclarations DGID, bilans fiscaux, logs d'activité, support prioritaire.</p>
                <a href="{{ $inscriptionUrl }}" class="btn btn-ghost-ink">Démarrer gratuitement</a>
            </div>
        </div>
    </div>
</section>

{{-- ─────────────── TABLEAU COMPARATIF (usage, pas fonctionnalités verrouillées) ─────────────── --}}
<section class="section" style="background:var(--paper); padding-top:0;">
    <div class="wrap">
        <div class="section-head reveal">
            <div class="eyebrow">Comparatif détaillé</div>
            <h2>Ce que change chaque plan</h2>
            <p>La différence entre les plans porte sur les <em>limites d'usage</em> — nombre de biens et de comptes. Les fonctionnalités, elles, sont les mêmes partout.</p>
        </div>

        <div class="table-scroll reveal">
            <table class="compare-table">
                <thead>
                    <tr>
                        <th>&nbsp;</th>
                        <th class="plan">Starter</th>
                        <th class="plan featured">Pro</th>
                        <th class="plan">Agence</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="compare-group-row"><td colspan="4">Limites d'usage</td></tr>
                    <tr>
                        <td class="feature">Unités (biens actifs)</td>
                        <td class="cell">{{ $ill($unites['starter'] ?? 15) }}</td>
                        <td class="cell featured">{{ $ill($unites['pro'] ?? 50) }}</td>
                        <td class="cell">{{ $ill($unites['agence'] ?? null) }}</td>
                    </tr>
                    <tr>
                        <td class="feature">Comptes équipe (directeur inclus)</td>
                        <td class="cell">{{ $ill($admins['starter'] ?? 2) }}</td>
                        <td class="cell featured">{{ $ill($admins['pro'] ?? 5) }}</td>
                        <td class="cell">{{ $ill($admins['agence'] ?? null) }}</td>
                    </tr>

                    <tr class="compare-group-row"><td colspan="4">Fonctionnalités (incluses dans tous les plans)</td></tr>
                    @foreach($fonctionnalites as $f)
                        <tr>
                            <td class="feature">{{ $f }}</td>
                            <td class="cell">{!! $check !!}</td>
                            <td class="cell featured">{!! $check !!}</td>
                            <td class="cell">{!! $check !!}</td>
                        </tr>
                    @endforeach

                    <tr class="compare-group-row"><td colspan="4">Accompagnement</td></tr>
                    <tr>
                        <td class="feature">Support</td>
                        <td class="cell">Standard</td>
                        <td class="cell featured">Standard</td>
                        <td class="cell">Prioritaire</td>
                    </tr>
                </tbody>
            </table>
        </div>
        <p class="pricing-note reveal" style="text-align:left; margin-top:18px;">Toutes les fonctionnalités sont disponibles quel que soit le plan. Vous changez de plan uniquement quand votre portefeuille dépasse la limite d'unités — jamais pour débloquer une fonction.</p>
    </div>
</section>

{{-- ─────────────── FAQ TARIFS ─────────────── --}}
<section class="section" style="background:var(--paper-light); padding-top:0;">
    <div class="wrap">
        <div class="section-head reveal">
            <div class="eyebrow">Facturation</div>
            <h2>Questions sur les tarifs</h2>
        </div>
        <div class="faq-list reveal">
            <div class="faq-item">
                <button class="faq-q">Quels moyens de paiement acceptez-vous ?<span class="plus"></span></button>
                <div class="faq-a"><p>Wave, Orange Money et virement bancaire. L'abonnement se règle mensuellement, sans engagement de durée.</p></div>
            </div>
            <div class="faq-item">
                <button class="faq-q">Puis-je changer de plan à tout moment ?<span class="plus"></span></button>
                <div class="faq-a"><p>Oui. Vous pouvez monter ou descendre de plan quand vous le souhaitez. Comme les fonctionnalités sont identiques partout, changer de plan ne fait qu'ajuster votre limite d'unités et de comptes.</p></div>
            </div>
            <div class="faq-item">
                <button class="faq-q">Que se passe-t-il pendant l'essai gratuit ?<span class="plus"></span></button>
                <div class="faq-a"><p>Vous accédez à l'ensemble du produit pendant 30 jours, sans carte bancaire. À la fin de l'essai, vous choisissez le plan adapté à la taille de votre portefeuille — ou vous vous arrêtez, sans frais.</p></div>
            </div>
            <div class="faq-item">
                <button class="faq-q">Que se passe-t-il si je dépasse ma limite d'unités ?<span class="plus"></span></button>
                <div class="faq-a"><p>Vous êtes invité à passer au plan supérieur pour continuer à ajouter des biens. Vos données existantes restent toujours accessibles.</p></div>
            </div>
        </div>
    </div>
</section>

{{-- ─────────────── CTA FINAL ─────────────── --}}
<section class="onink final-cta">
    <div class="wrap">
        <h2>Prêt à structurer votre agence ?</h2>
        <p>30 jours d'essai gratuit, sans carte bancaire. Votre espace est prêt en moins de 10 minutes.</p>
        <div class="hero-ctas">
            <a href="{{ $inscriptionUrl }}" class="btn btn-gold">Créer mon agence gratuitement →</a>
            <a href="{{ route('home') }}#faq" class="btn btn-ghost-onink">Voir la FAQ générale</a>
        </div>
    </div>
</section>

@endsection
