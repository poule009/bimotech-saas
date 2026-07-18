@extends('layouts.superadmin')

@section('title', $regle->titre)

@php
    use App\Support\RegleFiscaleCatalogue;
    use App\Models\RegleFiscale;

    // Rend une valeur d'historique lisible : les changements de statut sont
    // stockés en clé technique (non_verifie…) → on affiche le libellé humain.
    $histoValeur = fn ($champ, $v) => $champ === 'statut'
        ? ((RegleFiscale::STATUTS[$v] ?? $v) ?: '∅')
        : ($v ?: '∅');

    $chip = [
        'green' => 'bg-green/10 text-green',
        'teal'  => 'bg-teal/10 text-teal',
        'gold'  => 'bg-gold/10 text-gold',
        'gray'  => 'bg-ink/[0.06] text-muted',
    ];

    // Lignes de sources à afficher dans le formulaire : existantes (ou saisies
    // rejetées par la validation) + 2 lignes vierges pour en ajouter.
    $sourceRows = collect(old('sources', $regle->sources ?? []))
        ->map(fn ($s) => ['libelle' => $s['libelle'] ?? '', 'url' => $s['url'] ?? ''])
        ->values()
        ->all();
    $sourceRows = array_merge($sourceRows, [['libelle' => '', 'url' => ''], ['libelle' => '', 'url' => '']]);

    $groupeLabel = RegleFiscaleCatalogue::GROUPES[RegleFiscaleCatalogue::groupe($regle->categorie)] ?? $regle->categorie;
@endphp

