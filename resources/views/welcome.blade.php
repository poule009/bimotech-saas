<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="description" content="BimoTech Immo — Logiciel de gestion immobilière pour agences au Sénégal. Biens, contrats, loyers, quittances conformes TVA 18%, BRS, DGID. Essai gratuit 30 jours.">
<meta name="keywords" content="gestion immobilière Sénégal, logiciel agence immobilière Dakar, quittance loyer Sénégal, TVA immobilier CGI SN">
<meta name="robots" content="index, follow">
<link rel="canonical" href="https://immo.bimotechsn.com/">
<title>BimoTech Immo — Gestion immobilière Sénégal</title>
<meta property="og:type" content="website">
<meta property="og:url" content="https://immo.bimotechsn.com/">
<meta property="og:title" content="BimoTech Immo — Gestion immobilière professionnelle au Sénégal">
<meta property="og:description" content="Gérez biens, contrats, loyers et quittances conformes TVA 18% et CGI SN. Essai gratuit 30 jours, sans carte bancaire.">
<meta property="og:image" content="https://immo.bimotechsn.com/og-image.png">
<meta property="og:locale" content="fr_SN">
<meta property="og:site_name" content="BimoTech Immo">
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="BimoTech Immo — Logiciel gestion immobilière Sénégal">
<meta name="twitter:description" content="Biens, contrats, loyers, quittances. Conforme TVA 18%, BRS, DGID. Essai 30 jours gratuit.">
<meta name="twitter:image" content="https://immo.bimotechsn.com/og-image.png">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:ital,wght@0,300;0,400;0,500;0,600;1,300&display=swap" media="print" onload="this.media='all'">
<noscript><link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:ital,wght@0,300;0,400;0,500;0,600;1,300&display=swap"></noscript>
@vite(['resources/css/app.css', 'resources/js/app.js'])
<style>@keyframes fadeUp{from{opacity:0;transform:translateY(22px)}to{opacity:1;transform:translateY(0)}}@keyframes pulse-ring{0%{box-shadow:0 0 0 0 rgba(201,168,76,.5)}70%{box-shadow:0 0 0 8px rgba(201,168,76,0)}100%{box-shadow:0 0 0 0 rgba(201,168,76,0)}}@keyframes wa-pop{from{opacity:0;transform:scale(.5) translateY(20px)}to{opacity:1;transform:scale(1) translateY(0)}}.fade-up{animation:fadeUp .6s ease both}.pulse{animation:pulse-ring 2s infinite}</style>
</head>
<body class="font-body bg-[#f9fafb] text-[#111111] antialiased overflow-x-hidden [-webkit-font-smoothing:antialiased]">

@include('partials.public-nav', ['active' => ''])

