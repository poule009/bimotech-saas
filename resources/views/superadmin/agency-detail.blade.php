@extends('layouts.superadmin')

@section('title', $agency->name)

@php
    use App\Models\SubscriptionPayment;

    $fmt = fn ($n) => number_format((float) $n, 0, ',', ' ');

    $initials = \Illuminate\Support\Str::upper(
        \Illuminate\Support\Str::of($agency->name)->explode(' ')->filter()
            ->map(fn ($w) => \Illuminate\Support\Str::substr($w, 0, 1))->take(2)->implode('')
    );

    $chip = [
        'green' => 'bg-green/10 text-green',
        'gold'  => 'bg-gold/10 text-gold',
        'red'   => 'bg-error/10 text-error',
    ];

    // Libellé du plan (niveau) — « Essai » tant que l'abonnement n'est pas payé.
    $planLabel = $subscription && $subscription->plan_niveau
        ? config('plans.labels.'.$subscription->plan_niveau, ucfirst($subscription->plan_niveau))
        : ($subscription?->estEnEssai() ? 'Essai' : '—');

    $cycleLabel = $subscription && $subscription->plan
        ? (\App\Models\Subscription::LABELS[$subscription->plan] ?? ucfirst($subscription->plan))
        : null;

    // Dernière méthode de paiement enregistrée (paiements triés récent → ancien).
    $derniereMethode = optional($paiements->firstWhere(fn ($p) => (bool) $p->methode))->methode;

    // Jauges d'usage : [label, valeur, limite(null=illimité)].
    $jauges = [
        ['label' => 'Biens (unités)',  'val' => $stats['nb_biens'],  'lim' => $limites['unites']],
        ['label' => 'Comptes admins',  'val' => $stats['nb_admins'], 'lim' => $limites['admins']],
    ];

    $activiteStr = function (?\Carbon\Carbon $d) {
        if (! $d) return '';
        if ($d->isToday()) return $d->format('H:i');
        if ($d->isYesterday()) return 'Hier, '.$d->format('H:i');
        return $d->locale('fr')->isoFormat('D MMM');
    };
@endphp

