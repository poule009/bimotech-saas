@extends('layouts.app')

@php
    $statutPill = [
        'loue'       => ['Loué', 'bg-green/10 text-green'],
        'disponible' => ['Vacant', 'bg-gold/15 text-gold'],
        'en_travaux' => ['En travaux', 'bg-error/10 text-error'],
        'archive'    => ['Archivé', 'bg-paper-dim text-muted'],
    ];
    [$stLabel, $stClass] = $statutPill[$bien->statut] ?? [ucfirst($bien->statut), 'bg-paper-dim text-muted'];
    $fmt = fn ($n) => number_format((float) $n, 0, ',', ' ');
    $titre = $bien->titre_fallback;
@endphp

@section('title', $titre)
@section('page-title', 'Fiche bien')
@section('page-subtitle')
    <a href="{{ route('admin.biens.index') }}" class="text-teal font-semibold hover:underline">Biens</a>
    <span class="text-muted"> / {{ $titre }}</span>
@endsection

@section('content')
<div class="max-w-[1000px]">

    @if(session('success'))
        <div class="mb-5 rounded-lg bg-green/10 border border-green/25 px-4 py-3 text-[13px] text-green">{{ session('success') }}</div>
    @endif

    {{-- En-tête --}}
    @php $cover = $bien->photos->firstWhere('est_principale', true) ?? $bien->photos->first(); @endphp
    <div class="bg-white border border-line rounded-2xl overflow-hidden mb-5">
        <div class="h-[190px] relative flex items-end p-6 {{ $cover ? '' : 'bg-teal' }}">
            @if($cover)
                <img src="{{ \Illuminate\Support\Facades\Storage::url($cover->chemin) }}" alt="{{ $titre }}" class="absolute inset-0 w-full h-full object-cover">
                <div class="absolute inset-0 bg-gradient-to-t from-black/75 via-black/25 to-transparent"></div>
            @endif
            <div class="relative text-paper">
                <h2 class="font-display font-semibold text-[24px] mb-1">{{ $titre }}</h2>
                <div class="text-[13.5px] text-paper/85">{{ $bien->quartier ? $bien->quartier.', ' : '' }}{{ $bien->ville }} · {{ \App\Models\Bien::TYPES[$bien->type] ?? ucfirst($bien->type) }}</div>
            </div>
        </div>
        <div class="px-6 py-4 flex flex-wrap items-center gap-3">
            <span class="text-[12.5px] font-bold px-3.5 py-1.5 rounded-full {{ $stClass }}">{{ $stLabel }}</span>
            <span class="text-[12.5px] text-muted">Réf. {{ $bien->reference }}</span>
            <div class="flex items-center gap-2.5 ml-auto">
                <a href="{{ route('admin.biens.edit', $bien) }}" class="px-5 py-2.5 rounded-[10px] border-[1.5px] border-line bg-white text-ink text-[14px] font-bold hover:border-teal transition-colors">Modifier</a>
                @if(Route::has('admin.contrats.create') && ! $bien->contratActif)
                    <a href="{{ route('admin.contrats.create', ['bien_id' => $bien->id]) }}" class="px-5 py-2.5 rounded-[10px] bg-teal text-paper text-[14px] font-bold hover:bg-teal-deep transition-colors whitespace-nowrap">+ Nouveau contrat</a>
                @endif
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-[1.4fr_1fr] gap-5 items-start">
        <div class="space-y-5">
            {{-- Informations --}}
            <div class="f-card">
                <h3 class="f-card-title mb-4">Informations</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <div class="text-[11.5px] uppercase tracking-wide text-muted font-bold mb-1">Propriétaire</div>
                        <a href="{{ route('admin.users.show', $bien->proprietaire) }}" class="text-[15px] font-semibold text-teal hover:underline">{{ $bien->proprietaire->name ?? '—' }}</a>
                    </div>
                    <div><div class="text-[11.5px] uppercase tracking-wide text-muted font-bold mb-1">Type</div><div class="text-[15px] font-semibold">{{ \App\Models\Bien::TYPES[$bien->type] ?? ucfirst($bien->type) }}</div></div>
                    <div><div class="text-[11.5px] uppercase tracking-wide text-muted font-bold mb-1">Adresse</div><div class="text-[15px] font-semibold">{{ $bien->adresse }}</div></div>
                    <div><div class="text-[11.5px] uppercase tracking-wide text-muted font-bold mb-1">Loyer mensuel</div><div class="text-[15px] font-semibold text-gold">{{ $fmt($bien->loyer_mensuel) }} F</div></div>
                    @if($bien->surface_m2)<div><div class="text-[11.5px] uppercase tracking-wide text-muted font-bold mb-1">Surface</div><div class="text-[15px] font-semibold">{{ rtrim(rtrim($bien->surface_m2, '0'), '.') }} m²</div></div>@endif
                    <div><div class="text-[11.5px] uppercase tracking-wide text-muted font-bold mb-1">Ajouté le</div><div class="text-[15px] font-semibold">{{ optional($bien->created_at)->locale('fr')->isoFormat('D MMMM Y') }}</div></div>
                </div>
            </div>

            {{-- CFPB estimée (Contribution Foncière des Propriétés Bâties) --}}
            @isset($cfpb)
                @php $fmtCfpb = fn($n) => number_format((float) $n, 0, ',', ' '); @endphp
                <div class="f-card">
                    <h3 class="f-card-title mb-1">CFPB &amp; TEOM estimées</h3>
                    <p class="f-card-sub">Contribution Foncière des Propriétés Bâties &amp; Taxe Ordures Ménagères — {{ $annee }}.</p>

                    @if($cfpbCouvertParCgf)
                        <div class="rounded-lg bg-teal/10 text-teal-deep px-3 py-2.5 text-[12.5px] leading-snug flex items-start gap-1.5">
                            <x-icon name="info" size="14" class="mt-0.5 shrink-0" />
                            <span>Le propriétaire a opté pour la <span class="font-semibold">CGF</span> cette année : la CFPB et la TEOM sont couvertes par ce régime et ne sont pas estimées séparément (voir la fiche du propriétaire).</span>
                        </div>
                    @else
                        <div class="space-y-2.5 text-[13.5px]">
                            <div class="flex justify-between"><span class="text-muted">Valeur locative estimée (loyer × 12)</span><span class="font-semibold">{{ $fmtCfpb($cfpb['valeur_locative']) }} F</span></div>
                            <div class="flex justify-between pt-2 border-t border-paper-dim"><span class="font-bold">CFPB estimée (5 %)</span><span class="font-bold text-teal">{{ $fmtCfpb($cfpb['montant']) }} F</span></div>
                            <div class="flex justify-between"><span class="font-bold">TEOM — estimation à déclarer ({{ rtrim(rtrim(number_format($cfpb['teom_taux'], 1, ',', ' '), '0'), ',') }} %)</span><span class="font-bold text-teal">{{ $fmtCfpb($cfpb['teom_montant']) }} F</span></div>
                        </div>

                        {{-- Note : relie la TEOM (obligation annuelle du propriétaire) à la « TOM
                             refacturée au locataire » (ligne mensuelle du loyer) — même taxe, deux angles. --}}
                        <div class="mt-3 rounded-lg bg-teal/10 text-teal-deep px-3 py-2.5 text-[11.5px] leading-snug flex items-start gap-1.5">
                            <x-icon name="info" size="13" class="mt-0.5 shrink-0" />
                            <span>Même taxe que la <span class="font-semibold">« TOM refacturée au locataire »</span> visible sur le loyer : celle-ci est le montant mensuel que le propriétaire récupère auprès du locataire, la TEOM ci-dessus est l'estimation annuelle qu'il déclare à l'administration.</span>
                        </div>

                        {{-- Ligne TOM refacturée (champ tom_mensuelle) — style distinct (fond paper),
                             hors du registre fiscal principal pour bien la séparer des obligations. --}}
                        <div class="mt-3 flex items-center justify-between bg-paper rounded-lg px-4 py-3">
                            <span class="text-[13px] text-muted">TOM refacturée au locataire <span class="font-semibold text-ink">(dans le loyer)</span></span>
                            <span class="font-display font-semibold text-[15px] text-ink">{{ $fmtCfpb((int) $bien->tom_mensuelle) }} F / mois</span>
                        </div>

                        {{-- Badge PERMANENT (estimation structurelle) — jamais levable, style sobre/neutre
                             distinct du gold « à confirmer » (bornes CGF, DGID) qui, lui, est temporaire.
                             Vaut pour la CFPB ET la TEOM (même assiette, même limite structurelle). --}}
                        <div class="mt-3 rounded-lg bg-paper-dim border border-line text-ink/70 px-3 py-2.5 text-[11.5px] leading-snug flex items-start gap-1.5">
                            <x-icon name="alert-triangle" size="13" class="mt-0.5 shrink-0 text-muted" />
                            <span><span class="font-semibold">Estimation structurelle (permanente).</span> CFPB et TEOM utilisent le loyer annuel comme approximation de la valeur locative cadastrale. Les montants réels sont fixés par l'administration fiscale et peuvent différer significativement. Déclaration avant le 31 janvier.</span>
                        </div>

                        {{-- Navigation croisée (§4.3) --}}
                        <div class="mt-4 flex flex-wrap gap-2">
                            <a href="{{ route('admin.users.show', $bien->proprietaire) }}" class="inline-flex items-center gap-1.5 text-[12px] font-semibold text-teal bg-paper border border-line rounded-lg px-3 py-1.5 hover:border-teal">
                                <x-icon name="user" size="13" /> Fiche du propriétaire
                            </a>
                            @if($bien->contratActif)
                                <a href="{{ route('admin.contrats.show', $bien->contratActif) }}" class="inline-flex items-center gap-1.5 text-[12px] font-semibold text-teal bg-paper border border-line rounded-lg px-3 py-1.5 hover:border-teal">
                                    <x-icon name="file-text" size="13" /> Contrat lié
                                </a>
                            @endif
                        </div>
                    @endif
                </div>
            @endisset

            {{-- Contrat en cours --}}
            <div class="f-card">
                <h3 class="f-card-title mb-4">Contrat en cours</h3>
                @if($bien->contratActif)
                    @php $c = $bien->contratActif; @endphp
                    <div class="flex items-center gap-4 p-4 border border-line rounded-xl bg-paper">
                        <span class="w-[46px] h-[46px] rounded-[11px] bg-teal text-paper flex items-center justify-center font-bold text-[15px] shrink-0">{{ mb_strtoupper(mb_substr($c->locataire->name ?? '?', 0, 2)) }}</span>
                        <div class="min-w-0">
                            <div class="font-bold text-[14.5px] truncate">{{ $c->locataire->name ?? 'Locataire' }}</div>
                            <div class="text-[12.5px] text-muted">Depuis le {{ optional($c->date_debut)->locale('fr')->isoFormat('D MMM Y') }}</div>
                        </div>
                        <div class="ml-auto text-right shrink-0">
                            <strong class="block text-[16px]">{{ $fmt($c->loyer_contractuel) }} F</strong>
                            <span class="text-[11px] text-muted">par mois</span>
                        </div>
                    </div>
                @else
                    <div class="py-6 text-center text-[13.5px] text-muted">Aucun contrat en cours.</div>
                @endif
            </div>
        </div>

        {{-- Photos --}}
        <div class="f-card">
            <div class="flex items-center justify-between mb-1">
                <h3 class="f-card-title">Photos</h3>
                <span class="text-[12px] text-muted">{{ $bien->photos->count() }}/10</span>
            </div>
            <p class="f-card-sub">La photo « principale » sert de couverture dans les listes et l'en-tête.</p>

            {{-- Erreurs d'upload --}}
            @if($errors->has('photos') || collect($errors->keys())->contains(fn ($k) => str_starts_with($k, 'photos.')))
                <div class="mb-3 rounded-lg bg-error/10 border border-error/25 px-3 py-2 text-[12.5px] text-error space-y-0.5">
                    @foreach($errors->all() as $message)
                        <div>{{ $message }}</div>
                    @endforeach
                </div>
            @endif

            {{-- Formulaire d'ajout (multi-fichiers) --}}
            @can('update', $bien)
                <form method="POST" action="{{ route('admin.biens.photos.store', $bien) }}" enctype="multipart/form-data"
                      x-data="photosUpload" class="mb-4">
                    @csrf
                    <label class="block border-[1.5px] border-dashed border-line rounded-[12px] px-4 py-6 text-center cursor-pointer hover:border-teal hover:bg-paper transition-colors">
                        <input type="file" name="photos[]" accept="image/jpeg,image/png,image/webp" multiple class="hidden" x-on:change="pick">
                        <div class="flex flex-col items-center text-muted">
                            <x-icon name="upload" size="24" class="mb-1.5 text-teal" />
                            <span class="text-[13.5px] font-semibold text-ink" x-text="label">Cliquez ou glissez vos photos ici</span>
                            <span class="text-[11.5px] text-muted mt-0.5">JPG, PNG ou WEBP · 3 Mo max · jusqu'à 10 à la fois</span>
                        </div>
                    </label>
                    <button type="submit" x-bind:disabled="! hasFiles" x-bind:class="submitClass"
                            class="mt-3 w-full px-4 py-2.5 rounded-[10px] text-[13.5px] font-bold transition-colors">
                        Ajouter les photos
                    </button>
                </form>
            @endcan

            {{-- Galerie --}}
            @if($bien->photos->isNotEmpty())
                <div class="grid grid-cols-3 gap-2">
                    @foreach($bien->photos as $photo)
                        <div class="group relative aspect-square rounded-lg bg-paper-dim border border-line overflow-hidden">
                            <img src="{{ \Illuminate\Support\Facades\Storage::url($photo->chemin) }}" alt="{{ $photo->nom_original }}" loading="lazy" class="w-full h-full object-cover">

                            @if($photo->est_principale)
                                <span class="absolute top-1.5 left-1.5 bg-gold text-teal-deep text-[10px] font-extrabold px-2 py-0.5 rounded-full shadow-sm">Principale</span>
                            @endif

                            @can('update', $bien)
                                {{-- Actions au survol --}}
                                <div class="absolute inset-0 bg-black/45 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center gap-2">
                                    @unless($photo->est_principale)
                                        <form method="POST" action="{{ route('admin.biens.photos.principale', [$bien, $photo]) }}">
                                            @csrf @method('PATCH')
                                            <button type="submit" class="w-8 h-8 rounded-full bg-white text-gold flex items-center justify-center hover:scale-110 transition-transform shadow" title="Définir comme principale" aria-label="Définir comme principale">
                                                <x-icon name="star" size="15" />
                                            </button>
                                        </form>
                                    @endunless
                                    <form method="POST" action="{{ route('admin.biens.photos.destroy', [$bien, $photo]) }}"
                                          x-data="confirmForm" x-on:submit="submit" data-confirm="Supprimer cette photo ?">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="w-8 h-8 rounded-full bg-white text-error flex items-center justify-center hover:scale-110 transition-transform shadow" title="Supprimer" aria-label="Supprimer">
                                            <x-icon name="trash" size="15" />
                                        </button>
                                    </form>
                                </div>
                            @endcan
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-6 text-[13px] text-muted border-[1.5px] border-dashed border-line rounded-[10px]">Aucune photo pour l'instant</div>
            @endif
        </div>
    </div>
</div>
@endsection
