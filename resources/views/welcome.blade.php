@extends('layouts.vitrine')

@section('meta_title', 'Bimmo — Gérez votre agence immobilière, l\'esprit tranquille')
@section('meta_description', 'Biens, contrats, loyers et conformité fiscale sénégalaise (TVA, BRS, CGF, DGID) dans un seul outil pensé pour le droit sénégalais. 30 jours d\'essai gratuit.')

@php
    $inscriptionUrl = route('agency.register');
    $demoUrl        = 'https://wa.me/221781318176?text=' . rawurlencode('Bonjour, je souhaite une démo de Bimmo pour mon agence.'); // Démo → WhatsApp de contact.

    // Tarifs et limites réels — source unique : table `plans` via PlanService,
    // injectée par le View composer sous $plansGrille. Ils étaient écrits en dur
    // ici et se désynchronisaient de la page Tarifs et de la facturation réelle.
    $prix    = $plansGrille->map->prix_mensuel;
    $unites  = $plansGrille->map->limite_unites;
    $fmtPrix = fn ($n) => number_format((float) $n, 0, ',', ' ');
@endphp

@section('content')

{{-- ─────────────── HERO ─────────────── --}}
<section class="hero">
    <div class="wrap">
        <div class="hero-grid">
            <div>
                <div class="hero-badge"><span class="flag">🇸🇳</span> Conçu pour le marché sénégalais</div>
                <h1>Gérez votre agence immobilière, <em>l'esprit tranquille</em></h1>
                <p class="lede">Biens, contrats, loyers et conformité fiscale — dans un seul outil pensé pour le droit sénégalais. TVA, BRS, CGF, DGID : tout est déjà dans la loi, pas à paramétrer.</p>
                <div class="hero-ctas">
                    <a href="{{ $inscriptionUrl }}" class="btn btn-gold">Créer mon agence gratuitement →</a>
                    <a href="{{ $demoUrl }}" class="btn btn-ghost-onink">Demander une démo</a>
                </div>
                <div class="hero-trust">
                    <span><svg class="check-ico" viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="10" stroke="#E8C878" stroke-width="1.6"/><path d="M8 12.5l2.5 2.5L16 9" stroke="#E8C878" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>30 jours d'essai gratuit</span>
                    <span class="sep"></span>
                    <span>Aucune carte bancaire requise</span>
                    <span class="sep"></span>
                    <span>Accès immédiat</span>
                </div>
            </div>

            <div class="doc-stage">
                <div class="doc-card doc-back">
                    <div class="doc-head"><div><div class="t">Villa Almadies</div><div class="s">Fiscalité de ce bien</div></div><div class="doc-total">206 400 F</div></div>
                    <div class="doc-row"><span class="n">Taxe foncière</span><span class="d"></span><span class="v">51 000 F</span></div>
                    <div class="doc-row"><span class="n">Ordures ménagères</span><span class="d"></span><span class="v">155 400 F</span></div>
                </div>
                <div class="doc-card doc-mid">
                    <div class="doc-head"><div><div class="t">Aïssatou Diop</div><div class="s">Particulier · 3 biens</div></div><div class="doc-total">1 284 900 F</div></div>
                    <div class="doc-row"><span class="n">TVA</span><span class="d"></span><span class="v">64 800 F</span></div>
                    <div class="doc-row"><span class="n">BRS</span><span class="d"></span><span class="v">18 000 F</span></div>
                    <div class="doc-row"><span class="n">IRPP foncier</span><span class="d"></span><span class="v">354 000 F</span></div>
                </div>
                <div class="doc-card doc-front">
                    <div class="doc-head"><div><div class="t">Quittance de loyer</div><div class="s">Janvier 2027 · Studio Ouakam</div></div></div>
                    <div class="doc-row"><span class="n">Loyer</span><span class="d"></span><span class="v">200 000 F</span></div>
                    <div class="doc-row"><span class="n">Charges</span><span class="d"></span><span class="v">15 000 F</span></div>
                    <div class="doc-row" style="border-top:1.5px solid var(--ink); margin-top:4px; padding-top:12px;"><span class="n" style="font-weight:700;">Total payé</span><span class="d"></span><span class="v" style="font-size:14px;">215 000 F</span></div>
                </div>
                <div class="stamp">
                    <div class="stamp-inner">
                        <div class="stamp-text">CONFORME<span class="big">✓</span>CGI · SN</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ─────────────── LE CONSTAT ─────────────── --}}
