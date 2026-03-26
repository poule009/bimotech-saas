<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">
            Mon espace propriétaire — {{ auth()->user()->name }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">

            {{-- KPI --}}
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                    <div class="text-sm text-gray-500 mb-2">Mes biens</div>
                    <div class="text-3xl font-bold text-indigo-600">{{ $stats['nb_biens'] }}</div>
                    <div class="text-xs text-gray-400 mt-1">{{ $stats['nb_biens_loues'] }} loués</div>
                </div>
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                    <div class="text-sm text-gray-500 mb-2">Loyers bruts reçus</div>
                    <div class="text-3xl font-bold text-gray-900">
                        {{ number_format($stats['total_loyers'], 0, ',', ' ') }}
                        <span class="text-sm font-normal text-gray-400">FCFA</span>
                    </div>
                </div>
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                    <div class="text-sm text-gray-500 mb-2">Net perçu (après commission)</div>
                    <div class="text-3xl font-bold text-emerald-600">
                        {{ number_format($stats['total_net'], 0, ',', ' ') }}
                        <span class="text-sm font-normal text-gray-400">FCFA</span>
                    </div>
                    <div class="text-xs text-gray-400 mt-1">
                        Commission TTC déduite : {{ number_format($stats['total_commission'], 0, ',', ' ') }} F
                    </div>
                </div>
            </div>

            {{-- Mes biens --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center">
                    <h3 class="font-semibold text-gray-800">Mes biens</h3>
                    <a href="{{ route('biens.index') }}" class="text-sm text-indigo-600 hover:underline">
                        Voir tout →
                    </a>
                </div>
                <div class="divide-y divide-gray-50">
                    @forelse($biens as $bien)
                    <div class="px-6 py-4 flex justify-between items-center hover:bg-gray-50 transition">
                        <div>
                            <div class="font-medium text-gray-800">{{ $bien->reference }}</div>
                            <div class="text-sm text-gray-500">{{ $bien->adresse }}, {{ $bien->ville }}</div>
                            <div class="text-xs text-gray-400">{{ $bien->type }} · {{ $bien->surface_m2 }} m²</div>
                        </div>
                        <div class="text-right">
                            <div class="font-bold text-gray-900">
                                {{ number_format($bien->loyer_mensuel, 0, ',', ' ') }} F/mois
                            </div>
                            <span class="px-2 py-1 text-xs rounded-full font-medium
                                {{ $bien->statut === 'loue'       ? 'bg-emerald-100 text-emerald-700' : '' }}
                                {{ $bien->statut === 'disponible' ? 'bg-blue-100 text-blue-700'       : '' }}
                                {{ $bien->statut === 'en_travaux' ? 'bg-amber-100 text-amber-700'     : '' }}">
                                {{ ucfirst($bien->statut) }}
                            </span>
                        </div>
                    </div>
                    @empty
                    <div class="px-6 py-8 text-center text-gray-400 text-sm">
                        Aucun bien enregistré
                    </div>
                    @endforelse
                </div>
            </div>

            {{-- Derniers paiements --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h3 class="font-semibold text-gray-800">Derniers loyers reçus</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-50">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-400 uppercase">Bien</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-400 uppercase">Locataire</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-400 uppercase">Période</th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-400 uppercase">Loyer</th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-400 uppercase">Net reçu</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @forelse($derniersPaiements as $p)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-4 py-3 text-sm text-gray-800">{{ $p->contrat->bien->reference }}</td>
                                <td class="px-4 py-3 text-sm text-gray-600">{{ $p->contrat->locataire->name }}</td>
                                <td class="px-4 py-3 text-sm text-gray-600">
                                    {{ \Carbon\Carbon::parse($p->periode)->translatedFormat('F Y') }}
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
                                <td colspan="5" class="px-6 py-8 text-center text-gray-400 text-sm">
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