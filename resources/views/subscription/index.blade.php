<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Abonnement — {{ $agency->name }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 min-h-screen">

    {{-- ── Header ── --}}
    <header class="bg-white border-b border-gray-200 px-6 py-4 flex items-center justify-between">
        <div>
            <h1 class="text-xl font-bold text-gray-900">{{ $agency->name }}</h1>
            <p class="text-sm text-gray-500">Gestion de l'abonnement</p>
        </div>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button class="text-sm text-gray-500 hover:text-gray-700 transition">
                Déconnexion
            </button>
        </form>
    </header>

    <main class="max-w-5xl mx-auto px-4 py-10">

        {{-- ── Message warning ── --}}
        @if (session('warning'))
            <div class="mb-8 bg-amber-50 border border-amber-200 text-amber-800 rounded-xl px-6 py-4 flex items-start gap-3">
                <span class="text-2xl">⚠️</span>
                <div>
                    <p class="font-semibold">Accès restreint</p>
                    <p class="text-sm mt-0.5">{{ session('warning') }}</p>
                </div>
            </div>
        @endif

        {{-- ── Message succès ── --}}
        @if (session('success'))
            <div class="mb-8 bg-green-50 border border-green-200 text-green-800 rounded-xl px-6 py-4">
                ✅ {{ session('success') }}
            </div>
        @endif

        {{-- ── Statut actuel ── --}}
        @if ($subscription)
            <div class="mb-8 bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">Votre situation actuelle</h2>
                <div class="flex flex-wrap gap-6">

                    <div>
                        <p class="text-xs text-gray-400 uppercase tracking-wide">Statut</p>
                        @if ($subscription->estEnEssai())
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800 mt-1">
                                🕐 Période d'essai
                            </span>
                        @elseif ($subscription->estActif())
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800 mt-1">
                                ✅ Abonnement actif
                            </span>
                        @else
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800 mt-1">
                                ❌ Expiré
                            </span>
                        @endif
                    </div>

                    @if ($subscription->estEnEssai())
                        <div>
                            <p class="text-xs text-gray-400 uppercase tracking-wide">Fin de l'essai</p>
                            <p class="font-semibold text-gray-800 mt-1">
                                {{ $subscription->date_fin_essai->format('d/m/Y') }}
                                <span class="text-sm font-normal text-amber-600">
                                    ({{ $subscription->joursRestantsEssai() }} jours restants)
                                </span>
                            </p>
                        </div>
                    @elseif ($subscription->estActif())
                        <div>
                            <p class="text-xs text-gray-400 uppercase tracking-wide">Plan actuel</p>
                            <p class="font-semibold text-gray-800 mt-1">
                                {{ \App\Models\Subscription::LABELS[$subscription->plan] ?? $subscription->plan }}
                            </p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400 uppercase tracking-wide">Expire le</p>
                            <p class="font-semibold text-gray-800 mt-1">
                                {{ $subscription->date_fin_abonnement->format('d/m/Y') }}
                                <span class="text-sm font-normal text-gray-500">
                                    ({{ $subscription->joursRestantsAbonnement() }} jours restants)
                                </span>
                            </p>
                        </div>
                    @endif

                </div>
            </div>
        @endif

        {{-- ── Titre ── --}}
        <div class="text-center mb-8">
            <h2 class="text-2xl font-bold text-gray-900">Choisissez votre abonnement</h2>
            <p class="text-gray-500 mt-2">Accédez à toutes les fonctionnalités de BIMO-Tech sans limitation.</p>
            <div class="mt-3 inline-flex items-center gap-2 bg-amber-50 border border-amber-200 text-amber-700 rounded-lg px-4 py-2 text-sm">
                🔧 Paiement en ligne bientôt disponible — contactez-nous pour souscrire maintenant.
            </div>
        </div>

        {{-- ── Grille des plans ── --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-10">

            @php
                $plans = [
                    'mensuel'     => ['label' => 'Mensuel',     'prix' => '25 000',  'duree' => 'par mois',   'eco' => null,  'populaire' => false, 'support' => 'Support email'],
                    'trimestriel' => ['label' => 'Trimestriel', 'prix' => '67 500',  'duree' => 'par 3 mois', 'eco' => '10%', 'populaire' => false, 'support' => 'Support email'],
                    'semestriel'  => ['label' => 'Semestriel',  'prix' => '127 500', 'duree' => 'par 6 mois', 'eco' => '15%', 'populaire' => true,  'support' => 'Support prioritaire'],
                    'annuel'      => ['label' => 'Annuel',      'prix' => '240 000', 'duree' => 'par an',     'eco' => '20%', 'populaire' => false, 'support' => 'Support prioritaire'],
                ];
            @endphp

            @foreach ($plans as $planKey => $plan)
                <div class="bg-white rounded-xl border-2 {{ $plan['populaire'] ? 'border-blue-400' : 'border-gray-200' }} shadow-sm p-6 flex flex-col relative">

                    @if ($plan['populaire'])
                        <div class="absolute -top-3 left-1/2 -translate-x-1/2">
                            <span class="bg-blue-600 text-white text-xs font-bold px-3 py-1 rounded-full">
                                POPULAIRE
                            </span>
                        </div>
                    @endif

                    <p class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-3">
                        {{ $plan['label'] }}
                    </p>
                    <p class="text-3xl font-bold text-gray-900">
                        {{ $plan['prix'] }}
                        <span class="text-base font-normal text-gray-500">FCFA</span>
                    </p>
                    <p class="text-sm {{ $plan['eco'] ? 'text-green-600 font-medium' : 'text-gray-400' }} mt-1">
                        {{ $plan['eco'] ? 'Économie ' . $plan['eco'] : $plan['duree'] }}
                    </p>

                    <ul class="mt-5 space-y-2 text-sm text-gray-600 flex-1">
                        <li>✅ Biens illimités</li>
                        <li>✅ Contrats illimités</li>
                        <li>✅ Rapports financiers</li>
                        <li>✅ Génération PDF</li>
                        <li>✅ {{ $plan['support'] }}</li>
                    </ul>

                    <form
                        method="POST"
                        action="{{ route('subscription.payer') }}"
                        class="mt-6"
                        onsubmit="return confirm('Confirmer l\'abonnement {{ $plan['label'] }} à {{ $plan['prix'] }} FCFA ?')"
                    >
                        @csrf
                        <input type="hidden" name="plan" value="{{ $planKey }}">
                        <button
                            type="submit"
                            class="w-full py-2.5 px-4 rounded-lg text-sm font-semibold transition
                                {{ $plan['populaire']
                                    ? 'bg-blue-600 hover:bg-blue-700 text-white'
                                    : 'bg-gray-800 hover:bg-gray-900 text-white' }}"
                        >
                            Choisir ce plan
                        </button>
                    </form>

                </div>
            @endforeach

        </div>

        {{-- ── Historique des paiements ── --}}
        @if ($historique->count() > 0)
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden mb-8">

                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-800">📋 Historique des paiements</h2>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 text-gray-600 uppercase text-xs">
                            <tr>
                                <th class="px-6 py-3 text-left">Date</th>
                                <th class="px-6 py-3 text-center">Plan</th>
                                <th class="px-6 py-3 text-center">Méthode</th>
                                <th class="px-6 py-3 text-center">Période</th>
                                <th class="px-6 py-3 text-right">Montant</th>
                                <th class="px-6 py-3 text-center">Statut</th>
                                <th class="px-6 py-3 text-left">Référence</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach ($historique as $paiement)
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="px-6 py-4 text-gray-600">
                                        {{ $paiement->created_at->format('d/m/Y à H:i') }}
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            {{ \App\Models\Subscription::LABELS[$paiement->plan] ?? $paiement->plan }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-center text-gray-600">
                                        {{ \App\Models\SubscriptionPayment::METHODE_LABELS[$paiement->methode] ?? $paiement->methode }}
                                    </td>
                                    <td class="px-6 py-4 text-center text-gray-500 text-xs">
                                        @if ($paiement->periode_debut && $paiement->periode_fin)
                                            {{ $paiement->periode_debut->format('d/m/Y') }}
                                            →
                                            {{ $paiement->periode_fin->format('d/m/Y') }}
                                        @else
                                            —
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-right font-semibold text-gray-800">
                                        {{ number_format($paiement->montant, 0, ',', ' ') }} F
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        @php
                                            $statutColors = [
                                                'payé'       => 'bg-green-100 text-green-800',
                                                'en_attente' => 'bg-amber-100 text-amber-800',
                                                'échoué'     => 'bg-red-100 text-red-800',
                                                'remboursé'  => 'bg-gray-100 text-gray-800',
                                            ];
                                        @endphp
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statutColors[$paiement->statut] ?? 'bg-gray-100 text-gray-800' }}">
                                            {{ \App\Models\SubscriptionPayment::STATUT_LABELS[$paiement->statut] ?? $paiement->statut }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-xs text-gray-400 font-mono">
                                        {{ $paiement->reference ?? '—' }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

            </div>
        @endif

        {{-- ── Contact support ── --}}
        <div class="text-center bg-white rounded-xl border border-gray-200 shadow-sm p-6">
            <p class="text-gray-600 text-sm">
                Des questions sur nos offres ? Contactez-nous directement :
            </p>
            <p class="mt-2 font-semibold text-gray-900">📧 support@bimotech.sn</p>
            <p class="font-semibold text-gray-900">📞 +221 33 800 00 01</p>
            <p class="text-xs text-gray-400 mt-3">
                Le paiement en ligne sera disponible très prochainement via Wave, Orange Money et carte bancaire.
            </p>
        </div>

    </main>

</body>
</html>