<section class="section-tight" style="background:var(--paper-light);">
    <div class="wrap">
        <div class="section-head reveal">
            <div class="eyebrow">Le constat</div>
            <h2>La gestion locative au Sénégal repose encore sur Excel, WhatsApp et la mémoire</h2>
        </div>
        <div class="problem-list">
            <div class="problem-item reveal">
                <span class="num mono">01</span>
                <h3>Les retards de loyer sont la norme</h3>
                <p>Sans suivi centralisé, les relances se perdent et les propriétaires appellent pour savoir où en est leur bien.</p>
            </div>
            <div class="problem-item reveal">
                <span class="num mono">02</span>
                <h3>La fiscalité change souvent</h3>
                <p>TVA, BRS, CGF, droits d'enregistrement — les taux et échéances évoluent, et une erreur coûte cher.</p>
            </div>
            <div class="problem-item reveal">
                <span class="num mono">03</span>
                <h3>Les outils génériques ne collent pas</h3>
                <p>Les logiciels étrangers ignorent le droit sénégalais. Un tableur ne scale pas au-delà de quelques biens.</p>
            </div>
        </div>
    </div>
</section>

{{-- ─────────────── FONCTIONNALITÉS ─────────────── --}}
<section class="section" id="fonctionnalites" style="background:var(--paper);">
    <div class="wrap">
        <div class="section-head reveal">
            <div class="eyebrow">La solution</div>
            <h2>Tout ce dont votre agence a besoin, rien de superflu</h2>
            <p>Bimmo n'est pas un annuaire immobilier. C'est un outil de gestion professionnelle pour les agences qui veulent structurer leur activité.</p>
        </div>

        <div class="feature-row reveal">
            <div class="feature-copy">
                <div class="eyebrow">Biens &amp; contrats</div>
                <h3>Vos biens et vos baux, structurés dès le premier jour</h3>
                <p>Immeubles ou logements simples, particuliers ou entreprises — un formulaire qui s'adapte, pas dix formulaires différents.</p>
                <ul>
                    <li><svg width="16" height="16" viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="10" stroke="#B8892B" stroke-width="1.6"/><path d="M8 12.5l2.5 2.5L16 9" stroke="#B8892B" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>Statuts d'occupation en temps réel</li>
                    <li><svg width="16" height="16" viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="10" stroke="#B8892B" stroke-width="1.6"/><path d="M8 12.5l2.5 2.5L16 9" stroke="#B8892B" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>Contrats conformes loi n°88-04</li>
                </ul>
            </div>
            <div class="visual-card">
                <div class="visual-topbar"><div class="dot"></div><div class="dot"></div><div class="dot"></div><div class="url">app.bimotech.sn/biens</div></div>
                <div class="visual-body">
                    <div class="vb-title">Villa Almadies</div>
                    <div class="vb-sub">Almadies, Dakar · Loyer 850 000 FCFA/mois</div>
                    <div class="vb-tile-row">
                        <div class="vb-tile"><div class="l">Statut</div><div class="v2" style="font-size:14px;color:#2D5F4C;">Loué</div></div>
                        <div class="vb-tile"><div class="l">Occupation</div><div class="v2">100%</div></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="feature-row rev reveal">
            <div class="feature-copy">
                <div class="eyebrow">Paiements &amp; quittances</div>
                <h3>Chaque paiement génère sa quittance, automatiquement</h3>
                <p>Espèces, Wave, Orange Money ou virement — chaque encaissement produit un document légal, sans ressaisie.</p>
                <ul>
                    <li><svg width="16" height="16" viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="10" stroke="#B8892B" stroke-width="1.6"/><path d="M8 12.5l2.5 2.5L16 9" stroke="#B8892B" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>Retards groupés par niveau de gravité</li>
                    <li><svg width="16" height="16" viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="10" stroke="#B8892B" stroke-width="1.6"/><path d="M8 12.5l2.5 2.5L16 9" stroke="#B8892B" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>Relance WhatsApp en un clic</li>
                </ul>
            </div>
            <div class="visual-card">
                <div class="visual-topbar"><div class="dot"></div><div class="dot"></div><div class="dot"></div><div class="url">app.bimotech.sn/paiements</div></div>
                <div class="visual-body">
                    <div class="vb-title">Quittance générée</div>
                    <div class="vb-sub">Studio Ouakam · Janvier 2027</div>
                    <div class="vb-row"><span class="n">Loyer</span><span class="d"></span><span class="v">200 000 F</span></div>
                    <div class="vb-row"><span class="n">TOM</span><span class="d"></span><span class="v">13 500 F</span></div>
                    <span class="vb-badge"><span class="d2"></span>Payé aujourd'hui</span>
                </div>
            </div>
        </div>

        <div class="feature-row reveal">
            <div class="feature-copy">
                <div class="eyebrow">Espace propriétaire</div>
                <h3>Vos propriétaires consultent leurs revenus, sans vous appeler</h3>
                <p>Chaque propriétaire accède à ses biens, ses revenus nets et l'historique de ses paiements — en autonomie, en temps réel.</p>
                <ul>
                    <li><svg width="16" height="16" viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="10" stroke="#B8892B" stroke-width="1.6"/><path d="M8 12.5l2.5 2.5L16 9" stroke="#B8892B" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>Net après commission, calculé automatiquement</li>
                    <li><svg width="16" height="16" viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="10" stroke="#B8892B" stroke-width="1.6"/><path d="M8 12.5l2.5 2.5L16 9" stroke="#B8892B" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>Fiscalité estimée, expliquée en langage clair</li>
                </ul>
            </div>
            <div class="visual-card">
                <div class="visual-topbar"><div class="dot"></div><div class="dot"></div><div class="dot"></div><div class="url">app.bimotech.sn/proprietaires</div></div>
                <div class="visual-body">
                    <div class="vb-title">Fiscalité — Aïssatou Diop</div>
                    <div class="vb-sub">Total estimé cette année</div>
                    <div style="font-family:'Fraunces',serif; font-size:24px; font-weight:600; color:var(--ink); margin-bottom:14px;">1 284 900 <span style="font-size:13px; font-weight:500; color:var(--text-soft);">FCFA</span></div>
                    <div class="vb-row"><span class="n">IRPP foncier</span><span class="d"></span><span class="v">354 000 F</span></div>
                    <span class="vb-badge" style="background:#F3E6C8; color:#8a6620;"><span class="d2" style="background:#B8892B;"></span>Estimation</span>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ─────────────── CONFORMITÉ FISCALE (section signature sombre) ─────────────── --}}
