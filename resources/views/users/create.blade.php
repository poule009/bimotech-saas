<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ $role === 'proprietaire' ? route('admin.users.proprietaires') : route('admin.users.locataires') }}"
               class="text-gray-400 hover:text-gray-600 transition">←</a>
            <h2 class="font-semibold text-xl text-gray-800">
                Nouveau {{ $role === 'proprietaire' ? 'propriétaire' : 'locataire' }}
            </h2>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <form method="POST" action="{{ route('admin.users.store') }}" class="space-y-6">
                @csrf
                <input type="hidden" name="role" value="{{ $role }}">

                @if($errors->any())
                <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                    <ul class="text-sm text-red-600 space-y-1">
                        @foreach($errors->all() as $error)
                        <li>❌ {{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif

                {{-- Infos compte --}}
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 space-y-4">
                    <h3 class="font-semibold text-gray-800 border-b border-gray-100 pb-3">
                        Informations du compte
                    </h3>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Nom complet <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="name" value="{{ old('name') }}"
                                   placeholder="Moussa Diallo"
                                   class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 @error('name') border-red-400 @enderror">
                            @error('name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Email <span class="text-red-500">*</span>
                            </label>
                            <input type="email" name="email" value="{{ old('email') }}"
                                   placeholder="moussa@example.com"
                                   class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 @error('email') border-red-400 @enderror">
                            @error('email')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Téléphone</label>
                            <input type="text" name="telephone" value="{{ old('telephone') }}"
                                   placeholder="+221 77 000 00 00"
                                   class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Adresse</label>
                            <input type="text" name="adresse" value="{{ old('adresse') }}"
                                   placeholder="Almadies, Dakar"
                                   class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Mot de passe <span class="text-red-500">*</span>
                            </label>
                            <input type="password" name="password"
                                   class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 @error('password') border-red-400 @enderror">
                            @error('password')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Confirmer le mot de passe <span class="text-red-500">*</span>
                            </label>
                            <input type="password" name="password_confirmation"
                                   class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500">
                        </div>
                    </div>
                </div>

                {{-- Infos identité --}}
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 space-y-4">
                    <h3 class="font-semibold text-gray-800 border-b border-gray-100 pb-3">
                        Identité
                    </h3>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">CNI</label>
                            <input type="text" name="cni" value="{{ old('cni') }}"
                                   placeholder="SN-1234567"
                                   class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Date de naissance</label>
                            <input type="date" name="date_naissance" value="{{ old('date_naissance') }}"
                                   class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Genre</label>
                            <select name="genre" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500">
                                <option value="">-- Choisir --</option>
                                <option value="homme" {{ old('genre') === 'homme' ? 'selected' : '' }}>Homme</option>
                                <option value="femme" {{ old('genre') === 'femme' ? 'selected' : '' }}>Femme</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Ville</label>
                            <select name="ville" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500">
                                @foreach(['Dakar', 'Thiès', 'Saint-Louis', 'Ziguinchor', 'Kaolack', 'Mbour', 'Rufisque', 'Touba'] as $ville)
                                <option value="{{ $ville }}" {{ old('ville', 'Dakar') === $ville ? 'selected' : '' }}>
                                    {{ $ville }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Quartier</label>
                            <input type="text" name="quartier" value="{{ old('quartier') }}"
                                   placeholder="Almadies"
                                   class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500">
                        </div>
                    </div>
                </div>

                {{-- Champs spécifiques propriétaire --}}
                @if($role === 'proprietaire')
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 space-y-4">
                    <h3 class="font-semibold text-gray-800 border-b border-gray-100 pb-3">
                        Informations de paiement
                    </h3>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Mode paiement préféré</label>
                            <select name="mode_paiement_prefere" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500">
                                <option value="virement"     {{ old('mode_paiement_prefere') === 'virement'     ? 'selected' : '' }}>Virement bancaire</option>
                                <option value="mobile_money" {{ old('mode_paiement_prefere') === 'mobile_money' ? 'selected' : '' }}>Mobile Money</option>
                                <option value="especes"      {{ old('mode_paiement_prefere') === 'especes'      ? 'selected' : '' }}>Espèces</option>
                                <option value="cheque"       {{ old('mode_paiement_prefere') === 'cheque'       ? 'selected' : '' }}>Chèque</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Banque</label>
                            <input type="text" name="banque" value="{{ old('banque') }}"
                                   placeholder="CBAO, Ecobank..."
                                   class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Numéro Wave</label>
                            <input type="text" name="numero_wave" value="{{ old('numero_wave') }}"
                                   placeholder="+221 77 000 00 00"
                                   class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Numéro Orange Money</label>
                            <input type="text" name="numero_om" value="{{ old('numero_om') }}"
                                   placeholder="+221 77 000 00 00"
                                   class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">NINEA</label>
                            <input type="text" name="ninea" value="{{ old('ninea') }}"
                                   placeholder="00000000000"
                                   class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500">
                        </div>
                    </div>
                </div>
                @endif

                {{-- Champs spécifiques locataire --}}
                @if($role === 'locataire')
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 space-y-4">
                    <h3 class="font-semibold text-gray-800 border-b border-gray-100 pb-3">
                        Situation professionnelle
                    </h3>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Profession</label>
                            <input type="text" name="profession" value="{{ old('profession') }}"
                                   placeholder="Ingénieur, Commerçant..."
                                   class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Employeur</label>
                            <input type="text" name="employeur" value="{{ old('employeur') }}"
                                   placeholder="Sonatel, Ministère..."
                                   class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Revenu mensuel (FCFA)</label>
                            <input type="number" name="revenu_mensuel" value="{{ old('revenu_mensuel') }}"
                                   min="0" placeholder="500000"
                                   class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500">
                        </div>
                    </div>

                    <h3 class="font-semibold text-gray-800 border-b border-gray-100 pb-3 pt-2">
                        Contact d'urgence
                    </h3>
                    <div class="grid grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nom</label>
                            <input type="text" name="contact_urgence_nom" value="{{ old('contact_urgence_nom') }}"
                                   class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Téléphone</label>
                            <input type="text" name="contact_urgence_tel" value="{{ old('contact_urgence_tel') }}"
                                   class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Lien</label>
                            <input type="text" name="contact_urgence_lien" value="{{ old('contact_urgence_lien') }}"
                                   placeholder="père, mère, conjoint..."
                                   class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500">
                        </div>
                    </div>
                </div>
                @endif

                {{-- Boutons --}}
                <div class="flex justify-end gap-3">
                    <a href="{{ $role === 'proprietaire' ? route('admin.users.proprietaires') : route('admin.users.locataires') }}"
                       class="px-5 py-2 text-sm text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-lg transition">
                        Annuler
                    </a>
                    <button type="submit"
                            class="px-6 py-2 text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg transition">
                        Créer le {{ $role === 'proprietaire' ? 'propriétaire' : 'locataire' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>