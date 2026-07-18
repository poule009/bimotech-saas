@extends('layouts.superadmin')

@section('title', 'Règles fiscales')

@php
    use App\Support\RegleFiscaleCatalogue;

    // Styles de badge par variante de statut (mapping validé).
    $chip = [
        'green' => 'bg-green/10 text-green',
        'teal'  => 'bg-teal/10 text-teal',
        'gold'  => 'bg-gold/10 text-gold',
        'gray'  => 'bg-ink/[0.06] text-muted',
    ];

    $statutOptions = [
        'tous'        => 'Statut : Tous',
        'confirme'    => 'Confirmées',
        'non_verifie' => 'Non vérifiées',
    ];
@endphp

@section('content')
<div class="max-w-[1240px] mx-auto">

    {{-- ─────────── En-tête ─────────── --}}
    <div class="mb-1">
        <div class="text-[12px] font-semibold tracking-[0.12em] uppercase text-gold">Gestion plateforme</div>
        <h1 class="font-display font-medium text-[30px] md:text-[32px] text-ink mt-1">Règles fiscales</h1>
    </div>
    <p class="text-[14.5px] text-muted mb-6">
        Toutes les valeurs utilisées par le moteur fiscal, avec leur niveau de fiabilité et leur source.
    </p>

    {{-- ─────────── KPIs ─────────── --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3.5 mb-6">
        <div class="bg-white border border-line rounded-xl px-5 py-4">
            <div class="text-[11.5px] text-muted font-semibold uppercase tracking-wide mb-2.5">Règles confirmées</div>
            <div class="font-display text-[26px] font-medium text-green">{{ $nbConfirmees }}</div>
        </div>
        <div class="bg-white border border-line rounded-xl px-5 py-4">
            <div class="text-[11.5px] text-muted font-semibold uppercase tracking-wide mb-2.5">Non vérifiées</div>
            <div class="font-display text-[26px] font-medium text-gold">{{ $nbAVerifier }}</div>
        </div>
        <div class="bg-white border border-line rounded-xl px-5 py-4">
            <div class="text-[11.5px] text-muted font-semibold uppercase tracking-wide mb-2.5">Dernière modification</div>
            <div class="font-display text-[16px] font-medium text-ink mt-1.5">
                {{ $derniereMaj ? $derniereMaj->locale('fr')->diffForHumans() : '—' }}
            </div>
        </div>
    </div>

    {{-- ─────────── Recherche + filtres (soumission serveur, Alpine auto-submit) ─────────── --}}
    <form method="GET" action="{{ route('superadmin.regles.index') }}" x-data="agencyFilters"
          class="flex items-center gap-3 mb-4 flex-wrap">
        <label class="flex items-center gap-2 bg-white border border-line rounded-lg px-3 py-2.5 flex-1 min-w-[220px] max-w-[340px]">
            <svg class="w-4 h-4 text-muted shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
            <input type="text" name="q" value="{{ $q }}" x-on:input="search"
                   placeholder="Rechercher une règle…" autocomplete="off"
                   class="border-0 outline-none bg-transparent text-[13.5px] w-full text-ink placeholder:text-muted">
        </label>

        <select name="statut" x-on:change="apply"
                class="bg-white border border-line rounded-lg px-3 py-2.5 text-[13px] font-medium text-ink cursor-pointer">
            @foreach($statutOptions as $val => $label)
                <option value="{{ $val }}" @selected($statutFiltre === $val)>{{ $label }}</option>
            @endforeach
        </select>

        <select name="groupe" x-on:change="apply"
                class="bg-white border border-line rounded-lg px-3 py-2.5 text-[13px] font-medium text-ink cursor-pointer">
            <option value="tous" @selected($groupeFiltre === 'tous')>Catégorie : Toutes</option>
            @foreach($groupes as $val => $label)
                <option value="{{ $val }}" @selected($groupeFiltre === $val)>{{ $label }}</option>
            @endforeach
        </select>

        <div class="flex-1"></div>
        <span class="text-[12.5px] text-muted font-medium whitespace-nowrap">{{ $total }} règles au total</span>
        <noscript><button type="submit" class="text-[13px] font-semibold text-teal">Filtrer</button></noscript>
    </form>

    {{-- ─────────── Tableau ─────────── --}}
    <section class="bg-white border border-line rounded-xl overflow-hidden">
        @if($regles->isEmpty())
            <div class="px-5 py-14 text-center text-[13.5px] text-muted">
                Aucune règle ne correspond à ces critères.
            </div>
        @else
        <div class="overflow-x-auto">
            <table class="w-full border-collapse">
                <thead>
                    <tr class="text-left text-[11px] uppercase tracking-wide text-muted font-semibold bg-paper-dim">
                        <th class="px-4 py-3">Règle</th>
                        <th class="px-4 py-3">Valeur actuelle</th>
                        <th class="px-4 py-3">Statut</th>
                        <th class="px-4 py-3">Source</th>
                        <th class="px-4 py-3 whitespace-nowrap">Mise à jour</th>
                        <th class="px-4 py-3"></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($regles as $r)
                        @php
                            $bord     = $loop->last ? '' : 'border-b border-paper-dim';
                            $valeur   = RegleFiscaleCatalogue::valeur($r);
                            $source   = $r->sources[0]['libelle'] ?? null;
                            $nbAutres = max(0, count($r->sources ?? []) - 1);
                        @endphp
                        <tr class="relative text-[13.8px] hover:bg-paper/60 transition-colors">
                            {{-- Règle --}}
                            <td class="px-4 py-3.5 {{ $bord }}">
                                <div class="font-semibold text-ink">{{ $r->titre }}</div>
                                <div class="text-[12px] text-muted mt-0.5">{{ Str::limit($r->description, 60) }}</div>
                            </td>
                            {{-- Valeur (lecture seule, dérivée du moteur) --}}
                            <td class="px-4 py-3.5 {{ $bord }}">
                                <span class="font-mono font-semibold text-[13px] text-ink">{{ $valeur ?? '—' }}</span>
                            </td>
                            {{-- Statut --}}
                            <td class="px-4 py-3.5 {{ $bord }}">
                                <span class="text-[11.5px] font-semibold px-2.5 py-1 rounded-full inline-flex items-center gap-1.5 {{ $chip[$r->statut_variant] }}">
                                    <span class="w-1.5 h-1.5 rounded-full bg-current"></span>{{ $r->statut_label }}
                                </span>
                            </td>
                            {{-- Source --}}
                            <td class="px-4 py-3.5 {{ $bord }} text-muted">
                                <span class="text-[12.5px] line-clamp-2 max-w-[240px] inline-block align-middle">{{ $source ?? '—' }}</span>
                                @if($nbAutres > 0)
                                    <span class="text-[11px] font-semibold text-teal whitespace-nowrap"> +{{ $nbAutres }}</span>
                                @endif
                            </td>
                            {{-- Mise à jour --}}
                            <td class="px-4 py-3.5 {{ $bord }} text-muted whitespace-nowrap text-[12.5px]">
                                {{ $r->date_verification?->locale('fr')->isoFormat('D MMM Y') ?? '—' }}
                            </td>
                            {{-- Action (lien étiré : toute la ligne mène à la fiche) --}}
                            <td class="px-4 py-3.5 {{ $bord }} text-right">
                                <a href="{{ route('superadmin.regles.show', $r) }}"
                                   class="text-[12px] font-semibold text-teal border-b border-gold pb-px before:content-[''] before:absolute before:inset-0">Voir</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </section>

    <p class="text-[11.5px] text-muted/80 mt-3">
        La colonne « Valeur actuelle » reflète en lecture seule la valeur appliquée par le moteur de calcul.
        Sa modification relève du code (non éditable ici en v1).
    </p>
</div>
@endsection