{{-- ─── HERO ───────────────────────────────────────────── --}}
<section class="min-h-screen flex items-center justify-center flex-col text-center px-[5%] pt-[100px] pb-20 bg-white relative overflow-hidden">
    <div class="relative z-10 max-w-[820px] w-full">

        <div class="inline-flex items-center gap-2 bg-bimo-gold/[8%] border border-bimo-gold/25 rounded-full px-4 py-1.5 font-body font-medium text-xs text-bimo-gold mb-8 fade-up">
            <div class="w-[7px] h-[7px] bg-bimo-gold rounded-full pulse flex-shrink-0"></div>
            🇸🇳 Conçu pour le marché sénégalais
        </div>

        <h1 class="font-display font-extrabold text-[clamp(38px,6.5vw,76px)] leading-[1.04] tracking-[-2.5px] mb-6 fade-up" style="animation-delay:.08s">
            Gérez votre agence<br>immobilière, <em class="not-italic text-bimo-gold">l'esprit<br>tranquille</em>
        </h1>

        <p class="font-body text-[clamp(15px,2vw,18px)] text-[#9ca3af] max-w-[540px] mx-auto mb-10 leading-[1.75] fade-up" style="animation-delay:.16s">
            Biens, contrats, loyers, quittances et conformité fiscale — tout en un seul outil. Conforme TVA 18%, NINEA, loi 81-18 et CGI article 357.
        </p>

        <div class="flex items-center justify-center gap-3 flex-wrap mb-6 fade-up" style="animation-delay:.24s">
            <a href="{{ route('agency.register') }}"
               class="inline-flex items-center gap-1.5 font-body font-bold text-sm text-white no-underline px-7 py-3.5 rounded-[10px] bg-bimo-gold hover:opacity-95 hover:-translate-y-0.5 transition-all duration-200 shadow-md">
                Créer mon agence gratuitement
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
            </a>
            <a href="{{ route('demo') }}"
               class="inline-flex items-center gap-1.5 font-body font-medium text-sm text-[#111111] no-underline px-7 py-3.5 rounded-[10px] bg-white border border-[#d1d5db] hover:border-bimo-gold hover:text-bimo-gold hover:-translate-y-0.5 transition-all duration-200 shadow-sm">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="5 3 19 12 5 21 5 3"/></svg>
                Demander une démo
            </a>
        </div>
        <p class="font-body text-xs text-[#9ca3af] fade-up" style="animation-delay:.32s">30 jours d'essai gratuit · <span class="text-bimo-gold">Aucune carte bancaire requise</span> · Accès immédiat</p>

        {{-- Mockup navigateur --}}
        <div class="mt-16 relative max-w-[960px] w-full mx-auto fade-up" style="animation-delay:.4s">
            <div class="bg-[#222] border border-[#d1d5db] rounded-[16px] overflow-hidden shadow-[0_0_0_1px_rgba(255,255,255,.04),0_60px_120px_rgba(0,0,0,.7),inset_0_1px_0_rgba(255,255,255,.08)]">
                {{-- Browser bar --}}
                <div class="bg-[#2a2a2a] px-4 py-3 flex items-center gap-2.5 border-b border-white/[8%]">
                    <div class="flex gap-1.5">
                        <div class="w-[11px] h-[11px] rounded-full bg-[#ff5f57]"></div>
                        <div class="w-[11px] h-[11px] rounded-full bg-[#febc2e]"></div>
                        <div class="w-[11px] h-[11px] rounded-full bg-[#28c840]"></div>
                    </div>
                    <div class="flex-1 mx-3 bg-black/30 border border-white/[8%] rounded-[6px] px-3 py-1.5 flex items-center gap-1.5">
                        <span class="text-bimo-gold opacity-60">
                            <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0110 0v4"/></svg>
                        </span>
                        <span class="font-body text-[11px] text-[#9ca3af]" style="font-family:monospace">app.bimotech.sn/dashboard</span>
                    </div>
                </div>
                {{-- Browser content --}}
                <div class="grid md:grid-cols-[190px_1fr] min-h-[340px]">
                    {{-- Sidebar --}}
                    <div class="hidden md:flex flex-col gap-0.5 bg-[#1a1a1a] border-r border-white/[8%] p-3">
                        <div class="flex items-center gap-1.5 font-display font-extrabold text-[13px] text-bimo-gold px-2 py-1 mb-4">
                            <div class="w-2 h-2 bg-bimo-gold rounded-[2px]"></div>BimoTech
                        </div>
                        @foreach([['active','<rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/>','Dashboard'],['','<path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/>','Biens'],['','<path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/>','Contrats'],['','<rect x="1" y="4" width="22" height="16" rx="2"/><line x1="1" y1="10" x2="23" y2="10"/>','Paiements'],['','<polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/>','Rapports'],['','<path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/>','Locataires']] as [$a,$icon,$lbl])
                        <div class="flex items-center gap-2 px-2.5 py-2 rounded-[7px] font-body text-[11.5px] {{ $a === 'active' ? 'bg-bimo-gold/[8%] text-bimo-gold font-semibold' : 'text-[#6b7280]' }}">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="flex-shrink-0 opacity-70">{!! $icon !!}</svg>
                            {{ $lbl }}
                        </div>
                        @endforeach
                    </div>
                    {{-- Main --}}
                    <div class="bg-[#1a1a1a] p-5 flex flex-col gap-3.5">
                        <div>
                            <div class="font-display font-bold text-sm text-[#e5e7eb] mb-0.5">Tableau de bord</div>
                            <div class="font-body text-[11px] text-[#6b7280]">Agence Fann Immo · Avril 2026</div>
                        </div>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-2.5">
                            @foreach([['border-bimo-gold','text-bimo-gold','Loyers encaissés','3,2M','FCFA ce mois'],['border-[#4ade80]','text-[#4ade80]','Biens occupés','18/20','90% d\'occupation'],['border-[#f87171]','text-[#f87171]','Impayés','2','Relances envoyées'],['border-[#a78bfa]','text-[#a78bfa]','Net propriétaires','2,7M','Après commission']] as [$bc,$vc,$lbl,$val,$sub])
                            <div class="bg-[#222] border border-white/[8%] rounded-[10px] p-3 border-t-2 {{ $bc }}">
                                <div class="font-body text-[9.5px] text-[#9ca3af] uppercase tracking-[.6px] mb-1.5">{{ $lbl }}</div>
                                <div class="font-display font-extrabold text-[18px] leading-none {{ $vc }}">{{ $val }}</div>
                                <div class="font-body text-[9px] text-[#9ca3af] mt-1">{{ $sub }}</div>
                            </div>
                            @endforeach
                        </div>
                        <div class="hidden md:block bg-[#222] border border-white/[8%] rounded-[10px] overflow-hidden">
                            <div class="grid grid-cols-5 px-3.5 py-2 bg-white/[2%] border-b border-white/[6%] font-body text-[9px] text-[#9ca3af] uppercase tracking-[.7px]">
                                <span>Locataire</span><span>Bien</span><span>Montant</span><span>Mode</span><span>Statut</span>
                            </div>
                            @foreach([['A. Diallo','Villa Fann','350 000','Wave','Payé','paid'],['M. Ndiaye','Appt Plateau','280 000','Virement','Attente','pending'],['F. Sow','Bureau Almadies','520 000','Chèque','Payé','paid'],['I. Ba','Studio Mermoz','185 000','OM','Retard','late']] as [$n,$b,$m,$md,$st,$sc])
                            <div class="grid grid-cols-5 px-3.5 py-2.5 border-b border-white/[3%] last:border-0 font-body text-[10.5px] text-[#6b7280] items-center">
                                <span>{{ $n }}</span><span>{{ $b }}</span><span>{{ $m }}</span><span>{{ $md }}</span>
                                <span class="inline-block font-body font-semibold text-[9px] px-2 py-0.5 rounded-full {{ $sc === 'paid' ? 'bg-[rgba(34,197,94,.12)] text-[#4ade80]' : ($sc === 'late' ? 'bg-[rgba(239,68,68,.1)] text-[#f87171]' : 'bg-[rgba(234,179,8,.1)] text-[#fbbf24]') }}">{{ $st }}</span>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ─── TRUST BAR ──────────────────────────────────────── --}}
