<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.contrats.index') }}" class="text-gray-400 hover:text-gray-600 transition">←</a>
            <h2 class="font-semibold text-xl text-gray-800">Nouveau contrat de bail</h2>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-8">

                <form method="POST" action="{{ route('admin.contrats.store') }}" class="space-y-6">
                    @csrf

                    {{-- Bien --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Bien à louer <span class="text-red-500">*</span>
                        </label>
                        <select name="bien_id"
                                class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 @error('bien_id') border-red-400 @enderror">
                            <option value="">-- Sélectionner un bien disponible --</option>
                            @foreach($biens as $bien)
                            <option value="{{ $bien->id }}"
                                {{ old('bien_id', $bienPreselectionne?->id) == $bien->id ? 'selected' : '' }}>
                                {{ $bien->reference }} — {{ $bien->type }}, {{ $bien->adresse }}
                                ({{ number_format($bien->loyer_mensuel, 0, ',', ' ') }} F/mois)
                                — Proprio: {{ $bien->proprietaire->name }}
                            </option>
                            @endforeach
                        </select>
                        @error('bien_id')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                        @if($biens->isEmpty())
                        <p class="text-amber-600 text-xs mt-1">
                            ⚠️ Aucun bien disponible.
                            <a href="{{ route('biens.index') }}" class="underline">Vérifier les biens →</a>
                        </p>
                        @endif
                    </div>

                    {{-- Locataire --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Locataire <span class="text-red-500">*</span>
                        </label>
                        <select name="locataire_id"
                                class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 @error('locataire_id') border-red-400 @enderror">
                            <option value="">-- Sélectionner un locataire --</option>
                            @foreach($locataires as $locataire)
                            <option value="{{ $locataire->id }}"
                                {{ old('locataire_id') == $locataire->id ? 'selected' : '' }}>
                                {{ $locataire->name }} — {{ $locataire->email }}
                            </option>
                            @endforeach
                        </select>
                        @error('locataire_id')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Dates --}}
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Date de début <span class="text-red-500">*</span>
                            </label>
                            <input type="date" name="date_debut"
                                   value="{{ old('date_debut', now()->format('Y-m-d')) }}"
                                   class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 @error('date_debut') border-red-400 @enderror">
                            @error('date_debut')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Date de fin
                                <span class="text-gray-400 font-normal">(vide = durée indéterminée)</span>
                            </label>
                            <input type="date" name="date_fin"
                                   value="{{ old('date_fin') }}"
                                   class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 @error('date_fin') border-red-400 @enderror">
                            @error('date_fin')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    {{-- Caution --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Caution (FCFA) <span class="text-red-500">*</span>
                        </label>
                        <input type="number" name="caution"
                               value="{{ old('caution') }}" min="0"
                               placeholder="Généralement 1 ou 2 mois de loyer"
                               class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 @error('caution') border-red-400 @enderror">
                        @error('caution')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Observations --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Observations</label>
                        <textarea name="observations" rows="3"
                                  placeholder="Conditions particulières, préavis, état des lieux..."
                                  class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500">{{ old('observations') }}</textarea>
                    </div>

                    {{-- Info loyer automatique --}}
                    <div class="bg-blue-50 border border-blue-100 rounded-lg p-4 text-sm text-blue-700">
                        ℹ️ Le loyer contractuel sera automatiquement repris depuis la fiche du bien sélectionné.
                    </div>

                    {{-- Boutons --}}
                    <div class="flex justify-end gap-3 pt-4 border-t border-gray-100">
                        <a href="{{ route('admin.contrats.index') }}"
                           class="px-5 py-2 text-sm text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-lg transition">
                            Annuler
                        </a>
                        <button type="submit"
                                class="px-6 py-2 text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg transition">
                            Créer le contrat
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>