<section class="onink compliance" id="conformite">
    <div class="wrap">
        <div class="compliance-grid">
            <div class="compliance-copy reveal">
                <div class="eyebrow">Le différenciateur</div>
                <h2>Le seul outil qui parle vraiment le droit fiscal sénégalais</h2>
                <p>Pas de modules à activer, pas de configuration complexe. Chaque règle est vérifiée, sourcée, et affichée avec son niveau de fiabilité — jamais un chiffre inventé en silence.</p>
                <div class="compliance-stamp-row">
                    <div class="mini-stamp"><svg viewBox="0 0 24 24" fill="none"><path d="M8 12.5l2.5 2.5L16 9" stroke="#E8C878" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg></div>
                    <div class="txt"><strong>Sources documentées</strong>DGID, CGI, textes officiels — traçables module par module.</div>
                </div>
            </div>
            <div class="compliance-list reveal">
                <div class="compliance-item">
                    <div class="ico"><svg width="17" height="17" viewBox="0 0 24 24" fill="none"><path d="M4 20h16M5 20V10l7-5 7 5v10" stroke="#E8C878" stroke-width="1.5" stroke-linejoin="round"/></svg></div>
                    <div><h4>TVA &amp; BRS</h4><p>Calcul automatique sur les loyers, décomposition claire, déclaration mensuelle avant le 15.</p></div>
                </div>
                <div class="compliance-item">
                    <div class="ico"><svg width="17" height="17" viewBox="0 0 24 24" fill="none"><path d="M4 4h16v4H4V4Z" stroke="#E8C878" stroke-width="1.5" stroke-linejoin="round"/><path d="M5 8v11a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V8" stroke="#E8C878" stroke-width="1.5"/></svg></div>
                    <div><h4>CGF, CFPB &amp; TEOM</h4><p>Régime simplifié ou réel, exclusion mutuelle gérée automatiquement — jamais de double calcul affiché.</p></div>
                </div>
                <div class="compliance-item">
                    <div class="ico"><svg width="17" height="17" viewBox="0 0 24 24" fill="none"><path d="M6 3h9l5 5v13a1 1 0 0 1-1 1H6a1 1 0 0 1-1-1V4a1 1 0 0 1 1-1Z" stroke="#E8C878" stroke-width="1.5" stroke-linejoin="round"/></svg></div>
                    <div><h4>Droits d'enregistrement</h4><p>Montant et échéance calculés dès la signature du bail, sans recherche manuelle.</p></div>
                </div>
                <div class="compliance-item">
                    <div class="ico"><svg width="17" height="17" viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="9" stroke="#E8C878" stroke-width="1.5"/><path d="M12 7v5l3 3" stroke="#E8C878" stroke-width="1.5" stroke-linecap="round"/></svg></div>
                    <div><h4>Calendrier des échéances</h4><p>Toutes les déclarations à venir, triées par urgence, pour toutes vos agences et tous vos biens.</p></div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ─────────────── DÉMARRAGE ─────────────── --}}
