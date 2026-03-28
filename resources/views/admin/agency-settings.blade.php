<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Paramètres de l'agence
        </h2>
    </x-slot>

    <div class="max-w-3xl mx-auto py-8 px-4">

        {{-- Message succès --}}
        @if (session('success'))
            <div class="mb-6 bg-green-50 border border-green-200 text-green-800 rounded-lg px-4 py-3 text-sm">
                {{ session('success') }}
            </div>
        @endif

        {{-- Erreurs --}}
        @if ($errors->any())
            <div class="mb-6 bg-red-50 border border-red-200 rounded-lg p-4">
                <ul class="list-disc list-inside text-sm text-red-700 space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- ══════════════════════════════════════════════════════════════ --}}
        {{-- Formulaire suppression logo — EN DEHORS du formulaire principal --}}
        {{-- ══════════════════════════════════════════════════════════════ --}}
        @if ($agency->logo_path)
            <form
                id="form-delete-logo"
                method="POST"
                action="{{ route('admin.agency.logo.delete') }}"
                onsubmit="return confirm('Supprimer le logo ?')"
            >
                @csrf
                @method('DELETE')
            </form>
        @endif

        {{-- ══════════════════════════════════════════════════════════════ --}}
        {{-- Formulaire principal                                          --}}
        {{-- ══════════════════════════════════════════════════════════════ --}}
        <form
            method="POST"
            action="{{ route('admin.agency.settings.update') }}"
            enctype="multipart/form-data"
        >
            @csrf
            @method('PATCH')

            {{-- ── Section Logo ── --}}
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 mb-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-5">🖼️ Logo de l'agence</h2>

                <div class="flex items-center gap-6">

                    {{-- Aperçu logo actuel --}}
                    <div class="flex-shrink-0">
                        @if ($agency->logo_path)
                            <img
                                src="{{ Storage::url($agency->logo_path) }}"
                                alt="Logo {{ $agency->name }}"
                                class="h-20 w-20 object-contain rounded-lg border border-gray-200 p-1"
                            >
                        @else
                            <div class="h-20 w-20 rounded-lg border-2 border-dashed border-gray-300 flex items-center justify-center">
                                <span class="text-3xl">🏢</span>
                            </div>
                        @endif
                    </div>

                    {{-- Upload + suppression --}}
                    <div class="flex-1">
                        <label for="logo" class="block text-sm font-medium text-gray-700 mb-1">
                            Changer le logo
                        </label>
                        <input
                            type="file"
                            id="logo"
                            name="logo"
                            accept="image/png,image/jpeg,image/jpg,image/svg+xml,image/webp"
                            class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100"
                        >
                        <p class="mt-1 text-xs text-gray-400">PNG, JPG, SVG ou WEBP — max 2 Mo</p>

                        {{-- Bouton suppression lié au formulaire externe via form= --}}
                        @if ($agency->logo_path)
                            <button
                                type="submit"
                                form="form-delete-logo"
                                class="mt-2 text-xs text-red-500 hover:text-red-700"
                            >
                                Supprimer le logo actuel
                            </button>
                        @endif
                    </div>

                </div>
            </div>

            {{-- ── Section Couleur ── --}}
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 mb-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-5">🎨 Couleur principale</h2>

                <div class="flex items-center gap-4">
                    <input
                        type="color"
                        id="couleur_primaire"
                        name="couleur_primaire"
                        value="{{ $agency->couleur_primaire ?? '#1a3c5e' }}"
                        class="h-12 w-20 rounded-lg border border-gray-300 cursor-pointer p-1"
                    >
                    <div>
                        <label for="couleur_primaire" class="block text-sm font-medium text-gray-700">
                            Couleur de l'interface
                        </label>
                        <p class="text-xs text-gray-400 mt-0.5">
                            Utilisée pour la barre de navigation et les boutons principaux.
                        </p>
                    </div>
                </div>
            </div>

            {{-- ── Section Informations ── --}}
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 mb-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-5">📋 Informations de l'agence</h2>

                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">

                    {{-- Nom --}}
                    <div class="sm:col-span-2">
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-1">
                            Nom de l'agence <span class="text-red-500">*</span>
                        </label>
                        <input
                            type="text"
                            id="name"
                            name="name"
                            value="{{ old('name', $agency->name) }}"
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            required
                        >
                    </div>

                    {{-- Email --}}
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">
                            Email <span class="text-red-500">*</span>
                        </label>
                        <input
                            type="email"
                            id="email"
                            name="email"
                            value="{{ old('email', $agency->email) }}"
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            required
                        >
                    </div>

                    {{-- Téléphone --}}
                    <div>
                        <label for="telephone" class="block text-sm font-medium text-gray-700 mb-1">
                            Téléphone
                        </label>
                        <input
                            type="text"
                            id="telephone"
                            name="telephone"
                            value="{{ old('telephone', $agency->telephone) }}"
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        >
                    </div>

                    {{-- Adresse --}}
                    <div class="sm:col-span-2">
                        <label for="adresse" class="block text-sm font-medium text-gray-700 mb-1">
                            Adresse
                        </label>
                        <input
                            type="text"
                            id="adresse"
                            name="adresse"
                            value="{{ old('adresse', $agency->adresse) }}"
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        >
                    </div>

                </div>
            </div>

            {{-- ── Bouton sauvegarde ── --}}
            <div class="flex justify-end">
                <button
                    type="submit"
                    class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-6 py-3 rounded-lg shadow-sm transition"
                >
                    Sauvegarder les paramètres
                </button>
            </div>

        </form>

    </div>
</x-app-layout>