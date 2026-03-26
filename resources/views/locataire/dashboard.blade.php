<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">
            Mon espace locataire — {{ auth()->user()->name }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">

            @if($contrat)

            {{-- Infos bail --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                <h3 class="font-semibold text-gray-800 mb-4">Mon logement</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <div class="text-xs text-gray-400 uppercase tracking-wide mb-1">Bien</div>
                        <div class="font-semibold text-gray-800">{{ $contrat->bien->reference }}</div>
                        <div class="text-sm text-gray-500">{{ $contrat->bien->adresse }}, {{ $contrat->bien->ville }}</div>
                        <div class="text-xs text-gray-400 mt-1">
                            {{ $contrat->bien->type }}
                            @if($contrat->bien->surface_m2) · {{ $contrat->bien->surface_m2 }} m² @endif
                            @if($contrat->bien->nombre_pieces) · {{ $contrat->bien->nombre_pieces }} pièces @endif
                        </div>
                    </div>
                    <div>
                        <div class="text-xs text-gray-400 uppercase tracking-wide mb-1">Mon bail</div>
                        <div class="font-semibold text-gray-800">
                            {{ number_format($contrat->loyer_contractuel, 0, ',', ' ') }} FCFA/mois
                        </div>
                        <div class="text-sm text-gray-500">
                            Depuis le {{ \Carbon\Carbon::parse($contrat->date_debut)->format('d/m/Y') }}
                        </div>
                        <div class="text-xs text-emerald-600 font-medium mt-1">● Contrat actif</div>
                    </div>
                </div>
            </div>

            {{-- KPI --}}
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="bg-indigo-50 border border-indigo-100 rounded-xl p-4 text-center">
                    <div class="text-2xl font-bold text-indigo-700">{{ $stats['nb_paiements'] }}</div>
                    <div class="text-xs text-indigo-500 mt-1">Paiements effectués</div>
                </div>
                <div class="bg-emerald-50 border border-emerald-100 rounded-xl p-4 text-center">
                    <div class="text-2xl font-bold text-emerald-700">
                        {{ number_format($stats['total_paye'], 0, ',', ' ') }} F
                    </div>
                    <div class="text-xs text-emerald-500 mt-1">Total payé</div>
                </div>
                <div class="bg-amber-50 border border-amber-100 rounded-xl p-4 text-center">
                    <div class="text-2xl font-bold text-amber-700">
                        @if($prochainePeriode)
                            {{ $prochainePeriode->translatedFormat('M Y') }}
                        @else — @endif
                    </div>
                    <div class="text-xs text-amber-600 mt-1">Prochain loyer</div>
                </div>
            </div>

            {{-- Dernier paiement + bouton PDF --}}
            @if($dernierPaiement)
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                <div class="flex justify-between items-start">
                    <div>
                        <h3 class="font-semibold text-gray-800 mb-1">Dernier paiement</h3>
                        <div class="text-xs text-gray-400 font-mono">{{ $dernierPaiement->reference_paiement }}</div>
                        <div class="mt-3 space-y-1">
                            <div class="text-sm text-gray-600">
                                Période :
                                <span class="font-medium text-gray-800">
                                    {{ \Carbon\Carbon::parse($dernierPaiement->periode)->translatedFormat('F Y') }}
                                </span>
                            </div>
                            <div class="text-sm text-gray-600">
                                Montant :
                                <span class="font-bold text-gray-900">
                                    {{ number_format($dernierPaiement->montant_encaisse, 0, ',', ' ') }} FCFA
                                </span>
                            </div>
                            <div class="text-sm text-gray-600">
                                Mode :
                                <span class="font-medium">
                                    {{ ucfirst(str_replace('_', ' ', $dernierPaiement->mode_paiement)) }}
                                </span>
                            </div>
                            <div class="text-sm">
                                <span class="px-2 py-1 bg-emerald-100 text-emerald-700 text-xs rounded-full font-medium">
                                    ✓ Validé
                                </span>
                            </div>
                        </div>
                    </div>
                    <a href="{{ route('locataire.paiements.pdf', $dernierPaiement) }}"
                       target="_blank"
                       class="bg-indigo-600 hover:bg-indigo-700 text-white font-medium text-sm px-5 py-3 rounded-xl transition flex items-center gap-2">
                        📄 Quittance PDF
                    </a>
                </div>
            </div>
            @endif

            {{-- Historique --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h3 class="font-semibold text-gray-800">Historique de mes paiements</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-50">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-400 uppercase">Période</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-400 uppercase">Mode</th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-400 uppercase">Montant</th>
                                <th class="px-4 py-3 text-center text-xs font-medium text-gray-400 uppercase">Statut</th>
                                <th class="px-4 py-3 text-center text-xs font-medium text-gray-400 uppercase">PDF</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @forelse($paiements as $p)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-4 py-3 text-sm font-medium text-gray-800">
                                    {{ \Carbon\Carbon::parse($p->periode)->translatedFormat('F Y') }}
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-600">
                                    {{ ucfirst(str_replace('_', ' ', $p->mode_paiement)) }}
                                </td>
                                <td class="px-4 py-3 text-sm font-bold text-gray-900 text-right">
                                    {{ number_format($p->montant_encaisse, 0, ',', ' ') }} F
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <span class="px-2 py-1 text-xs rounded-full bg-emerald-100 text-emerald-700 font-medium">
                                        ✓ Validé
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <a href="{{ route('locataire.paiements.pdf', $p) }}"
                                       target="_blank"
                                       class="text-indigo-600 hover:text-indigo-800 text-xs font-medium">
                                        📄 PDF
                                    </a>
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

            @else
            {{-- Pas de contrat actif --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-12 text-center">
                <div class="text-5xl mb-4">🏠</div>
                <div class="text-lg font-semibold text-gray-800 mb-2">Aucun contrat actif</div>
                <div class="text-sm text-gray-500">
                    Votre contrat de bail n'est pas encore enregistré.<br>
                    Contactez l'agence BIMO-Tech pour plus d'informations.
                </div>
            </div>
            @endif

        </div>
    </div>
</x-app-layout>