<div class="px-[5%] py-10 border-t border-b border-[#e5e7eb] bg-[#eef0f2]">
    <div class="max-w-[1000px] mx-auto flex flex-wrap items-center justify-center gap-10">
        <div class="font-body font-semibold text-[11px] text-[#6b7280] uppercase tracking-[1.5px] whitespace-nowrap">Pourquoi BimoTech</div>
        <div class="w-px h-7 bg-[#d1d5db] flex-shrink-0 hidden md:block"></div>
        <div class="flex flex-wrap items-center justify-center gap-8 md:gap-12">
            @foreach([['Dakar · Thiès · Régions','Agences partout au Sénégal',15],['Wave · Orange Money · Virement','Tous modes de paiement inclus',13],['98%','Taux de recouvrement',22],['TVA · BRS · TOM · DGID','Conformité fiscale CGI SN',13]] as [$n,$d,$fs])
            <div class="text-center">
                <div class="font-display font-extrabold text-bimo-gold leading-none" style="font-size:{{ $fs }}px;letter-spacing:-.3px">{{ $n }}</div>
                <div class="font-body text-[11px] text-[#6b7280] mt-1">{{ $d }}</div>
            </div>
            @endforeach
        </div>
    </div>
</div>

{{-- ─── VIDÉO DÉMO ─────────────────────────────────────── --}}
<section class="px-[5%] py-20">
    <div class="max-w-[860px] mx-auto">
        <div class="text-center mb-10">
            <div class="inline-flex items-center gap-1.5 font-body font-semibold text-[11px] text-bimo-gold uppercase tracking-[2px] mb-4">
                <span class="w-4 h-px bg-bimo-gold inline-block"></span>Démonstration
            </div>
            <h2 class="font-display font-extrabold text-[clamp(1.6rem,3vw,2.2rem)] tracking-tight mb-3">La gestion locative <em class="not-italic text-bimo-gold">automatisée</em></h2>
            <p class="font-body text-[15px] text-[#6b7280] max-w-[520px] mx-auto leading-relaxed">Découvrez comment les agences modernes automatisent leur gestion — loyers, quittances, relances — en quelques clics.</p>
        </div>
        @php $videoUrl = 'https://www.youtube.com/embed/1CqDVBCsT78'; @endphp
        <div class="rounded-[18px] overflow-hidden border border-[#e5e7eb] shadow-xl bg-[#0d1117] mt-10">
            @if($videoUrl)
            <div class="relative pb-[56.25%] h-0">
                <iframe src="{{ $videoUrl }}" class="absolute top-0 left-0 w-full h-full border-0"
                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
            </div>
            @else
            <div class="flex flex-col items-center justify-center py-20 px-8 gap-6">
                <div class="w-[72px] h-[72px] rounded-full bg-bimo-gold/[8%] border border-bimo-gold/30 flex items-center justify-center cursor-pointer">
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="#C9A84C" stroke="none"><polygon points="5 3 19 12 5 21 5 3"/></svg>
                </div>
                <div class="text-center">
                    <div class="font-display font-bold text-base text-[#e6edf3] mb-1.5">Démo disponible sur demande</div>
                    <div class="font-body text-[13px] text-[#6e7681]">Obtenez une démonstration personnalisée avec un expert BimoTech</div>
                </div>
                <a href="{{ route('demo') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-bimo-gold/[8%] border border-bimo-gold/20 rounded-[10px] text-bimo-gold font-body font-semibold text-[13px] no-underline hover:bg-bimo-gold/15 transition-colors duration-200">
                    Demander une démo gratuite →
                </a>
            </div>
            @endif
        </div>
    </div>
