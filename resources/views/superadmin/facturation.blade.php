@extends('layouts.superadmin')

@section('title', 'Abonnements & facturation')

@php
    use App\Models\Subscription;
    use App\Models\SubscriptionPayment;

    $fmt = fn ($n) => number_format((float) $n, 0, ',', ' ');

    // Pastille de statut : vert = encaissé, rouge = rejeté, or = en attente.
    // On passe par le bucket du modèle, qui absorbe les anciens statuts PayTech
    // ('payé'/'échoué') encore présents dans l'historique.
    $statutChip = [
        'confirme'   => ['Payé',       'bg-green/10 text-green'],
        'rejete'     => ['Rejeté',     'bg-error/10 text-error'],
        'en_attente' => ['En attente', 'bg-gold/10 text-gold'],
        'rembourse'  => ['Remboursé',  'bg-paper-dim text-muted'],
    ];

    $statutOptions = [
        ''           => 'Statut : Tous',
        'confirme'   => 'Payés',
        'en_attente' => 'En attente',
        'rejete'     => 'Rejetés',
    ];

    $periodeOptions = [
        'mois'  => 'Période : Ce mois',
        '30j'   => '30 derniers jours',
        'tout'  => 'Tout l\'historique',
        'perso' => 'Personnalisée…',
    ];
@endphp

