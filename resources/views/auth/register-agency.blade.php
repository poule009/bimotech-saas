<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription Agence — BIMO-Tech</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 min-h-screen">

    <div class="min-h-screen flex flex-col justify-center py-12 px-4 sm:px-6 lg:px-8">

        {{-- Logo & Titre --}}
        <div class="sm:mx-auto sm:w-full sm:max-w-2xl text-center mb-8">
            <h1 class="text-3xl font-bold text-gray-900">BIMO-Tech</h1>
            <p class="mt-2 text-gray-600">Créez votre espace agence immobilière</p>
        </div>

        {{-- Carte formulaire --}}
        <div class="sm:mx-auto sm:w-full sm:max-w-2xl">
            <div class="bg-white py-8 px-6 shadow-sm rounded-xl border border-gray-200">

                {{-- Erreurs globales --}}
                @if ($errors->any())
                    <div class="mb-6 bg-red-50 border border-red-200 rounded-lg p-4">
                        <ul class="list-disc list-inside text-sm text-red-700 space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('agency.register.store') }}">
                    @csrf

                    {{-- ── Section 1 : Informations de l'agence ── --}}
                    <div class="mb-8">
                        <h2 class="text-lg font-semibold text-gray-800 border-b border-gray-200 pb-2 mb-5">
                            🏢 Votre agence
                        </h2>

                        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">

                            {{-- Nom de l'agence --}}
                            <div class="sm:col-span-2">
                                <label for="agency_name" class="block text-sm font-medium text-gray-700 mb-1">
                                    Nom de l'agence <span class="text-red-500">*</span>
                                </label>
                                <input
                                    type="text"
                                    id="agency_name"
                                    name="agency_name"
                                    value="{{ old('agency_name') }}"
                                    placeholder="Ex : Immobilier Prestige Dakar"
                                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('agency_name') border-red-500 @enderror"
                                    required
                                >
                                @error('agency_name')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Email de l'agence --}}
                            <div>
                                <label for="agency_email" class="block text-sm font-medium text-gray-700 mb-1">
                                    Email de l'agence <span class="text-red-500">*</span>
                                </label>
                                <input
                                    type="email"
                                    id="agency_email"
                                    name="agency_email"
                                    value="{{ old('agency_email') }}"
                                    placeholder="contact@monagence.sn"
                                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('agency_email') border-red-500 @enderror"
                                    required
                                >
                                @error('agency_email')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Téléphone --}}
                            <div>
                                <label for="agency_telephone" class="block text-sm font-medium text-gray-700 mb-1">
                                    Téléphone
                                </label>
                                <input
                                    type="text"
                                    id="agency_telephone"
                                    name="agency_telephone"
                                    value="{{ old('agency_telephone') }}"
                                    placeholder="+221 77 000 00 00"
                                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                >
                            </div>

                            {{-- Adresse --}}
                            <div class="sm:col-span-2">
                                <label for="agency_adresse" class="block text-sm font-medium text-gray-700 mb-1">
                                    Adresse
                                </label>
                                <input
                                    type="text"
                                    id="agency_adresse"
                                    name="agency_adresse"
                                    value="{{ old('agency_adresse') }}"
                                    placeholder="Plateau, Dakar"
                                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                >
                            </div>

                        </div>
                    </div>

                    {{-- ── Section 2 : Compte administrateur ── --}}
                    <div class="mb-8">
                        <h2 class="text-lg font-semibold text-gray-800 border-b border-gray-200 pb-2 mb-5">
                            👤 Votre compte administrateur
                        </h2>

                        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">

                            {{-- Nom admin --}}
                            <div class="sm:col-span-2">
                                <label for="admin_name" class="block text-sm font-medium text-gray-700 mb-1">
                                    Nom complet <span class="text-red-500">*</span>
                                </label>
                                <input
                                    type="text"
                                    id="admin_name"
                                    name="admin_name"
                                    value="{{ old('admin_name') }}"
                                    placeholder="Prénom et Nom"
                                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('admin_name') border-red-500 @enderror"
                                    required
                                >
                                @error('admin_name')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Email admin --}}
                            <div class="sm:col-span-2">
                                <label for="admin_email" class="block text-sm font-medium text-gray-700 mb-1">
                                    Email de connexion <span class="text-red-500">*</span>
                                </label>
                                <input
                                    type="email"
                                    id="admin_email"
                                    name="admin_email"
                                    value="{{ old('admin_email') }}"
                                    placeholder="votre@email.com"
                                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('admin_email') border-red-500 @enderror"
                                    required
                                >
                                @error('admin_email')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Mot de passe --}}
                            <div>
                                <label for="admin_password" class="block text-sm font-medium text-gray-700 mb-1">
                                    Mot de passe <span class="text-red-500">*</span>
                                </label>
                                <input
                                    type="password"
                                    id="admin_password"
                                    name="admin_password"
                                    placeholder="Min. 8 caractères"
                                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('admin_password') border-red-500 @enderror"
                                    required
                                >
                                @error('admin_password')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Confirmation mot de passe --}}
                            <div>
                                <label for="admin_password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">
                                    Confirmer le mot de passe <span class="text-red-500">*</span>
                                </label>
                                <input
                                    type="password"
                                    id="admin_password_confirmation"
                                    name="admin_password_confirmation"
                                    placeholder="Répétez le mot de passe"
                                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                    required
                                >
                            </div>

                        </div>
                    </div>

                    {{-- ── CGU ── --}}
                    <div class="mb-6">
                        <label class="flex items-start gap-3 cursor-pointer">
                            <input
                                type="checkbox"
                                name="cgu"
                                value="1"
                                class="mt-1 rounded border-gray-300 text-blue-600 focus:ring-blue-500 @error('cgu') border-red-500 @enderror"
                            >
                            <span class="text-sm text-gray-600">
                                J'accepte les
                                <a href="#" class="text-blue-600 hover:underline">conditions générales d'utilisation</a>
                                de la plateforme BIMO-Tech.
                            </span>
                        </label>
                        @error('cgu')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- ── Bouton submit ── --}}
                    <button
                        type="submit"
                        class="w-full flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-sm font-semibold text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition"
                    >
                        Créer mon agence
                    </button>

                </form>

            </div>

            {{-- Lien connexion --}}
            <p class="mt-4 text-center text-sm text-gray-600">
                Déjà inscrit ?
                <a href="{{ route('login') }}" class="text-blue-600 font-medium hover:underline">
                    Se connecter
                </a>
            </p>

        </div>
    </div>

</body>
</html>