</section>

{{-- ─── FONCTIONNALITÉS ───────────────────────────────── --}}
<section class="px-[5%] py-24" id="fonctionnalites">
    <div class="max-w-[1040px] mx-auto">
        <div class="mb-14">
            <div class="inline-flex items-center gap-1.5 font-body font-semibold text-[11px] text-bimo-gold uppercase tracking-[2px] mb-4">
                <span class="w-4 h-px bg-bimo-gold inline-block"></span>Fonctionnalités
            </div>
            <h2 class="font-display font-extrabold text-[clamp(28px,4vw,46px)] tracking-tight leading-[1.08] mb-4">Tout ce dont votre agence<br>a besoin, <em class="not-italic text-bimo-gold">rien de superflu</em></h2>
            <p class="font-body text-[15px] text-[#6b7280] max-w-[540px] leading-relaxed">BimoTech n'est pas un annuaire immobilier. C'est un outil de gestion professionnelle pour les agences qui veulent structurer leur activité.</p>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach([
                ['<path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><polyline points="9,22 9,12 15,12 15,22"/>','Gestion des biens','Ajoutez vos biens avec photos, surface, type et loyer. Suivez l\'occupation en temps réel avec des statuts clairs.'],
                ['<path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14,2 14,8 20,8"/>','Contrats de bail','Créez vos contrats de bail vide ou meublé. Durée, dépôt de garantie, clauses — tout est archivé et accessible.'],
                ['<rect x="1" y="4" width="22" height="16" rx="2"/><line x1="1" y1="10" x2="23" y2="10"/>','Encaissement des loyers','Espèces, virement, Wave, Orange Money, Free Money. Chaque paiement génère automatiquement une quittance légale.'],
                ['<path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87"/><path d="M16 3.13a4 4 0 010 7.75"/>','Espace propriétaire','Chaque propriétaire accède à ses biens, ses revenus nets et l\'historique de ses paiements. En temps réel, sans appel.'],
                ['<path d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z"/>','Espace locataire','Vos locataires accèdent à leurs quittances, suivent leur bail et contactent l\'agence via WhatsApp en un clic.'],
                ['<polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/>','Rapports financiers','Commission HT/TTC, TVA, TOM, BRS, net propriétaire. Bilans mensuels et annuels exportables avec taux d\'occupation.'],
            ] as [$icon,$title,$desc])
            <div class="bg-white border border-[#e5e7eb] rounded-[14px] p-8 hover:-translate-y-1 hover:shadow-lg hover:border-[#d1d5db] transition-all duration-200">
                <div class="w-11 h-11 bg-bimo-gold/[8%] border border-[#e5e7eb] rounded-[12px] flex items-center justify-center mb-5">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#C9A84C" stroke-width="1.8">{!! $icon !!}</svg>
                </div>
                <h3 class="font-display font-bold text-[15px] text-[#111111] mb-2">{{ $title }}</h3>
                <p class="font-body text-[13px] text-[#6b7280] leading-[1.7]">{{ $desc }}</p>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ─── TÉMOIGNAGES ───────────────────────────────────── --}}
