<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Page introuvable — BimoTech Immo</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 min-h-screen flex items-center justify-center">
    <div class="text-center px-6 py-16 max-w-lg mx-auto">
        <!-- Icône -->
        <div class="mb-8">
            <svg class="mx-auto h-24 w-24 text-indigo-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                    d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
            </svg>
        </div>

        <!-- Code erreur -->
        <h1 class="text-8xl font-extrabold text-indigo-600 mb-4">404</h1>

        <!-- Message -->
        <h2 class="text-2xl font-bold text-gray-800 mb-3">Page introuvable</h2>
        <p class="text-gray-500 mb-8">
            La page que vous recherchez n'existe pas ou a été déplacée.<br>
            Vérifiez l'adresse ou retournez au tableau de bord.
        </p>

        <!-- Actions -->
        <div class="flex flex-col sm:flex-row gap-3 justify-center">
            <a href="{{ url()->previous() }}"
               class="inline-flex items-center px-5 py-2.5 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition font-medium">
                ← Retour
            </a>
            <a href="{{ route('dashboard') }}"
               class="inline-flex items-center px-5 py-2.5 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition font-medium">
                🏠 Tableau de bord
            </a>
        </div>

        <!-- Logo -->
        <div class="mt-12 text-gray-400 text-sm">
            <span class="font-semibold text-indigo-500">BimoTech</span> Immo — Gestion immobilière au Sénégal
        </div>
    </div>
</body>
</html>