<section class="section" style="background:var(--paper-light);">
    <div class="wrap">
        <div class="section-head reveal">
            <div class="eyebrow">Démarrage</div>
            <h2>Opérationnel en moins de 10 minutes</h2>
        </div>
        <div class="steps">
            <div class="step reveal">
                <div class="step-num">1</div>
                <h3>Créez votre agence</h3>
                <p>Nom, NINEA, coordonnées. Votre espace est immédiatement actif et sécurisé.</p>
            </div>
            <div class="step reveal">
                <div class="step-num">2</div>
                <h3>Ajoutez vos biens</h3>
                <p>Importez votre portefeuille avec photos, loyers et propriétaires — ou saisissez-le au fil de l'eau.</p>
            </div>
            <div class="step reveal">
                <div class="step-num">3</div>
                <h3>Gérez en temps réel</h3>
                <p>Paiements, quittances, fiscalité — tout se génère automatiquement depuis votre tableau de bord.</p>
            </div>
        </div>
    </div>
</section>

{{-- ─────────────── VITRINE — site web intelligent par agence ─────────────── --}}
<section class="onink compliance" id="vitrine">
    <div class="wrap">
        <div class="compliance-grid">
            <div class="compliance-copy reveal">
                <div class="eyebrow">Inclus avec votre abonnement</div>
                <h2>Votre agence a désormais son site web — intelligent</h2>
                <p>Dès l'inscription, Bimmo génère le site vitrine de votre agence. Et il vit tout seul : un bien disponible est publié automatiquement, un bien loué est retiré automatiquement. Zéro webmaster, zéro maintenance — votre site suit votre gestion en temps réel.</p>
                <div class="compliance-stamp-row">
                    <div class="mini-stamp"><svg viewBox="0 0 24 24" fill="none"><path d="M8 12.5l2.5 2.5L16 9" stroke="#E8C878" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg></div>
                    <div class="txt"><strong>Une adresse à vous</strong>immo.bimotechsn.com/agences/votre-agence — à partager sur WhatsApp, vos cartes de visite, vos réseaux.</div>
                </div>
                <div class="hero-ctas">
                    <a href="https://immo.bimotechsn.com/agences/bimo-tech-yHw9By" target="_blank" rel="noopener" class="btn btn-ghost-onink">Voir un exemple →</a>
                    <a href="{{ $inscriptionUrl }}" class="btn btn-gold">Créer mon agence →</a>
                </div>
            </div>
            <div class="compliance-list reveal">
                <div class="compliance-item">
                    <div class="ico"><svg width="17" height="17" viewBox="0 0 24 24" fill="none"><path d="M12 3l8 4.5v9L12 21l-8-4.5v-9L12 3Z" stroke="#E8C878" stroke-width="1.5" stroke-linejoin="round"/><path d="M12 12l8-4.5M12 12v9M12 12L4 7.5" stroke="#E8C878" stroke-width="1.5" stroke-linejoin="round"/></svg></div>
                    <div><h4>Généré à l'inscription</h4><p>Logo, contact WhatsApp, slogan : votre site reprend l'identité de votre agence, sans rien configurer.</p></div>
                </div>
                <div class="compliance-item">
                    <div class="ico"><svg width="17" height="17" viewBox="0 0 24 24" fill="none"><path d="M21 12a9 9 0 1 1-2.6-6.3" stroke="#E8C878" stroke-width="1.5" stroke-linecap="round"/><path d="M21 4v5h-5" stroke="#E8C878" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg></div>
                    <div><h4>Toujours à jour, automatiquement</h4><p>Vous louez un bien dans Bimmo ? Il disparaît du site. Il se libère ? Il revient. Sans aucune action de votre part.</p></div>
                </div>
                <div class="compliance-item">
                    <div class="ico"><svg width="17" height="17" viewBox="0 0 24 24" fill="none"><rect x="3" y="4" width="18" height="14" rx="2" stroke="#E8C878" stroke-width="1.5"/><path d="M3 9h18M8 21h8" stroke="#E8C878" stroke-width="1.5" stroke-linecap="round"/></svg></div>
                    <div><h4>Vos biens mis en valeur</h4><p>Photos, quartiers, biens en vedette, filtres par type — et contact WhatsApp direct depuis chaque fiche.</p></div>
                </div>
                <div class="compliance-item">
                    <div class="ico"><svg width="17" height="17" viewBox="0 0 24 24" fill="none"><circle cx="11" cy="11" r="7" stroke="#E8C878" stroke-width="1.5"/><path d="M20 20l-4-4" stroke="#E8C878" stroke-width="1.5" stroke-linecap="round"/></svg></div>
                    <div><h4>Visible sur Google</h4><p>Chaque vitrine et chaque bien sont référencés automatiquement — vos annonces travaillent pour vous.</p></div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ─────────────── TARIFS (aperçu) ─────────────── --}}
