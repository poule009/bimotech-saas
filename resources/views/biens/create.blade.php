<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('biens.index') }}" class="text-gray-400 hover:text-gray-600 transition">←</a>
            <h2 class="font-semibold text-xl text-gray-800">Nouveau bien</h2>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-8">

                <form method="POST" action="{{ route('biens.store') }}" class="space-y-6">
                    @csrf

                    {{-- Propriétaire (admin seulement) --}}
                    @if(auth()->user()->isAdmin())
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Propriétaire <span class="text-red-500">*</span>
                        </label>
                        <select name="proprietaire_id"
                                class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-transparent @error('proprietaire_id') border-red-400 @enderror">
                            <option value="">-- Sélectionner un propriétaire --</option>
                            @foreach($proprietaires as $proprio)
                            <option value="{{ $proprio->id }}" {{ old('proprietaire_id') == $proprio->id ? 'selected' : '' }}>
                                {{ $proprio->name }} — {{ $proprio->email }}
                            </option>
                            @endforeach
                        </select>
                        @error('proprietaire_id')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    @else
                    <input type="hidden" name="proprietaire_id" value="{{ auth()->id() }}">
                    @endif

                    {{-- Type + Statut --}}
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Type de bien <span class="text-red-500">*</span>
                            </label>
                            <select name="type"
                                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 @error('type') border-red-400 @enderror">
                                <option value="">-- Choisir --</option>
                                @foreach(['Appartement', 'Villa', 'Studio', 'Bureau', 'Commerce', 'Terrain', 'Maison'] as $type)
                                <option value="{{ $type }}" {{ old('type') === $type ? 'selected' : '' }}>
                                    {{ $type }}
                                </option>
                                @endforeach
                            </select>
                            @error('type')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Statut <span class="text-red-500">*</span>
                            </label>
                            <select name="statut"
                                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 @error('statut') border-red-400 @enderror">
                                <option value="disponible" {{ old('statut') === 'disponible' ? 'selected' : '' }}>Disponible</option>
                                <option value="loue"       {{ old('statut') === 'loue'       ? 'selected' : '' }}>Loué</option>
                                <option value="en_travaux" {{ old('statut') === 'en_travaux' ? 'selected' : '' }}>En travaux</option>
                            </select>
                            @error('statut')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    {{-- Adresse --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Adresse <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="adresse" value="{{ old('adresse') }}"
                               placeholder="Ex: 25 Rue de Thiong"
                               class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 @error('adresse') border-red-400 @enderror">
                        @error('adresse')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Ville --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Ville <span class="text-red-500">*</span>
                        </label>
                        <select name="ville"
                                class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 @error('ville') border-red-400 @enderror">
                            @foreach(['Dakar', 'Thiès', 'Saint-Louis', 'Ziguinchor', 'Kaolack', 'Mbour', 'Rufisque', 'Touba'] as $ville)
                            <option value="{{ $ville }}" {{ old('ville', 'Dakar') === $ville ? 'selected' : '' }}>
                                {{ $ville }}
                            </option>
                            @endforeach
                        </select>
                        @error('ville')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Surface + Pièces --}}
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Surface (m²)</label>
                            <input type="number" name="surface_m2" value="{{ old('surface_m2') }}"
                                   min="1" placeholder="85"
                                   class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nombre de pièces</label>
                            <input type="number" name="nombre_pieces" value="{{ old('nombre_pieces') }}"
                                   min="1" placeholder="3"
                                   class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500">
                        </div>
                    </div>

                    {{-- Loyer + Commission --}}
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Loyer mensuel (FCFA) <span class="text-red-500">*</span>
                            </label>
                            <input type="number" name="loyer_mensuel" value="{{ old('loyer_mensuel') }}"
                                   min="1" placeholder="250000"
                                   class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 @error('loyer_mensuel') border-red-400 @enderror">
                            @error('loyer_mensuel')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Taux commission (%) <span class="text-red-500">*</span>
                            </label>
                            <input type="number" name="taux_commission" value="{{ old('taux_commission', 10) }}"
                                   min="1" max="20" step="0.5" placeholder="10"
                                   class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 @error('taux_commission') border-red-400 @enderror">
                            <p class="text-xs text-gray-400 mt-1">Entre 1% et 20%</p>
                            @error('taux_commission')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    {{-- Description --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                        <textarea name="description" rows="3" placeholder="Appartement F3 climatisé, 2ème étage..."
                                  class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500">{{ old('description') }}</textarea>
                    </div>

                    {{-- Boutons --}}
                    <div class="flex justify-end gap-3 pt-4 border-t border-gray-100">
                        <a href="{{ route('biens.index') }}"
                           class="px-5 py-2 text-sm text-gray-600 hover:text-gray-800 bg-gray-100 hover:bg-gray-200 rounded-lg transition">
                            Annuler
                        </a>
                        <button type="submit"
                                class="px-6 py-2 text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg transition">
                            Enregistrer le bien
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>