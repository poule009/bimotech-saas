<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Paiement échoué — BimoTech Immo</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 min-h-screen flex items-center justify-center">

    <div class="max-w-md w-full mx-auto px-4">
        <div class="bg-white rounded-2xl shadow-lg border border-gray-200 p-10 text-center">

            {{-- Icône échec --}}
            <div class="w-20 h-20 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-6">
                <svg class="w-10 h-10 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </div>

            <h1 class="text-2xl font-bold text-gray-900 mb-2">Paiement annulé</h1>
            <p class="text-gray-500 mb-6">
                Votre paiement n'a pas pu être traité ou a été annulé.
                Aucun montant n'a été débité.
            </p>

            <div class="bg-amber-50 border border-amber-200 rounded-xl p-4 mb-6 text-left text-sm text-amber-800">
                <p class="font-semibold mb-1">💡 Que faire ?</p>
                <ul class="space-y-1 text-amber-700">
                    <li>• Vérifiez que votre compte Wave / Orange Money est suffisamment approvisionné.</li>
                    <li>• Réessayez avec un autre mode de paiement.</li>
                    <li>• Contactez notre support si le problème persiste.</li>
                </ul>
            </div>

            <div class="flex flex-col gap-3">
                <a href="{{ route('subscription.index') }}"
                   class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold px-6 py-3 rounded-xl transition justify-center">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                    Réessayer le paiement
                </a>

                <a href="{{ route('admin.dashboard') }}"
                   class="inline-flex items-center gap-2 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold px-6 py-3 rounded-xl transition justify-center">
                    Retour au tableau de bord
                </a>
            </div>

            <div class="mt-6 pt-6 border-t border-gray-100">
                <p class="text-xs text-gray-400 mb-1">Besoin d'aide ?</p>
                <p class="text-sm font-medium text-gray-700">📧 support@bimotech.sn</p>
                <p class="text-sm font-medium text-gray-700">📞 +221 33 800 00 01</p>
            </div>

        </div>

        <p class="text-center text-xs text-gray-400 mt-6">
            BimoTech Immo · Gestion immobilière au Sénégal
        </p>
    </div>

</body>
</html>