<section class="section" id="tarifs" style="background:var(--paper);">
    <div class="wrap">
        <div class="section-head reveal">
            <div class="eyebrow">Tarifs</div>
            <h2>Starter, Pro ou Agence — 30 jours d'essai gratuit</h2>
            <p>Aucune carte bancaire. Choisissez votre plan après l'essai.</p>
        </div>
        <div class="pricing-grid">
            <div class="price-card reveal">
                <div class="price-tier">Starter</div>
                <div class="price-amount">{{ $fmtPrix($prix['starter'] ?? 0) }}<span class="u">FCFA / mois</span></div>
                <p class="price-desc">Jusqu'à {{ $unites['starter'] ?? 15 }} unités — Dashboard, biens, contrats, paiements, impayés, quittances PDF.</p>
                <a href="{{ $inscriptionUrl }}" class="btn btn-ghost-ink">Démarrer gratuitement</a>
            </div>
            <div class="price-card featured reveal">
                <div class="price-badge">★ Recommandé</div>
                <div class="price-tier">Pro</div>
                <div class="price-amount">{{ $fmtPrix($prix['pro'] ?? 0) }}<span class="u">FCFA / mois</span></div>
                <p class="price-desc">Jusqu'à {{ $unites['pro'] ?? 50 }} unités — tout Starter, plus immeubles, rapports PDF, import Excel, relevés propriétaires.</p>
                <a href="{{ $inscriptionUrl }}" class="btn btn-gold">Démarrer — 30 jours gratuits</a>
            </div>
            <div class="price-card reveal">
                <div class="price-tier">Agence</div>
                <div class="price-amount">{{ $fmtPrix($prix['agence'] ?? 0) }}<span class="u">FCFA / mois</span></div>
                <p class="price-desc">Unités illimitées — tout Pro, plus déclarations DGID, bilans fiscaux, logs d'activité, support prioritaire.</p>
                <a href="{{ $inscriptionUrl }}" class="btn btn-ghost-ink">Démarrer gratuitement</a>
            </div>
        </div>
        <div class="pricing-note reveal">Besoin de plus de détails ? <a href="{{ route('tarifs') }}">Comparer tous les plans →</a></div>
    </div>
