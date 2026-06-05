@extends('layouts.app')
@section('header', 'Abonnement')

@section('content')

@php
$niveaux = [
    'starter' => ['label'=>'Starter','desc'=>"Jusqu'à 15 unités",'popular'=>false,'features'=>["Biens, contrats & locataires","Dashboard & paiements","Quittances PDF","Impayés & relances"]],
    'pro'     => ['label'=>'Pro',    'desc'=>"Jusqu'à 50 unités",'popular'=>true, 'features'=>["Tout Starter +","Immeubles","Rapports & relevés PDF","Export CSV & Import Excel","Recherche globale"]],
    'agence'  => ['label'=>'Agence','desc'=>"Unités illimitées", 'popular'=>false,'features'=>["Tout Pro +","Fiscalité BRS / TVA","Bilans fiscaux DGID","Logs d'activité","Support prioritaire"]],
];
$tarifsNiveau = \App\Models\Subscription::TARIFS;
$estActif  = $subscription?->estActif();
$estEssai  = $subscription?->estEnEssai();
$estExpire = $subscription && !$estActif && !$estEssai;
$plansCfg       = config('plans');
$niveauBrut     = $subscription?->plan_niveau ?? 'legacy';
$niveauEffectif = $plansCfg['niveau_effectif'][$niveauBrut] ?? 'pro';
$niveauLabel    = $plansCfg['labels'][$niveauBrut] ?? 'Pro';
$hierarchy      = $plansCfg['hierarchy'];
$posEffective   = array_search($niveauEffectif, $hierarchy);
$featuresList = ['Gestion biens, contrats & locataires'=>'starter',"Gestion d'immeubles"=>'pro','Relevés propriétaires PDF'=>'pro','Export CSV'=>'pro','Recherche globale'=>'pro','Contrats formels PDF'=>'pro','Import Excel'=>'pro','Fiscalité BRS / TVA'=>'agence','Bilans fiscaux'=>'agence',"Logs d'activité"=>'agence'];
@endphp