<div class="px-[5%] py-24 bg-[#f3f4f6] border-t border-b border-[#e5e7eb]">
    <div class="max-w-[1040px] mx-auto">
        <div class="mb-14">
            <div class="inline-flex items-center gap-1.5 font-body font-semibold text-[11px] text-bimo-gold uppercase tracking-[2px] mb-4">
                <span class="w-4 h-px bg-bimo-gold inline-block"></span>Témoignages
            </div>
            <h2 class="font-display font-extrabold text-[clamp(28px,4vw,46px)] tracking-tight leading-[1.08]">Ce que disent les agences<br>qui utilisent <em class="not-italic text-bimo-gold">BimoTech</em></h2>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
            @foreach([
                ['AD','Aminata Dieng','Directrice — Agence Fann Immo, Dakar','Avant BimoTech, je passais mes lundis à relancer les locataires par téléphone. Maintenant les quittances partent automatiquement et mes propriétaires consultent leurs revenus eux-mêmes. Je récupère deux heures par semaine.'],
                ['MF','Moustapha Fall','Gérant — Cabinet Thioro Patrimoine, Thiès','La conformité TVA et TOM est ce qui m\'a convaincu. Mon comptable a validé les calculs du premier coup. Je n\'avais rien à paramétrer, tout était déjà dans la loi sénégalaise. Aucun autre logiciel ne fait ça.'],
                ['SC','Sokhna Cissé','Associée — Immo Almadies, Dakar','On gère 34 appartements à Almadies avec 2 personnes. BimoTech nous a permis de doubler notre portefeuille sans embaucher. Le tableau de bord suffit pour voir en 30 secondes ce qui est payé et ce qui ne l\'est pas.'],
            ] as [$initials,$name,$role,$text])
            <div class="bg-white border border-[#e5e7eb] rounded-[14px] p-7 hover:border-[#d1d5db] hover:-translate-y-1 hover:shadow-lg transition-all duration-200">
                <div class="font-[Georgia,serif] text-[32px] text-bimo-gold opacity-40 leading-none mb-4">"</div>
                <p class="font-body text-[13.5px] text-[#374151] leading-[1.75] mb-6 italic">{{ $text }}</p>
                <div class="flex items-center gap-2.5">
                    <div class="w-[38px] h-[38px] rounded-full bg-bimo-gold flex items-center justify-center font-display font-bold text-sm text-white flex-shrink-0">{{ $initials }}</div>
                    <div>
                        <div class="font-display font-bold text-[13px] text-[#111]">{{ $name }}</div>
                        <div class="font-body text-[11px] text-[#6b7280]">{{ $role }}</div>
                    </div>
                </div>
                <div class="text-bimo-gold text-xs mt-3 flex gap-0.5">★★★★★</div>
            </div>
            @endforeach
        </div>
    </div>
</div>

{{-- ─── CONFORMITÉ ────────────────────────────────────── --}}
<section class="px-[5%] py-24" id="conformite">
    <div class="max-w-[1040px] mx-auto">
        <div class="mb-14">
            <div class="inline-flex items-center gap-1.5 font-body font-semibold text-[11px] text-bimo-gold uppercase tracking-[2px] mb-4">
                <span class="w-4 h-px bg-bimo-gold inline-block"></span>Conformité fiscale
            </div>
            <h2 class="font-display font-extrabold text-[clamp(28px,4vw,46px)] tracking-tight leading-[1.08] mb-4">Le seul SaaS immobilier<br><em class="not-italic text-bimo-gold">conforme au droit sénégalais</em></h2>
            <p class="font-body text-[15px] text-[#6b7280] max-w-[540px] leading-relaxed">BimoTech intègre nativement les obligations fiscales et légales du Sénégal. Pas de modules à activer, pas de configuration complexe.</p>
        </div>
        <div class="bg-white border border-[#e5e7eb] rounded-[20px] p-12 shadow-sm">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-7">
                @foreach([
                    ['TVA 18% — CGI article 357','Calcul automatique sur les commissions d\'agence, décomposition HT/TTC claire sur chaque quittance.'],
                    ['Loi 81-18 — Encadrement des loyers','Vérification automatique des plafonds selon la surface. Alerte si le loyer saisi est non conforme.'],
                    ['TOM — Taxe sur Ordures Ménagères','Taux paramétrable par commune. Dakar 5%, autres communes selon arrêté local.'],
                    ['BRS — Contribution Foncière des Propriétés Bâties','Calcul et report automatique de la contribution foncière dans les bilans propriétaires.'],
                    ['NINEA — Identification fiscale de l\'agence','Le NINEA apparaît automatiquement sur toutes les quittances, factures et documents PDF.'],
                    ['Dépôt de garantie — 2 mois max','Plafonnement automatique au seuil légal loi 81-18, avec avertissement en cas de dépassement.'],
                ] as [$title,$desc])
                <div class="flex items-start gap-3.5">
                    <div class="w-6 h-6 bg-bimo-gold/[8%] border border-[#e5e7eb] rounded-[7px] flex items-center justify-center flex-shrink-0 mt-0.5">
                        <svg width="11" height="11" viewBox="0 0 12 12" fill="none" stroke="#C9A84C" stroke-width="2.5"><polyline points="2,6 5,9 10,3"/></svg>
                    </div>
                    <div>
                        <div class="font-body font-semibold text-sm text-[#111] mb-1">{{ $title }}</div>
                        <div class="font-body text-[12.5px] text-[#6b7280] leading-relaxed">{{ $desc }}</div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</section>