</section>

{{-- ─────────────── FAQ ─────────────── --}}
<section class="section" id="faq" style="background:var(--paper-light);">
    <div class="wrap">
        <div class="section-head reveal">
            <div class="eyebrow">Questions fréquentes</div>
            <h2>Ce que les agences nous demandent le plus souvent</h2>
        </div>
        <div class="faq-list reveal">
            <div class="faq-item">
                <button class="faq-q">Est-ce compatible avec mon comptable actuel ?<span class="plus"></span></button>
                <div class="faq-a"><p>Oui. Bimmo calcule automatiquement la fiscalité liée à vos loyers et vos biens, mais ne remplace pas la comptabilité générale de votre agence. Votre comptable garde son rôle — l'outil lui simplifie le travail en lui fournissant des données déjà organisées.</p></div>
            </div>
            <div class="faq-item">
                <button class="faq-q">Que se passe-t-il si j'arrête mon abonnement ?<span class="plus"></span></button>
                <div class="faq-a"><p>Vos données restent accessibles en lecture pendant une période de grâce. Aucun engagement n'est requis à l'inscription, et vous pouvez exporter vos données à tout moment.</p></div>
            </div>
            <div class="faq-item">
                <button class="faq-q">Les montants fiscaux affichés sont-ils garantis exacts ?<span class="plus"></span></button>
                <div class="faq-a"><p>Chaque montant affiche son niveau de fiabilité. Certains sont calculés directement depuis vos contrats (fiables), d'autres sont des estimations basées sur le loyer, clairement indiquées comme telles — le montant réel dépend parfois de l'administration fiscale elle-même.</p></div>
            </div>
            <div class="faq-item">
                <button class="faq-q">Puis-je gérer plusieurs agences avec un seul compte ?<span class="plus"></span></button>
                <div class="faq-a"><p>Chaque agence dispose de son propre espace isolé et sécurisé. Si vous gérez plusieurs structures, contactez-nous pour organiser vos accès.</p></div>
            </div>
            <div class="faq-item">
                <button class="faq-q">Comment mes locataires et propriétaires sont-ils informés ?<span class="plus"></span></button>
                <div class="faq-a"><p>Via des liens WhatsApp directs, jamais d'automatisation intrusive. Vos propriétaires ont aussi un accès autonome à leurs revenus et documents.</p></div>
            </div>
        </div>
    </div>
</section>

{{-- ─────────────── CTA FINAL ─────────────── --}}
<section class="onink final-cta">
    <div class="wrap">
        <h2>Rejoignez les agences qui gèrent mieux</h2>
        <p>Votre agence est en ligne en moins de 10 minutes. Aucune installation, aucun engagement.</p>
        <div class="hero-ctas">
            <a href="{{ $inscriptionUrl }}" class="btn btn-gold">Créer mon agence gratuitement →</a>
            <a href="{{ $demoUrl }}" class="btn btn-ghost-onink">Demander une démo</a>
        </div>
    </div>
</section>

@endsection
