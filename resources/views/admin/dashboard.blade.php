<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">
            Dashboard Admin — BIMO-Tech
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">

            {{-- KPI Cards --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <div class="flex justify-between items-start mb-3">
                        <span class="text-sm text-gray-500">Loyers encaissés</span>
                        <span class="text-xl">🏠</span>
                    </div>
                    <div class="text-2xl font-bold text-gray-900">
                        {{ number_format($stats['total_loyers'], 0, ',', ' ') }}
                        <span class="text-sm font-normal text-gray-400">FCFA</span>
                    </div>
                    <div class="text-xs text-gray-400 mt-1">{{ $stats['nb_contrats'] }} contrats actifs</div>
                </div>

                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <div class="flex justify-between items-start mb-3">
                        <span class="text-sm text-gray-500">Commission BIMO-Tech</span>
                        <span class="text-xl">💼</span>
                    </div>
                    <div class="text-2xl font-bold text-indigo-600">
                        {{ number_format($stats['total_commissions'], 0, ',', ' ') }}
                        <span class="text-sm font-normal text-gray-400">FCFA HT</span>
                    </div>
                    <div class="text-xs text-gray-400 mt-1">
                        TTC : {{ number_format($stats['total_commission_ttc'], 0, ',', ' ') }} FCFA
                    </div>
                </div>

                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <div class="flex justify-between items-start mb-3">
                        <span class="text-sm text-gray-500">TVA collectée 18%</span>
                        <span class="text-xl">🧾</span>
                    </div>
                    <div class="text-2xl font-bold text-amber-600">
                        {{ number_format($stats['total_tva'], 0, ',', ' ') }}
                        <span class="text-sm font-normal text-gray-400">FCFA</span>
                    </div>
                    <div class="text-xs text-gray-400 mt-1">À reverser à la DGI</div>
                </div>

                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <div class="flex justify-between items-start mb-3">
                        <span class="text-sm text-gray-500">Taux d'occupation</span>
                        <span class="text-xl">📊</span>
                    </div>
                    <div class="text-2xl font-bold text-emerald-600">{{ $stats['taux_occupation'] }}%</div>
                    <div class="w-full bg-gray-100 rounded-full h-2 mt-3">
                        <div class="bg-emerald-500 h-2 rounded-full" style="width: {{ $stats['taux_occupation'] }}%"></div>
                    </div>
                    <div class="text-xs text-gray-400 mt-1">
                        {{ $stats['nb_biens_loues'] }}/{{ $stats['nb_biens'] }} biens loués
                    </div>
                </div>
            </div>

            {{-- Compteurs --}}
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                <div class="bg-blue-50 border border-blue-100 rounded-xl p-4 text-center">
                    <div class="text-2xl font-bold text-blue-700">{{ $stats['nb_proprietaires'] }}</div>
                    <div class="text-xs text-blue-500 mt-1">Propriétaires</div>
                </div>
                <div class="bg-purple-50 border border-purple-100 rounded-xl p-4 text-center">
                    <div class="text-2xl font-bold text-purple-700">{{ $stats['nb_locataires'] }}</div>
                    <div class="text-xs text-purple-500 mt-1">Locataires</div>
                </div>
                <div class="bg-indigo-50 border border-indigo-100 rounded-xl p-4 text-center">
                    <div class="text-2xl font-bold text-indigo-700">{{ $stats['nb_biens'] }}</div>
                    <div class="text-xs text-indigo-500 mt-1">Biens</div>
                </div>
                <div class="bg-emerald-50 border border-emerald-100 rounded-xl p-4 text-center">
                    <div class="text-2xl font-bold text-emerald-700">
                        {{ number_format($stats['total_net_proprio'], 0, ',', ' ') }} F
                    </div>
                    <div class="text-xs text-emerald-500 mt-1">Net reversé</div>
                </div>
            </div>

            {{-- Derniers paiements --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center">
                    <h3 class="font-semibold text-gray-800">Derniers paiements</h3>
                    <a href="{{ route('admin.paiements.index') }}"
                       class="text-sm text-indigo-600 hover:underline">Voir tout →</a>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-50">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-400 uppercase">Réf</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-400 uppercase">Locataire</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-400 uppercase">Bien</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-400 uppercase">Période</th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-400 uppercase">Montant</th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-400 uppercase">Net proprio</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @forelse($derniersPaiements as $p)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-4 py-3 text-xs font-mono text-gray-400">{{ $p->reference_paiement }}</td>
                                <td class="px-4 py-3 text-sm text-gray-800">{{ $p->contrat->locataire->name }}</td>
                                <td class="px-4 py-3 text-sm text-gray-600">{{ $p->contrat->bien->reference }}</td>
                                <td class="px-4 py-3 text-sm text-gray-600">
                                    {{ \Carbon\Carbon::parse($p->periode)->translatedFormat('M Y') }}
                                </td>
                                <td class="px-4 py-3 text-sm font-semibold text-gray-900 text-right">
                                    {{ number_format($p->montant_encaisse, 0, ',', ' ') }} F
                                </td>
                                <td class="px-4 py-3 text-sm font-bold text-emerald-600 text-right">
                                    {{ number_format($p->net_proprietaire, 0, ',', ' ') }} F
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="px-6 py-8 text-center text-gray-400 text-sm">
                                    Aucun paiement enregistré
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>