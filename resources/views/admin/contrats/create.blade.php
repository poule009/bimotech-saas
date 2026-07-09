@extends('layouts.app')

@php
    $loyerPrefill = old('loyer_nu', $fromContrat->loyer_nu ?? $bienPreselectionne?->loyer_mensuel);
    $loyerPrefill = $loyerPrefill !== null ? (int) $loyerPrefill : '';
    $bienSel = $bienPreselectionne;
    $locSel  = $fromContrat?->locataire;
@endphp

@section('title', 'Nouveau contrat')
@section('page-title', $fromContrat ? 'Renouveler le contrat' : 'Nouveau contrat')
@section('page-subtitle')
    <a href="{{ route('admin.contrats.index') }}" class="text-teal font-semibold hover:underline">Contrats</a>
    <span class="text-muted"> / {{ $fromContrat ? 'Renouvellement' : 'Nouveau' }}</span>
@endsection

@section('content')
<form method="POST" action="{{ route('admin.contrats.store') }}"
      x-data="contratDuree" data-debut="{{ old('date_debut', date('Y-m-d')) }}" class="max-w-[1000px]">
    @csrf
    <input type="hidden" name="type_bail" value="{{ old('type_bail', 'habitation') }}">

    @if($errors->any())
        <div class="mb-5 rounded-lg bg-error/10 border border-error/25 px-4 py-3 text-[13px] text-error">
            <strong class="font-bold">Vérifiez le formulaire :</strong>
            <ul class="list-disc pl-5 mt-1">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
    @endif

    @if($fromContrat)
        <div class="mb-5 rounded-lg bg-teal/10 border border-teal/25 px-4 py-3 text-[13px] text-teal">
            🔄 Renouvellement — bien, locataire et loyer repris. Ajustez uniquement les nouvelles dates.
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-[1.5fr_1fr] gap-6 items-start">
        <div class="space-y-5">

            {{-- Bien & locataire --}}
            <div class="f-card">
                <h3 class="f-card-title">Bien et locataire</h3>
                <p class="f-card-sub">Le propriétaire est déduit automatiquement du bien — pas besoin de le resaisir.</p>

                <div class="mb-[18px]">
                    <x-search-or-create
                        name="bien_id" label="Bien" type="bien disponible"
                        :search-url="route('admin.biens.search-disponibles')"
                        :allow-create="false"
                        fill-field="loyer_nu"
                        :selected-id="old('bien_id', $bienSel?->id)"
                        :selected-name="$bienSel?->reference"
                        :selected-sub="$bienSel && $bienSel->relationLoaded('proprietaire') && $bienSel->proprietaire ? 'Propriétaire : '.$bienSel->proprietaire->name : ''" />
                    @error('bien_id')<p class="field-error">{{ $message }}</p>@enderror
                </div>

                <div>
                    <x-search-or-create
                        name="locataire_id" label="Locataire" type="locataire"
                        :search-url="route('admin.users.locataires.search')"
                        :create-url="route('admin.users.locataires.quick')"
                        :selected-id="old('locataire_id', $locSel?->id)"
                        :selected-name="$locSel?->name"
                        :selected-sub="$locSel?->telephone" />
                    @error('locataire_id')<p class="field-error">{{ $message }}</p>@enderror
                </div>
            </div>

            {{-- Conditions essentielles --}}
            <div class="f-card">
                <h3 class="f-card-title">Conditions essentielles</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-[18px]">
                    <div>
                        <label for="date_debut" class="f-label">Date de début</label>
                        <input id="date_debut" type="date" name="date_debut" x-model="debut" value="{{ old('date_debut', date('Y-m-d')) }}" class="f-input @error('date_debut') f-input-error @enderror">
                        @error('date_debut')<p class="field-error">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="duree" class="f-label">Durée</label>
                        <select id="duree" x-model="duree" class="f-select">
                            <option value="12">12 mois</option>
                            <option value="24">24 mois</option>
                            <option value="indeterminee">Indéterminée</option>
                        </select>
                    </div>
                </div>
                {{-- date_fin calculée depuis la durée --}}
                <input type="hidden" name="date_fin" x-bind:value="dateFin">

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="loyer_nu" class="f-label">Loyer mensuel (FCFA) <span class="text-gold text-[11px] font-bold uppercase">Pré-rempli</span></label>
                        <input id="loyer_nu" type="number" name="loyer_nu" value="{{ $loyerPrefill }}" min="1" step="1" placeholder="350000" class="f-input @error('loyer_nu') f-input-error @enderror">
                        @error('loyer_nu')<p class="field-error">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="caution" class="f-label">Dépôt de garantie (FCFA)</label>
                        <input id="caution" type="number" name="caution" value="{{ old('caution') }}" min="0" step="1" placeholder="700000" class="f-input">
                    </div>
                </div>
                <div class="mt-3 rounded-lg bg-gold/15 text-teal-deep px-4 py-3 text-[12.5px] leading-relaxed">
                    💡 Le loyer vient du prix affiché sur le bien. Modifiez-le ici si un montant différent a été négocié — ça ne changera pas le prix du bien ailleurs.
                </div>
            </div>

            {{-- Conditions supplémentaires (repliable) --}}
            <div class="f-card" x-data="collapsible">
                <div class="flex items-center justify-between cursor-pointer" x-on:click="toggle">
                    <div>
                        <h3 class="f-card-title mb-0.5">Conditions supplémentaires</h3>
                        <p class="text-[12.5px] text-muted">Charges, clauses particulières — facultatif</p>
                    </div>
                    <svg x-bind:class="chevClass" class="w-4 h-4 text-muted transition-transform" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 9l6 6 6-6"/></svg>
                </div>
                <div x-show="open" x-cloak class="mt-5 pt-5 border-t border-paper-dim space-y-[18px]">
                    <div>
                        <label for="reference_bail" class="f-label">Référence du bail <span class="text-muted font-normal">(optionnel — vide = générée automatiquement)</span></label>
                        <input id="reference_bail" type="text" name="reference_bail" value="{{ old('reference_bail') }}" maxlength="60" placeholder="Ex. votre n° de bail papier existant" class="f-input @error('reference_bail') f-input-error @enderror">
                        @error('reference_bail')<p class="field-error">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="charges_mensuelles" class="f-label">Charges mensuelles (FCFA) <span class="text-muted font-normal">(0 = incluses dans le loyer)</span></label>
                        <input id="charges_mensuelles" type="number" name="charges_mensuelles" value="{{ old('charges_mensuelles') }}" min="0" step="1" class="f-input max-w-[240px]">
                    </div>
                    <div>
                        <label for="clauses_particulieres" class="f-label">Clauses particulières</label>
                        <textarea id="clauses_particulieres" name="clauses_particulieres" rows="3" placeholder="Optionnel…" class="f-textarea">{{ old('clauses_particulieres') }}</textarea>
                    </div>
                </div>
            </div>
        </div>

        {{-- Colonne latérale --}}
        <div class="lg:sticky lg:top-6">
            <div class="f-card">
                <button type="submit" class="btn-primary mb-2.5">{{ $fromContrat ? 'Créer le renouvellement' : 'Créer le contrat' }}</button>
                <a href="{{ route('admin.contrats.index') }}" class="block w-full text-center py-[13px] rounded border-[1.5px] border-line bg-white text-ink text-sm font-semibold hover:border-teal transition-colors">Annuler</a>
                <p class="text-[12.5px] text-muted leading-relaxed mt-4 pt-4 border-t border-paper-dim">
                    Le loyer est dû le 1er de chaque mois — la première quittance est générée automatiquement.
                </p>
            </div>
        </div>
    </div>
</form>
@endsection
