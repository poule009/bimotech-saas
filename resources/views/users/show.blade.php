@extends('layouts.app')

@php
    $estLoc     = $user->isLocataire();
    $profil     = $estLoc ? null : $user->proprietaire;
    $entreprise = (bool) ($profil?->est_personne_morale_is);
    $fmt        = fn ($n) => number_format((float) $n, 0, ',', ' ');
    $modeLabels = ['especes'=>'Espèces','virement'=>'Virement bancaire','wave'=>'Wave','orange_money'=>'Orange Money','free_money'=>'Free Money','cheque'=>'Chèque','mobile_money'=>'Mobile Money'];
    $statutLabels = ['loue'=>['Loué','bg-green/10 text-green'],'disponible'=>['Vacant','bg-gold/15 text-gold'],'en_travaux'=>['En travaux','bg-error/10 text-error']];
@endphp

@section('title', $user->name)
@section('page-title', $estLoc ? 'Fiche locataire' : 'Fiche propriétaire')
@section('page-subtitle')
    <a href="{{ $estLoc ? route('admin.users.locataires') : route('admin.users.proprietaires') }}" class="text-teal font-semibold hover:underline">{{ $estLoc ? 'Locataires' : 'Propriétaires' }}</a>
    <span class="text-muted"> / {{ $user->name }}</span>
@endsection

@section('content')
@if($estLoc)
    @include('users._show-locataire')
