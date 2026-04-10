<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accès refusé — BimoTech Immo</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 min-h-screen flex items-center justify-center">
    <div class="text-center px-6 py-16 max-w-lg mx-auto">
        <!-- Icône -->
        <div class="mb-8">
            <svg class="mx-auto h-24 w-24 text-red-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                    d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
            </svg>
        </div>

        <!-- Code erreur -->
        <h1 class="text-8xl font-extrabold text-red-500 mb-4">403</h1>

        <!-- Message -->
        <h2 class="text-2xl font-bold text-gray-800 mb-3">Accès refusé</h2>
        <p class="text-gray-500 mb-8">
            Vous n'avez pas les permissions nécessaires pour accéder à cette page.<br>
            Si vous pensez qu'il s'agit d'une erreur, contactez votre administrateur.
        </p>

        <!-- Actions -->
        <div class="flex flex-col sm:flex-row gap-3 justify-center">
            <a href="<?php echo e(url()->previous()); ?>"
               class="inline-flex items-center px-5 py-2.5 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition font-medium">
                ← Retour
            </a>
            <a href="<?php echo e(route('dashboard')); ?>"
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
<?php /**PATH C:\Users\ph\bimotech-immo\resources\views/errors/403.blade.php ENDPATH**/ ?>