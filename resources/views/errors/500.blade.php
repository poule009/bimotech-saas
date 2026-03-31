<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Erreur serveur — BimoTech Immo</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 min-h-screen flex items-center justify-center">
    <div class="text-center px-6 py-16 max-w-lg mx-auto">
        <!-- Icône -->
        <div class="mb-8">
            <svg class="mx-auto h-24 w-24 text-orange-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
        </div>

        <!-- Code erreur -->
        <h1 class="text-8xl font-extrabold text-orange-500 mb-4">500</h1>

        <!-- Message -->
        <h2 class="text-2xl font-bold text-gray-800 mb-3">Erreur interne du serveur</h2>
        <p class="text-gray-500 mb-8">
            Une erreur inattendue s'est produite. Notre équipe technique a été notifiée.<br>
            Veuillez réessayer dans quelques instants.
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
