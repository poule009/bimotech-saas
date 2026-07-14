@extends('layouts.app')

@php $titre = $bien->titre_fallback; @endphp

@section('title', 'Modifier ' . $titre)
@section('page-title', 'Modifier le bien')
@section('page-subtitle')
    <a href="{{ route('admin.biens.show', $bien) }}" class="text-teal font-semibold hover:underline">{{ $titre }}</a>
    <span class="text-muted"> / Modifier</span>
@endsection

@section('content')
<form method="POST" action="{{ route('admin.biens.update', $bien) }}" class="max-w-[1000px]">
    @csrf
    @method('PUT')

    @if($errors->any())
        <div class="mb-5 rounded-lg bg-error/10 border border-error/25 px-4 py-3 text-[13px] text-error">
            <strong class="font-bold">Vérifiez le formulaire :</strong>
            <ul class="list-disc pl-5 mt-1">
                @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
            </ul>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-[1.5fr_1fr] gap-6 items-start">
        <div class="space-y-5">

            <div class="f-card">
                <h3 class="f-card-title">Informations générales</h3>
                <div class="mb-[18px]">
                    <label for="titre" class="f-label">Nom du bien <span class="text-muted font-normal">(optionnel)</span></label>
                    <input id="titre" type="text" name="titre" value="{{ old('titre', $bien->titre) }}" class="f-input">
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-[18px]">
                    <div>
                        <label for="type" class="f-label">Type de bien</label>
                        <select id="type" name="type" class="f-select @error('type') f-input-error @enderror">
                            @foreach(\App\Models\Bien::TYPES as $val => $lbl)
                                <option value="{{ $val }}" @selected(old('type', $bien->type) === $val)>{{ $lbl }}</option>
                            @endforeach
                        </select>
                        @error('type')<p class="field-error">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="statut" class="f-label">Statut</label>
                        <select id="statut" name="statut" class="f-select @error('statut') f-input-error @enderror">
                            @foreach(['disponible'=>'Disponible','loue'=>'Loué','en_travaux'=>'En travaux'] as $val => $lbl)
                                <option value="{{ $val }}" @selected(old('statut', $bien->statut) === $val)>{{ $lbl }}</option>
                            @endforeach
                        </select>
                        @error('statut')<p class="field-error">{{ $message }}</p>@enderror
                    </div>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-[18px]">
                    <div>
                        <label for="adresse" class="f-label">Adresse</label>
                        <input id="adresse" type="text" name="adresse" value="{{ old('adresse', $bien->adresse) }}" class="f-input @error('adresse') f-input-error @enderror">
                        @error('adresse')<p class="field-error">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="ville" class="f-label">Ville</label>
                        <input id="ville" type="text" name="ville" value="{{ old('ville', $bien->ville) }}" class="f-input @error('ville') f-input-error @enderror">
                        @error('ville')<p class="field-error">{{ $message }}</p>@enderror
                    </div>
                </div>
                <div class="mb-[18px]">
                    <label for="quartier" class="f-label">Quartier <span class="text-muted font-normal">(optionnel)</span></label>
                    <input id="quartier" type="text" name="quartier" value="{{ old('quartier', $bien->quartier) }}" class="f-input">
                </div>

                <x-search-or-create
                    name="proprietaire_id"
                    label="Propriétaire"
                    type="propriétaire"
                    :search-url="route('admin.users.proprietaires.search')"
                    :create-url="route('admin.users.proprietaires.quick')"
                    :selected-id="old('proprietaire_id', $bien->proprietaire_id)"
                    :selected-name="$bien->proprietaire->name ?? ''"
                    selected-sub="Propriétaire actuel" />
                @error('proprietaire_id')<p class="field-error">{{ $message }}</p>@enderror
            </div>

            <div class="f-card">
                <h3 class="f-card-title">Loyer & commission</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="loyer_mensuel" class="f-label">Loyer mensuel (FCFA)</label>
                        <input id="loyer_mensuel" type="number" name="loyer_mensuel" value="{{ old('loyer_mensuel', (int) $bien->loyer_mensuel) }}" min="1000" step="1" class="f-input @error('loyer_mensuel') f-input-error @enderror">
                        @error('loyer_mensuel')<p class="field-error">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="taux_commission" class="f-label">Commission agence (%)</label>
                        <input id="taux_commission" type="number" name="taux_commission" value="{{ old('taux_commission', (float) $bien->taux_commission) }}" min="0" max="30" step="0.5" class="f-input">
                    </div>
                    <div>
                        <label for="tom_mensuelle" class="f-label">TOM mensuelle (FCFA) <span class="text-muted font-normal">(optionnel)</span></label>
                        <input id="tom_mensuelle" type="number" name="tom_mensuelle" value="{{ old('tom_mensuelle', (int) $bien->tom_mensuelle) }}" min="0" step="1" class="f-input">
                        <p class="text-[11.5px] text-muted mt-1">Taxe ordures ménagères facturée au locataire. Reprise par défaut sur le bail.</p>
                    </div>
                    <div>
                        <label class="f-label">Logement meublé ?</label>
                        <label class="flex items-center gap-2.5 px-3.5 py-3 rounded-[9px] border border-line cursor-pointer hover:border-teal transition-colors">
                            <input type="checkbox" name="meuble" value="1" @checked(old('meuble', $bien->meuble)) class="w-[16px] h-[16px] accent-teal">
                            <span class="text-[14px]">Oui, ce bien est loué meublé</span>
                        </label>
                        <p class="text-[11.5px] text-muted mt-1">Impact TVA : meublé → 18 % sur le loyer ; non meublé (habitation) → exonéré.</p>
                    </div>
                </div>
            </div>

            <div class="f-card">
                <h3 class="f-card-title">Caractéristiques <span class="text-muted font-body font-normal text-[14px]">(optionnel)</span></h3>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-[18px]">
                    <div><label for="surface_m2" class="f-label">Surface (m²)</label><input id="surface_m2" type="number" name="surface_m2" value="{{ old('surface_m2', $bien->surface_m2 ? (int) $bien->surface_m2 : '') }}" min="1" class="f-input"></div>
                    <div><label for="nombre_pieces" class="f-label">Pièces</label><input id="nombre_pieces" type="number" name="nombre_pieces" value="{{ old('nombre_pieces', $bien->nombre_pieces) }}" min="1" class="f-input"></div>
                    <div><label for="nombre_chambres" class="f-label">Chambres</label><input id="nombre_chambres" type="number" name="nombre_chambres" value="{{ old('nombre_chambres', $bien->nombre_chambres) }}" min="0" class="f-input"></div>
                    <div><label for="nombre_sdb" class="f-label">SDB</label><input id="nombre_sdb" type="number" name="nombre_sdb" value="{{ old('nombre_sdb', $bien->nombre_sdb) }}" min="0" class="f-input"></div>
                </div>
                <div class="flex flex-wrap gap-5">
                    <label class="flex items-center gap-2 text-[14px] cursor-pointer"><input type="checkbox" name="parking" value="1" @checked(old('parking', $bien->parking)) class="w-[16px] h-[16px] accent-teal"> Parking</label>
                    <label class="flex items-center gap-2 text-[14px] cursor-pointer"><input type="checkbox" name="climatise" value="1" @checked(old('climatise', $bien->climatise)) class="w-[16px] h-[16px] accent-teal"> Climatisé</label>
                    <label class="flex items-center gap-2 text-[14px] cursor-pointer"><input type="checkbox" name="visible_portail" value="1" @checked(old('visible_portail', $bien->visible_portail)) class="w-[16px] h-[16px] accent-teal"> Visible sur le portail</label>
                </div>
                <p class="text-[11.5px] text-muted mt-2">Le caractère « meublé » (impact TVA) se règle plus haut, dans « Loyer & commission ».</p>
            </div>
        </div>

        <div class="lg:sticky lg:top-6">
            <div class="f-card">
                <button type="submit" class="btn-primary mb-2.5">Enregistrer les modifications</button>
                <a href="{{ route('admin.biens.show', $bien) }}" class="block w-full text-center py-[13px] rounded border-[1.5px] border-line bg-white text-ink text-sm font-semibold hover:border-teal transition-colors">Annuler</a>
            </div>
        </div>
    </div>
</form>
@endsection