@else
<div class="max-w-[1100px]" x-data="tabs">

    @if(session('success'))
        <div class="mb-5 rounded-lg bg-green/10 border border-green/25 px-4 py-3 text-[13px] text-green">{{ session('success') }}</div>
    @endif

    {{-- En-tête profil --}}
    <div class="bg-white border border-line rounded-xl p-6 md:p-7 flex flex-col sm:flex-row sm:items-center gap-5 mb-5">
        <div @class([
            'w-[68px] h-[68px] rounded-2xl flex items-center justify-center font-bold text-[22px] shrink-0',
            'bg-gold text-teal-deep' => $entreprise,
            'bg-teal text-paper' => ! $entreprise,
        ])>{{ mb_strtoupper(mb_substr($user->name, 0, 2)) }}</div>

        <div class="min-w-0 flex-1">
            <h2 class="font-display font-semibold text-[25px] mb-1.5">{{ $user->name }}</h2>
            <div class="flex flex-wrap items-center gap-x-4 gap-y-1.5 text-[13.5px] text-muted">
                @if($user->telephone)<span class="inline-flex items-center gap-1.5"><x-icon name="phone" size="13" /> {{ $user->telephone }}</span>@endif
                @if($user->email)<span class="inline-flex items-center gap-1.5"><x-icon name="mail" size="13" /> {{ $user->email }}</span>@endif
                @if($entreprise)
                    <span class="text-[12px] font-bold px-3 py-1 rounded-full bg-gold/15 text-gold">Entreprise</span>
                @else
                    <span class="text-[12px] font-bold px-3 py-1 rounded-full bg-green/10 text-green">Particulier</span>
                @endif
            </div>
        </div>

        <div class="flex items-center gap-2.5 shrink-0">
            <a href="{{ route('admin.users.edit', $user) }}" class="px-5 py-3 rounded-[10px] border-[1.5px] border-line bg-white text-ink text-[14px] font-bold hover:border-teal transition-colors">Modifier</a>
            @if(Route::has('admin.biens.create'))
                <a href="{{ route('admin.biens.create') }}" class="px-5 py-3 rounded-[10px] bg-teal text-paper text-[14px] font-bold hover:bg-teal-deep transition-colors whitespace-nowrap">+ Ajouter un bien</a>
            @endif
        </div>
    </div>

    {{-- Résumé financier --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-white border border-line rounded-xl p-4">
            <div class="text-[11.5px] uppercase tracking-wide text-muted font-bold mb-1.5">Biens</div>
            <div class="font-display font-semibold text-[20px]">{{ $stats['nb_biens'] ?? 0 }} <span class="text-[13px] text-muted font-body">· {{ $stats['nb_biens_loues'] ?? 0 }} loués</span></div>
        </div>
        <div class="bg-white border border-line rounded-xl p-4">
            <div class="text-[11.5px] uppercase tracking-wide text-muted font-bold mb-1.5">Loyers encaissés</div>
            <div class="font-display font-semibold text-[20px] text-gold">{{ $fmt($stats['total_loyers'] ?? 0) }} <span class="text-[13px] font-body text-muted">F</span></div>
        </div>
        <div class="bg-white border border-line rounded-xl p-4">
            <div class="text-[11.5px] uppercase tracking-wide text-muted font-bold mb-1.5">Net à reverser</div>
            <div class="font-display font-semibold text-[20px]">{{ $fmt($stats['total_net'] ?? 0) }} <span class="text-[13px] font-body text-muted">F</span></div>
        </div>
        <div class="bg-white border border-line rounded-xl p-4">
            <div class="text-[11.5px] uppercase tracking-wide text-muted font-bold mb-1.5">Paiements</div>
            <div class="font-display font-semibold text-[20px]">{{ $stats['nb_paiements'] ?? 0 }}</div>
        </div>
    </div>

    {{-- Onglets --}}
    <div class="flex gap-2 border-b-2 border-line mb-5">
        <button type="button" x-on:click="showInfo" x-bind:class="infoTabClass" class="px-4 py-3 text-[14.5px] font-bold border-b-[3px] -mb-0.5 transition-colors">Informations</button>
        <button type="button" x-on:click="showBiens" x-bind:class="biensTabClass" class="px-4 py-3 text-[14.5px] font-bold border-b-[3px] -mb-0.5 transition-colors">Biens liés · {{ $stats['nb_biens'] ?? 0 }}</button>
        <button type="button" x-on:click="showDocs" x-bind:class="docsTabClass" class="px-4 py-3 text-[14.5px] font-bold border-b-[3px] -mb-0.5 transition-colors">Documents</button>
    </div>

    {{-- Onglet Informations --}}
    <div x-show="isInfo">
        <div class="f-card mb-5">
            <h3 class="f-card-title mb-4">Coordonnées</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div><div class="text-[11.5px] uppercase tracking-wide text-muted font-bold mb-1">Téléphone</div><div class="text-[15px] font-semibold">{{ $user->telephone ?? '—' }}</div></div>
                <div><div class="text-[11.5px] uppercase tracking-wide text-muted font-bold mb-1">Email</div><div class="text-[15px] font-semibold">{{ $user->email ?? '—' }}</div></div>
                <div><div class="text-[11.5px] uppercase tracking-wide text-muted font-bold mb-1">Adresse</div><div class="text-[15px] font-semibold">{{ $user->adresse ?? $profil?->ville ?? '—' }}</div></div>
                <div><div class="text-[11.5px] uppercase tracking-wide text-muted font-bold mb-1">{{ $entreprise ? 'NINEA' : 'N° CNI' }}</div><div class="text-[15px] font-semibold">{{ ($entreprise ? $profil?->ninea : $profil?->cni) ?? '—' }}</div></div>
            </div>
        </div>
        <div class="f-card">
            <h3 class="f-card-title mb-4">Versement des loyers</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div><div class="text-[11.5px] uppercase tracking-wide text-muted font-bold mb-1">Mode préféré</div><div class="text-[15px] font-semibold">{{ $modeLabels[$profil?->mode_paiement_prefere] ?? 'Virement bancaire' }}</div></div>
                <div>
                    <div class="text-[11.5px] uppercase tracking-wide text-muted font-bold mb-1">Régime fiscal (TVA)</div>
                    @if($profil?->assujetti_tva)
                        <div class="text-[15px] font-semibold text-green">Assujetti à la TVA</div>
                    @else
                        <div class="text-[15px] font-semibold">Non assujetti à la TVA</div>
                        <div class="mt-1 inline-flex items-center gap-1.5 rounded-md bg-gold/15 text-teal-deep px-2 py-1 text-[11.5px] font-semibold">
                            <x-icon name="alert-triangle" size="13" /> Statut TVA non confirmé — à vérifier
                        </div>
                        <p class="text-[11.5px] text-muted mt-1">Tant que ce n'est pas confirmé, aucune TVA n'est facturée sur ses loyers (sécurité : pas de TVA indue). Modifiez la fiche pour confirmer l'assujettissement.</p>
                    @endif
                </div>
                <div>
                    <div class="text-[11.5px] uppercase tracking-wide text-muted font-bold mb-1">Retenue à la source (BRS)</div>
                    @if($profil?->est_personne_morale_is)
                        <div class="text-[15px] font-semibold">Non applicable</div>
                        <p class="text-[11.5px] text-muted mt-1">Personne morale (IS) — pas de retenue à la source sur ses loyers.</p>
                    @elseif($profil?->brs_dispense)
                        <div class="text-[15px] font-semibold text-gold">Dispensé de BRS</div>
                        <p class="text-[11.5px] text-muted mt-1">Aucune retenue de 5% n'est prélevée (dispense DGID). Décochez la dispense sur la fiche pour rétablir la retenue.</p>
                    @else
                        <div class="text-[15px] font-semibold text-green">Soumis à la BRS (5%)</div>
                        <p class="text-[11.5px] text-muted mt-1">Bailleur personne physique : 5% retenus sur les loyers ≥ 150 000 F et reversés à la DGID.</p>
                    @endif
                </div>
            </div>
        </div>

        {{-- Estimation IRPP foncier — Propriétaires Particuliers uniquement --}}
        @isset($irppEstimation)
            @php $fmtIrpp = fn($n) => number_format((float) $n, 0, ',', ' '); @endphp
            <div class="f-card mt-5">
                <h3 class="f-card-title mb-1">Estimation IRPP {{ $irppEstimation['annee'] }}</h3>
                <p class="f-card-sub">Impôt sur les revenus fonciers (loyers gérés dans l'app).</p>

                <div class="space-y-2.5 text-[13.5px]">
                    <div class="flex justify-between"><span class="text-muted">Loyers bruts encaissés {{ $irppEstimation['annee'] }}</span><span class="font-semibold">{{ $fmtIrpp($irppEstimation['revenu_brut_annuel']) }} F</span></div>
                    <div class="flex justify-between"><span class="text-muted">Après abattement 30%</span><span class="font-semibold">{{ $fmtIrpp($irppEstimation['base_apres_abattement']) }} F</span></div>
                    <div class="flex justify-between pt-2 border-t border-paper-dim"><span class="font-bold">IRPP estimé</span><span class="font-bold text-teal">{{ $fmtIrpp($irppEstimation['montant_estime']) }} F</span></div>
                </div>

                @if(collect($irppEstimation['detail'])->where('impot', '>', 0)->isNotEmpty())
                    <div class="mt-3 pt-3 border-t border-paper-dim">
                        <div class="text-[11.5px] uppercase tracking-wide text-muted font-bold mb-2">Détail par tranche</div>
                        <div class="space-y-1 text-[12.5px]">
                            @foreach($irppEstimation['detail'] as $t)
                                @if($t['impot'] > 0)
                                    <div class="flex justify-between text-muted"><span>{{ (int) $t['taux'] }}% sur {{ $fmtIrpp($t['assiette']) }} F</span><span class="font-semibold text-ink">{{ $fmtIrpp($t['impot']) }} F</span></div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- Badge de PÉRIMÈTRE (distinct du badge « à confirmer ») — toujours visible --}}
                <div class="mt-3 rounded-lg bg-teal/10 text-teal-deep px-3 py-2.5 text-[11.5px] leading-snug flex items-start gap-1.5">
                    <x-icon name="lightbulb" size="13" class="mt-0.5 shrink-0" />
                    <span>Cette estimation ne porte que sur les revenus locatifs gérés dans Bimothèque Immo. L'IRPP réel dépend de l'ensemble des revenus du propriétaire et de sa situation familiale, et doit être calculé lors de sa déclaration annuelle.</span>
                </div>
            </div>
        @endisset

        {{-- CGF — Contribution Globale Foncière (option, Particuliers uniquement) --}}
        @if(!empty($estParticulier))
            @php $fmtCgf = fn($n) => number_format((float) $n, 0, ',', ' '); @endphp

            {{-- Exclusion mutuelle : la CGF couvre l'IRPP-foncier + la CFPB de l'année en cours --}}
            @if(!empty($cgfCouvre))
                <div class="f-card mt-5 border-l-4 border-teal">
                    <div class="flex items-start gap-2">
                        <x-icon name="info" size="15" class="mt-0.5 shrink-0 text-teal" />
                        <p class="text-[12.5px] text-muted leading-snug">
                            <span class="font-semibold text-ink">IRPP foncier &amp; CFPB {{ $annee }} — Couvert par la CGF.</span>
                            Ce propriétaire a opté pour la Contribution Globale Foncière cette année ; l'IRPP foncier et la CFPB ne sont donc pas calculés séparément (voir l'encart CGF ci-dessous). Les données locatives sous-jacentes restent intactes.
                        </p>
                    </div>
                </div>
            @endif

            <div class="f-card mt-5">
                <h3 class="f-card-title mb-1">Contribution Globale Foncière (CGF)</h3>
                <p class="f-card-sub">Régime synthétique optionnel (Art. 75). Remplace l'IRPP foncier, l'IMF et la CFPB — pas la TVA.</p>

                @if(session('cgf_error'))
                    <div class="mb-4 rounded-lg bg-error/10 text-error px-3 py-2.5 text-[12px] leading-snug flex items-start gap-1.5">
                        <x-icon name="alert-triangle" size="13" class="mt-0.5 shrink-0" />
                        <span>{{ session('cgf_error') }}</span>
                    </div>
                @endif

                @if(!empty($cgfInfo['active']))
                    {{-- ÉTAT ACTIF : montant + échéancier --}}
                    <div class="space-y-2.5 text-[13.5px]">
                        <div class="flex justify-between"><span class="text-muted">Année d'option</span><span class="font-semibold">{{ $cgfInfo['annee'] }}</span></div>
                        <div class="flex justify-between"><span class="text-muted">Loyer brut prévisionnel</span><span class="font-semibold">{{ $fmtCgf($cgfInfo['revenu_prevu']) }} F</span></div>
                        <div class="flex justify-between pt-2 border-t border-paper-dim"><span class="font-bold">CGF due</span><span class="font-bold text-teal">{{ $fmtCgf($cgfInfo['montant']) }} F</span></div>
                    </div>

                    <div class="mt-3 pt-3 border-t border-paper-dim">
                        <div class="text-[11.5px] uppercase tracking-wide text-muted font-bold mb-2">
                            Échéancier — {{ $cgfInfo['mode_paiement'] === 'trois_versements' ? '3 versements' : 'versement unique' }}
                        </div>
                        <div class="space-y-1 text-[12.5px]">
                            @foreach($cgfInfo['echeances'] as $e)
                                <div class="flex justify-between text-muted">
                                    <span>{{ $e['libelle'] }} <span class="text-ink/40">({{ \Illuminate\Support\Carbon::parse($e['date'])->format('d/m/Y') }})</span></span>
                                    <span class="font-semibold text-ink">{{ $fmtCgf($e['montant']) }} F</span>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="mt-3 flex justify-between text-[12px] text-muted">
                        <span>Déclaration à déposer avant le</span>
                        <span class="font-semibold text-ink">1er février {{ $cgfInfo['annee'] }}</span>
                    </div>

                    {{-- Badge de fiabilité (discret) : bornes du barème = source privée --}}
                    <div class="mt-3 rounded-lg bg-gold/10 text-gold px-3 py-2.5 text-[11.5px] leading-snug flex items-start gap-1.5">
                        <x-icon name="info" size="13" class="mt-0.5 shrink-0" />
                        <span>Montant calculé selon le barème CGF (1/12 · 1,5/12 · 2/12) — les bornes 12M/18M sont à recouper avec le texte officiel de l'Art. 75.</span>
                    </div>

                    <form method="POST" action="{{ route('admin.users.cgf.desactiver', $user) }}" class="mt-4"
                          onsubmit="return confirm('Retirer l\'option CGF ? Le propriétaire repassera au régime réel (IRPP + CFPB).');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-[12.5px] font-semibold text-error hover:underline">Retirer l'option CGF</button>
                    </form>
                @else
                    {{-- FORMULAIRE D'OPTION --}}
                    <form method="POST" action="{{ route('admin.users.cgf.option', $user) }}" class="space-y-4">
                        @csrf
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-[12px] font-semibold text-muted mb-1">Année concernée</label>
                                <input type="number" name="cgf_annee" value="{{ old('cgf_annee', $annee) }}" min="2020" max="2100"
                                       class="w-full rounded-lg border border-line px-3 py-2 text-[13.5px]" required>
                            </div>
                            <div>
                                <label class="block text-[12px] font-semibold text-muted mb-1">Loyer brut prévisionnel (F)</label>
                                <input type="number" name="cgf_revenu_brut_prevu" value="{{ old('cgf_revenu_brut_prevu') }}" min="0" step="1000"
                                       placeholder="Loyers attendus de l'année"
                                       class="w-full rounded-lg border border-line px-3 py-2 text-[13.5px]" required>
                            </div>
                        </div>

                        <div>
                            <label class="block text-[12px] font-semibold text-muted mb-1.5">Mode de paiement</label>
                            <div class="flex flex-col sm:flex-row gap-2">
                                <label class="flex items-center gap-2 rounded-lg border border-line px-3 py-2 text-[13px] cursor-pointer flex-1">
                                    <input type="radio" name="cgf_mode_paiement" value="unique" {{ old('cgf_mode_paiement', 'unique') === 'unique' ? 'checked' : '' }}>
                                    <span>1 versement (fin février)</span>
                                </label>
                                <label class="flex items-center gap-2 rounded-lg border border-line px-3 py-2 text-[13px] cursor-pointer flex-1">
                                    <input type="radio" name="cgf_mode_paiement" value="trois_versements" {{ old('cgf_mode_paiement') === 'trois_versements' ? 'checked' : '' }}>
                                    <span>3 versements (fév · avr · juin)</span>
                                </label>
                            </div>
                        </div>

                        <div class="rounded-lg bg-paper-dim px-3 py-2.5 text-[11.5px] text-muted leading-snug">
                            Le loyer <span class="font-semibold text-ink">prévisionnel</span> est saisi manuellement (loyers attendus de l'année à venir) — indépendant des paiements réels. Éligibilité : loyer brut annuel ≤ 30 000 000 F. Déclaration avant le 1er février.
                        </div>

                        <button type="submit" class="btn-primary w-full sm:w-auto">Opter pour la CGF</button>
                    </form>
                @endif
            </div>
        @endif
    </div>

    {{-- Onglet Biens --}}
    <div x-show="isBiens" x-cloak>
        @if($biens->isEmpty())
            <div class="bg-white border border-line rounded-xl py-14 text-center text-muted text-[14px]">Aucun bien rattaché à ce propriétaire.</div>
        @else
            <div class="space-y-3">
                @foreach($biens as $bien)
                    @php [$stLabel, $stClass] = $statutLabels[$bien->statut] ?? [ucfirst($bien->statut), 'bg-paper-dim text-muted']; @endphp
                    <a href="{{ Route::has('admin.biens.show') ? route('admin.biens.show', $bien) : '#' }}"
                       class="flex items-center gap-4 p-4 border border-line rounded-xl bg-white hover:border-teal transition-colors">
                        <span class="w-[50px] h-[50px] rounded-[11px] bg-teal text-paper flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 21h18M5 21V7l7-4 7 4v14M9 21v-6h6v6"/></svg>
                        </span>
                        <div class="min-w-0 flex-1">
                            <div class="font-bold text-[15px] truncate">{{ $bien->reference }}</div>
                            <div class="text-[12.5px] text-muted truncate">{{ $bien->ville }}{{ $bien->type ? ' · '.ucfirst($bien->type) : '' }}</div>
                        </div>
                        <span class="text-[12px] font-bold px-3 py-1.5 rounded-full {{ $stClass }} shrink-0">{{ $stLabel }}</span>
                    </a>
                @endforeach
            </div>
            <div class="mt-5">{{ $biens->links() }}</div>
        @endif
    </div>

    {{-- Onglet Documents --}}
    <div x-show="isDocs" x-cloak>
        @if($profil?->piece_identite_path)
            <div class="f-card flex items-center gap-4">
                <div class="w-11 h-11 rounded-[10px] bg-teal/10 text-teal flex items-center justify-center shrink-0">
                    <x-icon name="file-text" size="20" />
                </div>
                <div class="min-w-0 flex-1">
                    <div class="text-[14.5px] font-bold">Pièce d'identité</div>
                    <div class="text-[12.5px] text-muted truncate">{{ basename($profil->piece_identite_path) }}</div>
                </div>
                <a href="{{ Storage::disk('public')->url($profil->piece_identite_path) }}" target="_blank" rel="noopener"
                   class="px-4 py-2.5 rounded-[10px] border-[1.5px] border-line bg-white text-ink text-[13.5px] font-bold hover:border-teal transition-colors shrink-0">Ouvrir</a>
            </div>
        @else
            <div class="bg-white border border-line rounded-xl py-14 text-center text-muted text-[14px]">
                Aucun document. Les pièces ajoutées depuis les biens ou contrats de ce propriétaire apparaîtront ici.
            </div>
        @endif
    </div>
</div>
@endif
@endsection