@section('content')
<div class="max-w-[1180px] mx-auto">

    {{-- ─────────── Fil d'ariane ─────────── --}}
    <div class="text-[12.5px] text-muted mb-3.5">
        <a href="{{ route('superadmin.regles.index') }}" class="text-teal-deep font-semibold border-b border-gold pb-px">Règles fiscales</a>
        &nbsp;/&nbsp; {{ $regle->titre }}
    </div>

    {{-- ─────────── Messages flash ─────────── --}}
    @if(session('success'))
        <div class="mb-4 rounded-lg bg-green/10 border border-green/25 px-4 py-3 text-[13px] text-green font-medium">{{ session('success') }}</div>
    @elseif(session('info'))
        <div class="mb-4 rounded-lg bg-ink/[0.04] border border-line px-4 py-3 text-[13px] text-muted font-medium">{{ session('info') }}</div>
    @endif

    {{-- ─────────── En-tête règle ─────────── --}}
    <div class="flex items-center justify-between gap-4 bg-white border border-line rounded-2xl px-6 py-5 mb-5 flex-wrap">
        <div class="min-w-0">
            <div class="font-display text-[22px] font-medium text-ink">{{ $regle->titre }}</div>
            <div class="text-[13px] text-muted mt-1">{{ Str::limit($regle->description, 90) }}</div>
            <div class="text-[11.5px] text-muted/70 mt-1">{{ $groupeLabel }} · clé <span class="font-mono">{{ $regle->cle }}</span></div>
        </div>
        <span class="text-[11.5px] font-semibold px-3 py-1 rounded-full inline-flex items-center gap-1.5 {{ $chip[$regle->statut_variant] }}">
            <span class="w-1.5 h-1.5 rounded-full bg-current"></span>{{ $regle->statut_label }}
        </span>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-[1.25fr_1fr] gap-5 items-start">

        {{-- ═════════ Colonne gauche : formulaire ═════════ --}}
        <div>
            <section class="bg-white border border-line rounded-xl overflow-hidden mb-5">
                <div class="px-5 py-4 border-b border-paper-dim font-display text-[16.5px] font-medium text-ink">Modifier la règle</div>
                <div class="px-5 py-5">

                    {{-- Bandeau d'avertissement --}}
                    <div class="flex gap-2.5 bg-gold/[0.08] border border-gold-soft rounded-lg px-3.5 py-3 text-[12px] text-[#8A6412] leading-relaxed mb-5">
                        <svg class="w-4 h-4 shrink-0 mt-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10.3 3.9 1.8 18a2 2 0 0 0 1.7 3h17a2 2 0 0 0 1.7-3L13.7 3.9a2 2 0 0 0-3.4 0Z"/><path d="M12 9v4M12 17h.01"/></svg>
                        <div>Le statut et la source sont utilisés dans la traçabilité affichée aux propriétaires. Une modification ici s'applique immédiatement à toutes les agences.</div>
                    </div>

                    @if($errors->any())
                        <div class="mb-5 rounded-lg bg-error/10 border border-error/25 px-4 py-3 text-[12.5px] text-error">
                            <ul class="list-disc list-inside space-y-0.5">
                                @foreach($errors->all() as $err)<li>{{ $err }}</li>@endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('superadmin.regles.update', $regle) }}">
                        @csrf
                        @method('PATCH')

                        {{-- Nom --}}
                        <div class="mb-4">
                            <label class="block text-[11px] font-semibold uppercase tracking-wide text-muted mb-1.5">Nom de la règle</label>
                            <input type="text" name="titre" value="{{ old('titre', $regle->titre) }}"
                                   class="w-full px-3 py-2.5 rounded-lg border border-line bg-paper text-[13.5px] text-ink outline-none focus:border-gold">
                        </div>

                        {{-- Valeur actuelle — LECTURE SEULE (dérivée du moteur) --}}
                        <div class="mb-4">
                            <label class="block text-[11px] font-semibold uppercase tracking-wide text-muted mb-1.5">
                                Valeur actuelle
                                <span class="normal-case tracking-normal font-normal text-muted/70">— lecture seule, appliquée par le moteur</span>
                            </label>
                            <div class="w-full px-3 py-2.5 rounded-lg border border-dashed border-line bg-paper-dim/50 text-[13.5px] font-mono font-semibold text-ink">
                                {{ $valeur ?? '— (règle qualitative / calendaire)' }}
                            </div>
                            @if($bareme)
                                <div class="mt-3 border border-line rounded-lg overflow-hidden">
                                    <div class="px-3 py-2 bg-paper-dim text-[11.5px] font-semibold text-muted uppercase tracking-wide">{{ $bareme['titre'] }} · {{ $bareme['unite'] }}</div>
                                    <table class="w-full border-collapse text-[12.5px]">
                                        <thead>
                                            <tr class="text-left text-muted">
                                                <th class="px-3 py-1.5 font-medium">Seuil bas</th>
                                                <th class="px-3 py-1.5 font-medium">Seuil haut</th>
                                                <th class="px-3 py-1.5 font-medium text-right">Taux</th>
                                            </tr>
                                        </thead>
                                        <tbody class="font-mono">
                                            @foreach($bareme['lignes'] as $ligne)
                                                <tr class="border-t border-paper-dim">
                                                    <td class="px-3 py-1.5">{{ $ligne['bas'] }}</td>
                                                    <td class="px-3 py-1.5">{{ $ligne['haut'] }}</td>
                                                    <td class="px-3 py-1.5 text-right font-semibold text-ink">{{ $ligne['taux'] }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @endif
                        </div>

                        {{-- Statut --}}
                        <div class="mb-4">
                            <label class="block text-[11px] font-semibold uppercase tracking-wide text-muted mb-1.5">Statut de fiabilité</label>
                            <select name="statut" class="w-full px-3 py-2.5 rounded-lg border border-line bg-white text-[13.5px] text-ink outline-none focus:border-gold cursor-pointer">
                                @foreach($statuts as $val => $label)
                                    <option value="{{ $val }}" @selected(old('statut', $regle->statut) === $val)>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Description --}}
                        <div class="mb-4">
                            <label class="block text-[11px] font-semibold uppercase tracking-wide text-muted mb-1.5">Description</label>
                            <textarea name="description" rows="4"
                                      class="w-full px-3 py-2.5 rounded-lg border border-line bg-paper text-[13.5px] text-ink outline-none focus:border-gold resize-y">{{ old('description', $regle->description) }}</textarea>
                        </div>

                        {{-- Sources (libellé + lien) --}}
                        <div class="mb-4">
                            <label class="block text-[11px] font-semibold uppercase tracking-wide text-muted mb-1.5">Sources</label>
                            <div class="space-y-2.5">
                                @foreach($sourceRows as $i => $src)
                                    <div class="grid grid-cols-1 sm:grid-cols-[1fr_1fr] gap-2">
                                        <input type="text" name="sources[{{ $i }}][libelle]" value="{{ $src['libelle'] }}"
                                               placeholder="Libellé de la source"
                                               class="px-3 py-2 rounded-lg border border-line bg-paper text-[13px] text-ink outline-none focus:border-gold">
                                        <input type="text" name="sources[{{ $i }}][url]" value="{{ $src['url'] }}"
                                               placeholder="https:// (optionnel)"
                                               class="px-3 py-2 rounded-lg border border-line bg-paper text-[13px] text-ink outline-none focus:border-gold">
                                    </div>
                                @endforeach
                            </div>
                            <p class="text-[11px] text-muted mt-1.5">Une ligne sans libellé est ignorée. Laissez vide pour retirer une source.</p>
                        </div>

                        {{-- Note --}}
                        <div class="mb-4">
                            <label class="block text-[11px] font-semibold uppercase tracking-wide text-muted mb-1.5">Note / réserve <span class="normal-case tracking-normal font-normal text-muted/70">(optionnel)</span></label>
                            <textarea name="note" rows="2"
                                      class="w-full px-3 py-2.5 rounded-lg border border-line bg-paper text-[13.5px] text-ink outline-none focus:border-gold resize-y">{{ old('note', $regle->note) }}</textarea>
                        </div>

                        {{-- Date de vérification --}}
                        <div class="mb-5">
                            <label class="block text-[11px] font-semibold uppercase tracking-wide text-muted mb-1.5">Date de dernière vérification</label>
                            <input type="date" name="date_verification"
                                   value="{{ old('date_verification', $regle->date_verification?->format('Y-m-d')) }}"
                                   class="px-3 py-2.5 rounded-lg border border-line bg-paper text-[13.5px] text-ink outline-none focus:border-gold">
                        </div>

                        <button type="submit" class="bg-teal-deep text-paper text-[13.5px] font-semibold px-5 py-3 rounded-lg hover:bg-teal transition-colors">
                            Enregistrer les modifications
                        </button>
                    </form>
                </div>
            </section>
        </div>

        {{-- ═════════ Colonne droite : contexte ═════════ --}}
        <div>
            {{-- Utilisée dans --}}
            <section class="bg-white border border-line rounded-xl overflow-hidden mb-5">
                <div class="px-5 py-4 border-b border-paper-dim font-display text-[16.5px] font-medium text-ink">Utilisée dans</div>
                <div class="px-5 py-4 text-[13px] text-ink">{{ $utiliseeDans }}</div>
            </section>

            {{-- Historique --}}
            <section class="bg-white border border-line rounded-xl overflow-hidden">
                <div class="px-5 py-4 border-b border-paper-dim font-display text-[16.5px] font-medium text-ink">Historique des modifications</div>
                <div class="px-5 py-4">
                    @forelse($historiques as $h)
                        <div class="flex gap-3 py-2.5 {{ $loop->last ? '' : 'border-b border-paper-dim' }}">
                            <div class="w-1.5 h-1.5 rounded-full bg-gold mt-1.5 shrink-0"></div>
                            <div class="min-w-0 text-[13px]">
                                <div class="text-ink">
                                    <b class="font-semibold">{{ $h->admin?->name ?? $h->admin_nom ?? 'Admin' }}</b>
                                    a modifié <b class="font-semibold">{{ $h->champ_label }}</b>
                                </div>
                                @if($h->champ !== 'description' && $h->champ !== 'sources')
                                    <div class="text-[12px] text-muted mt-0.5">
                                        <span class="line-through">{{ Str::limit($histoValeur($h->champ, $h->ancienne_valeur), 40) }}</span>
                                        &rarr; <span class="text-ink">{{ Str::limit($histoValeur($h->champ, $h->nouvelle_valeur), 40) }}</span>
                                    </div>
                                @endif
                                <div class="text-[11.5px] text-muted font-mono mt-1">{{ $h->created_at->locale('fr')->isoFormat('D MMM Y · HH:mm') }}</div>
                            </div>
                        </div>
                    @empty
                        <div class="text-[13px] text-muted">
                            Aucune modification enregistrée.
                            Règle initialisée le {{ $regle->created_at?->locale('fr')->isoFormat('D MMMM Y') ?? '—' }}.
                        </div>
                    @endforelse
                </div>
            </section>
        </div>
    </div>
</div>
@endsection
