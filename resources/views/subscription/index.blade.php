@extends('layouts.app')
@section('title', 'Abonnement')
@section('breadcrumb', 'Abonnement')

@section('content')
@php
$niveaux = [
    'starter' => [
        'label'    => 'Starter',
        'desc'     => "Jusqu'à 15 unités",
        'popular'  => false,
        'features' => ["Biens, contrats & locataires", "Dashboard & paiements", "Quittances PDF", "Impayés & relances"],
    ],
    'pro' => [
        'label'    => 'Pro',
        'desc'     => "Jusqu'à 50 unités",
        'popular'  => true,
        'features' => ["Tout Starter +", "Immeubles", "Rapports & relevés PDF", "Export CSV & Import Excel", "Recherche globale"],
    ],
    'agence' => [
        'label'    => 'Agence',
        'desc'     => "Unités illimitées",
        'popular'  => false,
        'features' => ["Tout Pro +", "Fiscalité BRS / TVA", "Bilans fiscaux DGID", "Logs d'activité", "Support prioritaire"],
    ],
];
$tarifsNiveau = \App\Models\Subscription::TARIFS;
$estActif  = $subscription?->estActif();
$estEssai  = $subscription?->estEnEssai();
$estExpire = $subscription && !$estActif && !$estEssai;

$plansCfg        = config('plans');
$niveauBrut      = $subscription?->plan_niveau ?? 'legacy';
$niveauEffectif  = $plansCfg['niveau_effectif'][$niveauBrut] ?? 'pro';
$niveauLabel     = $plansCfg['labels'][$niveauBrut] ?? 'Pro';
$hierarchy       = $plansCfg['hierarchy'];        // ['starter','pro','agence']
$posEffective    = array_search($niveauEffectif, $hierarchy);

// Fonctionnalités à afficher (libellé → niveau minimum requis)
$featuresList = [
    'Gestion biens, contrats & locataires' => 'starter',
    'Gestion d\'immeubles'                 => 'pro',
    'Relevés propriétaires PDF'            => 'pro',
    'Export CSV'                           => 'pro',
    'Recherche globale'                    => 'pro',
    'Contrats formels PDF'                 => 'pro',
    'Import Excel'                         => 'pro',
    'Fiscalité BRS / TVA'                  => 'agence',
    'Bilans fiscaux'                       => 'agence',
    'Logs d\'activité'                     => 'agence',
];
@endphp

<style>
.sub-page { max-width:900px; margin:0 auto; padding-bottom:60px; }

