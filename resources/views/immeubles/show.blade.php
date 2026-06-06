@extends('layouts.app')
@section('header', 'Immeubles › ' . $immeuble->nom)

@section('content')
<div class="space-y-4 md:space-y-5">

    {{-- Breadcrumb --}}
    <div class="flex items-center gap-2 font-body text-sm text-bimo-text/40">
        <a href="{{ route('admin.immeubles.index') }}" class="hover:text-bimo-text transition-colors duration-150">Immeubles</a>
        <svg class="w-3 h-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
        <span class="text-bimo-text font-medium">{{ $immeuble->nom }}</span>
    </div>

    {{-- Actions --}}
    <div class="flex flex-wrap gap-2">
        <a href="{{ route('admin.immeubles.edit', $immeuble) }}"
           class="inline-flex items-center gap-2 px-4 py-2.5 bg-bimo-navy text-white
                  font-display font-bold text-sm rounded-[10px] hover:bg-bimo-navy-dk transition-colors duration-150">
            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
            Modifier
        </a>
        <a href="{{ route('admin.biens.create', ['immeuble_id' => $immeuble->id]) }}"
           class="inline-flex items-center gap-2 px-4 py-2.5 bg-bimo-gold/10 border border-bimo-gold/25 text-bimo-gold
                  font-display font-bold text-sm rounded-[10px] hover:bg-bimo-gold/20 transition-all duration-150">
            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Ajouter une unité
        </a>
        <a href="{{ route('admin.immeubles.index') }}"
           class="inline-flex items-center gap-2 px-4 py-2.5 bg-white border border-bimo-navy/15 text-bimo-text/60
                  font-body text-sm rounded-[10px] hover:text-bimo-text hover:border-bimo-navy/30 transition-all duration-150">
            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg>
            Retour
        </a>
        @if(!$immeuble->biens->contains(fn($b) => $b->contratActif !== null))
        <form method="POST" action="{{ route('admin.immeubles.destroy', $immeuble) }}"
              data-confirm="L'immeuble {{ $immeuble->nom }} et ses {{ $immeuble->biens->count() }} unité(s) seront archivés. Cette action est irréversible."
              data-confirm-title="Archiver cet immeuble ?"
              data-confirm-ok="Oui, archiver">
            @csrf @method('DELETE')
            <button type="submit"
                    class="inline-flex items-center gap-2 px-4 py-2.5 bg-bimo-red/10 border border-bimo-red/20 text-bimo-red
                           font-body text-sm rounded-[10px] hover:bg-bimo-red/20 transition-all duration-150">
                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6"/></svg>
                Archiver
            </button>
        </form>
        @endif
    </div>

    {{-- Hero --}}
    @php $loyerTotal = $immeuble->biens->sum('loyer_mensuel'); @endphp
    <div class="bg-bimo-navy rounded-[14px] p-5 md:p-7 flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4 relative overflow-hidden">
        <div class="absolute top-0 right-0 w-48 h-48 rounded-full opacity-[0.06]"
             style="background: radial-gradient(circle, #C9A84C 0%, transparent 70%); transform: translate(30%, -30%)"></div>
        <div class="relative z-10">
            <div class="font-body font-medium text-[10px] uppercase tracking-widest text-white/30 mb-2">Immeuble</div>
            <div class="font-display font-extrabold text-xl text-white leading-tight mb-1">{{ $immeuble->nom }}</div>
            <div class="font-body text-sm text-white/50">
                {{ $immeuble->adresse }} · {{ $immeuble->ville }}
                @if($immeuble->nombre_niveaux) · {{ $immeuble->nombre_niveaux }} niveau(x) @endif
            </div>
        </div>
        <div class="relative z-10 text-right flex-shrink-0">
            <div class="font-body font-medium text-[9px] uppercase tracking-widest text-bimo-gold/50 mb-1">Unités</div>
            <div class="font-display font-extrabold text-3xl text-bimo-gold leading-none">{{ $immeuble->biens->count() }}</div>
            <div class="font-body text-[11px] text-white/30 mt-1">{{ $immeuble->biens->where('statut','loue')->count() }} louée(s)</div>
            @if($loyerTotal > 0)
            <div class="font-display font-bold text-sm text-bimo-gold mt-2">{{ number_format($loyerTotal, 0, ',', ' ') }} F/mois</div>
            <div class="font-body text-[10px] text-white/25">Loyer total mensuel</div>
            @endif
        </div>
    </div>

    {{-- Grid --}}
    <div class="grid grid-cols-1 lg:grid-cols-[1fr_300px] gap-5 items-start">

        {{-- Colonne gauche --}}
        <div class="space-y-4">

            {{-- Unités --}}
            <div class="bg-white rounded-[14px] border border-bimo-navy/10 overflow-hidden">
                <div class="flex items-center justify-between px-5 py-4 border-b border-bimo-navy/[5%] bg-bimo-bg2">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-[8px] bg-bimo-navy/10 flex items-center justify-center flex-shrink-0">
                            <svg class="w-4 h-4 text-bimo-text/50" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                        </div>
                        <span class="font-display font-bold text-sm text-bimo-text">Unités ({{ $immeuble->biens->count() }})</span>
                    </div>
                    <a href="{{ route('admin.biens.create', ['immeuble_id' => $immeuble->id]) }}"
                       class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-bimo-gold/10 border border-bimo-gold/20 rounded-[7px]
                              font-body font-medium text-xs text-bimo-gold hover:bg-bimo-gold/20 transition-all duration-150">
                        + Ajouter
                    </a>
                </div>

                @if($immeuble->biens->isEmpty())
                <div class="px-5 py-12 flex flex-col items-center text-center">
                    <div class="w-14 h-14 rounded-[14px] bg-bimo-gold/10 border border-bimo-gold/20 flex items-center justify-center mb-4">
                        <svg class="w-7 h-7 text-bimo-gold/60" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                            <path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/>
                        </svg>
                    </div>
                    <p class="font-display font-bold text-sm text-bimo-text mb-1">Aucun appartement dans cet immeuble</p>
                    <p class="font-body text-xs text-bimo-text/40 mb-5 max-w-xs leading-relaxed">
                        Ajoutez les appartements, studios ou bureaux un par un, ou plusieurs en une fois.
                    </p>
                    <a href="{{ route('admin.biens.create', ['immeuble_id' => $immeuble->id]) }}"
                       class="inline-flex items-center gap-2 px-4 py-2.5 bg-[var(--ac)] text-white
                              font-display font-bold text-sm rounded-[10px] hover:opacity-90 transition-opacity duration-150">
                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                        Ajouter le premier bien
                    </a>
                </div>
                @else
                {{-- Mobile --}}
                <div class="md:hidden divide-y divide-bimo-navy/[5%]">
                    @foreach($immeuble->biens as $bien)
                    @php
                        $bs = match($bien->statut) {
                            'loue'       => 'bg-bimo-navy/10 border-bimo-navy/15 text-bimo-text/70',
                            'disponible' => 'bg-bimo-gold/10 border-bimo-gold/20 text-bimo-gold',
                            'en_travaux' => 'bg-bimo-navy/5 border-bimo-navy/10 text-bimo-text/40',
                            default      => 'bg-bimo-navy/5 border-bimo-navy/10 text-bimo-text/30',
                        };
                    @endphp
                    <div class="px-5 py-3.5 flex items-center justify-between gap-3">
                        <div>
                            <div class="font-body font-semibold text-sm text-bimo-text">{{ $bien->titre ?? $bien->type_label }}</div>
                            <div class="font-body text-xs text-bimo-text/50">{{ $bien->reference }}</div>
                        </div>
                        <div class="text-right flex-shrink-0">
                            <div class="font-display font-bold text-sm text-bimo-gold">{{ number_format($bien->loyer_mensuel, 0, ',', ' ') }} F</div>
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full border text-[10px] font-body font-medium {{ $bs }}">{{ $bien->statut_label }}</span>
                        </div>
                    </div>
                    @endforeach
                </div>
                {{-- Desktop --}}
                <div class="hidden md:block overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-bimo-navy/[5%] bg-bimo-bg">
                                <th class="px-5 py-3 text-left font-body font-medium text-[10px] uppercase tracking-widest text-bimo-text/40">Référence</th>
                                <th class="px-5 py-3 text-left font-body font-medium text-[10px] uppercase tracking-widest text-bimo-text/40">Unité / Type</th>
                                <th class="px-5 py-3 text-left font-body font-medium text-[10px] uppercase tracking-widest text-bimo-text/40">Surface</th>
                                <th class="px-5 py-3 text-left font-body font-medium text-[10px] uppercase tracking-widest text-bimo-text/40">Loyer</th>
                                <th class="px-5 py-3 text-center font-body font-medium text-[10px] uppercase tracking-widest text-bimo-text/40">Statut</th>
                                <th class="px-5 py-3 text-left font-body font-medium text-[10px] uppercase tracking-widest text-bimo-text/40">Locataire</th>
                                <th class="px-5 py-3"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-bimo-navy/[5%]">
                            @foreach($immeuble->biens as $bien)
                            @php
                                $bs = match($bien->statut) {
                                    'loue'       => 'bg-bimo-navy/10 border-bimo-navy/15 text-bimo-text/70',
                                    'disponible' => 'bg-bimo-gold/10 border-bimo-gold/20 text-bimo-gold',
                                    'en_travaux' => 'bg-bimo-navy/5 border-bimo-navy/10 text-bimo-text/40',
                                    default      => 'bg-bimo-navy/5 border-bimo-navy/10 text-bimo-text/30',
                                };
                            @endphp
                            <tr class="hover:bg-bimo-bg transition-colors duration-100">
                                <td class="px-5 py-3.5 font-body text-[11px] text-bimo-text/40 uppercase tracking-widest">{{ $bien->reference }}</td>
                                <td class="px-5 py-3.5">
                                    @if($bien->titre)
                                    <div class="font-body font-semibold text-sm text-bimo-text">{{ $bien->titre }}</div>
                                    <div class="font-body text-xs text-bimo-text/50">{{ $bien->type_label }}</div>
                                    @else
                                    <div class="font-body text-sm text-bimo-text">{{ $bien->type_label }}</div>
                                    @endif
                                </td>
                                <td class="px-5 py-3.5 font-body text-xs text-bimo-text/60">{{ $bien->surface_m2 ? $bien->surface_m2.' m²' : '—' }}</td>
                                <td class="px-5 py-3.5 font-display font-bold text-sm text-bimo-gold">{{ number_format($bien->loyer_mensuel, 0, ',', ' ') }} F</td>
                                <td class="px-5 py-3.5 text-center">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full border text-[11px] font-body font-medium {{ $bs }}">{{ $bien->statut_label }}</span>
                                </td>
                                <td class="px-5 py-3.5 font-body text-xs text-bimo-text/60">{{ $bien->contratActif?->locataire?->name ?? '—' }}</td>
                                <td class="px-5 py-3.5">
                                    <a href="{{ route('admin.biens.show', $bien) }}"
                                       class="inline-flex items-center gap-1 px-3 py-1.5 border border-bimo-navy/10 rounded-[6px]
                                              font-body text-xs text-bimo-text/60 hover:text-bimo-gold hover:border-bimo-gold/30 transition-all duration-150">
                                        Voir <svg class="w-3 h-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @endif
            </div>

            {{-- Description --}}
            @if($immeuble->description)
            <div class="bg-white rounded-[14px] border border-bimo-navy/10 overflow-hidden">
                <div class="flex items-center gap-3 px-5 py-4 border-b border-bimo-navy/[5%] bg-bimo-bg2">
                    <div class="w-8 h-8 rounded-[8px] bg-bimo-navy/5 flex items-center justify-center flex-shrink-0">
                        <svg class="w-4 h-4 text-bimo-text/40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
                    </div>
                    <span class="font-display font-bold text-sm text-bimo-text">Description</span>
                </div>
                <div class="px-5 py-5">
                    <p class="font-body text-sm text-bimo-text/70 leading-relaxed">{{ $immeuble->description }}</p>
                </div>
            </div>
            @endif
        </div>

        {{-- Colonne droite --}}
        <div class="lg:sticky lg:top-6 space-y-4">
            <div class="bg-bimo-navy rounded-[14px] overflow-hidden">
                <div class="px-5 py-4 border-b border-white/[7%]">
                    <div class="font-display font-bold text-sm text-white">Propriétaire</div>
                </div>
                <div class="px-5 py-4">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-10 h-10 rounded-full flex items-center justify-center flex-shrink-0 font-display font-bold text-sm text-bimo-gold bg-bimo-gold/15 border border-bimo-gold/30">
                            {{ mb_strtoupper(mb_substr($immeuble->proprietaire?->name ?? 'P', 0, 2)) }}
                        </div>
                        <div>
                            <div class="font-body font-semibold text-sm text-white">{{ $immeuble->proprietaire?->name ?? '—' }}</div>
                            <div class="font-body text-xs text-white/40">{{ $immeuble->proprietaire?->email ?? '' }}</div>
                        </div>
                    </div>
                    @if($immeuble->proprietaire?->telephone)
                    <div class="flex items-center justify-between py-2.5 border-t border-white/[6%]">
                        <span class="font-body text-xs text-white/40">Téléphone</span>
                        <span class="font-body text-xs text-white/70">{{ $immeuble->proprietaire->telephone }}</span>
                    </div>
                    @endif
                    @if($immeuble->proprietaire)
                    <a href="{{ route('admin.users.show', $immeuble->proprietaire) }}"
                       class="flex items-center justify-center gap-2 mt-3 px-4 py-2.5 border border-white/10 rounded-[9px]
                              font-body text-xs text-bimo-gold hover:text-white hover:border-white/20 transition-all duration-150">
                        Voir le profil →
                    </a>
                    @endif
                </div>
            </div>

            <div class="bg-bimo-navy rounded-[14px] overflow-hidden">
                <div class="px-5 py-4 border-b border-white/[7%]">
                    <div class="font-display font-bold text-sm text-white">Récapitulatif</div>
                </div>
                <div class="px-5 py-2 divide-y divide-white/[6%]">
                    @foreach([
                        ['Total unités',  (string) $immeuble->biens->count(), ''],
                        ['Louées',        (string) $immeuble->biens->where('statut','loue')->count(), 'text-bimo-gold font-semibold'],
                        ['Disponibles',   (string) $immeuble->biens->where('statut','disponible')->count(), ''],
                        ['En travaux',    (string) $immeuble->biens->where('statut','en_travaux')->count(), ''],
                        ['Niveaux',       (string) ($immeuble->nombre_niveaux ?? '—'), ''],
                        ['Ville',         $immeuble->ville, ''],
                        ['Créé le',       $immeuble->created_at?->format('d/m/Y') ?? '—', ''],
                    ] as [$lbl, $val, $cls])
                    <div class="flex items-center justify-between py-2.5">
                        <span class="font-body text-xs text-white/40">{{ $lbl }}</span>
                        <span class="font-body text-xs text-white/70 {{ $cls }}">{{ $val }}</span>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