@section('content')
<div class="max-w-[1240px] mx-auto">

    {{-- ─────────── En-tête ─────────── --}}
    <div class="mb-6">
        <div class="text-[12px] font-semibold tracking-[0.12em] uppercase text-gold">Gestion plateforme</div>
        <h1 class="font-display font-medium text-[30px] md:text-[32px] text-ink mt-1">Abonnements &amp; facturation</h1>
        <p class="text-[14.5px] text-muted mt-1.5">Vue d'ensemble de tous les paiements, toutes agences confondues.</p>
    </div>

    @if(session('success'))
        <div class="mb-5 rounded-lg bg-green/10 border border-green/25 px-4 py-3 text-[13px] text-green">{{ session('success') }}</div>
    @endif
    @if(session('error') || $errors->any())
        <div class="mb-5 rounded-lg bg-error/10 border border-error/25 px-4 py-3 text-[13px] text-error">
            {{ session('error') ?? $errors->first() }}
        </div>
    @endif

    {{-- ─────────── Sous-navigation ─────────── --}}
    <div class="flex gap-6 border-b border-line mb-6">
        <a href="{{ route('superadmin.facturation') }}"
           class="pb-3 -mb-px text-[13.5px] font-semibold border-b-2 border-gold text-teal-deep">Facturation</a>
        <a href="{{ route('superadmin.plans.config') }}"
           class="pb-3 -mb-px text-[13.5px] font-semibold border-b-2 border-transparent text-muted hover:text-ink transition-colors">Configuration des plans</a>
    </div>

    {{-- ─────────── KPI ─────────── --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3.5 mb-7">
        <div class="bg-white border border-line rounded-xl p-[18px]">
            <div class="text-[11.5px] font-semibold uppercase tracking-wide text-muted mb-2.5">Encaissé · {{ $periodeLabel }}</div>
            <div class="font-display font-medium text-[26px] leading-none text-green">{{ $fmt($stats['encaisse']) }}</div>
            <div class="mt-2.5 text-[12px] text-muted">FCFA — {{ $fmt($stats['nb_encaisses']) }} paiement{{ $stats['nb_encaisses'] > 1 ? 's' : '' }} réussi{{ $stats['nb_encaisses'] > 1 ? 's' : '' }}</div>
        </div>
        <div class="bg-white border border-line rounded-xl p-[18px]">
            <div class="text-[11.5px] font-semibold uppercase tracking-wide text-muted mb-2.5">Rejetés</div>
            <div class="font-display font-medium text-[26px] leading-none {{ $stats['montant_echecs'] > 0 ? 'text-error' : 'text-ink' }}">{{ $fmt($stats['montant_echecs']) }}</div>
            <div class="mt-2.5 text-[12px] text-muted">FCFA — {{ $fmt($stats['nb_agences_echec']) }} agence{{ $stats['nb_agences_echec'] > 1 ? 's' : '' }} concernée{{ $stats['nb_agences_echec'] > 1 ? 's' : '' }}</div>
        </div>
        <div class="bg-white border border-line rounded-xl p-[18px]">
            <div class="text-[11.5px] font-semibold uppercase tracking-wide text-muted mb-2.5">MRR actuel</div>
            <div class="font-display font-medium text-[26px] leading-none text-ink">{{ $fmt($stats['mrr']) }}</div>
            <div class="mt-2.5 text-[12px] text-muted">FCFA / mois</div>
        </div>
        <div class="bg-white border border-line rounded-xl p-[18px]">
            <div class="text-[11.5px] font-semibold uppercase tracking-wide text-muted mb-2.5">Taux de réussite</div>
            <div class="font-display font-medium text-[26px] leading-none text-ink">
                {{ $stats['taux_reussite'] === null ? '—' : $stats['taux_reussite'].'%' }}
            </div>
            <div class="mt-2.5 text-[12px] text-muted">
                {{ $stats['taux_reussite'] === null ? 'aucun paiement traité sur 30 j' : 'sur les 30 derniers jours' }}
            </div>
        </div>
    </div>

    {{-- Rappel actionnable : des paiements attendent une validation manuelle. --}}
    @if($stats['nb_attente'] > 0 && $filtres['statut'] !== 'en_attente')
        <a href="{{ route('superadmin.facturation', ['statut' => 'en_attente', 'periode' => 'tout']) }}"
           class="flex items-center gap-2.5 mb-5 rounded-lg bg-gold/10 border border-gold/25 px-4 py-3 text-[13px] text-gold-deep hover:bg-gold/15 transition-colors">
            <svg class="w-4 h-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="12" r="10"/><path d="M12 8v4l2.5 2.5"/></svg>
            <span><strong>{{ $fmt($stats['nb_attente']) }} paiement{{ $stats['nb_attente'] > 1 ? 's' : '' }}</strong> en attente de validation — à confirmer ou rejeter.</span>
        </a>
    @endif

    {{-- ─────────── Filtres ─────────── --}}
    <form method="GET" action="{{ route('superadmin.facturation') }}" x-data="billingFilters('{{ $filtres['periode'] }}')"
          class="flex items-center gap-3 mb-4 flex-wrap">
        <select name="statut" x-on:change="apply"
                class="bg-white border border-line rounded-lg px-3 py-2.5 text-[13px] font-medium text-ink cursor-pointer">
            @foreach($statutOptions as $val => $label)
                <option value="{{ $val }}" @selected($filtres['statut'] === ($val ?: null))>{{ $label }}</option>
            @endforeach
        </select>

        <select name="plan" x-on:change="apply"
                class="bg-white border border-line rounded-lg px-3 py-2.5 text-[13px] font-medium text-ink cursor-pointer">
            <option value="">Plan : Tous</option>
            @foreach($plans as $niveau => $plan)
                <option value="{{ $niveau }}" @selected($filtres['plan'] === $niveau)>{{ $plan->libelle }}</option>
            @endforeach
        </select>

        <select name="periode" x-on:change="onPeriode"
                class="bg-white border border-line rounded-lg px-3 py-2.5 text-[13px] font-medium text-ink cursor-pointer">
            @foreach($periodeOptions as $val => $label)
                <option value="{{ $val }}" @selected($filtres['periode'] === $val)>{{ $label }}</option>
            @endforeach
        </select>

        {{-- Bornes de la période personnalisée — masquées sur les autres choix. --}}
        <div x-show="isPerso" x-cloak class="flex items-center gap-2">
            <input type="date" name="du" value="{{ $filtres['du'] }}"
                   class="bg-white border border-line rounded-lg px-3 py-2.5 text-[13px] text-ink">
            <span class="text-[13px] text-muted">→</span>
            <input type="date" name="au" value="{{ $filtres['au'] }}"
                   class="bg-white border border-line rounded-lg px-3 py-2.5 text-[13px] text-ink">
            <button type="submit" class="text-[13px] font-semibold px-3 py-2.5 rounded-lg bg-teal text-paper hover:bg-teal-deep transition-colors">Filtrer</button>
        </div>

        <div class="flex-1"></div>

        <a href="{{ route('superadmin.facturation.export', request()->query()) }}"
           class="flex items-center gap-2 bg-white border border-line rounded-lg px-4 py-2.5 text-[13px] font-semibold text-ink hover:bg-paper-dim transition-colors">
            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4M7 10l5 5 5-5M12 15V3"/></svg>
            Exporter
        </a>
        <noscript><button type="submit" class="text-[13px] font-semibold text-teal">Filtrer</button></noscript>
    </form>

    {{-- ─────────── Tableau ─────────── --}}
    <section class="bg-white border border-line rounded-xl overflow-hidden">
        @if($paiements->isEmpty())
            <div class="px-5 py-14 text-center">
                <div class="text-[13.5px] text-muted">Aucun paiement sur cette période.</div>
                @if($filtres['periode'] !== 'tout')
                    <a href="{{ route('superadmin.facturation', ['periode' => 'tout']) }}"
                       class="inline-block mt-2 text-[12.5px] font-semibold text-teal border-b border-gold pb-px">Voir tout l'historique</a>
                @endif
            </div>
        @else
        <div class="overflow-x-auto">
            <table class="w-full border-collapse">
                <thead>
                    <tr class="text-left text-[11px] uppercase tracking-wide text-muted font-semibold bg-paper-dim">
                        <th class="px-4 py-3">Agence</th>
                        <th class="px-4 py-3">Plan</th>
                        <th class="px-4 py-3">Montant</th>
                        <th class="px-4 py-3">Méthode</th>
                        <th class="px-4 py-3 whitespace-nowrap">Date</th>
                        <th class="px-4 py-3">Statut</th>
                        <th class="px-4 py-3 text-right">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($paiements as $p)
                        @php
                            $bord   = $loop->last ? '' : 'border-b border-paper-dim';
                            $bucket = $p->bucket();
                            $chip   = $statutChip[$bucket] ?? [$p->statut_label, 'bg-paper-dim text-muted'];
                            $plan   = $plans->get($p->plan_niveau);
                        @endphp
                        <tr class="text-[13.8px] hover:bg-paper/60 transition-colors">
                            <td class="px-4 py-3.5 {{ $bord }}">
                                <div class="font-semibold text-ink">{{ $p->agency?->name ?? 'Agence #'.$p->agency_id }}</div>
                                <div class="text-[12px] text-muted">{{ $p->agency?->email }}</div>
                            </td>
                            <td class="px-4 py-3.5 {{ $bord }}">
                                <span class="text-[11.5px] font-semibold px-2.5 py-1 rounded-full bg-paper-dim text-teal-deep inline-block">{{ $plan?->libelle ?? ($p->plan_niveau ?: '—') }}</span>
                                <div class="text-[11.5px] text-muted mt-1">{{ Subscription::LABELS[$p->plan] ?? $p->plan }}</div>
                            </td>
                            <td class="px-4 py-3.5 {{ $bord }}">
                                <span class="tabular-nums font-semibold text-ink whitespace-nowrap">{{ $fmt($p->montant) }} F</span>
                            </td>
                            <td class="px-4 py-3.5 {{ $bord }} text-muted text-[12.5px]">{{ SubscriptionPayment::METHODE_LABELS[$p->methode] ?? $p->methode }}</td>
                            <td class="px-4 py-3.5 {{ $bord }} text-muted whitespace-nowrap">{{ $p->created_at?->locale('fr')->isoFormat('D MMM Y') }}</td>
                            <td class="px-4 py-3.5 {{ $bord }}">
                                <span class="text-[11.5px] font-semibold px-2.5 py-1 rounded-full inline-flex items-center gap-1.5 {{ $chip[1] }}">
                                    <span class="w-1.5 h-1.5 rounded-full bg-current"></span>{{ $chip[0] }}
                                </span>
                                @if($p->motif_rejet)
                                    <div class="text-[11.5px] text-muted mt-1 max-w-[180px]">{{ $p->motif_rejet }}</div>
                                @endif
                            </td>
                            <td class="px-4 py-3.5 {{ $bord }} text-right whitespace-nowrap">
                                @if($bucket === 'confirme')
                                    <a href="{{ route('superadmin.paiements.recu', $p) }}"
                                       class="text-[12px] font-semibold text-teal border-b border-gold pb-px">Reçu</a>
                                @elseif($bucket === 'en_attente')
                                    {{-- Validation manuelle : le flux Confirmer / Rejeter vit
                                         ici depuis que l'écran « paiements en attente » a été
                                         absorbé. Le motif de rejet reste obligatoire — il est
                                         affiché à l'agence. --}}
                                    <div x-data="rejectRow">
                                        <div class="flex items-center justify-end gap-2.5">
                                            @if($p->justificatif)
                                                <a href="{{ Storage::disk('public')->url($p->justificatif) }}" target="_blank" rel="noopener"
                                                   class="text-[12px] font-semibold text-muted hover:text-ink border-b border-line pb-px">Justificatif</a>
                                            @endif
                                            <form method="POST" action="{{ route('superadmin.paiements.confirmer', $p) }}"
                                                  x-data="confirmForm" x-on:submit="submit"
                                                  data-confirm="Confirmer ce paiement de {{ $fmt($p->montant) }} F ? L'abonnement de {{ $p->agency?->name }} sera activé.">
                                                @csrf
                                                <button type="submit" class="text-[12px] font-semibold text-green border-b border-green/40 pb-px">Confirmer</button>
                                            </form>
                                            <button type="button" x-on:click="toggle"
                                                    class="text-[12px] font-semibold text-error border-b border-error/40 pb-px">Rejeter</button>
                                        </div>

                                        <form method="POST" action="{{ route('superadmin.paiements.rejeter', $p) }}"
                                              x-show="open" x-cloak class="flex items-center gap-2 mt-2.5">
                                            @csrf
                                            <input type="text" name="motif_rejet" required maxlength="255"
                                                   placeholder="Motif (affiché à l'agence)"
                                                   class="border border-line rounded-lg px-2.5 py-1.5 text-[12px] w-[190px] outline-none focus:border-gold">
                                            <button type="submit" class="text-[12px] font-semibold px-2.5 py-1.5 rounded-lg bg-error text-white whitespace-nowrap">Rejeter</button>
                                        </form>
                                    </div>
                                @elseif($bucket === 'rejete')
                                    {{-- « Relancer » = ouvrir la fiche agence pour contacter
                                         le client (pas d'envoi automatisé — règle Bimmo). --}}
                                    <a href="{{ route('superadmin.agencies.show', $p->agency_id) }}"
                                       class="text-[12px] font-semibold text-teal border-b border-gold pb-px">Relancer</a>
                                @else
                                    <a href="{{ route('superadmin.agencies.show', $p->agency_id) }}"
                                       class="text-[12px] font-semibold text-teal border-b border-gold pb-px">Voir</a>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($paiements->hasPages())
            <div class="flex items-center justify-between gap-4 px-5 py-3.5 border-t border-paper-dim text-[12.5px] text-muted flex-wrap">
                <span>Affichage de {{ $fmt($paiements->firstItem()) }} à {{ $fmt($paiements->lastItem()) }} sur {{ $fmt($paiements->total()) }} transactions</span>
                <div class="flex items-center gap-1.5">
                    @if($paiements->onFirstPage())
                        <span class="w-8 h-8 rounded-lg border border-line flex items-center justify-center text-muted/40">‹</span>
                    @else
                        <a href="{{ $paiements->previousPageUrl() }}" class="w-8 h-8 rounded-lg border border-line bg-white flex items-center justify-center font-semibold hover:border-gold">‹</a>
                    @endif
                    @foreach($paiements->getUrlRange(1, $paiements->lastPage()) as $page => $url)
                        @if($page == $paiements->currentPage())
                            <span class="w-8 h-8 rounded-lg bg-teal-deep text-paper flex items-center justify-center font-semibold">{{ $page }}</span>
                        @else
                            <a href="{{ $url }}" class="w-8 h-8 rounded-lg border border-line bg-white flex items-center justify-center font-semibold hover:border-gold">{{ $page }}</a>
                        @endif
                    @endforeach
                    @if($paiements->hasMorePages())
                        <a href="{{ $paiements->nextPageUrl() }}" class="w-8 h-8 rounded-lg border border-line bg-white flex items-center justify-center font-semibold hover:border-gold">›</a>
                    @else
                        <span class="w-8 h-8 rounded-lg border border-line flex items-center justify-center text-muted/40">›</span>
                    @endif
                </div>
            </div>
        @endif
        @endif
    </section>
</div>
@endsection