<div class="max-w-3xl mx-auto space-y-6 pb-16">

    {{-- Breadcrumb --}}
    <nav class="flex items-center gap-2 font-body text-sm text-bimo-text/40">
        <a href="{{ route('admin.dashboard') }}" class="hover:text-bimo-text transition-colors duration-150">Tableau de bord</a>
        <svg class="w-3 h-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
        <span class="text-bimo-text font-medium">Abonnement</span>
    </nav>

    {{-- Statut actuel --}}
    @if($subscription)
    <div class="flex items-center justify-between gap-4 flex-wrap px-5 py-4 rounded-[14px] border {{ $estEssai ? 'bg-bimo-gold/[6%] border-bimo-gold/25' : ($estActif ? 'bg-bimo-navy/[4%] border-bimo-navy/10' : 'bg-bimo-red/[5%] border-bimo-red/20') }}">
        <div class="flex items-center gap-3">
            <div class="w-11 h-11 rounded-[12px] flex items-center justify-center flex-shrink-0 {{ $estEssai ? 'bg-bimo-gold/15' : ($estActif ? 'bg-bimo-navy/10' : 'bg-bimo-red/10') }}">
                @if($estExpire)
                <svg class="w-5 h-5 text-bimo-red" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
                @elseif($estEssai)
                <svg class="w-5 h-5 text-bimo-gold" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                @else
                <svg class="w-5 h-5 text-bimo-text/60" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
                @endif
            </div>
            <div>
                <div class="font-display font-bold text-sm {{ $estEssai ? 'text-bimo-gold' : ($estActif ? 'text-bimo-text' : 'text-bimo-red') }}">
                    @if($estEssai) Période d'essai en cours
                    @elseif($estActif) Abonnement actif — {{ \App\Models\Subscription::LABELS[$subscription->plan] ?? '' }}
                    @else Accès expiré @endif
                </div>
                <div class="font-body text-xs {{ $estEssai ? 'text-bimo-gold/60' : ($estActif ? 'text-bimo-text/50' : 'text-bimo-red/60') }} mt-0.5">
                    @if($estEssai) Expire le {{ $subscription->date_fin_essai->format('d/m/Y') }} · {{ $subscription->joursRestantsEssai() }} jours restants · Accès Pro complet inclus
                    @elseif($estActif) Expire le {{ $subscription->date_fin_abonnement->format('d/m/Y') }} · {{ $subscription->joursRestantsAbonnement() }} jours restants
                    @else Votre essai ou abonnement a expiré. Choisissez un plan. @endif
                </div>
            </div>
        </div>
        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-[8px] font-display font-bold text-xs {{ $estEssai ? 'bg-bimo-gold/15 text-bimo-gold' : ($estActif ? 'bg-bimo-navy/10 text-bimo-text' : 'bg-bimo-red/10 text-bimo-red') }}">
            @if(!$estExpire)<svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>@endif
            @if($estEssai) Essai gratuit 30 jours @elseif($estActif) Actif @else Expiré @endif
        </span>
    </div>
    @endif

    {{-- Titre --}}
    <div>
        <h1 class="font-display font-extrabold text-xl text-bimo-text tracking-tight">Choisissez votre abonnement</h1>
        <p class="font-body text-sm text-bimo-text/50 mt-1">Aucune carte bancaire. Essai gratuit 30 jours inclus.</p>
    </div>

    {{-- Toggle mensuel / annuel --}}
    <div id="billing-toggle"
         class="inline-flex bg-bimo-bg2 border border-bimo-navy/10 rounded-[10px] p-1 gap-1">
        <button id="btn-mensuel" onclick="setBilling('mensuel')"
                class="px-4 py-2 rounded-[8px] font-display font-bold text-sm bg-white text-bimo-text shadow-sm transition-all duration-150">
            Mensuel
        </button>
        <button id="btn-annuel" onclick="setBilling('annuel')"
                class="px-4 py-2 rounded-[8px] font-display font-bold text-sm text-bimo-text/50 hover:text-bimo-text transition-all duration-150 flex items-center gap-2">
            Annuel
            <span class="bg-bimo-navy/10 text-bimo-text/60 text-[9px] font-body font-bold px-2 py-0.5 rounded-full uppercase tracking-wide">2 mois offerts</span>
        </button>
    </div>

    {{-- 3 cartes plans --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        @foreach($niveaux as $niveauKey => $n)
        @php $pM = $tarifsNiveau[$niveauKey]['mensuel']; $pA = $tarifsNiveau[$niveauKey]['annuel']; @endphp
        <div class="bg-bimo-navy rounded-[14px] border {{ $n['popular'] ? 'border-bimo-gold' : 'border-white/[7%]' }} p-5 flex flex-col relative {{ $n['popular'] ? 'bg-gradient-to-b from-bimo-navy to-bimo-navy-dk' : '' }}">
            @if($n['popular'])
            <div class="absolute -top-3 left-1/2 -translate-x-1/2 bg-bimo-gold text-bimo-text text-[10px] font-display font-extrabold px-4 py-0.5 rounded-full whitespace-nowrap shadow-lg">
                Le plus populaire
            </div>
            @endif
            <div class="font-body font-medium text-[10px] uppercase tracking-widest text-white/40 mb-3">{{ $n['label'] }}</div>
            <div id="price-{{ $niveauKey }}" class="font-display font-extrabold text-2xl text-white leading-none mb-1">
                {{ number_format($pM, 0, ',', ' ') }}<span class="font-body text-sm text-white/40 ml-1">FCFA / mois</span>
            </div>
            <div id="annual-detail-{{ $niveauKey }}" class="font-body text-xs text-white/30 mb-3 hidden">
                facturé {{ number_format($pA, 0, ',', ' ') }} FCFA / an
            </div>
            <div id="monthly-ph-{{ $niveauKey }}" class="font-body text-xs text-white/30 mb-3">&nbsp;</div>
            <div class="font-body text-xs text-white/40 mb-5">{{ $n['desc'] }}</div>
            <ul class="flex-1 space-y-2 mb-5">
                @foreach($n['features'] as $feat)
                <li class="flex items-center gap-2 font-body text-sm text-white/60">
                    <svg class="w-3.5 h-3.5 text-bimo-gold flex-shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                    {{ $feat }}
                </li>
                @endforeach
            </ul>
            <form method="POST" action="{{ route('subscription.initier') }}"
                  onsubmit="return confirm('Activer le plan {{ $n['label'] }} ?')">
                @csrf
                <input type="hidden" name="plan_niveau" value="{{ $niveauKey }}">
                <input type="hidden" name="plan" class="plan-billing-input" value="mensuel">
                <button type="submit"
                        class="w-full py-2.5 rounded-[9px] font-display font-bold text-sm text-center transition-all duration-150 {{ $n['popular'] ? 'bg-bimo-gold text-bimo-text hover:opacity-90 shadow-lg' : 'bg-white/10 text-white hover:bg-white/15' }} cursor-pointer">
                    Choisir {{ $n['label'] }}
                </button>
            </form>
        </div>
        @endforeach
    </div>

    {{-- Comparatif tarifs --}}
    <div class="bg-bimo-navy rounded-[14px] border border-white/[7%] overflow-hidden">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-white/[6%]">
                    <th class="px-5 py-3 text-left font-body font-medium text-[10px] uppercase tracking-widest text-white/30">Plan</th>
                    <th class="px-5 py-3 text-right font-body font-medium text-[10px] uppercase tracking-widest text-white/30">Mensuel</th>
                    <th class="px-5 py-3 text-right font-body font-medium text-[10px] uppercase tracking-widest text-white/30">Annuel</th>
                    <th class="px-5 py-3 text-center font-body font-medium text-[10px] uppercase tracking-widests text-white/30">Économie / an</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-white/[4%]">
                @foreach($niveaux as $niveauKey => $n)
                @php $pM = $tarifsNiveau[$niveauKey]['mensuel']; $pA = $tarifsNiveau[$niveauKey]['annuel']; $saving = ($pM * 12) - $pA; @endphp
                <tr>
                    <td class="px-5 py-3.5 font-body font-medium text-sm text-white/70">{{ $n['label'] }}</td>
                    <td class="px-5 py-3.5 text-right font-display font-bold text-sm {{ $n['popular'] ? 'text-bimo-gold' : 'text-white/60' }}">{{ number_format($pM,0,',','') }} FCFA</td>
                    <td class="px-5 py-3.5 text-right font-display font-bold text-sm {{ $n['popular'] ? 'text-bimo-gold' : 'text-white/60' }}">{{ number_format($pA,0,',','') }} FCFA</td>
                    <td class="px-5 py-3.5 text-center">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-body font-semibold bg-bimo-gold/10 border border-bimo-gold/20 text-bimo-gold">{{ number_format($saving,0,',','') }} FCFA</span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- Fonctionnalités --}}
    <div class="bg-bimo-navy rounded-[14px] border border-white/[7%] overflow-hidden">
        <div class="flex items-center justify-between px-5 py-4 border-b border-white/[6%]">
            <span class="font-display font-bold text-sm text-white">Fonctionnalités incluses</span>
            @if($estActif || $estEssai)
            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-display font-bold bg-bimo-gold/15 border border-bimo-gold/25 text-bimo-gold">{{ $niveauLabel }}</span>
            @else
            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-display font-bold bg-bimo-gold/15 border border-bimo-gold/25 text-bimo-gold">Plan Pro</span>
            @endif
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2">
            @foreach($featuresList as $label => $niveauReqis)
            @php
                $posRequise = array_search($niveauReqis, $hierarchy);
                $posCheck   = $estEssai ? 1 : $posEffective;
                $accessible = $posCheck >= $posRequise;
                $reqLabel   = $plansCfg['labels'][$niveauReqis] ?? ucfirst($niveauReqis);
            @endphp
            <div class="flex items-center gap-3 px-5 py-3 border-b border-white/[4%] {{ $loop->even ? 'sm:border-l border-white/[4%]' : '' }} {{ $accessible ? 'text-white/70' : 'text-white/20' }}">
                @if($accessible)
                <svg class="w-4 h-4 text-bimo-gold flex-shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                @else
                <svg class="w-4 h-4 text-white/20 flex-shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0110 0v4"/></svg>
                @endif
                <span class="font-body text-sm">{{ $label }}</span>
                @if(!$accessible)
                <span class="ml-auto font-body font-bold text-[10px] px-2 py-0.5 rounded-full bg-bimo-gold/10 text-bimo-gold whitespace-nowrap">Plan {{ $reqLabel }}</span>
                @endif
            </div>
            @endforeach
        </div>
    </div>

    {{-- Historique --}}
    @if($historique->count() > 0)
    <div class="bg-bimo-navy rounded-[14px] border border-white/[7%] overflow-hidden">
        <div class="flex items-center justify-between px-5 py-4 border-b border-white/[6%]">
            <span class="font-display font-bold text-sm text-white">Historique des paiements</span>
            <span class="inline-flex items-center px-2.5 py-0.5 rounded-[6px] font-body text-[11px] font-medium bg-white/[7%] text-white/40">{{ $historique->count() }} entrée{{ $historique->count() > 1 ? 's' : '' }}</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-white/[5%]">
                        <th class="px-5 py-3 text-left font-body font-medium text-[10px] uppercase tracking-widest text-white/30">Date</th>
                        <th class="px-5 py-3 text-left font-body font-medium text-[10px] uppercase tracking-widest text-white/30">Plan</th>
                        <th class="px-5 py-3 text-left font-body font-medium text-[10px] uppercase tracking-widests text-white/30">Méthode</th>
                        <th class="px-5 py-3 text-left font-body font-medium text-[10px] uppercase tracking-widest text-white/30">Période</th>
                        <th class="px-5 py-3 text-right font-body font-medium text-[10px] uppercase tracking-widest text-white/30">Montant</th>
                        <th class="px-5 py-3 text-center font-body font-medium text-[10px] uppercase tracking-widest text-white/30">Statut</th>
                        <th class="px-5 py-3 text-left font-body font-medium text-[10px] uppercase tracking-widest text-white/30">Référence</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/[4%]">
                    @foreach($historique as $paiement)
                    <tr class="hover:bg-white/[2%] transition-colors duration-100">
                        <td class="px-5 py-3.5 font-body text-sm text-white/50">{{ $paiement->created_at->format('d/m/Y') }}</td>
                        <td class="px-5 py-3.5">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-[5px] font-body font-bold text-[10px] bg-bimo-gold/15 text-bimo-gold">{{ \App\Models\Subscription::LABELS[$paiement->plan] ?? $paiement->plan }}</span>
                        </td>
                        <td class="px-5 py-3.5 font-body text-sm text-white/50">{{ \App\Models\SubscriptionPayment::METHODE_LABELS[$paiement->methode] ?? $paiement->methode }}</td>
                        <td class="px-5 py-3.5 font-body text-xs text-white/40">
                            @if($paiement->periode_debut && $paiement->periode_fin)
                                {{ $paiement->periode_debut->format('d/m/Y') }} → {{ $paiement->periode_fin->format('d/m/Y') }}
                            @else — @endif
                        </td>
                        <td class="px-5 py-3.5 text-right font-display font-bold text-sm text-white">{{ number_format($paiement->montant,0,',','') }} F</td>
                        <td class="px-5 py-3.5 text-center">
                            @php $sc = ['payé'=>'bg-bimo-navy/30 text-white/70','en_attente'=>'bg-bimo-gold/15 text-bimo-gold','échoué'=>'bg-bimo-red/15 text-bimo-red']; @endphp
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-body font-medium {{ $sc[$paiement->statut] ?? $sc['en_attente'] }}">
                                {{ \App\Models\SubscriptionPayment::STATUT_LABELS[$paiement->statut] ?? $paiement->statut }}
                            </span>
                        </td>
                        <td class="px-5 py-3.5 font-body text-[10px] text-white/30" style="font-family:monospace">{{ $paiement->reference ?? '—' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    {{-- Support --}}
    <div class="bg-bimo-navy rounded-[14px] border border-white/[7%] px-6 py-5 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div class="flex items-center gap-4">
            <div class="w-10 h-10 rounded-[10px] bg-bimo-gold/10 border border-bimo-gold/20 flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5 text-bimo-gold" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z"/></svg>
            </div>
            <div>
                <div class="font-display font-bold text-sm text-white">Une question sur votre abonnement ?</div>
                <div class="font-body text-xs text-white/40 mt-0.5">Notre équipe répond sous 2h en jours ouvrables.</div>
            </div>
        </div>
        <div class="flex flex-col gap-2 sm:text-right">
            <div class="flex items-center gap-2 font-body text-sm text-white/60">
                <svg class="w-3.5 h-3.5 text-white/30" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                support@bimotech.sn
            </div>
            <div class="flex items-center gap-2 font-body text-sm text-white/60">
                <svg class="w-3.5 h-3.5 text-white/30" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07A19.5 19.5 0 013.07 9.8a19.79 19.79 0 01-3.07-8.67A2 2 0 012 0h3a2 2 0 012 1.72c.127.96.361 1.903.7 2.81a2 2 0 01-.45 2.11L6.09 7.91a16 16 0 006 6l1.27-1.27a2 2 0 012.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0122 14.92z"/></svg>
                +221 33 800 00 01
            </div>
        </div>
    </div>

</div>

@push('scripts')
<script>
const tarifsNiveau = @json($tarifsNiveau);
function setBilling(mode) {
    var isMensuel = (mode === 'mensuel');
    var btnM = document.getElementById('btn-mensuel'), btnA = document.getElementById('btn-annuel');
    btnM.classList.toggle('bg-white', isMensuel);
    btnM.classList.toggle('text-bimo-text', isMensuel);
    btnM.classList.toggle('shadow-sm', isMensuel);
    btnM.classList.toggle('text-bimo-text/50', !isMensuel);
    btnA.classList.toggle('bg-white', !isMensuel);
    btnA.classList.toggle('text-bimo-text', !isMensuel);
    btnA.classList.toggle('shadow-sm', !isMensuel);
    btnA.classList.toggle('text-bimo-text/50', isMensuel);
    ['starter','pro','agence'].forEach(function(n){
        var price = isMensuel ? tarifsNiveau[n].mensuel : tarifsNiveau[n].annuel;
        var period = isMensuel ? '/ mois' : '/ an';
        document.getElementById('price-' + n).innerHTML = new Intl.NumberFormat('fr-FR').format(price) + '<span class="font-body text-sm text-white/40 ml-1">FCFA ' + period + '</span>';
        document.getElementById('annual-detail-' + n).classList.toggle('hidden', isMensuel);
        document.getElementById('monthly-ph-' + n).classList.toggle('hidden', !isMensuel);
    });
    document.querySelectorAll('.plan-billing-input').forEach(function(el){ el.value = mode; });
}
</script>
@endpush
@endsection
