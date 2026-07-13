@extends('layouts.app')

@php
    $fmt = fn ($n) => number_format((float) $n, 0, ',', ' ');
    $bien = $contrat->bien;
@endphp

@section('title', 'Modifier le contrat')
@section('page-title', 'Modifier le contrat')
@section('page-subtitle')
    <a href="{{ route('admin.contrats.show', $contrat) }}" class="text-teal font-semibold hover:underline">{{ $bien->reference ?? '' }}</a>
    <span class="text-muted"> / Modifier</span>
@endsection

@section('content')
<form method="POST" action="{{ route('admin.contrats.update', $contrat) }}"
      x-data="contratForm"
      data-apercu-url="{{ route('admin.contrats.apercu-fiscal') }}"
      x-on:soc:chosen="onBienChosen"
      class="max-w-[1000px]">
    @csrf
    @method('PUT')
    <input type="hidden" name="type_bail" value="{{ old('type_bail', $contrat->type_bail) }}">
    {{-- bien_id non modifiable : sert uniquement à l'aperçu fiscal AJAX --}}
    <input type="hidden" name="bien_id" value="{{ $contrat->bien_id }}">

    @if($errors->any())
        <div class="mb-5 rounded-lg bg-error/10 border border-error/25 px-4 py-3 text-[13px] text-error">
            <strong class="font-bold">Vérifiez le formulaire :</strong>
            <ul class="list-disc pl-5 mt-1">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-[1.5fr_1fr] gap-6 items-start">
        <div class="space-y-5">
            <div class="f-card">
                <h3 class="f-card-title">Bien et locataire</h3>

                {{-- Bien : non modifiable après création --}}
                <div class="mb-[18px]">
                    <label class="f-label">Bien</label>
                    <div class="flex items-center gap-3 bg-paper border border-line rounded-[10px] px-4 py-3">
                        <span class="w-9 h-9 rounded-[9px] bg-teal text-paper flex items-center justify-center text-[12.5px] font-bold shrink-0">{{ mb_strtoupper(mb_substr($bien->reference ?? 'B', 0, 2)) }}</span>
                        <div class="min-w-0"><div class="font-bold text-[14px] truncate">{{ $bien->reference ?? '—' }}</div><div class="text-[12px] text-muted truncate">Le bien d'un contrat ne se change pas — résiliez et recréez si besoin.</div></div>
                    </div>
                </div>

                <div>
                    <x-search-or-create
                        name="locataire_id" label="Locataire" type="locataire"
                        :search-url="route('admin.users.locataires.search')"
                        :create-url="route('admin.users.locataires.quick')"
                        :selected-id="old('locataire_id', $contrat->locataire_id)"
                        :selected-name="$contrat->locataire->name ?? ''"
                        :selected-sub="$contrat->locataire->email ?? ''" />
                    @error('locataire_id')<p class="field-error">{{ $message }}</p>@enderror
                </div>
            </div>

            <div class="f-card">
                <h3 class="f-card-title">Conditions</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-[18px]">
                    <div>
                        <label class="f-label">Date de début</label>
                        <div class="px-3.5 py-3 rounded-[9px] bg-paper border border-line text-[14.5px] text-muted">{{ optional($contrat->date_debut)->format('d/m/Y') }}</div>
                    </div>
                    <div>
                        <label for="date_fin" class="f-label">Date de fin <span class="text-muted font-normal">(vide = indéterminée)</span></label>
                        <input id="date_fin" type="date" name="date_fin" value="{{ old('date_fin', optional($contrat->date_fin)->format('Y-m-d')) }}" class="f-input @error('date_fin') f-input-error @enderror">
                        @error('date_fin')<p class="field-error">{{ $message }}</p>@enderror
                    </div>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="loyer_nu" class="f-label">Loyer mensuel (FCFA)</label>
                        <input id="loyer_nu" type="number" name="loyer_nu" value="{{ old('loyer_nu', (int) $contrat->loyer_nu) }}" min="1" step="1" x-model="loyer" x-on:input="onInput" class="f-input @error('loyer_nu') f-input-error @enderror">
                        @error('loyer_nu')<p class="field-error">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="caution" class="f-label">Dépôt de garantie (FCFA)</label>
                        <input id="caution" type="number" name="caution" value="{{ old('caution', (int) $contrat->caution) }}" min="0" step="1" class="f-input">
                    </div>
                </div>
            </div>

            <div class="f-card" x-data="collapsible">
                <div class="flex items-center justify-between cursor-pointer" x-on:click="toggle">
                    <div><h3 class="f-card-title mb-0.5">Conditions supplémentaires</h3><p class="text-[12.5px] text-muted">Charges, clauses — facultatif</p></div>
                    <svg x-bind:class="chevClass" class="w-4 h-4 text-muted transition-transform" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 9l6 6 6-6"/></svg>
                </div>
                <div x-show="open" x-cloak class="mt-5 pt-5 border-t border-paper-dim space-y-[18px]">
                    <div>
                        <label for="reference_bail" class="f-label">Référence du bail</label>
                        <input id="reference_bail" type="text" name="reference_bail" value="{{ old('reference_bail', $contrat->reference_bail) }}" maxlength="60" class="f-input @error('reference_bail') f-input-error @enderror">
                        @error('reference_bail')<p class="field-error">{{ $message }}</p>@enderror
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="charges_mensuelles" class="f-label">Charges mensuelles (FCFA)</label>
                            <input id="charges_mensuelles" type="number" name="charges_mensuelles" value="{{ old('charges_mensuelles', (int) $contrat->charges_mensuelles) }}" min="0" step="1" x-model="charges" x-on:input="onInput" class="f-input">
                        </div>
                        <div>
                            <label for="tom_amount" class="f-label">TOM mensuelle (FCFA)</label>
                            <input id="tom_amount" type="number" name="tom_amount" value="{{ old('tom_amount', (int) $contrat->tom_amount) }}" min="0" step="1" x-model="tom" x-on:input="onInput" class="f-input">
                        </div>
                    </div>

                    <div x-show="showChargesMode" x-cloak>
                        <label for="mode_facturation_charges" class="f-label">Mode de facturation des charges</label>
                        <select id="mode_facturation_charges" name="mode_facturation_charges" x-model="mode" x-on:change="onInput" class="f-select max-w-[420px]">
                            @foreach(\App\Models\Contrat::MODES_FACTURATION_CHARGES as $val => $lbl)
                                <option value="{{ $val }}" @selected(old('mode_facturation_charges', $contrat->mode_facturation_charges ?? 'debours') === $val)>{{ $lbl }}</option>
                            @endforeach
                        </select>
                        <p class="text-[11.5px] text-muted mt-1">Débours = refacturé à l'identique (0 % TVA). Forfait = montant fixe (TVA 18 %).</p>
                    </div>
                    <div>
                        <label for="clauses_particulieres" class="f-label">Clauses particulières</label>
                        <textarea id="clauses_particulieres" name="clauses_particulieres" rows="3" class="f-textarea">{{ old('clauses_particulieres', $contrat->clauses_particulieres) }}</textarea>
                    </div>

                    {{-- Enregistrement du bail (DGID) --}}
                    <div class="pt-4 border-t border-paper-dim">
                        <h4 class="text-[13.5px] font-bold text-ink mb-1">Enregistrement du bail (DGID)</h4>
                        <p class="text-[11.5px] text-muted mb-3">Ajuste le calcul des droits d'enregistrement (2 % du loyer annuel + charges).</p>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label for="droit_enreg_nombre_feuilles" class="f-label">Nombre de feuilles de l'acte</label>
                                <input id="droit_enreg_nombre_feuilles" type="number" name="droit_enreg_nombre_feuilles" value="{{ old('droit_enreg_nombre_feuilles', (int) ($contrat->droit_enreg_nombre_feuilles ?? 2)) }}" min="1" max="50" class="f-input">
                                <p class="text-[11.5px] text-muted mt-1">Timbre = 2 000 F × nombre de feuilles.</p>
                            </div>
                            <div class="flex flex-col justify-center gap-2.5 pt-2">
                                <input type="hidden" name="droit_enreg_renouvelable" value="0">
                                <label class="flex items-center gap-2 text-[14px] cursor-pointer"><input type="checkbox" name="droit_enreg_renouvelable" value="1" @checked(old('droit_enreg_renouvelable', $contrat->droit_enreg_renouvelable ?? true)) class="w-[16px] h-[16px] accent-teal"> Bail renouvelable</label>
                                <input type="hidden" name="enregistrement_exonere" value="0">
                                <label class="flex items-center gap-2 text-[14px] cursor-pointer"><input type="checkbox" name="enregistrement_exonere" value="1" @checked(old('enregistrement_exonere', $contrat->enregistrement_exonere)) class="w-[16px] h-[16px] accent-teal"> Exonéré d'enregistrement</label>
                            </div>
                        </div>
                        <p class="text-[11.5px] text-muted mt-2">Un bail ≤ 12 mois <strong>non</strong> renouvelable est calculé au prorata de sa durée. Sinon, base 12 mois.</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="lg:sticky lg:top-6 space-y-5">

            {{-- Aperçu fiscal (calculé par FiscalService via AJAX) --}}
            <div class="f-card">
                <div class="flex items-center justify-between mb-1">
                    <h3 class="f-card-title mb-0">Aperçu fiscal</h3>
                    <span x-show="isLoading" x-cloak class="text-[11px] text-muted">Calcul…</span>
                </div>
                <p class="f-card-sub">Estimation — calcul officiel réappliqué à chaque quittance.</p>

                <div x-show="!show" x-cloak class="text-[12.5px] text-muted py-2">
                    Ajustez le loyer pour voir le détail TVA.
                </div>

                <div x-show="show" x-cloak class="text-[13px]">
                    <div class="flex justify-between py-1.5 border-b border-paper-dim">
                        <span class="text-muted">Loyer HT</span><span class="font-semibold" x-text="loyerHtTxt"></span>
                    </div>
                    <div class="flex justify-between py-1.5 border-b border-paper-dim">
                        <span class="text-muted">TVA loyer (<span x-text="tauxTvaLabel"></span>)</span>
                        <span class="font-semibold" x-text="tvaLoyerTxt"></span>
                    </div>
                    <div class="flex justify-between py-1.5 border-b border-paper-dim">
                        <span class="text-muted">Loyer TTC</span><span class="font-semibold" x-text="loyerTtcTxt"></span>
                    </div>
                    <template x-if="hasCharges">
                        <div>
                            <div class="flex justify-between py-1.5 border-b border-paper-dim">
                                <span class="text-muted">Charges</span><span class="font-semibold" x-text="chargesTxt"></span>
                            </div>
                            <div x-show="hasTvaCharges" class="flex justify-between py-1.5 border-b border-paper-dim">
                                <span class="text-muted">TVA charges (forfait)</span><span class="font-semibold" x-text="tvaChargesTxt"></span>
                            </div>
                        </div>
                    </template>
                    <template x-if="hasTom">
                        <div class="flex justify-between py-1.5 border-b border-paper-dim">
                            <span class="text-muted">TOM refacturée au locataire</span><span class="font-semibold" x-text="tomTxt"></span>
                        </div>
                    </template>
                    <div class="flex justify-between py-2 border-b-2 border-ink mt-1">
                        <span class="font-bold">Total encaissé locataire</span>
                        <span class="font-bold text-teal" x-text="encaisseTxt"></span>
                    </div>
                    <div class="flex justify-between py-1.5 border-b border-paper-dim">
                        <span class="text-muted">Commission agence TTC<br><span class="text-[11px]">(dont TVA <span x-text="tvaCommTxt"></span>)</span></span>
                        <span class="font-semibold" x-text="commTtcTxt"></span>
                    </div>
                    <div class="flex justify-between py-2">
                        <span class="font-bold">Net à verser au propriétaire</span>
                        <span class="font-bold text-gold" x-text="netTxt"></span>
                    </div>
                    <div x-show="exonere" x-cloak class="mt-2 rounded-md bg-green/10 text-green px-3 py-2 text-[11.5px]">
                        Loyer exonéré de TVA (habitation non meublée).
                    </div>
                </div>
            </div>

            <div class="f-card">
                <button type="submit" class="btn-primary mb-2.5">Enregistrer</button>
                <a href="{{ route('admin.contrats.show', $contrat) }}" class="block w-full text-center py-[13px] rounded border-[1.5px] border-line bg-white text-ink text-sm font-semibold hover:border-teal transition-colors">Annuler</a>
            </div>
        </div>
    </div>
</form>
@endsection
