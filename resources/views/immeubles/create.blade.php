@extends('layouts.app')

@section('title', 'Nouvel immeuble')
@section('page-title', 'Nouvel immeuble')
@section('page-subtitle')
    <a href="{{ route('admin.biens.index') }}" class="text-teal font-semibold hover:underline">Biens</a>
    <span class="text-muted"> / Nouvel immeuble</span>
@endsection

@section('content')
<form method="POST" action="{{ route('admin.immeubles.store') }}" x-data="immeubleForm" class="max-w-[1000px]">
    @csrf
    <input type="hidden" name="avec_unites"       x-bind:value="avecUnitesValue">
    <input type="hidden" name="mode_numerotation" x-bind:value="modeValue">
    <input type="hidden" name="avec_rdc"          x-bind:value="avecRdcValue">
    <input type="hidden" name="rdc_different"     x-bind:value="rdcDifferentValue">

    @if($errors->any())
        <div class="mb-5 rounded-lg bg-error/10 border border-error/25 px-4 py-3 text-[13px] text-error">
            <strong class="font-bold">Vérifiez le formulaire :</strong>
            <ul class="list-disc pl-5 mt-1">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-[1.5fr_1fr] gap-6 items-start">
        <div class="space-y-5">

            {{-- Informations --}}
            <div class="f-card">
                <h3 class="f-card-title">Informations de l'immeuble</h3>
                <p class="f-card-sub">Les appartements hériteront de cette adresse et de ce propriétaire.</p>
                <div class="mb-[18px]">
                    <label for="nom" class="f-label">Nom de l'immeuble</label>
                    <input id="nom" type="text" name="nom" value="{{ old('nom') }}" placeholder="Ex. Résidence Ngor 5" class="f-input @error('nom') f-input-error @enderror">
                    @error('nom')<p class="field-error">{{ $message }}</p>@enderror
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-[18px]">
                    <div>
                        <label for="adresse" class="f-label">Adresse</label>
                        <input id="adresse" type="text" name="adresse" value="{{ old('adresse') }}" class="f-input @error('adresse') f-input-error @enderror">
                        @error('adresse')<p class="field-error">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="ville" class="f-label">Ville</label>
                        <input id="ville" type="text" name="ville" value="{{ old('ville', 'Dakar') }}" class="f-input @error('ville') f-input-error @enderror">
                        @error('ville')<p class="field-error">{{ $message }}</p>@enderror
                    </div>
                </div>
                <div class="mb-[18px]">
                    <x-search-or-create name="proprietaire_id" label="Propriétaire" type="propriétaire"
                        :search-url="route('admin.users.proprietaires.search')"
                        :create-url="route('admin.users.proprietaires.quick')"
                        :selected-id="old('proprietaire_id')" />
                    @error('proprietaire_id')<p class="field-error">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="description" class="f-label">Description <span class="text-muted font-normal">(optionnel)</span></label>
                    <textarea id="description" name="description" rows="2" class="f-textarea">{{ old('description') }}</textarea>
                </div>
            </div>

            {{-- Appartements --}}
            <div class="f-card">
                <div class="flex items-center justify-between gap-5">
                    <div>
                        <h3 class="f-card-title mb-0.5">Créer les appartements maintenant</h3>
                        <p class="text-[12.5px] text-muted leading-snug">Sinon, l'immeuble est créé vide et vous ajouterez les unités depuis sa fiche.</p>
                    </div>
                    <button type="button" x-on:click="toggleUnites" x-bind:class="unitesSwitchClass" class="relative w-[42px] h-6 rounded-full shrink-0 transition-colors" aria-label="Créer les appartements">
                        <span x-bind:class="unitesKnobClass" class="absolute top-[2.5px] w-[19px] h-[19px] rounded-full bg-white shadow transition-all"></span>
                    </button>
                </div>

                <div x-show="showUnites" x-cloak class="mt-5 pt-5 border-t border-paper-dim space-y-[18px]">
                    {{-- Mode --}}
                    <div>
                        <label class="f-label">Comment numéroter les appartements ?</label>
                        <div class="flex bg-paper-dim rounded-full p-1 w-fit">
                            <button type="button" x-on:click="setSimple" x-bind:class="simpleTabClass" class="text-[13.5px] font-bold px-5 py-2 rounded-full transition-colors">Liste simple</button>
                            <button type="button" x-on:click="setEtage" x-bind:class="etageTabClass" class="text-[13.5px] font-bold px-5 py-2 rounded-full transition-colors">Par étage</button>
                        </div>
                    </div>

                    {{-- Simple --}}
                    <div x-show="showSimple">
                        <label for="nombre_unites" class="f-label">Nombre d'appartements</label>
                        <input id="nombre_unites" type="number" name="nombre_unites" value="{{ old('nombre_unites', 4) }}" min="1" max="999" class="f-input max-w-[200px]">
                    </div>

                    {{-- Par étage --}}
                    <div x-show="showEtage" x-cloak class="space-y-[18px]">
                        <div class="grid grid-cols-2 gap-4 max-w-[420px]">
                            <div><label for="nombre_etages" class="f-label">Nombre d'étages</label><input id="nombre_etages" type="number" name="nombre_etages" value="{{ old('nombre_etages', 2) }}" min="0" max="99" class="f-input"></div>
                            <div><label for="unites_par_niveau" class="f-label">Apparts / étage</label><input id="unites_par_niveau" type="number" name="unites_par_niveau" value="{{ old('unites_par_niveau', 2) }}" min="1" max="26" class="f-input"></div>
                        </div>
                        <div class="flex items-center justify-between gap-5 max-w-[420px]">
                            <div class="text-[14px] font-semibold">Inclure un rez-de-chaussée</div>
                            <button type="button" x-on:click="toggleRdc" x-bind:class="rdcSwitchClass" class="relative w-[42px] h-6 rounded-full shrink-0 transition-colors" aria-label="Inclure RDC">
                                <span x-bind:class="rdcKnobClass" class="absolute top-[2.5px] w-[19px] h-[19px] rounded-full bg-white shadow transition-all"></span>
                            </button>
                        </div>
                        <div x-show="showRdc" x-cloak class="flex items-center justify-between gap-5 max-w-[420px]">
                            <div class="text-[14px] font-semibold">RDC de type/loyer différent</div>
                            <button type="button" x-on:click="toggleRdcDiff" x-bind:class="rdcDiffSwitchClass" class="relative w-[42px] h-6 rounded-full shrink-0 transition-colors" aria-label="RDC différent">
                                <span x-bind:class="rdcDiffKnobClass" class="absolute top-[2.5px] w-[19px] h-[19px] rounded-full bg-white shadow transition-all"></span>
                            </button>
                        </div>
                        <div x-show="showRdcDiff" x-cloak class="grid grid-cols-2 gap-4 max-w-[420px]">
                            <div>
                                <label for="rdc_type" class="f-label">Type du RDC</label>
                                <select id="rdc_type" name="rdc_type" class="f-select">
                                    @foreach(\App\Models\Bien::TYPES as $val => $lbl)<option value="{{ $val }}" @selected(old('rdc_type', 'commerce') === $val)>{{ $lbl }}</option>@endforeach
                                </select>
                            </div>
                            <div><label for="rdc_loyer" class="f-label">Loyer RDC (FCFA)</label><input id="rdc_loyer" type="number" name="rdc_loyer" value="{{ old('rdc_loyer') }}" min="0" step="1" class="f-input"></div>
                        </div>
                    </div>

                    {{-- Commun à toutes les unités --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2">
                        <div>
                            <label for="type_unite" class="f-label">Type des appartements</label>
                            <select id="type_unite" name="type_unite" class="f-select">
                                @foreach(\App\Models\Bien::TYPES as $val => $lbl)<option value="{{ $val }}" @selected(old('type_unite', 'appartement') === $val)>{{ $lbl }}</option>@endforeach
                            </select>
                        </div>
                        <div><label for="loyer_par_unite" class="f-label">Loyer par appartement (FCFA)</label><input id="loyer_par_unite" type="number" name="loyer_par_unite" value="{{ old('loyer_par_unite') }}" min="0" step="1" placeholder="180000" class="f-input"></div>
                        <div><label for="charges_par_unite" class="f-label">Charges <span class="text-muted font-normal">(optionnel)</span></label><input id="charges_par_unite" type="number" name="charges_par_unite" value="{{ old('charges_par_unite') }}" min="0" step="1" class="f-input"></div>
                        <div><label for="taux_commission" class="f-label">Commission (%)</label><input id="taux_commission" type="number" name="taux_commission" value="{{ old('taux_commission', 10) }}" min="0" max="30" step="0.5" class="f-input"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="lg:sticky lg:top-6">
            <div class="f-card">
                <button type="submit" class="btn-primary mb-2.5">Créer l'immeuble</button>
                <a href="{{ route('admin.biens.index') }}" class="block w-full text-center py-[13px] rounded border-[1.5px] border-line bg-white text-ink text-sm font-semibold hover:border-teal transition-colors">Annuler</a>
                <p class="text-[12.5px] text-muted leading-relaxed mt-4 pt-4 border-t border-paper-dim">
                    Chaque appartement devient un bien à part entière, réutilisable dans un contrat.
                </p>
            </div>
        </div>
    </div>
</form>
@endsection
