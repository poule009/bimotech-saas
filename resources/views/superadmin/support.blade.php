@extends('layouts.superadmin')

@section('title', 'Support / Debug')

@php
    use Illuminate\Support\Str;

    // Initiales pour les avatars (2 lettres max).
    $initiales = function (?string $nom) {
        $mots = preg_split('/\s+/', trim((string) $nom));
        $mots = array_filter($mots);
        if (empty($mots)) {
            return '—';
        }
        $premier = Str::substr($mots[0], 0, 1);
        $dernier = count($mots) > 1 ? Str::substr(end($mots), 0, 1) : '';
        return Str::upper($premier.$dernier);
    };

    // Date lisible : « Aujourd'hui, 10:04 » / « Hier, 17:52 » / « 15 juil., 09:12 ».
    $dateLisible = function ($date) {
        if (! $date) {
            return '—';
        }
        if ($date->isToday()) {
            return "Aujourd'hui, ".$date->format('H:i');
        }
        if ($date->isYesterday()) {
            return 'Hier, '.$date->format('H:i');
        }
        return $date->locale('fr')->isoFormat('D MMM, HH:mm');
    };

    // Durée totale figée d'une session terminée.
    $dureeLisible = function (int $s) {
        $h = intdiv($s, 3600);
        $m = intdiv($s % 3600, 60);
        if ($h > 0) {
            return $h.' h '.str_pad((string) $m, 2, '0', STR_PAD_LEFT);
        }
        return max(1, $m).' min';
    };

    $periodeOptions = [
        '7j'    => 'Période : 7 derniers jours',
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
        <h1 class="font-display font-medium text-[30px] md:text-[32px] text-ink mt-1">Support / Debug</h1>
        <p class="text-[14.5px] text-muted mt-1.5">Retrouve une agence rapidement, suis les sessions d'impersonation, garde une trace de chaque intervention.</p>
    </div>

    @if(session('success'))
        <div class="mb-5 rounded-lg bg-green/10 border border-green/25 px-4 py-3 text-[13px] text-green">{{ session('success') }}</div>
    @endif
    @if(session('warning'))
        <div class="mb-5 rounded-lg bg-gold/10 border border-gold/25 px-4 py-3 text-[13px] text-gold-deep">{{ session('warning') }}</div>
    @endif
    @if(session('error') || $errors->any())
        <div class="mb-5 rounded-lg bg-error/10 border border-error/25 px-4 py-3 text-[13px] text-error">
            {{ session('error') ?? $errors->first() }}
        </div>
    @endif

    {{-- ─────────── Recherche rapide ─────────── --}}
    <form method="GET" action="{{ route('superadmin.support') }}"
          class="bg-white border border-line rounded-xl p-[22px] mb-6">
        <div class="text-[12px] font-semibold uppercase tracking-wide text-muted mb-2.5">Recherche rapide</div>
        <label class="flex items-center gap-2.5 bg-paper-dim border border-line rounded-lg px-4 py-3">
            <svg class="w-[17px] h-[17px] opacity-50 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
            <input type="text" name="q" value="{{ $filtres['q'] }}" autocomplete="off"
                   placeholder="Nom d'agence, email, téléphone…"
                   class="border-none outline-none bg-transparent text-[14.5px] w-full text-ink">
        </label>
        <p class="text-[11.5px] text-muted mt-2.5">Trouve n'importe quelle agence en un instant pour accéder directement à sa fiche.</p>

        {{-- Plusieurs résultats : liste courte (un seul résultat → redirection directe côté contrôleur). --}}
        @if($resultats->count() > 1)
            <div class="mt-3 border-t border-paper-dim pt-3 divide-y divide-paper-dim">
                @foreach($resultats as $agence)
                    <a href="{{ route('superadmin.agencies.show', $agence) }}"
                       class="flex items-center justify-between gap-3 py-2.5 group">
                        <div class="min-w-0">
                            <div class="font-semibold text-[13.5px] text-teal-deep group-hover:text-gold-deep transition-colors">{{ $agence->name }}</div>
                            <div class="text-[12px] text-muted truncate">{{ $agence->email }}{{ $agence->telephone ? ' · '.$agence->telephone : '' }}</div>
                        </div>
                        <svg class="w-4 h-4 text-muted shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m9 18 6-6-6-6"/></svg>
                    </a>
                @endforeach
            </div>
        @elseif($filtres['q'] !== '' && $resultats->isEmpty())
            <div class="mt-3 border-t border-paper-dim pt-3 text-[13px] text-muted">
                Aucune agence ne correspond à « {{ $filtres['q'] }} ».
            </div>
        @endif
    </form>

    {{-- ─────────── Sessions en cours ─────────── --}}
    <div class="flex items-center gap-2.5 mb-3">
        <span class="relative flex w-2.5 h-2.5">
            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-error opacity-60"></span>
            <span class="relative inline-flex rounded-full w-2.5 h-2.5 bg-error"></span>
        </span>
        <h2 class="font-display font-medium text-[18px] text-ink">Sessions en cours</h2>
    </div>

    @forelse($actives as $session)
        @php
            $nomAdmin  = $session->admin?->name ?? $session->admin_name ?? 'Admin #'.$session->admin_id;
            $nomAgence = $session->agency?->name ?? $session->agency_name ?? 'Agence #'.$session->agency_id;
            $stale     = $session->isStale();
        @endphp
        <div @class([
                'flex items-center gap-3.5 bg-white rounded-xl px-5 py-4 mb-3 border-[1.5px]',
                'border-error' => ! $stale,
                'border-line' => $stale,
             ])>
            <div class="w-10 h-10 rounded-[10px] bg-teal-deep text-gold-soft flex items-center justify-center font-display font-semibold text-[14px] shrink-0">{{ $initiales($nomAdmin) }}</div>
            <div class="flex-1 min-w-0">
                <div class="font-semibold text-[14px] text-ink">
                    {{ $nomAdmin }} est connecté en tant qu'@if($session->agency_id)<a href="{{ route('superadmin.agencies.show', $session->agency_id) }}" class="text-teal-deep border-b border-gold">{{ $nomAgence }}</a>@else<span class="text-teal-deep">{{ $nomAgence }}</span>@endif
                    @if($stale)
                        <span class="text-[10.5px] font-semibold px-1.5 py-0.5 rounded bg-paper-dim text-muted ml-1">présumée abandonnée</span>
                    @endif
                </div>
                <div class="text-[12px] text-muted mt-0.5">Démarré à {{ $session->started_at?->format('H:i') }} — {{ $dateLisible($session->started_at) }}</div>
            </div>
            <div @class([
                    'tabular-nums text-[13px] font-semibold mr-1',
                    'text-error' => ! $stale,
                    'text-muted' => $stale,
                 ])
                 @unless($stale) x-data="impersonationTimer" data-started="{{ $session->started_at?->timestamp }}" x-text="label" @endunless>{{ $dureeLisible($session->durationSeconds()) }}</div>
            <form method="POST" action="{{ route('superadmin.support.impersonations.terminate', $session) }}"
                  x-data="confirmForm" x-on:submit="submit"
                  data-confirm="Terminer la session de {{ $nomAdmin }} sur {{ $nomAgence }} ? Il sera déconnecté de l'agence à sa prochaine action.">
                @csrf
                <button type="submit" class="text-[12px] font-semibold px-3.5 py-2 rounded-lg border border-error text-error bg-white hover:bg-error/10 transition-colors whitespace-nowrap">Terminer la session</button>
            </form>
        </div>
    @empty
        <div class="bg-white border border-dashed border-line rounded-xl px-5 py-6 text-center text-[13px] text-muted mb-7">
            Aucune session d'impersonation en cours.
        </div>
    @endforelse

    {{-- ─────────── Historique ─────────── --}}
    <h2 class="font-display font-medium text-[18px] text-ink mt-7 mb-4">Historique des sessions</h2>

    <form method="GET" action="{{ route('superadmin.support') }}" x-data="billingFilters('{{ $filtres['periode'] }}')"
          class="flex items-center gap-3 mb-4 flex-wrap">
        {{-- On conserve la recherche courante si présente. --}}
        @if($filtres['q'] !== '')
            <input type="hidden" name="q" value="{{ $filtres['q'] }}">
        @endif

        <select name="admin" x-on:change="apply"
                class="bg-white border border-line rounded-lg px-3 py-2.5 text-[13px] font-medium text-ink cursor-pointer">
            <option value="">Administrateur : Tous</option>
            @foreach($admins as $admin)
                <option value="{{ $admin->id }}" @selected($filtres['admin'] === $admin->id)>{{ $admin->name }}</option>
            @endforeach
        </select>

        <select name="periode" x-on:change="onPeriode"
                class="bg-white border border-line rounded-lg px-3 py-2.5 text-[13px] font-medium text-ink cursor-pointer">
            @foreach($periodeOptions as $val => $label)
                <option value="{{ $val }}" @selected($filtres['periode'] === $val)>{{ $label }}</option>
            @endforeach
        </select>

        <div x-show="isPerso" x-cloak class="flex items-center gap-2">
            <input type="date" name="du" value="{{ $filtres['du'] }}"
                   class="bg-white border border-line rounded-lg px-3 py-2.5 text-[13px] text-ink">
            <span class="text-[13px] text-muted">→</span>
            <input type="date" name="au" value="{{ $filtres['au'] }}"
                   class="bg-white border border-line rounded-lg px-3 py-2.5 text-[13px] text-ink">
            <button type="submit" class="text-[13px] font-semibold px-3 py-2.5 rounded-lg bg-teal text-paper hover:bg-teal-deep transition-colors">Filtrer</button>
        </div>

        <div class="flex-1"></div>
        <span class="text-[12.5px] text-muted font-medium">{{ $historique->total() }} session{{ $historique->total() > 1 ? 's' : '' }}</span>
        <noscript><button type="submit" class="text-[13px] font-semibold text-teal">Filtrer</button></noscript>
    </form>

    <section class="bg-white border border-line rounded-xl overflow-hidden">
        @if($historique->isEmpty())
            <div class="px-5 py-14 text-center text-[13.5px] text-muted">Aucune session sur cette période.</div>
        @else
        <div class="overflow-x-auto">
            <table class="w-full border-collapse">
                <thead>
                    <tr class="text-left text-[11px] uppercase tracking-wide text-muted font-semibold bg-paper-dim">
                        <th class="px-4 py-3">Administrateur</th>
                        <th class="px-4 py-3">Agence</th>
                        <th class="px-4 py-3 whitespace-nowrap">Début</th>
                        <th class="px-4 py-3 whitespace-nowrap">Fin</th>
                        <th class="px-4 py-3">Durée</th>
                        <th class="px-4 py-3 text-right">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($historique as $session)
                        @php
                            $bord = $loop->last ? '' : 'border-b border-paper-dim';
                            $nomAdmin  = $session->admin?->name ?? $session->admin_name ?? 'Admin #'.$session->admin_id;
                            $nomAgence = $session->agency?->name ?? $session->agency_name ?? 'Agence #'.$session->agency_id;
                        @endphp
                        <tr class="text-[13.8px] hover:bg-paper/60 transition-colors">
                            <td class="px-4 py-3.5 {{ $bord }}">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-[26px] h-[26px] rounded-md bg-paper-dim text-teal-deep flex items-center justify-center font-bold text-[11px] shrink-0">{{ $initiales($nomAdmin) }}</div>
                                    <span class="text-ink">{{ $nomAdmin }}</span>
                                    @if($session->end_reason === 'revoked')
                                        <span class="text-[10.5px] font-semibold px-1.5 py-0.5 rounded bg-error/10 text-error">coupée</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-4 py-3.5 {{ $bord }}">
                                @if($session->agency_id)
                                    <a href="{{ route('superadmin.agencies.show', $session->agency_id) }}" class="font-semibold text-teal-deep border-b border-gold">{{ $nomAgence }}</a>
                                @else
                                    <span class="font-semibold text-ink">{{ $nomAgence }}</span>
                                @endif
                            </td>
                            <td class="px-4 py-3.5 {{ $bord }} text-muted whitespace-nowrap">{{ $dateLisible($session->started_at) }}</td>
                            <td class="px-4 py-3.5 {{ $bord }} text-muted whitespace-nowrap">{{ $dateLisible($session->ended_at) }}</td>
                            <td class="px-4 py-3.5 {{ $bord }} tabular-nums text-muted">{{ $dureeLisible($session->durationSeconds()) }}</td>
                            <td class="px-4 py-3.5 {{ $bord }} text-right whitespace-nowrap">
                                <a href="{{ route('superadmin.activity-logs.index', ['agency' => $session->agency_id]) }}"
                                   class="text-[12px] font-semibold text-teal border-b border-gold pb-px">Voir le journal</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if($historique->hasPages())
            <div class="flex items-center justify-between gap-4 px-5 py-3.5 border-t border-paper-dim text-[12.5px] text-muted flex-wrap">
                <span>Affichage de {{ $historique->firstItem() }} à {{ $historique->lastItem() }} sur {{ $historique->total() }} sessions</span>
                <div class="flex items-center gap-1.5">
                    @if($historique->onFirstPage())
                        <span class="w-8 h-8 rounded-lg border border-line flex items-center justify-center text-muted/40">‹</span>
                    @else
                        <a href="{{ $historique->previousPageUrl() }}" class="w-8 h-8 rounded-lg border border-line bg-white flex items-center justify-center font-semibold hover:border-gold">‹</a>
                    @endif
                    @foreach($historique->getUrlRange(1, $historique->lastPage()) as $page => $url)
                        @if($page == $historique->currentPage())
                            <span class="w-8 h-8 rounded-lg bg-teal-deep text-paper flex items-center justify-center font-semibold">{{ $page }}</span>
                        @else
                            <a href="{{ $url }}" class="w-8 h-8 rounded-lg border border-line bg-white flex items-center justify-center font-semibold hover:border-gold">{{ $page }}</a>
                        @endif
                    @endforeach
                    @if($historique->hasMorePages())
                        <a href="{{ $historique->nextPageUrl() }}" class="w-8 h-8 rounded-lg border border-line bg-white flex items-center justify-center font-semibold hover:border-gold">›</a>
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
