@extends('layouts.app')

@php
    $user   = auth()->user();
    $prenom = \Illuminate\Support\Str::of($user->name ?? 'Bonjour')->explode(' ')->first();
    $dateStr = \Carbon\Carbon::now()->locale('fr')->isoFormat('dddd D MMMM Y');
    $vacants = max(0, ($stats['nb_biens'] ?? 0) - ($stats['nb_biens_loues'] ?? 0));
    $fmt = fn ($n) => number_format((float) $n, 0, ',', ' ');
@endphp

@section('title', 'Tableau de bord')
@section('page-title', 'Bonjour, ' . $prenom)
@section('page-subtitle', \Illuminate\Support\Str::ucfirst($dateStr))

@section('topbar-actions')
    <a href="{{ Route::has('admin.biens.create') ? route('admin.biens.create') : '#' }}"
       class="hidden sm:inline-flex items-center gap-1.5 bg-teal text-paper px-4 py-2.5 rounded-[9px] text-[13px] font-semibold hover:bg-teal-deep transition-colors">
        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 5v14M5 12h14"/></svg>
        Ajouter un bien
    </a>
@endsection

@section('content')
<div class="max-w-[1180px]">

    {{-- ─────────── Bannière onboarding / bienvenue (teal plein, sans dégradé) ─────────── --}}
    @if($onboarding)
        @php
            $steps = [
                "Compléter l'identité"   => $onboarding['identite'],
                'Ajouter le logo'        => $onboarding['logo'],
                'Ajouter la signature'   => $onboarding['signature'],
                'Ajouter le cachet'      => $onboarding['cachet'],
            ];
            $done = collect($steps)->filter()->count();
            $pct  = (int) round($done / max(1, count($steps)) * 100);
            $isWelcome = $done === 0;
        @endphp
        <div class="bg-teal text-paper rounded-xl p-5 md:p-6 mb-6 flex flex-col sm:flex-row items-start sm:items-center gap-4">
            <div class="w-12 h-12 rounded-full bg-teal-deep flex items-center justify-center shrink-0 font-display font-semibold text-[13px] text-gold">
                {{ $pct }}%
            </div>
            <div class="flex-1 min-w-0">
                <strong class="block text-[14.5px] mb-0.5">
                    @if($isWelcome)
                        Bienvenue {{ $prenom }}, votre agence est créée
                    @else
                        Complétez le profil de votre agence
                    @endif
                </strong>
                <span class="text-[12.5px] text-paper/70">
                    @if($isWelcome)
                        Première étape : ajoutez un bien, puis créez un bail. Vos quittances seront prêtes à générer.
                    @else
                        {{ $done }}/{{ count($steps) }} étapes faites — encore quelques réglages et tout est prêt.
                    @endif
                </span>
            </div>
            <div class="flex items-center gap-2 shrink-0">
                <a href="{{ route('admin.agency.settings') }}" class="bg-gold text-teal-deep px-4 py-2.5 rounded-lg text-[12.5px] font-bold hover:opacity-90 transition-opacity whitespace-nowrap">
                    Compléter mon profil
                </a>
                <form method="POST" action="{{ route('admin.onboarding.dismiss') }}">
                    @csrf
                    <button type="submit" class="text-paper/60 hover:text-paper px-1.5 leading-none" aria-label="Masquer"><x-icon name="x" size="16" /></button>
                </form>
            </div>
        </div>
    @endif

    {{-- ─────────── KPIs ─────────── --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">

        {{-- Revenus --}}
        <div class="bg-white border border-line rounded-xl p-5">
            <div class="flex items-center justify-between mb-3.5">
                <span class="text-[12px] text-muted font-semibold">Loyers encaissés — {{ $periodeLabel }}</span>
                <span class="w-[30px] h-[30px] rounded-lg bg-green/10 text-green flex items-center justify-center">
                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 1v22M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6"/></svg>
                </span>
            </div>
            <div class="font-display font-semibold text-[25px] leading-none mb-1.5">{{ $fmt($statsMois['loyers']) }} <span class="text-[14px] text-muted font-body">F</span></div>
            @if(!is_null($delta['loyers']))
                <div class="text-[12px] font-semibold {{ $delta['loyers'] >= 0 ? 'text-green' : 'text-error' }}">
                    {{ $delta['loyers'] >= 0 ? '↑' : '↓' }} {{ abs($delta['loyers']) }}% vs mois précédent
                </div>
            @else
                <div class="text-[12px] font-semibold text-muted">—</div>
            @endif
        </div>

        {{-- Biens loués --}}
        <div class="bg-white border border-line rounded-xl p-5">
            <div class="flex items-center justify-between mb-3.5">
                <span class="text-[12px] text-muted font-semibold">Biens loués</span>
                <span class="w-[30px] h-[30px] rounded-lg bg-gold/15 text-gold flex items-center justify-center">
                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 21h18M5 21V7l7-4 7 4v14M10 21v-5h4v5"/></svg>
                </span>
            </div>
            <div class="font-display font-semibold text-[25px] leading-none mb-1.5">{{ $stats['nb_biens_loues'] }} <span class="text-[16px] text-muted font-body">/ {{ $stats['nb_biens'] }}</span></div>
            <div class="text-[12px] font-semibold text-muted">{{ $vacants }} vacant{{ $vacants > 1 ? 's' : '' }}</div>
        </div>

        {{-- Loyers en retard --}}
        <div class="bg-white border border-line rounded-xl p-5">
            <div class="flex items-center justify-between mb-3.5">
                <span class="text-[12px] text-muted font-semibold">Loyers en retard</span>
                <span class="w-[30px] h-[30px] rounded-lg bg-error/10 text-error flex items-center justify-center">
                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 9v4M12 17h.01M10.3 3.9L1.8 18a2 2 0 001.7 3h17a2 2 0 001.7-3L13.7 3.9a2 2 0 00-3.4 0z"/></svg>
                </span>
            </div>
            <div class="font-display font-semibold text-[25px] leading-none mb-1.5">{{ $nb_impayes_mois }}</div>
            <div class="text-[12px] font-semibold {{ ($delta['impayes'] ?? 0) > 0 ? 'text-error' : 'text-muted' }}">
                @if(! $periodeEchue && $nb_a_encaisser > 0)
                    {{-- Tolérance en cours : pas de retard, mais l'encaissement
                         reste à faire. On l'annonce plutôt que d'afficher un 0 muet. --}}
                    {{ $nb_a_encaisser }} à encaisser
                @elseif(!is_null($delta['impayes']) && $delta['impayes'] != 0)
                    {{ $delta['impayes'] > 0 ? '↑ +'.$delta['impayes'] : '↓ '.$delta['impayes'] }} vs mois dernier
                @else
                    Contrats actifs non soldés
                @endif
            </div>
        </div>

        {{-- Baux à renouveler --}}
        <div class="bg-white border border-line rounded-xl p-5">
            <div class="flex items-center justify-between mb-3.5">
                <span class="text-[12px] text-muted font-semibold">Baux à renouveler</span>
                <span class="w-[30px] h-[30px] rounded-lg bg-paper-dim text-teal flex items-center justify-center">
                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/></svg>
                </span>
            </div>
            <div class="font-display font-semibold text-[25px] leading-none mb-1.5">{{ $contrats_a_renouveler->count() }}</div>
            <div class="text-[12px] font-semibold text-muted">D'ici 30 jours</div>
        </div>
    </div>

    {{-- ─────────── Grille principale ─────────── --}}
    <div class="grid grid-cols-1 lg:grid-cols-[1.6fr_1fr] gap-5 items-start">

        {{-- Colonne gauche --}}
        <div class="space-y-5">

            {{-- Évolution des revenus --}}
            <div class="bg-white border border-line rounded-xl p-6">
                <div class="flex items-center justify-between mb-5">
                    <h3 class="font-display font-semibold text-[16px]">Revenus locatifs</h3>
                    <span class="text-[12px] text-muted">12 derniers mois</span>
                </div>
                @php
                    $pts = $loyersParMois;
                    $n   = $pts->count();
                    $max = max(1, (float) ($pts->max('total') ?? 0));
                    $moisFr = ['01'=>'Jan','02'=>'Fév','03'=>'Mar','04'=>'Avr','05'=>'Mai','06'=>'Jun','07'=>'Jul','08'=>'Aoû','09'=>'Sep','10'=>'Oct','11'=>'Nov','12'=>'Déc'];
                    $coords = [];
                    foreach ($pts as $i => $p) {
                        $x = $n > 1 ? ($i / ($n - 1)) * 540 + 10 : 275;
                        $y = 135 - ((float) $p->total / $max) * 115;
                        $coords[] = round($x, 1) . ',' . round($y, 1);
                    }
                    $polyline = implode(' ', $coords);
                @endphp
                @if($n === 0)
                    <div class="h-[150px] flex items-center justify-center text-[13px] text-muted">Aucun paiement enregistré pour l'instant.</div>
                @else
                    <svg viewBox="0 0 560 160" width="100%" height="160" class="overflow-visible">
                        <g stroke="rgb(var(--line))" stroke-width="1">
                            <line x1="0" y1="20" x2="560" y2="20"/><line x1="0" y1="77" x2="560" y2="77"/><line x1="0" y1="135" x2="560" y2="135"/>
                        </g>
                        <polyline fill="none" stroke="rgb(var(--teal))" stroke-width="2.5" points="{{ $polyline }}"/>
                        @foreach($coords as $i => $c)
                            @php [$cx, $cy] = explode(',', $c); @endphp
                            <circle cx="{{ $cx }}" cy="{{ $cy }}" r="{{ $i === count($coords)-1 ? 4.5 : 3 }}" fill="{{ $i === count($coords)-1 ? 'rgb(var(--gold))' : 'rgb(var(--teal))' }}"/>
                        @endforeach
                        <g font-family="Inter" font-size="10.5" fill="rgb(var(--muted))">
                            @foreach($pts as $i => $p)
                                @if($n <= 8 || $i % 2 === 0)
                                    @php $x = $n > 1 ? ($i / ($n - 1)) * 540 + 10 : 275; @endphp
                                    <text x="{{ round($x - 8, 1) }}" y="155">{{ $moisFr[substr($p->mois, 5, 2)] ?? '' }}</text>
                                @endif
                            @endforeach
                        </g>
                    </svg>
                @endif
            </div>

            {{-- Quittances récentes --}}
            <div class="bg-white border border-line rounded-xl p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="font-display font-semibold text-[16px]">Paiements récents</h3>
                    <a href="{{ route('admin.paiements.index') }}" class="text-[12px] font-semibold text-teal hover:underline">Voir tout →</a>
                </div>
                @if($derniersPaiements->isEmpty())
                    <div class="py-8 text-center text-[13px] text-muted">Aucun paiement pour l'instant.</div>
                @else
                    <table class="w-full">
                        <thead>
                            <tr class="text-left text-[11px] uppercase tracking-wide text-muted font-semibold border-b border-line">
                                <th class="pb-2.5 pr-2">Bien</th><th class="pb-2.5 px-2">Locataire</th><th class="pb-2.5 px-2 text-right">Montant</th><th class="pb-2.5 pl-2 text-right">Statut</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($derniersPaiements->take(5) as $p)
                                <tr class="border-b border-paper-dim last:border-0 text-[13px]">
                                    <td class="py-3 pr-2 font-semibold">{{ $p->contrat?->bien?->reference ?? '—' }}</td>
                                    <td class="py-3 px-2 text-ink/70">{{ $p->contrat?->locataire?->name ?? '—' }}</td>
                                    <td class="py-3 px-2 text-right font-semibold">{{ $fmt($p->montant_encaisse) }} F</td>
                                    <td class="py-3 pl-2 text-right">
                                        <span class="inline-block text-[11px] font-bold px-2.5 py-1 rounded-full bg-green/10 text-green">Payé</span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
        </div>

        {{-- Colonne droite --}}
        <div class="space-y-5">

            {{-- Taux d'occupation --}}
            <div class="bg-white border border-line rounded-xl p-6">
                <h3 class="font-display font-semibold text-[16px] mb-4">Taux d'occupation</h3>
                @php
                    $bLoue    = (int) ($biensParStatut['loue'] ?? 0);
                    $bTravaux = (int) ($biensParStatut['en_travaux'] ?? 0);
                    $bVacant  = (int) ($biensParStatut['disponible'] ?? 0);
                    $bTotal   = max(1, $bLoue + $bTravaux + $bVacant);
                    $circ     = 2 * 3.14159 * 54;  // circonférence r=54
                    $segLoue    = $bLoue / $bTotal * $circ;
                    $segTravaux = $bTravaux / $bTotal * $circ;
                    $segVacant  = $bVacant / $bTotal * $circ;
                @endphp
                <div class="relative w-[130px] h-[130px] mx-auto my-2">
                    <svg viewBox="0 0 130 130" class="w-full h-full -rotate-90">
                        <circle cx="65" cy="65" r="54" fill="none" stroke="rgb(var(--line))" stroke-width="16"/>
                        <circle cx="65" cy="65" r="54" fill="none" stroke="rgb(var(--green))" stroke-width="16"
                                stroke-dasharray="{{ round($segLoue,1) }} {{ round($circ - $segLoue,1) }}" stroke-dashoffset="0"/>
                        <circle cx="65" cy="65" r="54" fill="none" stroke="rgb(var(--gold))" stroke-width="16"
                                stroke-dasharray="{{ round($segTravaux,1) }} {{ round($circ - $segTravaux,1) }}" stroke-dashoffset="{{ round(-$segLoue,1) }}"/>
                    </svg>
                    <div class="absolute inset-0 flex flex-col items-center justify-center">
                        <strong class="font-display font-semibold text-[19px] leading-none">{{ (int) round($stats['taux_occupation'] ?? 0) }}%</strong>
                        <span class="text-[10.5px] text-muted mt-0.5">occupé</span>
                    </div>
                </div>
                <div class="space-y-2 mt-2">
                    <div class="flex items-center gap-2.5 text-[12.5px]"><span class="w-2.5 h-2.5 rounded-[3px] bg-green"></span> Loué — {{ $bLoue }} bien{{ $bLoue > 1 ? 's' : '' }}</div>
                    <div class="flex items-center gap-2.5 text-[12.5px]"><span class="w-2.5 h-2.5 rounded-[3px] bg-gold"></span> En travaux — {{ $bTravaux }}</div>
                    <div class="flex items-center gap-2.5 text-[12.5px]"><span class="w-2.5 h-2.5 rounded-[3px] bg-line"></span> Vacant — {{ $bVacant }}</div>
                </div>
            </div>

            {{-- À traiter --}}
            <div class="bg-white border border-line rounded-xl p-6">
                <h3 class="font-display font-semibold text-[16px] mb-4">À traiter</h3>
                @if($impayes_urgents->isEmpty() && $contrats_a_renouveler->isEmpty())
                    <div class="py-6 text-center text-[13px] text-muted inline-flex items-center justify-center gap-1.5 w-full"><x-icon name="check-circle" size="15" class="text-green" /> Rien à traiter pour l'instant</div>
                @else
                    <div class="divide-y divide-paper-dim">
                        @foreach($impayes_urgents as $c)
                            <div class="flex items-start gap-3 py-3 first:pt-0">
                                <span class="w-[18px] h-[18px] rounded-[5px] border-[1.5px] border-line mt-0.5 shrink-0"></span>
                                <div class="flex-1 min-w-0">
                                    <div class="text-[13px] font-semibold">Relancer {{ $c->locataire?->name ?? 'le locataire' }}</div>
                                    <div class="text-[11.5px] text-error font-semibold">Loyer non soldé — {{ $c->bien?->reference ?? $c->bien?->ville }}</div>
                                </div>
                            </div>
                        @endforeach
                        @foreach($contrats_a_renouveler as $c)
                            <div class="flex items-start gap-3 py-3 first:pt-0">
                                <span class="w-[18px] h-[18px] rounded-[5px] border-[1.5px] border-line mt-0.5 shrink-0"></span>
                                <div class="flex-1 min-w-0">
                                    <div class="text-[13px] font-semibold">Bail {{ $c->bien?->reference ?? '' }} — renouvellement</div>
                                    <div class="text-[11.5px] text-muted">Expire le {{ \Carbon\Carbon::parse($c->date_fin)->locale('fr')->isoFormat('D MMM Y') }}</div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
