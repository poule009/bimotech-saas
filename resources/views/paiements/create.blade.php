<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.paiements.index') }}" class="text-gray-400 hover:text-gray-600 transition">←</a>
            <h2 class="font-semibold text-xl text-gray-800">Enregistrer un paiement</h2>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-8">

                <form method="POST" action="{{ route('admin.paiements.store') }}" class="space-y-6">
                    @csrf

                    {{-- Alertes erreurs --}}
                    @if($errors->any())
                    <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                        <ul class="text-sm text-red-600 space-y-1">
                            @foreach($errors->all() as $error)
                            <li>❌ {{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    {{-- Contrat --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Contrat <span class="text-red-500">*</span>
                        </label>
                        <select name="contrat_id" id="contrat_id"
                                onchange="chargerInfosContrat(this)"
                                class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 @error('contrat_id') border-red-400 @enderror">
                            <option value="">-- Sélectionner un contrat actif --</option>
                            @foreach($contrats as $contrat)
                            <option value="{{ $contrat->id }}"
                                data-loyer="{{ $contrat->loyer_contractuel }}"
                                data-commission="{{ $contrat->bien->taux_commission }}"
                                data-bien="{{ $contrat->bien->reference }}"
                                data-locataire="{{ $contrat->locataire->name }}"
                                {{ old('contrat_id', $contratPreselectionne?->id) == $contrat->id ? 'selected' : '' }}>
                                #{{ $contrat->id }} — {{ $contrat->bien->reference }}
                                · {{ $contrat->locataire->name }}
                                · {{ number_format($contrat->loyer_contractuel, 0, ',', ' ') }} F/mois
                            </option>
                            @endforeach
                        </select>
                        @error('contrat_id')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Infos contrat sélectionné --}}
                    <div id="infos-contrat" class="hidden bg-blue-50 border border-blue-100 rounded-lg p-4 text-sm">
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <span class="text-blue-400 text-xs uppercase">Bien</span>
                                <div class="font-semibold text-blue-800" id="info-bien">—</div>
                            </div>
                            <div>
                                <span class="text-blue-400 text-xs uppercase">Locataire</span>
                                <div class="font-semibold text-blue-800" id="info-locataire">—</div>
                            </div>
                            <div>
                                <span class="text-blue-400 text-xs uppercase">Loyer mensuel</span>
                                <div class="font-semibold text-blue-800" id="info-loyer">—</div>
                            </div>
                            <div>
                                <span class="text-blue-400 text-xs uppercase">Commission</span>
                                <div class="font-semibold text-blue-800" id="info-commission">—</div>
                            </div>
                        </div>
                    </div>

                    {{-- Période --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Période (mois concerné) <span class="text-red-500">*</span>
                        </label>
                        <input type="month" name="periode"
                               value="{{ old('periode', now()->format('Y-m')) }}"
                               class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 @error('periode') border-red-400 @enderror">
                        @error('periode')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Montant --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Montant encaissé (FCFA) <span class="text-red-500">*</span>
                        </label>
                        <input type="number" name="montant_encaisse" id="montant_encaisse"
                               value="{{ old('montant_encaisse') }}"
                               min="1" placeholder="250000"
                               oninput="calculerApercu()"
                               class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 @error('montant_encaisse') border-red-400 @enderror">
                        @error('montant_encaisse')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Aperçu calcul automatique --}}
                    <div id="apercu-calcul" class="hidden bg-gray-50 border border-gray-200 rounded-lg p-4">
                        <div class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-3">
                            Aperçu du calcul
                        </div>
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Montant encaissé</span>
                                <span class="font-semibold text-gray-900" id="ap-montant">—</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Commission HT</span>
                                <span class="text-amber-600" id="ap-commission-ht">—</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-500 text-xs">TVA 18% sur commission</span>
                                <span class="text-gray-500 text-xs" id="ap-tva">—</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Commission TTC</span>
                                <span class="text-amber-700 font-medium" id="ap-commission-ttc">—</span>
                            </div>
                            <div class="flex justify-between pt-2 border-t border-gray-200">
                                <span class="font-semibold text-gray-800">Net propriétaire</span>
                                <span class="font-bold text-emerald-600" id="ap-net">—</span>
                            </div>
                        </div>
                    </div>

                    {{-- Mode de paiement --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Mode de paiement <span class="text-red-500">*</span>
                        </label>
                        <select name="mode_paiement"
                                class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 @error('mode_paiement') border-red-400 @enderror">
                            <option value="">-- Choisir --</option>
                            <option value="especes"      {{ old('mode_paiement') === 'especes'      ? 'selected' : '' }}>Espèces</option>
                            <option value="virement"     {{ old('mode_paiement') === 'virement'     ? 'selected' : '' }}>Virement bancaire</option>
                            <option value="mobile_money" {{ old('mode_paiement') === 'mobile_money' ? 'selected' : '' }}>Mobile Money (Wave / OM)</option>
                            <option value="cheque"       {{ old('mode_paiement') === 'cheque'       ? 'selected' : '' }}>Chèque</option>
                        </select>
                        @error('mode_paiement')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Date de paiement --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Date du règlement <span class="text-red-500">*</span>
                        </label>
                        <input type="date" name="date_paiement"
                               value="{{ old('date_paiement', now()->format('Y-m-d')) }}"
                               class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 @error('date_paiement') border-red-400 @enderror">
                        @error('date_paiement')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Premier paiement + Caution --}}
                    <div class="bg-indigo-50 border border-indigo-100 rounded-lg p-4 space-y-3">
                        <label class="flex items-center gap-3 cursor-pointer">
                            <input type="checkbox" name="est_premier_paiement" value="1"
                                   id="premier_paiement"
                                   onchange="toggleCaution(this.checked)"
                                   {{ old('est_premier_paiement') ? 'checked' : '' }}
                                   class="w-4 h-4 text-indigo-600 rounded">
                            <span class="text-sm font-medium text-indigo-800">
                                Premier paiement (inclure la caution)
                            </span>
                        </label>

                        <div id="bloc-caution" class="{{ old('est_premier_paiement') ? '' : 'hidden' }}">
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Caution perçue (FCFA)
                            </label>
                            <input type="number" name="caution_percue"
                                   value="{{ old('caution_percue') }}" min="0"
                                   placeholder="250000"
                                   class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500">
                        </div>
                    </div>

                    {{-- Notes --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                        <textarea name="notes" rows="2"
                                  placeholder="Observations particulières..."
                                  class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500">{{ old('notes') }}</textarea>
                    </div>

                    {{-- Boutons --}}
                    <div class="flex justify-end gap-3 pt-4 border-t border-gray-100">
                        <a href="{{ route('admin.paiements.index') }}"
                           class="px-5 py-2 text-sm text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-lg transition">
                            Annuler
                        </a>
                        <button type="submit"
                                class="px-6 py-2 text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg transition">
                            Valider le paiement
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
    // Taux de commission du contrat sélectionné
    let tauxCommission = 0;

    function chargerInfosContrat(select) {
        const option = select.options[select.selectedIndex];
        if (!option.value) {
            document.getElementById('infos-contrat').classList.add('hidden');
            document.getElementById('apercu-calcul').classList.add('hidden');
            tauxCommission = 0;
            return;
        }

        tauxCommission = parseFloat(option.dataset.commission) || 0;

        document.getElementById('info-bien').textContent       = option.dataset.bien;
        document.getElementById('info-locataire').textContent  = option.dataset.locataire;
        document.getElementById('info-loyer').textContent      = parseInt(option.dataset.loyer).toLocaleString('fr-FR') + ' FCFA';
        document.getElementById('info-commission').textContent = option.dataset.commission + '%';
        document.getElementById('infos-contrat').classList.remove('hidden');

        // Pré-remplir le montant avec le loyer du contrat
        const montantInput = document.getElementById('montant_encaisse');
        if (!montantInput.value) {
            montantInput.value = option.dataset.loyer;
        }
        calculerApercu();
    }

    function calculerApercu() {
        const montant = parseFloat(document.getElementById('montant_encaisse').value) || 0;
        if (!montant || !tauxCommission) return;

        const commissionHT  = Math.round(montant * tauxCommission / 100);
        const tva           = Math.round(commissionHT * 0.18);
        const commissionTTC = commissionHT + tva;
        const net           = montant - commissionTTC;

        document.getElementById('ap-montant').textContent        = montant.toLocaleString('fr-FR') + ' F';
        document.getElementById('ap-commission-ht').textContent  = '- ' + commissionHT.toLocaleString('fr-FR') + ' F';
        document.getElementById('ap-tva').textContent            = '- ' + tva.toLocaleString('fr-FR') + ' F';
        document.getElementById('ap-commission-ttc').textContent = '- ' + commissionTTC.toLocaleString('fr-FR') + ' F';
        document.getElementById('ap-net').textContent            = net.toLocaleString('fr-FR') + ' F';

        document.getElementById('apercu-calcul').classList.remove('hidden');
    }

    function toggleCaution(checked) {
        const bloc = document.getElementById('bloc-caution');
        checked ? bloc.classList.remove('hidden') : bloc.classList.add('hidden');
    }

    // Initialiser si contrat pré-sélectionné
    window.addEventListener('DOMContentLoaded', () => {
        const select = document.getElementById('contrat_id');
        if (select.value) chargerInfosContrat(select);
    });
    </script>
</x-app-layout>