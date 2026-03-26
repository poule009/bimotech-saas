<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800">Contrats de bail</h2>
            <a href="{{ route('admin.contrats.create') }}"
               class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium px-4 py-2 rounded-lg transition">
                + Nouveau contrat
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            @if(session('success'))
            <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-lg text-sm">
                ✅ {{ session('success') }}
            </div>
            @endif

            {{-- Stats --}}
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                <div class="bg-white rounded-xl border border-gray-100 p-4 text-center shadow-sm">
                    <div class="text-2xl font-bold text-gray-900">{{ $stats['total'] }}</div>
                    <div class="text-xs text-gray-400 mt-1">Total</div>
                </div>
                <div class="bg-emerald-50 border border-emerald-100 rounded-xl p-4 text-center">
                    <div class="text-2xl font-bold text-emerald-700">{{ $stats['actifs'] }}</div>
                    <div class="text-xs text-emerald-500 mt-1">Actifs</div>
                </div>
                <div class="bg-red-50 border border-red-100 rounded-xl p-4 text-center">
                    <div class="text-2xl font-bold text-red-700">{{ $stats['resilies'] }}</div>
                    <div class="text-xs text-red-500 mt-1">Résiliés</div>
                </div>
                <div class="bg-gray-50 border border-gray-100 rounded-xl p-4 text-center">
                    <div class="text-2xl font-bold text-gray-700">{{ $stats['expires'] }}</div>
                    <div class="text-xs text-gray-400 mt-1">Expirés</div>
                </div>
            </div>

            {{-- Tableau --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-100">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-400 uppercase">Bien</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-400 uppercase">Locataire</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-400 uppercase">Début</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-400 uppercase">Fin</th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-400 uppercase">Loyer</th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-400 uppercase">Caution</th>
                                <th class="px-4 py-3 text-center text-xs font-medium text-gray-400 uppercase">Statut</th>
                                <th class="px-4 py-3 text-center text-xs font-medium text-gray-400 uppercase">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @forelse($contrats as $contrat)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-4 py-3">
                                    <div class="font-medium text-sm text-gray-800">{{ $contrat->bien->reference }}</div>
                                    <div class="text-xs text-gray-400">{{ $contrat->bien->ville }}</div>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="text-sm text-gray-800">{{ $contrat->locataire->name }}</div>
                                    <div class="text-xs text-gray-400">{{ $contrat->locataire->email }}</div>
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-600">
                                    {{ \Carbon\Carbon::parse($contrat->date_debut)->format('d/m/Y') }}
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-600">
                                    {{ $contrat->date_fin ? \Carbon\Carbon::parse($contrat->date_fin)->format('d/m/Y') : 'Indéterminé' }}
                                </td>
                                <td class="px-4 py-3 text-sm font-semibold text-gray-900 text-right">
                                    {{ number_format($contrat->loyer_contractuel, 0, ',', ' ') }} F
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-600 text-right">
                                    {{ number_format($contrat->caution, 0, ',', ' ') }} F
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <span class="px-2 py-1 text-xs rounded-full font-medium
                                        {{ $contrat->statut === 'actif'   ? 'bg-emerald-100 text-emerald-700' : '' }}
                                        {{ $contrat->statut === 'resilié' ? 'bg-red-100 text-red-600'         : '' }}
                                        {{ $contrat->statut === 'expiré'  ? 'bg-gray-100 text-gray-500'       : '' }}">
                                        {{ ucfirst($contrat->statut) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <div class="flex justify-center gap-2">
                                        <a href="{{ route('admin.contrats.show', $contrat) }}"
                                           class="text-indigo-600 hover:text-indigo-800 text-xs font-medium">
                                            Voir
                                        </a>
                                        @if($contrat->statut === 'actif')
                                        <form method="POST" action="{{ route('admin.contrats.destroy', $contrat) }}"
                                              onsubmit="return confirm('Résilier ce contrat ?')">
                                            @csrf @method('DELETE')
                                            <button type="submit"
                                                    class="text-red-500 hover:text-red-700 text-xs font-medium">
                                                Résilier
                                            </button>
                                        </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="px-6 py-12 text-center text-gray-400">
                                    <div class="text-4xl mb-2">📄</div>
                                    <div class="text-sm">Aucun contrat enregistré</div>
                                    <a href="{{ route('contrats.create') }}"
                                       class="mt-3 inline-block text-indigo-600 text-sm hover:underline">
                                        Créer le premier contrat →
                                    </a>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($contrats->hasPages())
                <div class="px-6 py-4 border-t border-gray-100">
                    {{ $contrats->links() }}
                </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>