<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.contrats.index') }}" class="text-gray-400 hover:text-gray-600 transition">←</a>
                <h2 class="font-semibold text-xl text-gray-800">
                    Contrat #{{ $contrat->id }}
                </h2>
                <span class="px-2 py-1 text-xs rounded-full font-medium
                    {{ $contrat->statut === 'actif'   ? 'bg-emerald-100 text-emerald-700' : '' }}
                    {{ $contrat->statut === 'resilié' ? 'bg-red-100 text-red-600'         : '' }}
                    {{ $contrat->statut === 'expiré'  ? 'bg-gray-100 text-gray-500'       : '' }}">
                    {{ ucfirst($contrat->statut) }}
                </span>
            </div>
            @if($contrat->statut === 'actif')
            <a href="{{ route('admin.paiements.create', ['contrat_id' => $contrat->id]) }}"
               class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium px-4 py-2 rounded-lg transition">
                + Enregistrer un paiement
            </a>
            @endif
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            @if(session('success'))
            <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-lg text-sm">
                ✅ {{ session('success') }}
            </div>
            @endif

            {{-- KPI --}}
            <div class="grid grid-cols-3 gap-4">
                <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4 text-center">
                    <div class="text-2xl font-bold text-gray-900">{{ $nbPaiements }}</div>
                    <div class="text-xs text-gray-400 mt-1">Paiements effectués</div>
                </div>
                <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4 text-center">
                    <div class="text-2xl font-bold text-emerald-600">
                        {{ number_format($totalPaye, 0, ',', ' ') }} F
                    </div>
                    <div class="text-xs text-gray-400 mt-1">Total encaissé</div>
                </div>
                <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4 text-center">
                    <div class="text-2xl font-bold text-indigo-600">
                        {{ number_format($totalNet, 0, ',', ' ') }} F
                    </div>
                    <div class="text-xs text-gray-400 mt-1">Net propriétaire</div>
                </div>
            </div>

            {{-- Détails --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                <h3 class="font-semibold text-gray-800 mb-4">Détails du contrat</h3>
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-6">
                    <div>
                        <div class="text-xs text-gray-400 uppercase tracking-wide mb-1">Bien</div>
                        <div class="font-semibold text-gray-800">{{ $contrat->bien->reference }}</div>
                        <div class="text-sm text-gray-500">{{ $contrat->bien->adresse }}</div>
                    </div>
                    <div>
                        <div class="text-xs text-gray-400 uppercase tracking-wide mb-1">Propriétaire</div>
                        <div class="font-semibold text-gray-800">{{ $contrat->bien->proprietaire->name }}</div>
                        <div class="text-sm text-gray-500">{{ $contrat->bien->proprietaire->telephone ?? '' }}</div>
                    </div>
                    <div>
                        <div class="text-xs text-gray-400 uppercase tracking-wide mb-1">Locataire</div>
                        <div class="font-semibold text-gray-800">{{ $contrat->locataire->name }}</div>
                        <div class="text-sm text-gray-500">{{ $contrat->locataire->telephone ?? '' }}</div>
                    </div>
                    <div>
                        <div class="text-xs text-gray-400 uppercase tracking-wide mb-1">Début du bail</div>
                        <div class="font-semibold text-gray-800">
                            {{ \Carbon\Carbon::parse($contrat->date_debut)->format('d/m/Y') }}
                        </div>
                    </div>
                    <div>
                        <div class="text-xs text-gray-400 uppercase tracking-wide mb-1">Fin du bail</div>
                        <div class="font-semibold text-gray-800">
                            {{ $contrat->date_fin
                                ? \Carbon\Carbon::parse($contrat->date_fin)->format('d/m/Y')
                                : 'Durée indéterminée' }}
                        </div>
                    </div>
                    <div>
                        <div class="text-xs text-gray-400 uppercase tracking-wide mb-1">Prochain loyer</div>
                        <div class="font-semibold text-amber-600">
                            {{ $prochainePeriode->translatedFormat('F Y') }}
                        </div>
                    </div>
                    <div>
                        <div class="text-xs text-gray-400 uppercase tracking-wide mb-1">Loyer contractuel</div>
                        <div class="font-bold text-gray-900">
                            {{ number_format($contrat->loyer_contractuel, 0, ',', ' ') }} FCFA
                        </div>
                    </div>
                    <div>
                        <div class="text-xs text-gray-400 uppercase tracking-wide mb-1">Caution</div>
                        <div class="font-semibold text-gray-800">
                            {{ number_format($contrat->caution, 0, ',', ' ') }} FCFA
                        </div>
                    </div>
                    <div>
                        <div class="text-xs text-gray-400 uppercase tracking-wide mb-1">Commission</div>
                        <div class="font-semibold text-indigo-600">
                            {{ $contrat->bien->taux_commission }}% + TVA 18%
                        </div>
                    </div>
                </div>
                @if($contrat->observations)
                <div class="mt-4 pt-4 border-t border-gray-100">
                    <div class="text-xs text-gray-400 uppercase tracking-wide mb-1">Observations</div>
                    <div class="text-sm text-gray-600">{{ $contrat->observations }}</div>
                </div>
                @endif
            </div>

            {{-- Historique paiements --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h3 class="font-semibold text-gray-800">Historique des paiements</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-50">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-400 uppercase">Référence</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-400 uppercase">Période</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-400 uppercase">Mode</th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-400 uppercase">Montant</th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-400 uppercase">Commission TTC</th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-400 uppercase">Net proprio</th>
                                <th class="px-4 py-3 text-center text-xs font-medium text-gray-400 uppercase">PDF</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @forelse($contrat->paiements->sortByDesc('periode') as $p)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-4 py-3 text-xs font-mono text-gray-400">{{ $p->reference_paiement }}</td>
                                <td class="px-4 py-3 text-sm text-gray-800">
                                    {{ \Carbon\Carbon::parse($p->periode)->translatedFormat('F Y') }}
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-600">
                                    {{ ucfirst(str_replace('_', ' ', $p->mode_paiement)) }}
                                </td>
                                <td class="px-4 py-3 text-sm font-semibold text-gray-900 text-right">
                                    {{ number_format($p->montant_encaisse, 0, ',', ' ') }} F
                                </td>
                                <td class="px-4 py-3 text-sm text-amber-600 text-right">
                                    {{ number_format($p->commission_ttc, 0, ',', ' ') }} F
                                </td>
                                <td class="px-4 py-3 text-sm font-bold text-emerald-600 text-right">
                                    {{ number_format($p->net_proprietaire, 0, ',', ' ') }} F
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <a href="{{ route('admin.paiements.pdf', $p) }}"
                                       target="_blank"
                                       class="text-indigo-600 hover:text-indigo-800 text-xs font-medium">
                                        📄 PDF
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="px-6 py-8 text-center text-gray-400 text-sm">
                                    Aucun paiement enregistré
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Résiliation --}}
            @if($contrat->statut === 'actif')
            <div class="bg-red-50 rounded-2xl border border-red-100 p-6">
                <h3 class="font-semibold text-red-800 mb-2">Résilier ce contrat</h3>
                <p class="text-sm text-red-600 mb-4">
                    Le bien sera automatiquement remis en statut "disponible".
                </p>
                <form method="POST" action="{{ route('admin.contrats.destroy', $contrat) }}"
                      onsubmit="return confirm('Résilier définitivement ce contrat ?')">
                    @csrf @method('DELETE')
                    <button type="submit"
                            class="bg-red-600 hover:bg-red-700 text-white text-sm font-medium px-4 py-2 rounded-lg transition">
                        Résilier le contrat
                    </button>
                </form>
            </div>
            @endif

        </div>
    </div>
</x-app-layout>