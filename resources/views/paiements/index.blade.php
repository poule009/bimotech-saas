<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800">Paiements</h2>
            <a href="{{ route('admin.paiements.create') }}"
               class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium px-4 py-2 rounded-lg transition">
                + Nouveau paiement
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- Alertes --}}
            @if(session('success'))
            <div class="mb-4 bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-lg text-sm">
                ✅ {{ session('success') }}
            </div>
            @endif

            {{-- Tableau --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-100">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-400 uppercase">Référence</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-400 uppercase">Locataire</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-400 uppercase">Bien</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-400 uppercase">Période</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-400 uppercase">Mode</th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-400 uppercase">Loyer</th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-400 uppercase">Commission TTC</th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-400 uppercase">Net Proprio</th>
                                <th class="px-4 py-3 text-center text-xs font-medium text-gray-400 uppercase">Statut</th>
                                <th class="px-4 py-3 text-center text-xs font-medium text-gray-400 uppercase">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @forelse($paiements as $p)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-4 py-3 text-xs font-mono text-gray-500">
                                    {{ $p->reference_paiement }}
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-800">
                                    {{ $p->contrat->locataire->name }}
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-600">
                                    <div>{{ $p->contrat->bien->reference }}</div>
                                    <div class="text-xs text-gray-400">{{ $p->contrat->bien->ville }}</div>
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-600">
                                    {{ \Carbon\Carbon::parse($p->periode)->translatedFormat('F Y') }}
                                </td>
                                <td class="px-4 py-3">
                                    <span class="px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-600 font-medium">
                                        {{ ucfirst(str_replace('_', ' ', $p->mode_paiement)) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-sm font-semibold text-gray-900 text-right">
                                    {{ number_format($p->montant_encaisse, 0, ',', ' ') }} F
                                </td>
                                <td class="px-4 py-3 text-sm text-amber-600 font-medium text-right">
                                    {{ number_format($p->commission_ttc, 0, ',', ' ') }} F
                                    <div class="text-xs text-gray-400">
                                        TVA: {{ number_format($p->tva_commission, 0, ',', ' ') }} F
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-sm font-bold text-emerald-600 text-right">
                                    {{ number_format($p->net_proprietaire, 0, ',', ' ') }} F
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <span class="px-2 py-1 text-xs rounded-full font-medium
                                        {{ $p->statut === 'valide'     ? 'bg-emerald-100 text-emerald-700' : '' }}
                                        {{ $p->statut === 'en_attente' ? 'bg-amber-100 text-amber-700'    : '' }}
                                        {{ $p->statut === 'annule'     ? 'bg-red-100 text-red-600'        : '' }}">
                                        {{ ucfirst($p->statut) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        {{-- Voir détail --}}
                                        <a href="{{ route('admin.paiements.show', $p) }}"
                                           class="text-indigo-600 hover:text-indigo-800 text-xs font-medium">
                                            Voir
                                        </a>
                                        {{-- Télécharger PDF --}}
                                        @if($p->statut === 'valide')
                                        <a href="{{ route('admin.paiements.pdf', $p) }}"
                                           class="bg-indigo-50 hover:bg-indigo-100 text-indigo-700 text-xs font-medium px-2 py-1 rounded-lg transition"
                                           target="_blank">
                                            📄 PDF
                                        </a>
                                        @endif
                                        {{-- Annuler --}}
                                        @if($p->statut === 'valide')
                                        <form method="POST" action="{{ route('admin.paiements.annuler', $p) }}"
                                              onsubmit="return confirm('Annuler ce paiement ?')">
                                            @csrf @method('PATCH')
                                            <button type="submit"
                                                    class="text-red-500 hover:text-red-700 text-xs font-medium">
                                                Annuler
                                            </button>
                                        </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="10" class="px-6 py-12 text-center text-gray-400">
                                    <div class="text-4xl mb-2">💰</div>
                                    <div class="text-sm">Aucun paiement enregistré</div>
                                    <a href="{{ route('paiements.create') }}"
                                       class="mt-3 inline-block text-indigo-600 text-sm hover:underline">
                                        Enregistrer le premier paiement →
                                    </a>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                @if($paiements->hasPages())
                <div class="px-6 py-4 border-t border-gray-100">
                    {{ $paiements->links() }}
                </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>