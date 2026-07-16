@extends('layouts.superadmin')

@section('title', 'Configuration des plans')

@php
    $fmt = fn ($n) => number_format((float) $n, 0, ',', ' ');

    // Fonctionnalités incluses — texte statique en v1 (hors scope : gestion
    // dynamique des features). Le modèle produit actuel n'active/désactive rien
    // par plan : seules les limites d'usage varient (cf. config/plans.features = []).
    $features = [
        'starter' => ['Gestion locative complète', 'Quittances & rapports PDF', 'Moteur fiscal sénégalais'],
        'pro'     => ['Tout Starter, plus :', 'Import Excel', 'Relevés propriétaires', 'Portail public agence'],
        'agence'  => ['Tout Pro, plus :', 'Biens et comptes illimités', 'Support prioritaire', 'Accès API (à venir)'],
        'legacy'  => ['Plan figé — clients beta historiques', 'Accès équivalent Pro', 'Non facturé (exclu du MRR)', 'Migration manuelle uniquement'],
    ];
@endphp

@section('content')
<div class="max-w-[1240px] mx-auto">

    {{-- ─────────── En-tête ─────────── --}}
    <div class="mb-6">
        <div class="text-[12px] font-semibold tracking-[0.12em] uppercase text-gold">Gestion plateforme</div>
        <h1 class="font-display font-medium text-[30px] md:text-[32px] text-ink mt-1">Configuration des plans</h1>
        <p class="text-[14.5px] text-muted mt-1.5">Prix, limites et fonctionnalités de chaque plan d'abonnement.</p>
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
           class="pb-3 -mb-px text-[13.5px] font-semibold border-b-2 border-transparent text-muted hover:text-ink transition-colors">Facturation</a>
        <a href="{{ route('superadmin.plans.config') }}"
           class="pb-3 -mb-px text-[13.5px] font-semibold border-b-2 border-gold text-teal-deep">Configuration des plans</a>
    </div>

    {{-- Règle métier — non négociable, et garantie techniquement par le snapshot
         du montant à l'encaissement (subscriptions.montant_paye). --}}
    <div class="flex items-start gap-2.5 mb-6 rounded-lg bg-gold/10 border border-gold/25 px-4 py-3 text-[12.5px] text-gold-deep">
        <svg class="w-4 h-4 shrink-0 mt-px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M12 9v4M12 17h.01"/><path d="M10.3 3.9 1.8 18a2 2 0 0 0 1.7 3h17a2 2 0 0 0 1.7-3L13.7 3.9a2 2 0 0 0-3.4 0z"/></svg>
        <span>Toute modification n'affecte que les <strong>nouveaux abonnements</strong> et les <strong>prochains renouvellements</strong> — les agences déjà engagées gardent leur tarif actuel jusqu'à la fin de leur cycle.</span>
    </div>

    {{-- ─────────── Cartes de plans ─────────── --}}
    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        @foreach($plans as $niveau => $plan)
            @php
                $nbAgences = (int) ($compteurs[$niveau] ?? 0);
                $recommande = $niveau === 'pro';
            @endphp

            <form method="POST" action="{{ route('superadmin.plans.update', $plan) }}"
                  class="bg-white border rounded-xl p-5 flex flex-col {{ $recommande ? 'border-gold ring-1 ring-gold' : 'border-line' }}">
                @csrf
                @method('PATCH')

                @if($recommande)
                    <span class="self-start text-[10.5px] font-bold uppercase tracking-wide bg-gold text-white px-2.5 py-0.5 rounded-full mb-2.5">Recommandé</span>
                @endif

                <div class="font-display font-medium text-[19px] text-ink">{{ $plan->libelle }}</div>

                {{-- Lien vers la liste Agences, filtre pré-appliqué en query param
                     (la liste sait déjà filtrer par plan). --}}
                @if($nbAgences > 0)
                    <a href="{{ route('superadmin.agencies.index', ['plan' => $niveau]) }}"
                       class="self-start text-[11.5px] font-semibold text-teal border-b border-gold pb-px mt-1.5">
                        Voir {{ $nbAgences > 1 ? "les {$nbAgences} agences" : "l'agence" }} sur ce plan →
                    </a>
                @else
                    <div class="text-[11.5px] text-muted mt-1.5">Aucune agence sur ce plan</div>
                @endif

                @if($plan->verrouille)
                    <div class="text-[11.5px] text-muted mt-1">Plan figé — non modifiable</div>
                @endif

                {{-- Champs. Legacy est en lecture seule : `disabled` retire aussi les
                     champs de la requête, en plus de la garde serveur. --}}
                <div class="mt-4 space-y-3.5">
                    <div>
                        <label class="block text-[11px] font-semibold uppercase tracking-wide text-muted mb-1.5">Prix mensuel</label>
                        <div class="flex items-center border border-line rounded-lg px-3 py-2 bg-paper {{ $plan->verrouille ? 'opacity-50' : '' }}">
                            <input type="number" name="prix_mensuel" min="0" step="100"
                                   value="{{ old('prix_mensuel', $plan->prix_mensuel) }}"
                                   @disabled($plan->verrouille)
                                   class="border-0 outline-none bg-transparent text-[14px] font-semibold tabular-nums w-full text-ink">
                            <span class="text-[11.5px] text-muted ml-1.5 shrink-0">FCFA</span>
                        </div>
                    </div>

                    <div>
                        <label class="block text-[11px] font-semibold uppercase tracking-wide text-muted mb-1.5">Prix annuel</label>
                        <div class="flex items-center border border-line rounded-lg px-3 py-2 bg-paper {{ $plan->verrouille ? 'opacity-50' : '' }}">
                            <input type="number" name="prix_annuel" min="0" step="100"
                                   value="{{ old('prix_annuel', $plan->prix_annuel) }}"
                                   @disabled($plan->verrouille)
                                   class="border-0 outline-none bg-transparent text-[14px] font-semibold tabular-nums w-full text-ink">
                            <span class="text-[11.5px] text-muted ml-1.5 shrink-0">FCFA</span>
                        </div>
                    </div>

                    <div>
                        <label class="block text-[11px] font-semibold uppercase tracking-wide text-muted mb-1.5">Limite de biens</label>
                        <div class="flex items-center border border-line rounded-lg px-3 py-2 bg-paper {{ $plan->verrouille ? 'opacity-50' : '' }}">
                            <input type="number" name="limite_unites" min="1"
                                   value="{{ old('limite_unites', $plan->limite_unites) }}"
                                   placeholder="Illimité"
                                   @disabled($plan->verrouille)
                                   class="border-0 outline-none bg-transparent text-[14px] font-semibold tabular-nums w-full text-ink placeholder:font-normal placeholder:text-muted">
                            <span class="text-[11.5px] text-muted ml-1.5 shrink-0">biens</span>
                        </div>
                    </div>

                    <div>
                        <label class="block text-[11px] font-semibold uppercase tracking-wide text-muted mb-1.5">Limite d'utilisateurs</label>
                        <div class="flex items-center border border-line rounded-lg px-3 py-2 bg-paper {{ $plan->verrouille ? 'opacity-50' : '' }}">
                            <input type="number" name="limite_admins" min="1"
                                   value="{{ old('limite_admins', $plan->limite_admins) }}"
                                   placeholder="Illimité"
                                   @disabled($plan->verrouille)
                                   class="border-0 outline-none bg-transparent text-[14px] font-semibold tabular-nums w-full text-ink placeholder:font-normal placeholder:text-muted">
                            <span class="text-[11.5px] text-muted ml-1.5 shrink-0">comptes</span>
                        </div>
                    </div>
                </div>

                <p class="text-[11px] text-muted mt-2">Laisser vide = illimité.</p>

                {{-- Fonctionnalités (affichage seul en v1) --}}
                <ul class="mt-4 space-y-1.5 flex-1">
                    @foreach($features[$niveau] ?? [] as $f)
                        <li class="flex items-start gap-2 text-[12.5px] text-muted">
                            <svg class="w-3.5 h-3.5 text-green shrink-0 mt-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg>
                            <span>{{ $f }}</span>
                        </li>
                    @endforeach
                </ul>

                {{-- Enregistrement indépendant, une carte à la fois. --}}
                <div class="mt-4 pt-4 border-t border-paper-dim">
                    @if($plan->verrouille)
                        <div class="w-full text-center bg-paper-dim text-muted text-[12.5px] font-semibold py-2.5 rounded-lg cursor-not-allowed">
                            Verrouillé
                        </div>
                    @else
                        <button type="submit"
                                class="w-full bg-teal text-paper text-[12.5px] font-semibold py-2.5 rounded-lg hover:bg-teal-deep transition-colors">
                            Enregistrer
                        </button>
                    @endif
                </div>
            </form>
        @endforeach
    </div>

    {{-- ─────────── Historique des modifications ─────────── --}}
    @if($historique->isNotEmpty())
        <section class="mt-8 bg-white border border-line rounded-xl overflow-hidden">
            <div class="px-5 py-4 border-b border-paper-dim font-display font-medium text-[16.5px] text-ink">
                Dernières modifications
            </div>
            <div class="divide-y divide-paper-dim">
                @foreach($historique as $h)
                    <div class="px-5 py-3 flex items-center justify-between gap-4 text-[13px] flex-wrap">
                        <div>
                            <span class="font-semibold text-ink">{{ $h->plan?->libelle ?? '—' }}</span>
                            <span class="text-muted"> · {{ $h->champ_label }} :</span>
                            <span class="text-muted line-through tabular-nums">{{ $h->ancienne_valeur ?? 'Illimité' }}</span>
                            <span class="text-muted mx-1">→</span>
                            <span class="font-semibold text-ink tabular-nums">{{ $h->nouvelle_valeur ?? 'Illimité' }}</span>
                        </div>
                        <div class="text-[12px] text-muted whitespace-nowrap">
                            {{ $h->user?->name ?? 'Système' }} · {{ $h->created_at->locale('fr')->isoFormat('D MMM Y, HH:mm') }}
                        </div>
                    </div>
                @endforeach
            </div>
        </section>
    @endif
</div>
@endsection