/* ── Statut banner ── */
.sub-banner {
    border-radius:14px; padding:20px 24px;
    display:flex; align-items:center; justify-content:space-between; gap:16px;
    margin-bottom:28px; flex-wrap:wrap;
}
.sub-banner.essai  { background:rgba(201,168,76,.07); border:1px solid rgba(201,168,76,.2); }
.sub-banner.actif  { background:rgba(34,197,94,.06);  border:1px solid rgba(34,197,94,.2); }
.sub-banner.expire { background:rgba(239,68,68,.06);  border:1px solid rgba(239,68,68,.2); }
.sub-banner-left   { display:flex; align-items:center; gap:14px; }
.sub-banner-icon   {
    width:44px; height:44px; border-radius:12px;
    display:flex; align-items:center; justify-content:center; flex-shrink:0;
}
.sub-banner.essai  .sub-banner-icon { background:rgba(201,168,76,.15); }
.sub-banner.actif  .sub-banner-icon { background:rgba(34,197,94,.15); }
.sub-banner.expire .sub-banner-icon { background:rgba(239,68,68,.15); }
.sub-banner-title  { font-family:'Syne',sans-serif; font-size:14px; font-weight:700; color:#e6edf3; display:flex; align-items:center; gap:8px; flex-wrap:wrap; }
.sub-banner-sub    { font-size:12.5px; color:#8b949e; margin-top:2px; }
.sub-banner-badge  {
    display:inline-flex; align-items:center; gap:6px;
    padding:7px 16px; border-radius:8px;
    font-family:'Syne',sans-serif; font-size:12px; font-weight:700;
    white-space:nowrap;
}
.sub-banner.essai  .sub-banner-badge { background:rgba(201,168,76,.15); color:#c9a84c; }
.sub-banner.actif  .sub-banner-badge { background:rgba(34,197,94,.12);  color:#4ade80; }
.sub-banner.expire .sub-banner-badge { background:rgba(239,68,68,.12);  color:#f87171; }

/* ── Badge niveau ── */
.niveau-badge {
    display:inline-flex; align-items:center;
    padding:2px 10px; border-radius:99px;
    font-size:11px; font-weight:700; font-family:'Syne',sans-serif;
    white-space:nowrap;
}
.niveau-badge.starter { background:rgba(107,114,128,.15); color:#9ca3af; }
.niveau-badge.pro     { background:rgba(99,102,241,.15);  color:#818cf8; }
.niveau-badge.agence  { background:rgba(201,168,76,.15);  color:#c9a84c; }
.niveau-badge.legacy  { background:rgba(99,102,241,.15);  color:#818cf8; }

/* ── Section titre ── */
.sub-section-title {
    font-family:'Syne',sans-serif; font-size:18px; font-weight:700;
    color:#e6edf3; margin-bottom:6px;
}
.sub-section-sub { font-size:13px; color:#8b949e; margin-bottom:24px; }

/* ── Billing toggle ── */
.billing-toggle {
    display:inline-flex; background:#21262d;
    border:1px solid rgba(255,255,255,.07); border-radius:10px;
    padding:4px; gap:4px; margin-bottom:20px;
}
.billing-btn {
    padding:7px 18px; border-radius:7px; border:none;
    font-family:'Syne',sans-serif; font-size:12px; font-weight:700;
    cursor:pointer; transition:all .15s; color:#8b949e; background:transparent;
    display:flex; align-items:center; gap:8px;
}
.billing-btn.active { background:#161b22; color:#e6edf3; box-shadow:0 1px 3px rgba(0,0,0,.3); }
.billing-btn .eco-tag {
    background:rgba(34,197,94,.15); color:#4ade80;
    font-size:9px; font-weight:800; padding:2px 7px;
    border-radius:99px; letter-spacing:.3px;
}

/* ── Plan cards ── */
.plans-row {
    display:grid; grid-template-columns:repeat(3,1fr); gap:14px;
    margin-bottom:28px;
}
.plan-card {
    background:#161b22; border:1px solid rgba(255,255,255,.07);
    border-radius:14px; padding:22px 18px;
    display:flex; flex-direction:column;
    position:relative; transition:border-color .2s, transform .2s;
    cursor:default;
}
.plan-card:hover { border-color:rgba(201,168,76,.25); transform:translateY(-2px); }
.plan-card.popular {
    border-color:#c9a84c;
    background:linear-gradient(160deg,rgba(201,168,76,.05) 0%,#161b22 60%);
}
.plan-card-badge {
    position:absolute; top:-11px; left:50%; transform:translateX(-50%);
    background:linear-gradient(135deg,#c9a84c,#e8c96a);
    color:#080c12; font-size:10px; font-weight:800;
    font-family:'Syne',sans-serif;
    padding:3px 14px; border-radius:99px; white-space:nowrap;
    box-shadow:0 3px 10px rgba(201,168,76,.3);
}
.plan-card-eco {
    display:inline-block; background:rgba(34,197,94,.12); color:#4ade80;
    font-size:10px; font-weight:700; padding:2px 8px;
    border-radius:99px; margin-bottom:10px;
}
.plan-card-name {
    font-family:'Syne',sans-serif; font-size:11px; font-weight:700;
    color:#8b949e; text-transform:uppercase; letter-spacing:1.2px; margin-bottom:10px;
}
.plan-card-price {
    font-family:'Syne',sans-serif; font-size:26px; font-weight:800;
    color:#e6edf3; line-height:1; margin-bottom:2px;
}
.plan-card-price span { font-size:12px; color:#8b949e; font-weight:400; }
.plan-card-mensuel { font-size:11px; color:#8b949e; margin-bottom:16px; min-height:16px; }
.plan-card-saving  { font-size:11px; color:#4ade80; margin-bottom:16px; min-height:16px; }
.plan-card-btn {
    margin-top:auto;
    display:block; text-align:center;
    padding:10px; border-radius:9px;
    font-family:'DM Sans',sans-serif; font-size:13px; font-weight:600;
    border:none; cursor:pointer; transition:all .2s; text-decoration:none;
    width:100%;
}
.plan-card-btn-outline {
    background:transparent; color:#e6edf3;
    border:1px solid rgba(255,255,255,.12);
}
.plan-card-btn-outline:hover { border-color:rgba(255,255,255,.25); background:rgba(255,255,255,.04); }
.plan-card-btn-gold {
    background:linear-gradient(135deg,#c9a84c,#e8c96a);
    color:#080c12;
    box-shadow:0 3px 12px rgba(201,168,76,.25);
}
.plan-card-btn-gold:hover { opacity:.9; box-shadow:0 5px 20px rgba(201,168,76,.35); }

/* ── Tableau comparatif ── */
.compare-table {
    background:#161b22; border:1px solid rgba(255,255,255,.07);
    border-radius:14px; overflow:hidden; margin-bottom:24px;
}
.compare-table table { width:100%; border-collapse:collapse; font-size:13px; }
.compare-table th {
    padding:11px 18px; text-align:right;
    font-size:10px; color:#6e7681; text-transform:uppercase;
    letter-spacing:.7px; font-weight:600;
    background:rgba(255,255,255,.02);
    border-bottom:1px solid rgba(255,255,255,.06);
}
.compare-table th:first-child { text-align:left; }
.compare-table td {
    padding:12px 18px; text-align:right;
    color:#8b949e; border-bottom:1px solid rgba(255,255,255,.04);
}
.compare-table td:first-child { text-align:left; color:#c9d1d9; font-weight:500; }
.compare-table tr:last-child td { border-bottom:none; }
.compare-table .gold { color:#c9a84c; font-family:'Syne',sans-serif; font-weight:700; }
.eco-pill {
    display:inline-block; background:rgba(34,197,94,.1); color:#4ade80;
    font-size:10px; font-weight:700; padding:2px 9px; border-radius:99px;
}

/* ── Features card ── */
.features-card {
    background:#161b22; border:1px solid rgba(255,255,255,.07);
    border-radius:14px; overflow:hidden; margin-bottom:24px;
}
.features-head {
    padding:14px 20px; border-bottom:1px solid rgba(255,255,255,.06);
    display:flex; align-items:center; justify-content:space-between; gap:12px; flex-wrap:wrap;
}
.features-head-title { font-family:'Syne',sans-serif; font-size:13px; font-weight:700; color:#e6edf3; }
.features-grid {
    display:grid; grid-template-columns:repeat(2,1fr); gap:0;
}
.feature-item {
    display:flex; align-items:center; gap:10px;
    padding:11px 20px; border-bottom:1px solid rgba(255,255,255,.04);
    font-size:12.5px;
}
.feature-item:nth-child(odd)  { border-right:1px solid rgba(255,255,255,.04); }
.feature-item.unlocked { color:#c9d1d9; }
.feature-item.locked   { color:#484f58; }
.feature-icon-ok   { color:#4ade80; flex-shrink:0; }
.feature-icon-lock { color:#484f58; flex-shrink:0; }
.feature-lock-pill {
    margin-left:auto; font-size:10px; font-weight:700;
    padding:2px 8px; border-radius:99px;
    background:rgba(201,168,76,.1); color:#c9a84c; white-space:nowrap;
}

/* ── Historique ── */
.hist-card {
    background:#161b22; border:1px solid rgba(255,255,255,.07);
    border-radius:14px; overflow:hidden;
}
.hist-head {
    padding:14px 20px; border-bottom:1px solid rgba(255,255,255,.06);
    display:flex; align-items:center; justify-content:space-between;
}
.hist-head-title { font-family:'Syne',sans-serif; font-size:13px; font-weight:700; color:#e6edf3; }
.hist-table { width:100%; border-collapse:collapse; font-size:12.5px; }
.hist-table th {
    padding:9px 18px; font-size:10px; color:#6e7681;
    text-transform:uppercase; letter-spacing:.7px; font-weight:600;
    background:rgba(255,255,255,.02); border-bottom:1px solid rgba(255,255,255,.06);
    text-align:left;
}
.hist-table td {
    padding:12px 18px; color:#8b949e;
    border-bottom:1px solid rgba(255,255,255,.04); vertical-align:middle;
}
.hist-table tr:last-child td { border-bottom:none; }
.hist-table tr:hover td { background:rgba(255,255,255,.02); }
.hist-badge {
    display:inline-block; font-size:10px; font-weight:700;
    padding:2px 9px; border-radius:99px;
}
.hist-badge.paye    { background:rgba(34,197,94,.12);  color:#4ade80; }
.hist-badge.attente { background:rgba(234,179,8,.1);   color:#fbbf24; }
.hist-badge.echoue  { background:rgba(239,68,68,.1);   color:#f87171; }
.plan-pill {
    display:inline-block; padding:2px 9px;
    background:rgba(201,168,76,.1); color:#c9a84c;
    font-size:10px; font-weight:700; border-radius:6px;
}

/* ── Support ── */
.support-card {
    background:#161b22; border:1px solid rgba(255,255,255,.07);
    border-radius:14px; padding:22px 24px;
    display:flex; align-items:center; justify-content:space-between;
    gap:16px; flex-wrap:wrap; margin-top:20px;
}
.support-left { display:flex; align-items:center; gap:14px; }
.support-icon {
    width:40px; height:40px; border-radius:10px;
    background:rgba(201,168,76,.1); border:1px solid rgba(201,168,76,.15);
    display:flex; align-items:center; justify-content:center; flex-shrink:0;
}
.support-title { font-family:'Syne',sans-serif; font-size:13px; font-weight:700; color:#e6edf3; }
.support-sub   { font-size:12px; color:#8b949e; margin-top:2px; }
.support-contacts { display:flex; gap:20px; flex-wrap:wrap; }
.support-contact   { font-size:12.5px; color:#8b949e; display:flex; align-items:center; gap:6px; }

@media(max-width:660px){ .plans-row{ grid-template-columns:1fr; } }
@media(max-width:540px){ .features-grid{ grid-template-columns:1fr; } .feature-item:nth-child(odd){ border-right:none; } }
@media(max-width:768px){
    .compare-table { overflow-x:auto; }
    .compare-table table { min-width:480px; }
    .hist-card { overflow-x:auto; }
    .hist-table { min-width:480px; }
    .sub-banner { flex-direction:column; align-items:flex-start; }
    .support-card { flex-direction:column; align-items:flex-start; }
    .support-contacts { flex-direction:column; gap:8px; }
}
</style>

<div class="sub-page">

    {{-- Breadcrumb --}}
    <div style="display:flex;align-items:center;gap:8px;font-size:13px;color:#6b7280;margin-bottom:20px">
        <a href="{{ route('admin.dashboard') }}" style="color:#6b7280;text-decoration:none">Tableau de bord</a>
        <svg style="width:12px;height:12px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
        <span style="color:#e6edf3;font-weight:500">Abonnement</span>
    </div>

    @if($errors->has('general'))
    <div class="flash-error">{{ $errors->first('general') }}</div>
    @endif

    {{-- Statut actuel --}}
    @if($subscription)
    @if($estEssai)
    <div class="sub-banner essai">
        <div class="sub-banner-left">
            <div class="sub-banner-icon">
                <svg style="width:20px;height:20px;color:#c9a84c" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
            </div>
            <div>
                <div class="sub-banner-title">
                    Période d'essai en cours
                    <span class="niveau-badge pro">Pro</span>
                </div>
                <div class="sub-banner-sub">
                    Expire le {{ $subscription->date_fin_essai->format('d/m/Y') }} —
                    <strong style="color:#c9a84c">{{ $subscription->joursRestantsEssai() }} jours restants</strong>
                    · Accès Pro complet inclus
                </div>
            </div>
        </div>
        <div class="sub-banner-badge">
            <svg style="width:13px;height:13px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
            Essai gratuit 30 jours
        </div>
    </div>
    @elseif($estActif)
    <div class="sub-banner actif">
        <div class="sub-banner-left">
            <div class="sub-banner-icon">
                <svg style="width:20px;height:20px;color:#4ade80" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
            </div>
            <div>
                <div class="sub-banner-title">
                    Abonnement actif — {{ \App\Models\Subscription::LABELS[$subscription->plan] ?? '' }}
                    <span class="niveau-badge {{ $niveauBrut }}">{{ $niveauLabel }}</span>
                </div>
                <div class="sub-banner-sub">
                    Expire le {{ $subscription->date_fin_abonnement->format('d/m/Y') }} —
                    <strong style="color:#4ade80">{{ $subscription->joursRestantsAbonnement() }} jours restants</strong>
                </div>
            </div>
        </div>
        <div class="sub-banner-badge">
            <svg style="width:13px;height:13px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
            Actif
        </div>
    </div>
    @else
    <div class="sub-banner expire">
        <div class="sub-banner-left">
            <div class="sub-banner-icon">
                <svg style="width:20px;height:20px;color:#f87171" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
            </div>
            <div>
                <div class="sub-banner-title">Accès expiré</div>
                <div class="sub-banner-sub">Votre essai ou abonnement a expiré. Choisissez un plan pour retrouver l'accès complet.</div>
            </div>
        </div>
        <div class="sub-banner-badge">Expiré</div>
    </div>
    @endif
    @endif

    {{-- Titre section --}}
    <div class="sub-section-title">Choisissez votre abonnement</div>
    <div class="sub-section-sub">Aucune carte bancaire. Essai gratuit 30 jours inclus.</div>

    {{-- Toggle Mensuel / Annuel --}}
    <div class="billing-toggle">
        <button class="billing-btn active" id="btn-mensuel" onclick="setBilling('mensuel')">Mensuel</button>
        <button class="billing-btn" id="btn-annuel" onclick="setBilling('annuel')">
            Annuel <span class="eco-tag">2 mois offerts</span>
        </button>
    </div>

    {{-- 3 cartes Starter / Pro / Agence --}}
    <div class="plans-row">
        @foreach($niveaux as $niveauKey => $n)
        @php
            $pMensuel = $tarifsNiveau[$niveauKey]['mensuel'];
            $pAnnuel  = $tarifsNiveau[$niveauKey]['annuel'];
        @endphp
        <div class="plan-card {{ $n['popular'] ? 'popular' : '' }}">
            @if($n['popular'])
            <div class="plan-card-badge">Le plus populaire</div>
            @endif

            <div class="plan-card-name">{{ $n['label'] }}</div>

            <div class="plan-card-price" id="price-{{ $niveauKey }}">
                {{ number_format($pMensuel, 0, ',', ' ') }}
                <span>FCFA / mois</span>
            </div>
            <div class="plan-card-mensuel" id="annual-detail-{{ $niveauKey }}" style="display:none">
                facturé {{ number_format($pAnnuel, 0, ',', ' ') }} FCFA / an
            </div>
            <div class="plan-card-mensuel" id="monthly-ph-{{ $niveauKey }}">&nbsp;</div>

            <div style="font-size:12px;color:#6e7681;margin-bottom:14px">{{ $n['desc'] }}</div>

            <ul style="list-style:none;padding:0;margin:0 0 18px;flex:1">
                @foreach($n['features'] as $feat)
                <li style="display:flex;align-items:center;gap:8px;padding:4px 0;font-size:12px;color:#8b949e">
                    <svg style="width:13px;height:13px;color:#4ade80;flex-shrink:0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                    {{ $feat }}
                </li>
                @endforeach
            </ul>

            <form method="POST" action="{{ route('subscription.initier') }}"
                  data-confirm="Activer le plan {{ $n['label'] }} ?"
                  onsubmit="return confirm(this.dataset.confirm)">
                @csrf
                <input type="hidden" name="plan_niveau" value="{{ $niveauKey }}">
                <input type="hidden" name="plan" class="plan-billing-input" value="mensuel">
                <button type="submit" class="plan-card-btn {{ $n['popular'] ? 'plan-card-btn-gold' : 'plan-card-btn-outline' }}">
                    Choisir {{ $n['label'] }}
                </button>
            </form>
        </div>
        @endforeach
    </div>

    {{-- Comparatif plans --}}
    <div class="compare-table">
        <table>
            <thead>
                <tr>
                    <th>Plan</th>
                    <th>Mensuel</th>
                    <th>Annuel</th>
                    <th style="text-align:center">Économie / an</th>
                </tr>
            </thead>
            <tbody>
                @foreach($niveaux as $niveauKey => $n)
                @php
                    $pM     = $tarifsNiveau[$niveauKey]['mensuel'];
                    $pA     = $tarifsNiveau[$niveauKey]['annuel'];
                    $saving = ($pM * 12) - $pA;
                @endphp
                <tr>
                    <td>{{ $n['label'] }}</td>
                    <td class="{{ $n['popular'] ? 'gold' : '' }}">{{ number_format($pM, 0, ',', ' ') }} FCFA</td>
                    <td class="{{ $n['popular'] ? 'gold' : '' }}">{{ number_format($pA, 0, ',', ' ') }} FCFA</td>
                    <td style="text-align:center">
                        <span class="eco-pill">{{ number_format($saving, 0, ',', ' ') }} FCFA</span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- Fonctionnalités incluses --}}
    <div class="features-card">
        <div class="features-head">
            <div class="features-head-title">Fonctionnalités incluses dans votre abonnement</div>
            @if($estActif || $estEssai)
                <span class="niveau-badge {{ $niveauBrut }}">{{ $niveauLabel }}</span>
            @else
                <span class="niveau-badge pro">Plan Pro</span>
            @endif
        </div>
        <div class="features-grid">
            @foreach($featuresList as $label => $niveauReqis)
            @php
                $posRequise  = array_search($niveauReqis, $hierarchy);
                // En essai : accès Pro complet (posEffective basé sur 'pro' = index 1)
                $posCheck    = ($estEssai) ? 1 : $posEffective;
                $accessible  = $posCheck >= $posRequise;
                $reqLabel    = $plansCfg['labels'][$niveauReqis] ?? ucfirst($niveauReqis);
            @endphp
            <div class="feature-item {{ $accessible ? 'unlocked' : 'locked' }}">
                @if($accessible)
                    <svg class="feature-icon-ok" style="width:15px;height:15px;flex-shrink:0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                @else
                    <svg class="feature-icon-lock" style="width:15px;height:15px;flex-shrink:0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                @endif
                {{ $label }}
                @if(!$accessible)
                    <span class="feature-lock-pill">Plan {{ $reqLabel }}</span>
                @endif
            </div>
            @endforeach
        </div>
    </div>

    {{-- Historique --}}
    @if($historique->count() > 0)
    <div class="hist-card">
        <div class="hist-head">
            <div class="hist-head-title">Historique des paiements</div>
            <span style="background:#21262d;color:#8b949e;font-size:11px;font-weight:600;padding:3px 10px;border-radius:6px">{{ $historique->count() }} entrée{{ $historique->count() > 1 ? 's' : '' }}</span>
        </div>
        <div style="overflow-x:auto">
            <table class="hist-table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Plan</th>
                        <th>Méthode</th>
                        <th>Période</th>
                        <th style="text-align:right">Montant</th>
                        <th style="text-align:center">Statut</th>
                        <th>Référence</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($historique as $paiement)
                    <tr>
                        <td>{{ $paiement->created_at->format('d/m/Y') }}</td>
                        <td><span class="plan-pill">{{ \App\Models\Subscription::LABELS[$paiement->plan] ?? $paiement->plan }}</span></td>
                        <td>{{ \App\Models\SubscriptionPayment::METHODE_LABELS[$paiement->methode] ?? $paiement->methode }}</td>
                        <td style="font-size:11px">
                            @if($paiement->periode_debut && $paiement->periode_fin)
                                {{ $paiement->periode_debut->format('d/m/Y') }} → {{ $paiement->periode_fin->format('d/m/Y') }}
                            @else — @endif
                        </td>
                        <td style="text-align:right;font-family:'Syne',sans-serif;font-weight:700;color:#e6edf3">
                            {{ number_format($paiement->montant, 0, ',', ' ') }} F
                        </td>
                        <td style="text-align:center">
                            @php $sc = ['payé'=>'paye','en_attente'=>'attente','échoué'=>'echoue']; @endphp
                            <span class="hist-badge {{ $sc[$paiement->statut] ?? 'attente' }}">
                                {{ \App\Models\SubscriptionPayment::STATUT_LABELS[$paiement->statut] ?? $paiement->statut }}
                            </span>
                        </td>
                        <td style="font-family:monospace;font-size:10px;color:#484f58">{{ $paiement->reference ?? '—' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    {{-- Support --}}
    <div class="support-card">
        <div class="support-left">
            <div class="support-icon">
                <svg style="width:18px;height:18px;color:#c9a84c" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z"/></svg>
            </div>
            <div>
                <div class="support-title">Une question sur votre abonnement ?</div>
                <div class="support-sub">Notre équipe répond sous 2h en jours ouvrables.</div>
            </div>
        </div>
        <div class="support-contacts">
            <div class="support-contact">
                <svg style="width:13px;height:13px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                support@bimotech.sn
            </div>
            <div class="support-contact">
                <svg style="width:13px;height:13px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07A19.5 19.5 0 013.07 9.8a19.79 19.79 0 01-3.07-8.67A2 2 0 012 0h3a2 2 0 012 1.72c.127.96.361 1.903.7 2.81a2 2 0 01-.45 2.11L6.09 7.91a16 16 0 006 6l1.27-1.27a2 2 0 012.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0122 14.92z"/></svg>
                +221 33 800 00 01
            </div>
        </div>
    </div>

</div>

@push('scripts')
<script>
const tarifsNiveau = @json($tarifsNiveau);

function setBilling(mode) {
    const isMensuel = (mode === 'mensuel');
    document.getElementById('btn-mensuel').classList.toggle('active', isMensuel);
    document.getElementById('btn-annuel').classList.toggle('active', !isMensuel);

    ['starter', 'pro', 'agence'].forEach(n => {
        const price  = isMensuel ? tarifsNiveau[n].mensuel : tarifsNiveau[n].annuel;
        const period = isMensuel ? '/ mois' : '/ an';
        document.getElementById('price-' + n).innerHTML =
            new Intl.NumberFormat('fr-FR').format(price) + ' <span>FCFA ' + period + '</span>';

        document.getElementById('annual-detail-' + n).style.display = isMensuel ? 'none' : 'block';
        document.getElementById('monthly-ph-' + n).style.display    = isMensuel ? 'block' : 'none';
    });

    document.querySelectorAll('.plan-billing-input').forEach(el => { el.value = mode; });
}
</script>
@endpush
@endsection