{{-- ─── COMMENT ÇA MARCHE ──────────────────────────────── --}}
<section class="px-[5%] py-24 bg-[#1a1a1a] border-t border-b border-white/[8%]">
    <div class="max-w-[1040px] mx-auto">
        <div class="mb-14">
            <div class="inline-flex items-center gap-1.5 font-body font-semibold text-[11px] text-bimo-gold uppercase tracking-[2px] mb-4">
                <span class="w-4 h-px bg-bimo-gold inline-block"></span>Démarrage rapide
            </div>
            <h2 class="font-display font-extrabold text-[clamp(28px,4vw,46px)] tracking-tight leading-[1.08] text-white">Opérationnel en <em class="not-italic text-bimo-gold">moins de 10 minutes</em></h2>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 relative">
            <div class="hidden md:block absolute top-[27px] left-[calc(16.7%+12px)] right-[calc(16.7%+12px)] h-px bg-bimo-gold z-0"></div>
            @foreach([['1','Créez votre agence','Renseignez nom, NINEA et coordonnées. Votre espace est immédiatement actif, sécurisé et isolé.'],['2','Ajoutez vos biens','Importez votre portefeuille avec photos, loyers et propriétaires. Créez vos premiers contrats de bail.'],['3','Gérez en temps réel','Paiements, quittances PDF, rapports fiscaux — tout se génère automatiquement depuis votre tableau de bord.']] as [$num,$title,$desc])
            <div class="text-center relative z-10">
                <div class="w-[54px] h-[54px] bg-bimo-gold rounded-full flex items-center justify-center font-display font-extrabold text-lg text-white mx-auto mb-5 shadow-xl">{{ $num }}</div>
                <div class="font-display font-bold text-[15px] text-white mb-2">{{ $title }}</div>
                <div class="font-body text-[13px] text-[#9ca3af] leading-relaxed">{{ $desc }}</div>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ─── TARIFS ─────────────────────────────────────────── --}}
<section class="px-[5%] py-24 bg-[#f3f4f6] border-t border-[#e5e7eb]" id="tarifs">
    <div class="max-w-[1040px] mx-auto">
        <div class="text-center mb-10">
            <div class="inline-flex items-center justify-center gap-1.5 font-body font-semibold text-[11px] text-bimo-gold uppercase tracking-[2px] mb-4">
                <span class="w-4 h-px bg-bimo-gold inline-block"></span>Tarifs
            </div>
            <h2 class="font-display font-extrabold text-[clamp(28px,4vw,46px)] tracking-tight leading-[1.08] mb-3">Starter, Pro ou Agence —<br><em class="not-italic text-bimo-gold">30 jours d'essai gratuit</em></h2>
            <p class="font-body text-[15px] text-[#6b7280] mx-auto">Aucune carte bancaire. Choisissez votre plan après l'essai.</p>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-5 max-w-[840px] mx-auto mb-8">
            @foreach([
                [false,'Starter','19 900','Jusqu\'à 15 unités — Dashboard, biens, contrats, paiements, impayés, quittances PDF.'],
                [true,'Pro','49 900','Jusqu\'à 50 unités — Tout Starter + immeubles, rapports PDF, import Excel, relevés propriétaires.'],
                [false,'Agence','89 900','Unités illimitées — Tout Pro + déclarations DGID, bilans fiscaux, logs d\'activité, support prioritaire.'],
            ] as [$featured,$name,$price,$desc])
            <div class="bg-white border rounded-[14px] p-8 flex flex-col relative transition-all duration-200 hover:-translate-y-1 {{ $featured ? 'border-bimo-gold shadow-xl pt-11' : 'border-[#e5e7eb] hover:shadow-lg hover:border-[#d1d5db]' }}">
                @if($featured)
                <div class="absolute -top-3.5 left-1/2 -translate-x-1/2 bg-bimo-gold text-[#111] font-body font-extrabold text-[11px] px-4 py-1 rounded-full whitespace-nowrap">⭐ Recommandé</div>
                @endif
                <div class="font-body font-bold text-[11px] text-[#6b7280] uppercase tracking-[1.5px] mb-3">{{ $name }}</div>
                <div class="font-display font-extrabold text-[36px] text-[#111] leading-none mb-3 flex items-baseline gap-1.5">
                    {{ $price }} <span class="font-body text-sm text-[#6b7280] font-normal">FCFA</span><span class="font-body text-[13px] text-[#6b7280] font-normal">/ mois</span>
                </div>
                <p class="font-body text-[13px] text-[#6b7280] leading-relaxed mb-6 flex-1">{{ $desc }}</p>
                <a href="{{ route('agency.register') }}"
                   class="block text-center py-3 rounded-[10px] font-body font-semibold text-[13.5px] no-underline transition-all duration-200 {{ $featured ? 'bg-bimo-gold text-[#111] hover:opacity-90 hover:-translate-y-0.5 shadow-md' : 'border border-[#d1d5db] text-[#111] hover:border-bimo-gold hover:text-bimo-gold hover:bg-[#f9fafb]' }}">
                    {{ $featured ? 'Démarrer — 30 jours gratuits' : 'Démarrer gratuitement' }}
                </a>
            </div>
            @endforeach
        </div>
        <div class="text-center">
            <a href="{{ route('pricing') }}" class="inline-flex items-center gap-1.5 font-body text-[13px] text-[#6b7280] no-underline border-b border-[#e5e7eb] pb-0.5 hover:text-[#111] hover:border-[#d1d5db] transition-colors duration-150">
                Comparer tous les plans en détail
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
            </a>
        </div>
    </div>
