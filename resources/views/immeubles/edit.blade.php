@extends('layouts.app')

@section('title', 'Modifier ' . $immeuble->nom)
@section('page-title', 'Modifier l\'immeuble')
@section('page-subtitle')
    <a href="{{ route('admin.immeubles.show', $immeuble) }}" class="text-teal font-semibold hover:underline">{{ $immeuble->nom }}</a>
    <span class="text-muted"> / Modifier</span>
@endsection

@section('content')
<form method="POST" action="{{ route('admin.immeubles.update', $immeuble) }}" class="max-w-[1000px]">
    @csrf
    @method('PUT')

    @if($errors->any())
        <div class="mb-5 rounded-lg bg-error/10 border border-error/25 px-4 py-3 text-[13px] text-error">
            <strong class="font-bold">Vérifiez le formulaire :</strong>
            <ul class="list-disc pl-5 mt-1">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-[1.5fr_1fr] gap-6 items-start">
        <div class="space-y-5">
            <div class="f-card">
                <h3 class="f-card-title">Informations de l'immeuble</h3>
                <div class="mb-[18px]">
                    <label for="nom" class="f-label">Nom de l'immeuble</label>
                    <input id="nom" type="text" name="nom" value="{{ old('nom', $immeuble->nom) }}" class="f-input @error('nom') f-input-error @enderror">
                    @error('nom')<p class="field-error">{{ $message }}</p>@enderror
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-[18px]">
                    <div>
                        <label for="adresse" class="f-label">Adresse</label>
                        <input id="adresse" type="text" name="adresse" value="{{ old('adresse', $immeuble->adresse) }}" class="f-input @error('adresse') f-input-error @enderror">
                        @error('adresse')<p class="field-error">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="ville" class="f-label">Ville</label>
                        <input id="ville" type="text" name="ville" value="{{ old('ville', $immeuble->ville) }}" class="f-input @error('ville') f-input-error @enderror">
                        @error('ville')<p class="field-error">{{ $message }}</p>@enderror
                    </div>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-[18px]">
                    <div>
                        <label for="nombre_niveaux" class="f-label">Nombre de niveaux <span class="text-muted font-normal">(optionnel)</span></label>
                        <input id="nombre_niveaux" type="number" name="nombre_niveaux" value="{{ old('nombre_niveaux', $immeuble->nombre_niveaux) }}" min="1" max="99" class="f-input">
                    </div>
                </div>
                <x-search-or-create name="proprietaire_id" label="Propriétaire" type="propriétaire"
                    :search-url="route('admin.users.proprietaires.search')"
                    :create-url="route('admin.users.proprietaires.quick')"
                    :selected-id="old('proprietaire_id', $immeuble->proprietaire_id)"
                    :selected-name="$immeuble->proprietaire->name ?? ''"
                    selected-sub="Propriétaire actuel" />
                @error('proprietaire_id')<p class="field-error">{{ $message }}</p>@enderror

                <div class="mt-[18px]">
                    <label for="description" class="f-label">Description <span class="text-muted font-normal">(optionnel)</span></label>
                    <textarea id="description" name="description" rows="2" class="f-textarea">{{ old('description', $immeuble->description) }}</textarea>
                </div>
            </div>

            {{-- Mise à jour en masse (optionnel) --}}
            <div class="f-card">
                <h3 class="f-card-title">Appliquer à tous les appartements <span class="text-muted font-body font-normal text-[14px]">(optionnel)</span></h3>
                <p class="f-card-sub">Laissez vide pour ne pas toucher aux unités. Sinon, ces valeurs remplacent celles de <strong>toutes</strong> les unités.</p>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div><label for="loyer_par_unite" class="f-label">Loyer / unité</label><input id="loyer_par_unite" type="number" name="loyer_par_unite" value="{{ old('loyer_par_unite') }}" min="0" step="1" class="f-input"></div>
                    <div><label for="charges_par_unite" class="f-label">Charges / unité</label><input id="charges_par_unite" type="number" name="charges_par_unite" value="{{ old('charges_par_unite') }}" min="0" step="1" class="f-input"></div>
                    <div><label for="taux_commission" class="f-label">Commission (%)</label><input id="taux_commission" type="number" name="taux_commission" value="{{ old('taux_commission') }}" min="0" max="30" step="0.5" class="f-input"></div>
                </div>
            </div>
        </div>

        <div class="lg:sticky lg:top-6">
            <div class="f-card">
                <button type="submit" class="btn-primary mb-2.5">Enregistrer</button>
                <a href="{{ route('admin.immeubles.show', $immeuble) }}" class="block w-full text-center py-[13px] rounded border-[1.5px] border-line bg-white text-ink text-sm font-semibold hover:border-teal transition-colors">Annuler</a>
            </div>
        </div>
    </div>
</form>
@endsection
