<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Super Admin — Plateforme BIMO-Tech</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 min-h-screen">

    {{-- ── Header ── --}}
    <header class="bg-gray-900 text-white px-6 py-4 flex items-center justify-between shadow">
        <div>
            <h1 class="text-xl font-bold">⚙️ Super Administration</h1>
            <p class="text-gray-400 text-sm">Plateforme BIMO-Tech — Vue globale</p>
        </div>
        <div class="flex items-center gap-4">
            <span class="text-sm text-gray-300">{{ Auth::user()->name }}</span>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="text-sm bg-gray-700 hover:bg-gray-600 px-3 py-1 rounded transition">
                    Déconnexion
                </button>
            </form>
        </div>
    </header>

    <main class="max-w-7xl mx-auto px-4 py-8">

        {{-- Message succès --}}
        @if (session('success'))
            <div class="mb-6 bg-green-50 border border-green-200 text-green-800 rounded-lg px-4 py-3 text-sm">
                {{ session('success') }}
            </div>
        @endif

        {{-- ── Stats globales ── --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">

            <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-200 text-center">
                <p class="text-3xl font-bold text-gray-800">{{ $stats['nb_agences'] }}</p>
                <p class="text-sm text-gray-500 mt-1">Agences totales</p>
                <p class="text-xs text-green-600 mt-1">{{ $stats['nb_agences_actives'] }} actives</p>
            </div>

            <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-200 text-center">
                <p class="text-3xl font-bold text-gray-800">{{ $stats['nb_users'] }}</p>
                <p class="text-sm text-gray-500 mt-1">Utilisateurs</p>
                <p class="text-xs text-gray-400 mt-1">Tous rôles confondus</p>
            </div>

            <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-200 text-center">
                <p class="text-3xl font-bold text-gray-800">{{ $stats['nb_biens'] }}</p>
                <p class="text-sm text-gray-500 mt-1">Biens gérés</p>
                <p class="text-xs text-gray-400 mt-1">{{ $stats['nb_contrats'] }} contrats actifs</p>
            </div>

            <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-200 text-center">
                <p class="text-3xl font-bold text-blue-700">
                    {{ number_format($stats['total_commissions'], 0, ',', ' ') }} F
                </p>
                <p class="text-sm text-gray-500 mt-1">Commissions totales</p>
                <p class="text-xs text-gray-400 mt-1">
                    {{ number_format($stats['total_loyers'], 0, ',', ' ') }} F encaissés
                </p>
            </div>

        </div>

        {{-- ── Liste des agences ── --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">

            <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
    <h2 class="text-lg font-semibold text-gray-800">Toutes les agences</h2>
    <div class="flex items-center gap-3">
        
           <a href="{{ route('superadmin.subscriptions') }}"
            class="text-sm bg-gray-700 hover:bg-gray-800 text-white px-4 py-2 rounded-lg transition"
        >
            💳 Abonnements
        </a>
        
          <a href="{{ route('superadmin.agencies.create') }}"
            class="text-sm bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition"
        >
            + Nouvelle agence
        </a>
    </div>
</div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 text-gray-600 uppercase text-xs">
                        <tr>
                            <th class="px-6 py-3 text-left">Agence</th>
                            <th class="px-6 py-3 text-center">Statut</th>
                            <th class="px-6 py-3 text-center">Admins</th>
                            <th class="px-6 py-3 text-center">Biens</th>
                            <th class="px-6 py-3 text-center">Contrats actifs</th>
                            <th class="px-6 py-3 text-right">Loyers encaissés</th>
                            <th class="px-6 py-3 text-right">Commissions</th>
                            <th class="px-6 py-3 text-center">Inscrite le</th>
                            <th class="px-6 py-3 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($agences as $agence)
                            <tr class="hover:bg-gray-50 transition {{ $agence->actif ? '' : 'opacity-50' }}">

                                {{-- Nom + email --}}
                                <td class="px-6 py-4">
                                    <p class="font-semibold text-gray-900">{{ $agence->name }}</p>
                                    <p class="text-gray-400 text-xs">{{ $agence->email }}</p>
                                </td>

                                {{-- Statut --}}
                                <td class="px-6 py-4 text-center">
                                    @if ($agence->actif)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            Active
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                            Suspendue
                                        </span>
                                    @endif
                                </td>

                                {{-- Admins --}}
                                <td class="px-6 py-4 text-center text-gray-700">
                                    {{ $agence->nb_admins }}
                                </td>

                                {{-- Biens --}}
                                <td class="px-6 py-4 text-center text-gray-700">
                                    {{ $agence->biens_count }}
                                </td>

                                {{-- Contrats actifs --}}
                                <td class="px-6 py-4 text-center text-gray-700">
                                    {{ $agence->contrats_count }}
                                </td>

                                {{-- Loyers --}}
                                <td class="px-6 py-4 text-right text-gray-800 font-medium">
                                    {{ number_format($agence->total_loyers, 0, ',', ' ') }} F
                                </td>

                                {{-- Commissions --}}
                                <td class="px-6 py-4 text-right text-blue-700 font-semibold">
                                    {{ number_format($agence->total_commissions, 0, ',', ' ') }} F
                                </td>

                                {{-- Date inscription --}}
                                <td class="px-6 py-4 text-center text-gray-500">
                                    {{ $agence->created_at->format('d/m/Y') }}
                                </td>

                                {{-- Actions --}}
                                <td class="px-6 py-4 text-center">
                                    <div class="flex items-center justify-center gap-2">

                                        {{-- Voir le détail --}}
                                        
                                          <a  href="{{ route('superadmin.agencies.show', $agence) }}"
                                            class="text-xs bg-gray-100 hover:bg-gray-200 text-gray-700 px-3 py-1 rounded transition"
                                        >
                                            Détail
                                        </a>

                                        {{-- Activer / Suspendre --}}
                                        <form
                                            method="POST"
                                            action="{{ route('superadmin.agencies.toggle', $agence) }}"
                                            onsubmit="return confirm('{{ $agence->actif ? 'Suspendre cette agence ?' : 'Activer cette agence ?' }}')"
                                        >
                                            @csrf
                                            @method('PATCH')
                                            <button
                                                type="submit"
                                                class="text-xs px-3 py-1 rounded transition
                                                    {{ $agence->actif
                                                        ? 'bg-red-100 hover:bg-red-200 text-red-700'
                                                        : 'bg-green-100 hover:bg-green-200 text-green-700' }}"
                                            >
                                                {{ $agence->actif ? 'Suspendre' : 'Activer' }}
                                            </button>
                                        </form>

                                    </div>
                                </td>

                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="px-6 py-8 text-center text-gray-400">
                                    Aucune agence enregistrée.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </main>

</body>
</html>