</section>

{{-- ─── CTA FINAL ──────────────────────────────────────── --}}
<div class="px-[5%] py-28 text-center bg-[#111111] border-t border-[#e5e7eb] relative overflow-hidden">
    <div class="relative z-10 max-w-[600px] mx-auto">
        <div class="inline-flex items-center justify-center gap-1.5 font-body font-semibold text-[11px] text-bimo-gold uppercase tracking-[2px] mb-6">
            <span class="w-4 h-px bg-bimo-gold inline-block"></span>Prêt à vous lancer ?
        </div>
        <h2 class="font-display font-extrabold text-[clamp(28px,4vw,46px)] tracking-[-2px] leading-[1.08] text-white mb-4">Rejoignez les agences qui<br>gèrent mieux avec <em class="not-italic text-bimo-gold">BimoTech</em></h2>
        <p class="font-body font-light text-[16px] text-[#9ca3af] max-w-[460px] mx-auto mb-10 leading-[1.75]">Votre agence est en ligne en moins de 10 minutes. Aucune installation, aucun engagement.</p>
        <div class="flex items-center justify-center gap-3 flex-wrap mb-6">
            <a href="{{ route('agency.register') }}" class="inline-flex items-center gap-1.5 font-body font-bold text-sm text-[#111] no-underline px-7 py-3.5 rounded-[10px] bg-bimo-gold hover:opacity-95 hover:-translate-y-0.5 transition-all duration-200">
                Créer mon agence gratuitement →
            </a>
            <a href="{{ route('demo') }}" class="inline-flex items-center gap-1.5 font-body font-medium text-sm text-white no-underline px-7 py-3.5 rounded-[10px] bg-transparent border border-white/10 hover:border-white/20 transition-all duration-200">
                Demander une démo
            </a>
        </div>
        <div class="flex flex-wrap items-center justify-center gap-4 md:gap-6 font-body text-[12.5px] text-[#6b7280]">
            @foreach(['30 jours d\'essai gratuit','Sans carte bancaire','Données hébergées au Sénégal','Support WhatsApp inclus'] as $item)
            <span class="flex items-center gap-1.5"><span class="w-1 h-1 rounded-full bg-bimo-gold inline-block flex-shrink-0"></span>{{ $item }}</span>
            @endforeach
        </div>
    </div>
</div>

