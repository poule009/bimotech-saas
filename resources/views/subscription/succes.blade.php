<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Paiement réussi — {{ $agency->name }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 min-h-screen flex items-center justify-center">

    <div class="max-w-md w-full mx-auto px-4">
        <div class="bg-white rounded-2xl shadow-lg border border-gray-200 p-10 text-center">

            {{-- Icône succès --}}
            <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-6">
                <svg class="w-10 h-10 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                </svg>
            </div>

            <h1 class="text-2xl font-bold text-gray-900 mb-2">Paiement réussi !</h1>
            <p class="text-gray-500 mb-6">
                Votre abonnement BimoTech Immo est maintenant actif pour
                <strong class="text-gray-800">{{ $agency->name }}</strong>.
            </p>

            @if($agency->subscription && $agency->subscription->estActif())
                <div class="bg-green-50 border border-green-200 rounded-xl p-4 mb-6 text-left">
                    <div class="flex items-center gap-3 mb-2">
                        <span class="text-green-600 font-semibold text-sm">✅ Abonnement actif</span>
                    </div>
                    <div class="text-sm text-gray-600 space-y-1">
                        <div class="flex justify-between">
                            <span class="text-gray-500">Plan :</span>
                            <span class="font-medium">
                                {{ \App\Models\Subscription::LABELS[$agency->subscription->plan] ?? $agency->subscription->plan }}
                            </span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">Expire le :</span>
                            <span class="font-medium">
                                {{ $agency->subscription->date_fin_abonnement->format('d/m/Y') }}
                            </span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">Jours restants :</span>
                            <span class="font-medium text-green-700">
                                {{ $agency->subscription->joursRestantsAbonnement() }} jours
                            </span>
                        </div>
                    </div>
                </div>
            @endif

            <a href="{{ route('admin.dashboard') }}"
               class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold px-6 py-3 rounded-xl transition w-full justify-center">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
                Accéder au tableau de bord
            </a>

            <p class="text-xs text-gray-400 mt-4">
                Un email de confirmation vous a été envoyé à {{ $agency->email }}.
            </p>

        </div>

        <p class="text-center text-xs text-gray-400 mt-6">
            BimoTech Immo · Gestion immobilière au Sénégal
        </p>
    </div>

</body>
</html>
