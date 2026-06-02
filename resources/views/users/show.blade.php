@extends('layouts.app')
@section('header', ($user->isProprietaire() ? 'Propriétaires' : 'Locataires') . ' › ' . $user->name)

@section('content')
<div class="space-y-4 md:space-y-5">

    {{-- Breadcrumb --}}
    <div class="flex items-center gap-2 font-body text-sm text-bimo-navy/40">
        @if($user->isProprietaire())
            <a href="{{ route('admin.users.proprietaires') }}" class="hover:text-bimo-navy transition-colors duration-150">Propriétaires</a>
        @else
            <a href="{{ route('admin.users.locataires') }}" class="hover:text-bimo-navy transition-colors duration-150">Locataires</a>
        @endif
        <svg class="w-3 h-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
        <span class="text-bimo-navy font-medium">{{ $user->name }}</span>
    </div>

    {{-- Actions --}}
    <div class="flex flex-wrap gap-2">
        <a href="{{ route('admin.users.edit', $user) }}"
           class="inline-flex items-center gap-2 px-4 py-2.5 bg-bimo-navy text-white
                  font-display font-bold text-sm rounded-[10px] hover:bg-bimo-navy-dk transition-colors duration-150">
            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
            Modifier
        </a>
        @if($user->isProprietaire())
        <a href="{{ route('admin.contrats.create') }}"
           class="inline-flex items-center gap-2 px-4 py-2.5 bg-white border border-bimo-navy/15 text-bimo-navy/60
                  font-body text-sm rounded-[10px] hover:text-bimo-navy hover:border-bimo-navy/30 transition-all duration-150">
            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><line x1="12" y1="11" x2="12" y2="17"/><line x1="9" y1="14" x2="15" y2="14"/></svg>
            Nouveau contrat
        </a>
        <a href="{{ route('admin.bailleurs.show', $user->id) }}"
           class="inline-flex items-center gap-2 px-4 py-2.5 bg-bimo-gold/10 border border-bimo-gold/25 text-bimo-gold
                  font-body text-sm rounded-[10px] hover:bg-bimo-gold/20 transition-all duration-150">
            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><line x1="19" y1="8" x2="19" y2="14"/><line x1="22" y1="11" x2="16" y2="11"/></svg>
            Portefeuille
        </a>
        @endif
        <a href="{{ $user->isProprietaire() ? route('admin.users.proprietaires') : route('admin.users.locataires') }}"
           class="inline-flex items-center gap-2 px-4 py-2.5 bg-white border border-bimo-navy/15 text-bimo-navy/60
                  font-body text-sm rounded-[10px] hover:text-bimo-navy hover:border-bimo-navy/30 transition-all duration-150">
            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg>
            Retour
        </a>
        <form method="POST" action="{{ route('admin.users.destroy', $user) }}"
              data-confirm="L'utilisateur {{ $user->name }} sera supprimé définitivement."
              data-confirm-title="Supprimer cet utilisateur ?"
              data-confirm-ok="Oui, supprimer">
            @csrf @method('DELETE')
            <button type="submit"
                    class="inline-flex items-center gap-2 px-4 py-2.5 bg-bimo-red/10 border border-bimo-red/20 text-bimo-red
                           font-body text-sm rounded-[10px] hover:bg-bimo-red/20 transition-all duration-150">
                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6"/></svg>
                Supprimer
            </button>
        </form>
    </div>

    {{-- Hero --}}
    <div class="bg-bimo-navy rounded-[14px] p-5 md:p-7 flex flex-col sm:flex-row sm:items-center gap-4 relative overflow-hidden">
        <div class="absolute top-0 right-0 w-48 h-48 rounded-full opacity-[0.06]"
             style="background: radial-gradient(circle, #C9A84C 0%, transparent 70%); transform: translate(30%, -30%)">
        </div>
        <div class="w-14 h-14 rounded-full flex items-center justify-center flex-shrink-0 font-display font-bold text-2xl text-bimo-gold
                    bg-bimo-gold/15 border-2 border-bimo-gold/30 relative z-10">
            {{ mb_strtoupper(mb_substr($user->name, 0, 2)) }}
        </div>
        <div class="flex-1 relative z-10">
            <div class="font-display font-extrabold text-xl text-white leading-tight mb-1">{{ $user->name }}</div>
            <div class="font-body text-sm text-white/50 mb-2">
                {{ $user->email }}
                @if($user->telephone) · {{ $user->telephone }} @endif
            </div>
            <span class="inline-flex items-center px-2.5 py-1 rounded-full border text-xs font-body font-medium
                         {{ $user->isProprietaire() ? 'bg-bimo-gold/10 border-bimo-gold/25 text-bimo-gold' : 'bg-bimo-navy-dk/50 border-white/10 text-white/70' }}">
                {{ $user->isProprietaire() ? 'Propriétaire' : 'Locataire' }}
            </span>
        </div>
        <div class="relative z-10 text-right flex-shrink-0">
            <div class="font-body font-medium text-[9px] uppercase tracking-widest text-white/30 mb-1">Membre depuis</div>
            <div class="font-display font-bold text-base text-white">{{ $user->created_at?->format('d/m/Y') }}</div>
        </div>
    </div>

    {{-- ═══ PROPRIÉTAIRE ═══ --}}
    @if($user->isProprietaire())

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
        <div class="bg-bimo-gold/[8%] rounded-[14px] border border-bimo-gold/25 p-4">
            <div class="font-body font-medium text-[9.5px] uppercase tracking-widest text-bimo-gold/70 mb-1">Total loyers</div>
            <div class="font-display font-extrabold text-xl text-bimo-gold leading-none">
                {{ number_format($stats['total_loyers'] ?? 0, 0, ',', ' ') }}<span class="font-body font-normal text-sm text-bimo-gold/50"> F</span>
            </div>
            <div class="font-body text-[10.5px] text-bimo-gold/60 mt-1">FCFA encaissés</div>
        </div>
        <div class="bg-white rounded-[14px] border border-bimo-navy/10 p-4">
            <div class="font-body font-medium text-[9.5px] uppercase tracking-widest text-bimo-navy/50 mb-1">Net reversé</div>
            <div class="font-display font-extrabold text-xl text-bimo-navy leading-none">
                {{ number_format($stats['total_net'] ?? 0, 0, ',', ' ') }}<span class="font-body font-normal text-sm text-bimo-navy/40"> F</span>
            </div>
            <div class="font-body text-[10.5px] text-bimo-navy/40 mt-1">Après commission</div>
        </div>
        <div class="bg-white rounded-[14px] border border-bimo-navy/10 p-4">
            <div class="font-body font-medium text-[9.5px] uppercase tracking-widest text-bimo-navy/50 mb-1">Biens</div>
            <div class="font-display font-extrabold text-xl text-bimo-navy leading-none">{{ $stats['nb_biens'] ?? 0 }}</div>
            <div class="font-body text-[10.5px] text-bimo-navy/40 mt-1">{{ $stats['nb_biens_loues'] ?? 0 }} loué(s)</div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-[1fr_280px] gap-5 items-start">
        <div class="space-y-4">

            {{-- Biens --}}
            <div class="bg-white rounded-[14px] border border-bimo-navy/10 overflow-hidden">
                <div class="flex items-center justify-between px-5 py-4 border-b border-bimo-navy/[5%] bg-bimo-bg2">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-[8px] bg-bimo-gold/15 flex items-center justify-center flex-shrink-0">
                            <svg class="w-4 h-4 text-bimo-gold" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/></svg>
                        </div>
                        <span class="font-display font-bold text-sm text-bimo-navy">Biens ({{ $stats['nb_biens'] ?? 0 }})</span>
                    </div>
                    <a href="{{ route('admin.biens.create') }}"
                       class="font-body text-xs text-bimo-gold hover:text-bimo-navy transition-colors duration-150">+ Ajouter</a>
                </div>
                @if(isset($biens) && $biens->count() > 0)
                {{-- Mobile --}}
                <div class="md:hidden divide-y divide-bimo-navy/[5%]">
                    @foreach($biens as $bien)
                    <div class="px-5 py-3.5 flex items-center justify-between gap-3">
                        <div>
                            <div class="font-body font-semibold text-sm text-bimo-navy">{{ $bien->reference }}</div>
                            <div class="font-body text-xs text-bimo-navy/50">{{ $bien->adresse }}, {{ $bien->ville }}</div>
                        </div>
                        <div class="text-right">
                            <div class="font-display font-bold text-sm text-bimo-gold">{{ number_format($bien->loyer_mensuel, 0, ',', ' ') }} F</div>
                            <a href="{{ route('admin.biens.show', $bien) }}" class="font-body text-xs text-bimo-navy/40 hover:text-bimo-gold transition-colors duration-150">Voir →</a>
                        </div>
                    </div>
                    @endforeach
                </div>
                {{-- Desktop --}}
                <div class="hidden md:block overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-bimo-navy/[5%] bg-bimo-bg">
                                <th class="px-5 py-3 text-left font-body font-medium text-[10px] uppercase tracking-widest text-bimo-navy/40">Référence</th>
                                <th class="px-5 py-3 text-left font-body font-medium text-[10px] uppercase tracking-widest text-bimo-navy/40">Adresse</th>
                                <th class="px-5 py-3 text-right font-body font-medium text-[10px] uppercase tracking-widest text-bimo-navy/40">Loyer</th>
                                <th class="px-5 py-3 text-center font-body font-medium text-[10px] uppercase tracking-widest text-bimo-navy/40">Statut</th>
                                <th class="px-5 py-3 text-left font-body font-medium text-[10px] uppercase tracking-widest text-bimo-navy/40">Locataire</th>
                                <th class="px-5 py-3 text-center font-body font-medium text-[10px] uppercase tracking-widest text-bimo-navy/40">Voir</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-bimo-navy/[5%]">
                            @foreach($biens as $bien)
                            @php
                                $bs = match($bien->statut) {
                                    'loue'       => 'bg-bimo-navy/10 border-bimo-navy/15 text-bimo-navy/70',
                                    'disponible' => 'bg-bimo-gold/10 border-bimo-gold/20 text-bimo-gold',
                                    'en_travaux' => 'bg-bimo-navy/5 border-bimo-navy/10 text-bimo-navy/40',
                                    default      => 'bg-bimo-navy/5 border-bimo-navy/10 text-bimo-navy/30',
                                };
                            @endphp
                            <tr class="hover:bg-bimo-bg transition-colors duration-100">
                                <td class="px-5 py-3.5 font-body font-semibold text-xs text-bimo-navy">{{ $bien->reference }}</td>
                                <td class="px-5 py-3.5 font-body text-xs text-bimo-navy/60">{{ $bien->adresse }}, {{ $bien->ville }}</td>
                                <td class="px-5 py-3.5 text-right font-display font-bold text-sm text-bimo-gold">{{ number_format($bien->loyer_mensuel, 0, ',', ' ') }} F</td>
                                <td class="px-5 py-3.5 text-center">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full border text-[11px] font-body font-medium {{ $bs }}">{{ $bien->statut_label }}</span>
                                </td>
                                <td class="px-5 py-3.5 font-body text-xs text-bimo-navy/60">{{ $bien->contratActif?->locataire?->name ?? '—' }}</td>
                                <td class="px-5 py-3.5 text-center">
                                    <a href="{{ route('admin.biens.show', $bien) }}"
                                       class="w-7 h-7 inline-flex items-center justify-center border border-bimo-navy/10 rounded-[6px] text-bimo-navy/40 hover:text-bimo-gold hover:border-bimo-gold/30 transition-all duration-150">
                                        <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="px-5 py-10 text-center font-body text-sm text-bimo-navy/30">
                    Aucun bien enregistré pour ce propriétaire.
                </div>
                @endif
            </div>

            {{-- Paiements récents --}}
            @if(isset($paiements) && $paiements->count() > 0)
            <div class="bg-white rounded-[14px] border border-bimo-navy/10 overflow-hidden">
                <div class="flex items-center gap-3 px-5 py-4 border-b border-bimo-navy/[5%] bg-bimo-bg2">
                    <div class="w-8 h-8 rounded-[8px] bg-bimo-navy/5 flex items-center justify-center flex-shrink-0">
                        <svg class="w-4 h-4 text-bimo-navy/40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="1" y="4" width="22" height="16" rx="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>
                    </div>
                    <span class="font-display font-bold text-sm text-bimo-navy">Derniers paiements</span>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-bimo-navy/[5%] bg-bimo-bg">
                                <th class="px-5 py-3 text-left font-body font-medium text-[10px] uppercase tracking-widest text-bimo-navy/40">Bien</th>
                                <th class="px-5 py-3 text-left font-body font-medium text-[10px] uppercase tracking-widest text-bimo-navy/40">Période</th>
                                <th class="px-5 py-3 text-right font-body font-medium text-[10px] uppercase tracking-widest text-bimo-navy/40">Montant</th>
                                <th class="px-5 py-3 text-right font-body font-medium text-[10px] uppercase tracking-widest text-bimo-navy/40">Net reversé</th>
                                <th class="px-5 py-3 text-center font-body font-medium text-[10px] uppercase tracking-widests text-bimo-navy/40">PDF</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-bimo-navy/[5%]">
                            @foreach($paiements as $p)
                            <tr class="hover:bg-bimo-bg transition-colors duration-100">
                                <td class="px-5 py-3.5 font-body font-medium text-sm text-bimo-navy">{{ $p->contrat?->bien?->reference ?? '—' }}</td>
                                <td class="px-5 py-3.5">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-display font-semibold bg-bimo-gold/10 border border-bimo-gold/20 text-bimo-gold">
                                        {{ \Carbon\Carbon::parse($p->periode)->translatedFormat('M Y') }}
                                    </span>
                                </td>
                                <td class="px-5 py-3.5 text-right font-display font-bold text-sm text-bimo-gold">{{ number_format($p->montant_encaisse, 0, ',', ' ') }} F</td>
                                <td class="px-5 py-3.5 text-right font-body font-semibold text-sm text-bimo-navy">{{ number_format($p->net_a_verser_proprietaire ?? $p->net_proprietaire ?? 0, 0, ',', ' ') }} F</td>
                                <td class="px-5 py-3.5 text-center">
                                    <a href="{{ route('admin.paiements.pdf', $p) }}" target="_blank"
                                       class="w-7 h-7 inline-flex items-center justify-center border border-bimo-navy/10 rounded-[6px] text-bimo-navy/40 hover:text-bimo-gold hover:border-bimo-gold/30 transition-all duration-150">
                                        <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endif

        </div>

        {{-- Sidebar propriétaire --}}
        <div class="lg:sticky lg:top-6 space-y-4">
            <div class="bg-bimo-navy rounded-[14px] overflow-hidden">
                <div class="px-5 py-4 border-b border-white/[7%]">
                    <div class="font-display font-bold text-sm text-white">Informations</div>
                </div>
                <div class="px-5 py-2 divide-y divide-white/[6%]">
                    @php
                        $rows = [['Nom', $user->name, ''], ['Email', $user->email, 'text-xs']];
                        if ($user->telephone) $rows[] = ['Téléphone', $user->telephone, ''];
                        if ($user->adresse) $rows[] = ['Adresse', $user->adresse, 'text-xs'];
                        if ($user->proprietaire?->ninea) $rows[] = ['NINEA', $user->proprietaire->ninea, ''];
                        if ($user->proprietaire?->mode_paiement_prefere) $rows[] = ['Mode paiement', ucfirst($user->proprietaire->mode_paiement_prefere), ''];
                        $rows[] = ['Total loyers', number_format($stats['total_loyers'] ?? 0, 0, ',', ' ') . ' F', 'text-bimo-gold font-semibold'];
                        $rows[] = ['Net reversé', number_format($stats['total_net'] ?? 0, 0, ',', ' ') . ' F', ''];
                        $rows[] = ['Membre depuis', $user->created_at?->format('d/m/Y') ?? '—', ''];
                    @endphp
                    @foreach($rows as [$lbl, $val, $cls])
                    <div class="flex items-center justify-between py-2.5">
                        <span class="font-body text-xs text-white/40">{{ $lbl }}</span>
                        <span class="font-body text-xs text-white/70 {{ $cls }}">{{ $val }}</span>
                    </div>
                    @endforeach
                </div>
            </div>

            @if(isset($locatairesActifs) && $locatairesActifs->count() > 0)
            <div class="bg-bimo-navy rounded-[14px] overflow-hidden">
                <div class="px-5 py-4 border-b border-white/[7%]">
                    <div class="font-display font-bold text-sm text-white">Locataires actifs</div>
                </div>
                <div class="px-5 py-2 divide-y divide-white/[6%]">
                    @foreach($locatairesActifs as $lc)
                    <div class="py-3">
                        <div class="font-body font-medium text-sm text-white/80">{{ $lc->locataire?->name ?? '—' }}</div>
                        <div class="font-body text-xs text-white/35 mt-0.5">{{ $lc->bien?->reference }}</div>
                        <div class="font-body text-xs text-bimo-gold mt-0.5">{{ number_format($lc->loyer_contractuel, 0, ',', ' ') }} F/mois</div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
    </div>

    {{-- ═══ LOCATAIRE ═══ --}}
    @elseif($user->isLocataire())

    <div class="grid grid-cols-1 lg:grid-cols-[1fr_280px] gap-5 items-start">
        <div class="space-y-4">

            {{-- Contrat actif --}}
            @if(isset($stats['contrat_actif']) && $stats['contrat_actif'])
            @php $contrat = $stats['contrat_actif']; @endphp
            <div class="bg-white rounded-[14px] border border-bimo-navy/10 overflow-hidden">
                <div class="flex items-center justify-between px-5 py-4 border-b border-bimo-navy/[5%] bg-bimo-bg2">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-[8px] bg-bimo-gold/15 flex items-center justify-center flex-shrink-0">
                            <svg class="w-4 h-4 text-bimo-gold" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                        </div>
                        <span class="font-display font-bold text-sm text-bimo-navy">Contrat actif</span>
                    </div>
                    <a href="{{ route('admin.contrats.show', $contrat) }}"
                       class="font-body text-xs text-bimo-navy/40 hover:text-bimo-gold transition-colors duration-150">Voir →</a>
                </div>
                <div class="px-5 py-5 grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <div class="font-body font-medium text-[10px] uppercase tracking-widest text-bimo-navy/40 mb-1">Bien loué</div>
                        <div class="font-body font-medium text-sm text-bimo-navy">{{ $contrat->bien?->reference ?? '—' }}</div>
                        <div class="font-body text-xs text-bimo-navy/50">{{ $contrat->bien?->adresse }}</div>
                        <div class="font-body text-xs text-bimo-navy/50">{{ $contrat->bien?->ville }}</div>
                    </div>
                    <div>
                        <div class="font-body font-medium text-[10px] uppercase tracking-widest text-bimo-navy/40 mb-1">Loyer contractuel</div>
                        <div class="font-display font-bold text-base text-bimo-gold">{{ number_format($contrat->loyer_contractuel, 0, ',', ' ') }} F</div>
                    </div>
                    <div>
                        <div class="font-body font-medium text-[10px] uppercase tracking-widest text-bimo-navy/40 mb-1">Début contrat</div>
                        <div class="font-body text-sm text-bimo-navy">{{ $contrat->date_debut?->format('d/m/Y') }}</div>
                        <div class="font-body text-xs text-bimo-navy/50">{{ $contrat->date_fin?->format('d/m/Y') ?? 'Ouvert' }}</div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                <div class="bg-bimo-gold/[8%] rounded-[14px] border border-bimo-gold/25 p-4">
                    <div class="font-body font-medium text-[9.5px] uppercase tracking-widest text-bimo-gold/70 mb-1">Total payé</div>
                    <div class="font-display font-extrabold text-xl text-bimo-gold leading-none">
                        {{ number_format($stats['total_paye'] ?? 0, 0, ',', ' ') }}<span class="font-body font-normal text-sm text-bimo-gold/50"> F</span>
                    </div>
                    <div class="font-body text-[10.5px] text-bimo-gold/60 mt-1">FCFA versés</div>
                </div>
                <div class="bg-white rounded-[14px] border border-bimo-navy/10 p-4">
                    <div class="font-body font-medium text-[9.5px] uppercase tracking-widest text-bimo-navy/50 mb-1">Paiements</div>
                    <div class="font-display font-extrabold text-xl text-bimo-navy leading-none">{{ $stats['nb_paiements'] ?? 0 }}</div>
                    <div class="font-body text-[10.5px] text-bimo-navy/40 mt-1">Validés</div>
                </div>
                <div class="bg-white rounded-[14px] border border-bimo-navy/10 p-4">
                    <div class="font-body font-medium text-[9.5px] uppercase tracking-widest text-bimo-navy/50 mb-1">Statut</div>
                    <div class="font-display font-extrabold text-xl text-bimo-navy leading-none">Actif</div>
                    <div class="font-body text-[10.5px] text-bimo-navy/40 mt-1">Contrat en cours</div>
                </div>
            </div>

            @else
            <div class="flex items-center justify-between gap-3 bg-bimo-gold/[8%] border border-bimo-gold/25 rounded-[12px] px-4 py-4 flex-wrap">
                <div>
                    <div class="font-body font-semibold text-sm text-bimo-gold mb-0.5">Aucun contrat actif</div>
                    <p class="font-body text-xs text-bimo-gold/70">Ce locataire n'a pas de bail en cours.</p>
                </div>
                <a href="{{ route('admin.contrats.create') }}"
                   class="font-body font-semibold text-sm text-bimo-gold hover:text-bimo-navy transition-colors duration-150 whitespace-nowrap">
                    Créer un contrat →
                </a>
            </div>
            @endif

        </div>

        {{-- Sidebar locataire --}}
        <div class="lg:sticky lg:top-6">
            <div class="bg-bimo-navy rounded-[14px] overflow-hidden">
                <div class="px-5 py-4 border-b border-white/[7%]">
                    <div class="font-display font-bold text-sm text-white">Informations</div>
                </div>
                <div class="px-5 py-2 divide-y divide-white/[6%]">
                    @php
                        $locRows = [['Nom', $user->name, ''], ['Email', $user->email, 'text-xs']];
                        if ($user->telephone) $locRows[] = ['Téléphone', $user->telephone, ''];
                        if ($user->adresse) $locRows[] = ['Adresse', $user->adresse, 'text-xs'];
                        if ($user->locataire?->profession) $locRows[] = ['Profession', $user->locataire->profession, ''];
                        if ($user->locataire?->employeur) $locRows[] = ['Employeur', $user->locataire->employeur, ''];
                        if ($user->locataire?->contact_urgence_nom) $locRows[] = ['Contact urgence', $user->locataire->contact_urgence_nom, 'text-xs'];
                        $locRows[] = ['Membre depuis', $user->created_at?->format('d/m/Y') ?? '—', ''];
                    @endphp
                    @foreach($locRows as [$lbl, $val, $cls])
                    <div class="flex items-center justify-between py-2.5">
                        <span class="font-body text-xs text-white/40">{{ $lbl }}</span>
                        <span class="font-body text-xs text-white/70 {{ $cls }}">{{ $val }}</span>
                    </div>
                    @endforeach
                    @if($user->locataire?->taux_effort)
                    <div class="flex items-center justify-between py-2.5">
                        <span class="font-body text-xs text-white/40">Taux d'effort</span>
                        <span class="font-body text-xs font-semibold {{ (float)$user->locataire->taux_effort > 33 ? 'text-bimo-red' : 'text-bimo-gold' }}">
                            {{ $user->locataire->taux_effort }}
                        </span>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @endif

</div>
@endsection