{{-- ─── FOOTER ─────────────────────────────────────────── --}}
<footer class="bg-[#1a1a1a] border-t border-[#e5e7eb] px-[5%] pt-14 pb-8">
    <div class="max-w-[1040px] mx-auto">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-[1.5fr_1fr_1fr_1fr] gap-12 pb-12 border-b border-[#e5e7eb]">
            <div>
                <a href="{{ url('/') }}" class="block mb-3"><img src="/images/logo.jpeg" alt="BiMO-tech Immo" class="h-9 w-auto"></a>
                <p class="font-body text-[13px] text-[#9ca3af] leading-relaxed max-w-[220px] mb-6">La plateforme de gestion immobilière professionnelle pour les agences sénégalaises.</p>
                <div class="inline-flex items-center gap-1.5 bg-white/[6%] border border-white/10 rounded-[6px] px-2.5 py-1.5 font-body text-[11px] text-[#9ca3af] mb-6">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#C9A84C" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                    Conforme CGI · Loi 81-18 · NINEA
                </div>
                <div class="flex gap-2.5">
                    @foreach([['https://wa.me/221781318176','rgba(37,211,102,.15)','rgba(37,211,102,.3)','<path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>']] as [$href,$h1,$h2,$icon])
                    <a href="{{ $href }}" target="_blank" rel="noopener"
                       class="w-[34px] h-[34px] rounded-[8px] bg-white/[6%] border border-white/[8%] flex items-center justify-center transition-all duration-150 hover:bg-[{{ $h1 }}] hover:border-[{{ $h2 }}]">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="#8b949e">{!! $icon !!}</svg>
                    </a>
                    @endforeach
                </div>
            </div>
            <div>
                <div class="font-display font-bold text-xs text-[#e5e7eb] uppercase tracking-[1px] mb-4">Produit</div>
                @foreach([['#fonctionnalites','Fonctionnalités'],['#tarifs','Tarifs'],[route('faq'),'FAQ'],[route('demo'),'Demander une démo']] as [$href,$lbl])
                <a href="{{ $href }}" class="block font-body text-[13px] text-[#9ca3af] no-underline mb-2.5 hover:text-white transition-colors duration-150">{{ $lbl }}</a>
                @endforeach
            </div>
            <div>
                <div class="font-display font-bold text-xs text-[#e5e7eb] uppercase tracking-[1px] mb-4">Conformité</div>
                @foreach([['#conformite','TVA 18% — CGI 357'],['#conformite','Loi 81-18'],['#conformite','Quittances légales'],['#conformite','NINEA & DGID']] as [$href,$lbl])
                <a href="{{ $href }}" class="block font-body text-[13px] text-[#9ca3af] no-underline mb-2.5 hover:text-white transition-colors duration-150">{{ $lbl }}</a>
                @endforeach
            </div>
            <div>
                <div class="font-display font-bold text-xs text-[#e5e7eb] uppercase tracking-[1px] mb-4">Entreprise</div>
                @foreach([[route('contact'),'Contact'],[route('login'),'Connexion'],[route('mentions-legales'),'Mentions légales'],[route('confidentialite'),'Confidentialité']] as [$href,$lbl])
                <a href="{{ $href }}" class="block font-body text-[13px] text-[#9ca3af] no-underline mb-2.5 hover:text-white transition-colors duration-150">{{ $lbl }}</a>
                @endforeach
            </div>
        </div>
        <div class="flex flex-col md:flex-row items-center justify-between gap-4 mt-8">
            <div class="font-body text-xs text-[#6b7280]">© {{ date('Y') }} BimoTech · Dakar, Sénégal · support@bimotech.sn</div>
            <div class="flex gap-6">
                <a href="{{ route('mentions-legales') }}" class="font-body text-xs text-[#6b7280] no-underline hover:text-[#9ca3af] transition-colors duration-150">Mentions légales</a>
                <a href="{{ route('confidentialite') }}" class="font-body text-xs text-[#6b7280] no-underline hover:text-[#9ca3af] transition-colors duration-150">Confidentialité</a>
            </div>
        </div>
    </div>
</footer>

{{-- Schema.org --}}
@php
$schemaOrg = json_encode(['@context'=>'https://schema.org','@graph'=>[['@type'=>'SoftwareApplication','name'=>'BimoTech Immo','url'=>'https://immo.bimotechsn.com','description'=>'Logiciel de gestion immobilière pour agences au Sénégal. Conforme TVA 18%, BRS et CGI SN.','applicationCategory'=>'BusinessApplication','operatingSystem'=>'Web','offers'=>['@type'=>'Offer','price'=>'0','priceCurrency'=>'XOF','description'=>'Essai gratuit 30 jours'],'aggregateRating'=>['@type'=>'AggregateRating','ratingValue'=>'4.9','reviewCount'=>'12']],['@type'=>'Organization','name'=>'BimoTech','url'=>'https://immo.bimotechsn.com','contactPoint'=>['@type'=>'ContactPoint','telephone'=>'+221-78-131-81-76','contactType'=>'customer support','availableLanguage'=>'French'],'address'=>['@type'=>'PostalAddress','addressLocality'=>'Dakar','addressCountry'=>'SN']]]], JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
@endphp
<script type="application/ld+json">{!! $schemaOrg !!}</script>

{{-- Bouton WhatsApp flottant --}}
<a href="https://wa.me/221781318176?text=Bonjour%2C%20je%20voudrais%20une%20d%C3%A9mo%20de%20BimoTech%20Immo"
   target="_blank" rel="noopener" title="Discuter sur WhatsApp"
   class="fixed bottom-7 right-7 z-[999] w-14 h-14 rounded-full bg-[#25D366] flex items-center justify-center shadow-[0_4px_20px_rgba(37,211,102,.4)] no-underline hover:scale-110 hover:shadow-[0_6px_28px_rgba(37,211,102,.55)] transition-all duration-150"
   style="animation:wa-pop .4s cubic-bezier(.34,1.56,.64,1) .8s both">
    <svg width="28" height="28" viewBox="0 0 24 24" fill="white"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
</a>

</body>
</html>
