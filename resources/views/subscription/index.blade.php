@extends('layouts.app')

@php
    use App\Models\Subscription;
    $fmt = fn ($n) => number_format((float) $n, 0, ',', ' ');

    // $plans provient du controller (dérivé de config/plans.php — source unique).
    $niveauActuel = $subscription?->plan_niveau ?? 'starter';
    if ($niveauActuel === 'legacy') $niveauActuel = 'pro';

    $barCls = fn ($pct) => $pct >= 100 ? 'bg-error' : ($pct >= 80 ? 'bg-amber' : 'bg-green');
@endphp

@section('title', 'Abonnement')
@section('page-title', 'Abonnement')

@section('content')
<div class="max-w-[1000px]">

    @if(session('success'))
        <div class="mb-5 rounded-lg bg-green/10 border border-green/25 px-4 py-3 text-[13px] text-green flex items-center gap-2"><x-icon name="check-circle" size="16" /> {{ session('success') }}</div>
    @endif
    @if(session('warning'))
        <div class="mb-5 rounded-lg bg-amber/10 border border-amber/30 px-4 py-3 text-[13px] text-amber flex items-center gap-2"><x-icon name="alert-triangle" size="16" /> {{ session('warning') }}</div>
    @endif

    {{-- ─────────── Bannière d'état ─────────── --}}
    @if($subscription)
        @php
            $banners = [
                Subscription::ETAT_ESSAI => [
                    'grad'  => 'from-teal to-green',
                    'icon'  => 'sparkles',
                    'title' => 'Essai gratuit — ' . $subscription->joursRestantsEssai() . ' jour' . ($subscription->joursRestantsEssai() > 1 ? 's' : '') . ' restant' . ($subscription->joursRestantsEssai() > 1 ? 's' : ''),
                    'sub'   => 'Profitez de toutes les fonctionnalités. Aucun paiement requis pendant l\'essai.',
                ],
                Subscription::ETAT_GRACE => [
                    'grad'  => 'from-amber to-gold',
                    'icon'  => 'clock',
                    'title' => 'Période de grâce — ' . $subscription->joursRestantsGrace() . ' jour' . ($subscription->joursRestantsGrace() > 1 ? 's' : '') . ' pour régulariser',
                    'sub'   => 'Vous pouvez consulter vos données, mais plus en créer ni modifier. Déclarez votre paiement pour retrouver un accès complet.',
                ],
                Subscription::ETAT_SUSPENDU => [
                    'grad'  => 'from-error to-crit',
                    'icon'  => 'alert-triangle',
                    'title' => 'Compte suspendu',
                    'sub'   => 'La période de grâce est terminée. Déclarez votre paiement pour réactiver votre agence — vos données sont conservées.',
                ],
                Subscription::ETAT_ACTIF => [
                    'grad'  => 'from-green to-teal',
                    'icon'  => 'check-circle',
                    'title' => 'Abonnement actif — plan ' . ($plans[$niveauActuel]['nom'] ?? ucfirst($niveauActuel)),
                    'sub'   => $subscription->date_fin_abonnement ? 'Prochaine échéance le ' . $subscription->date_fin_abonnement->format('d/m/Y') . '.' : 'Accès complet.',
                ],
            ];
            $b = $banners[$etat] ?? $banners[Subscription::ETAT_SUSPENDU];
        @endphp
        <div class="rounded-2xl p-6 mb-6 text-white bg-gradient-to-br {{ $b['grad'] }} shadow-md flex items-center gap-5 flex-wrap">
            <span class="w-12 h-12 rounded-[14px] bg-white/20 flex items-center justify-center shrink-0"><x-icon name="{{ $b['icon'] }}" size="24" /></span>
            <div class="flex-1 min-w-[220px]">
                <div class="font-display font-semibold text-[16px] mb-0.5">{{ $b['title'] }}</div>
                <div class="text-[13px] text-white/90 leading-snug">{{ $b['sub'] }}</div>
            </div>
            @if($etat !== Subscription::ETAT_ACTIF)
                <a href="{{ route('subscription.declarer') }}" class="bg-white text-teal px-5 py-2.5 rounded-[10px] text-[13px] font-bold shrink-0">Déclarer un paiement</a>
            @endif
        </div>
    @endif

    {{-- ─────────── Barres d'usage ─────────── --}}
    <div class="grid sm:grid-cols-2 gap-4 mb-6">
        @foreach([['Biens gérés', $usage['biens']], ['Comptes équipe', $usage['equipe']]] as [$label, $u])
            <div class="bg-white border border-line rounded-[14px] px-6 py-5">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-[13px] font-bold text-teal-deep">{{ $label }}</span>
                    <span class="font-display font-semibold text-[15px]">{{ $u['n'] }} / {{ $u['max'] ?? '∞' }}</span>
                </div>
                <div class="h-2.5 bg-paper-dim rounded-full overflow-hidden">
                    <div class="h-full rounded-full {{ $barCls($u['pct']) }}" style="width: {{ $u['max'] ? $u['pct'] : 8 }}%"></div>
                </div>
                @if($u['max'] && $u['pct'] >= 80)
                    <div class="text-[11.5px] text-amber font-semibold mt-2">Proche de la limite — pensez à passer au plan supérieur.</div>
                @elseif(!$u['max'])
                    <div class="text-[11.5px] text-muted mt-2">Illimité sur ce plan.</div>
                @endif
            </div>
        @endforeach
    </div>

    {{-- ─────────── Plans ─────────── --}}
    <div class="bg-white border border-line rounded-2xl p-7 mb-6">
        <div class="f-card-title">Votre plan</div>
        <p class="f-card-sub">Toutes les fonctionnalités sont incluses dans chaque plan — seuls le nombre de biens et de comptes équipe varient.</p>

        <div class="grid sm:grid-cols-3 gap-4">
            @foreach($plans as $key => $p)
                @php $courant = $key === $niveauActuel; @endphp
                <div @class([
                        'relative border-2 rounded-[14px] p-5 text-center',
                        'border-teal bg-paper' => $courant,
                        'border-line' => ! $courant,
                    ])>
                    @if($courant)<span class="absolute -top-3 left-1/2 -translate-x-1/2 bg-teal text-white text-[10.5px] font-bold px-3 py-1 rounded-full">Plan actuel</span>@endif
                    <div class="font-display font-semibold text-[17px] mb-1">{{ $p['nom'] }}</div>
                    <div class="text-[22px] font-bold mb-1">{{ $fmt($p['prix']) }} F<span class="text-[12px] font-normal text-muted">/mois</span></div>
                    <div class="text-[12.5px] text-muted mb-4 leading-relaxed">{{ $p['biens'] }}<br>{{ $p['equipe'] }}</div>
                    @if($courant)
                        <div class="w-full py-2.5 rounded-[9px] text-[13px] font-bold bg-teal text-white">Plan actuel</div>
                    @else
                        <a href="{{ route('subscription.declarer', ['plan' => $key]) }}" class="block w-full py-2.5 rounded-[9px] text-[13px] font-bold border-[1.5px] border-teal text-teal hover:bg-teal hover:text-white transition-colors">Passer à ce plan</a>
                    @endif
                </div>
            @endforeach
        </div>
    </div>

    {{-- ─────────── Historique ─────────── --}}
    <div class="bg-white border border-line rounded-2xl p-7">
        <div class="f-card-title mb-4">Historique des paiements</div>
        @if($historique->isEmpty())
            <p class="text-[13.5px] text-muted">Aucun paiement déclaré pour le moment.</p>
        @else
            <div class="overflow-x-auto">
                <table class="w-full border-collapse text-[13.5px]">
                    <thead>
                        <tr class="text-left">
                            <th class="px-3 py-2.5 bg-paper-dim text-[11px] uppercase text-muted font-bold rounded-l-lg">Date</th>
                            <th class="px-3 py-2.5 bg-paper-dim text-[11px] uppercase text-muted font-bold">Montant</th>
                            <th class="px-3 py-2.5 bg-paper-dim text-[11px] uppercase text-muted font-bold">Méthode</th>
                            <th class="px-3 py-2.5 bg-paper-dim text-[11px] uppercase text-muted font-bold rounded-r-lg">Statut</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($historique as $p)
                            @php
                                $pill = match($p->statut) {
                                    'confirme', 'payé'    => ['bg-green/12 text-green', 'Confirmé'],
                                    'rejete', 'échoué'    => ['bg-error/12 text-error', 'Rejeté'],
                                    default               => ['bg-amber/15 text-amber', 'En attente'],
                                };
                            @endphp
                            <tr class="border-b border-paper-dim last:border-0">
                                <td class="px-3 py-3">{{ $p->created_at->format('d/m/Y') }}</td>
                                <td class="px-3 py-3">{{ $fmt($p->montant) }} F</td>
                                <td class="px-3 py-3">{{ \App\Models\SubscriptionPayment::METHODE_LABELS[$p->methode] ?? ucfirst($p->methode) }}</td>
                                <td class="px-3 py-3">
                                    <span class="text-[11px] font-bold px-2.5 py-1 rounded-full {{ $pill[0] }}">{{ $pill[1] }}</span>
                                    @if(($p->statut === 'rejete' || $p->statut === 'échoué') && $p->motif_rejet)
                                        <span class="text-[11.5px] text-error ml-1">— {{ $p->motif_rejet }}</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

</div>
@endsection