@section('content')
<div class="max-w-[1100px] mx-auto" x-data="agencyTabs">

    {{-- Fil d'ariane --}}
    <div class="text-[12.5px] text-muted mb-4">
        <a href="{{ route('superadmin.agencies.index') }}" class="text-teal font-semibold border-b border-gold pb-px">Agences</a>
        <span class="mx-1.5">/</span>{{ $agency->name }}
    </div>

    {{-- Flash --}}
    @if(session('success'))
        <div class="mb-4 rounded-lg bg-green/10 border border-green/25 px-4 py-3 text-[13px] text-green">{{ session('success') }}</div>
    @endif
    @if(session('error') || $errors->any())
        <div class="mb-4 rounded-lg bg-error/10 border border-error/25 px-4 py-3 text-[13px] text-error">{{ session('error') ?? $errors->first() }}</div>
    @endif

    {{-- ─────────── En-tête agence ─────────── --}}
    <div class="bg-white border border-line rounded-2xl p-6 mb-5 flex items-center justify-between gap-4 flex-wrap">
        <div class="flex items-center gap-4 min-w-0">
            <div class="w-[54px] h-[54px] rounded-xl bg-teal-deep text-gold-soft flex items-center justify-center font-display font-semibold text-[19px] shrink-0">{{ $initials }}</div>
            <div class="min-w-0">
                <h1 class="font-display font-medium text-[24px] text-ink truncate">{{ $agency->name }}</h1>
                <div class="flex items-center gap-2.5 text-[12.5px] text-muted mt-1 flex-wrap">
                    <span class="text-[11.5px] font-semibold px-2.5 py-0.5 rounded-full bg-paper-dim text-teal-deep">{{ $planLabel }}</span>
                    <span class="text-[11.5px] font-semibold px-2.5 py-0.5 rounded-full inline-flex items-center gap-1.5 {{ $chip[$statut['variant']] }}">
                        <span class="w-1.5 h-1.5 rounded-full bg-current"></span>{{ $statut['label'] }}
                    </span>
                    @if($agency->adresse)<span>{{ $agency->adresse }}</span>@endif
                </div>
            </div>
        </div>

        <div class="flex items-center gap-2.5 flex-wrap">
            <a href="{{ route('superadmin.activity-logs.index', ['agency' => $agency->id]) }}"
               class="text-[13px] font-semibold px-4 py-2.5 rounded-lg border border-line bg-white text-ink hover:bg-paper-dim transition-colors">Voir le journal</a>

            <form method="POST" action="{{ route('superadmin.agencies.toggle', $agency) }}"
                  x-data="confirmForm" x-on:submit="submit"
                  data-confirm="{{ $agency->actif ? 'Suspendre '.$agency->name.' ? L\'accès sera coupé immédiatement.' : 'Réactiver '.$agency->name.' ?' }}">
                @csrf
                @method('PATCH')
                @if($agency->actif)
                    <button type="submit" class="text-[13px] font-semibold px-4 py-2.5 rounded-lg border border-error/30 text-error hover:bg-error/5 transition-colors">Suspendre</button>
                @else
                    <button type="submit" class="text-[13px] font-semibold px-4 py-2.5 rounded-lg border border-green/40 text-green hover:bg-green/5 transition-colors">Réactiver</button>
                @endif
            </form>

            @if($adminCible)
                <form method="POST" action="{{ route('superadmin.impersonate', $adminCible) }}"
                      x-data="confirmForm" x-on:submit="submit"
                      data-confirm="Se connecter en tant que {{ $agency->name }} ({{ $adminCible->name }}) ? Cette action est tracée dans le journal.">
                    @csrf
                    <button type="submit" class="flex items-center gap-2 text-[13px] font-semibold px-4 py-2.5 rounded-lg bg-gold text-white hover:bg-[#A67A24] transition-colors">
                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/><path d="M10 17l5-5-5-5"/><path d="M15 12H3"/></svg>
                        Se connecter en tant qu'agence
                    </button>
                </form>
            @else
                <span class="text-[12.5px] text-muted italic px-2">Aucun compte admin à impersonner</span>
            @endif
        </div>
    </div>

    {{-- Bandeau d'avertissement impersonation --}}
    <div class="flex items-center gap-2.5 bg-gold/[0.08] border border-gold-soft rounded-lg px-4 py-3 text-[12.5px] text-gold mb-6">
        <svg class="w-4 h-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10.29 3.86 1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><path d="M12 9v4M12 17h.01"/></svg>
        La connexion en tant qu'agence est tracée automatiquement dans le journal d'activité — visible par tous les administrateurs.
    </div>

    {{-- ─────────── Onglets ─────────── --}}
    <div class="flex gap-1 border-b border-line mb-6 overflow-x-auto">
        <button type="button" x-on:click="showInfos"    x-bind:class="infosClass"    class="px-4 py-2.5 text-[13.5px] font-semibold border-b-2 -mb-px whitespace-nowrap transition-colors">Informations</button>
        <button type="button" x-on:click="showAbo"      x-bind:class="aboClass"      class="px-4 py-2.5 text-[13.5px] font-semibold border-b-2 -mb-px whitespace-nowrap transition-colors">Abonnement &amp; facturation</button>
        <button type="button" x-on:click="showUsage"    x-bind:class="usageClass"    class="px-4 py-2.5 text-[13.5px] font-semibold border-b-2 -mb-px whitespace-nowrap transition-colors">Usage</button>
        <button type="button" x-on:click="showActivite" x-bind:class="activiteClass" class="px-4 py-2.5 text-[13.5px] font-semibold border-b-2 -mb-px whitespace-nowrap transition-colors">Activité</button>
    </div>

    {{-- ─────────── Onglet 1 · Informations ─────────── --}}
    <div x-show="isInfos" x-cloak class="grid grid-cols-1 lg:grid-cols-[1.3fr_1fr] gap-5">
        <section class="bg-white border border-line rounded-xl overflow-hidden">
            <div class="flex items-center justify-between px-5 py-4 border-b border-paper-dim">
                <span class="font-display font-medium text-[16.5px] text-ink">Informations générales</span>
                <a href="{{ route('superadmin.agencies.edit', $agency) }}" class="text-[12.5px] font-semibold text-teal border-b border-gold pb-px">Modifier</a>
            </div>
            <div class="px-5 py-1">
                @php
                    $infos = [
                        'Contact principal' => $adminCible?->name ?? '—',
                        'Téléphone'         => $agency->telephone ?: '—',
                        'Email'             => $agency->email ?: '—',
                        'Adresse'           => $agency->adresse ?: '—',
                        'Inscrite le'       => $agency->created_at?->locale('fr')->isoFormat('D MMMM Y') ?? '—',
                        'Comptes utilisateurs' => $stats['nb_users'].' compte'.($stats['nb_users'] > 1 ? 's' : ''),
                    ];
                @endphp
                @foreach($infos as $label => $value)
                    <div class="flex justify-between gap-4 py-3 text-[13.5px] border-b border-paper-dim last:border-0">
                        <span class="text-muted">{{ $label }}</span>
                        <span class="font-semibold text-ink text-right">{{ $value }}</span>
                    </div>
                @endforeach
            </div>
        </section>

        <section class="bg-white border border-line rounded-xl overflow-hidden h-fit">
            <div class="px-5 py-4 border-b border-paper-dim font-display font-medium text-[16.5px] text-ink">Répartition des comptes</div>
            <div class="px-5 py-1">
                @foreach(['Administrateurs' => $stats['nb_admins'], 'Propriétaires' => $stats['nb_proprietaires'], 'Locataires' => $stats['nb_locataires']] as $label => $n)
                    <div class="flex justify-between gap-4 py-3 text-[13.5px] border-b border-paper-dim last:border-0">
                        <span class="text-muted">{{ $label }}</span>
                        <span class="font-semibold text-ink tabular-nums">{{ $fmt($n) }}</span>
                    </div>
                @endforeach
            </div>
        </section>
    </div>

    {{-- ─────────── Onglet 2 · Abonnement & facturation ─────────── --}}
    <div x-show="isAbo" x-cloak class="grid grid-cols-1 lg:grid-cols-[1fr_1.3fr] gap-5">
        <section class="bg-white border border-line rounded-xl overflow-hidden h-fit">
            <div class="px-5 py-4 border-b border-paper-dim font-display font-medium text-[16.5px] text-ink">Abonnement</div>
            <div class="px-5 py-1">
                @php
                    $abo = [
                        'Plan'            => $planLabel.($cycleLabel ? ' · '.$cycleLabel : ''),
                        'Montant'         => $subscription?->montant_paye ? $fmt($subscription->montant_paye).' FCFA' : '—',
                        'Début'           => $subscription?->date_debut_abonnement?->locale('fr')->isoFormat('D MMM Y') ?? '—',
                        'Prochaine échéance' => $subscription?->date_fin_abonnement?->locale('fr')->isoFormat('D MMM Y') ?? '—',
                        'Méthode'         => $derniereMethode ? (SubscriptionPayment::METHODE_LABELS[$derniereMethode] ?? $derniereMethode) : '—',
                    ];
                @endphp
                @foreach($abo as $label => $value)
                    <div class="flex justify-between gap-4 py-3 text-[13.5px] border-b border-paper-dim last:border-0">
                        <span class="text-muted">{{ $label }}</span>
                        <span class="font-semibold text-ink text-right">{{ $value }}</span>
                    </div>
                @endforeach
            </div>
        </section>

        <section class="bg-white border border-line rounded-xl overflow-hidden">
            <div class="px-5 py-4 border-b border-paper-dim font-display font-medium text-[16.5px] text-ink">Historique des paiements</div>
            <div class="px-5 py-1">
                @forelse($paiements as $p)
                    @php
                        $ok = in_array($p->statut, [SubscriptionPayment::STATUT_CONFIRME, 'payé'], true);
                        $attente = $p->statut === SubscriptionPayment::STATUT_EN_ATTENTE;
                    @endphp
                    <div class="flex items-center gap-3 py-3 text-[13.5px] border-b border-paper-dim last:border-0">
                        <div @class([
                            'w-[30px] h-[30px] rounded-lg flex items-center justify-center shrink-0 text-[13px] font-bold',
                            'bg-green/10 text-green' => $ok,
                            'bg-amber/10 text-amber' => $attente,
                            'bg-error/10 text-error' => ! $ok && ! $attente,
                        ])>{{ $ok ? '✓' : ($attente ? '⏳' : '✕') }}</div>
                        <div class="flex-1 min-w-0">
                            <div class="font-semibold text-ink">{{ $p->statut_label }}</div>
                            <div class="text-[12px] text-muted">
                                {{ $p->created_at?->locale('fr')->isoFormat('D MMM Y') }}
                                @if($p->methode) · {{ SubscriptionPayment::METHODE_LABELS[$p->methode] ?? $p->methode }}@endif
                            </div>
                        </div>
                        <div class="tabular-nums font-semibold text-[13px] text-ink whitespace-nowrap">{{ $fmt($p->montant) }} F</div>
                    </div>
                @empty
                    <div class="py-8 text-center text-[13px] text-muted">Aucun paiement enregistré.</div>
                @endforelse
            </div>
        </section>
    </div>

    {{-- ─────────── Onglet 3 · Usage ─────────── --}}
    <div x-show="isUsage" x-cloak>
        <section class="bg-white border border-line rounded-xl overflow-hidden max-w-[640px]">
            <div class="px-5 py-4 border-b border-paper-dim font-display font-medium text-[16.5px] text-ink">Usage vs limites du plan</div>
            <div class="px-5 py-5 space-y-5">
                @foreach($jauges as $j)
                    @php
                        $lim = $j['lim'];
                        $pct = ($lim && $lim > 0) ? min(100, (int) round($j['val'] / $lim * 100)) : 0;
                        $high = $pct >= 80;
                    @endphp
                    <div>
                        <div class="flex justify-between items-baseline text-[13px] mb-1.5">
                            <b class="font-semibold text-ink">{{ $j['label'] }}</b>
                            <span class="text-muted tabular-nums text-[12px]">{{ $lim === null ? $j['val'].' · illimité' : $j['val'].' / '.$lim }}</span>
                        </div>
                        <div class="h-[7px] bg-paper-dim rounded-full overflow-hidden">
                            <div class="h-full rounded-full {{ $lim === null ? 'bg-teal' : ($high ? 'bg-gold' : 'bg-teal') }}" style="width: {{ $lim === null ? 100 : $pct }}%"></div>
                        </div>
                    </div>
                @endforeach

                {{-- Contrats actifs : pas de plafond de plan → simple compteur. --}}
                <div class="flex justify-between items-baseline text-[13px] pt-1 border-t border-paper-dim mt-1">
                    <b class="font-semibold text-ink pt-4">Contrats actifs</b>
                    <span class="text-ink font-semibold tabular-nums pt-4">{{ $fmt($stats['nb_contrats']) }}</span>
                </div>
            </div>
        </section>
    </div>

    {{-- ─────────── Onglet 4 · Activité ─────────── --}}
    <div x-show="isActivite" x-cloak>
        <section class="bg-white border border-line rounded-xl overflow-hidden max-w-[720px]">
            <div class="flex items-center justify-between px-5 py-4 border-b border-paper-dim">
                <div class="font-display font-medium text-[16.5px] text-ink">Activité de l'agence</div>
                <a href="{{ route('superadmin.activity-logs.index', ['agency' => $agency->id]) }}" class="text-[12.5px] font-semibold text-teal border-b border-gold pb-px">Journal complet</a>
            </div>
            <ul class="py-1.5">
                @forelse($activites as $act)
                    <li class="flex gap-3 px-5 py-3 border-b border-paper-dim last:border-0">
                        <div class="w-[7px] h-[7px] rounded-full bg-gold mt-1.5 shrink-0"></div>
                        <div class="text-[13.5px] leading-snug text-ink min-w-0 flex-1">
                            {{ $act->description }}
                            @if($act->user?->name)<span class="text-muted"> — {{ $act->user->name }}</span>@endif
                        </div>
                        <div class="text-[11.5px] text-muted whitespace-nowrap ml-auto pl-2.5 font-medium tabular-nums">{{ $activiteStr($act->created_at) }}</div>
                    </li>
                @empty
                    <li class="px-5 py-8 text-center text-[13px] text-muted">Aucune activité enregistrée pour cette agence.</li>
                @endforelse
            </ul>
        </section>
    </div>

</div>
@endsection
