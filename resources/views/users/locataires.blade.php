<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800">Locataires</h2>
            <a href="{{ route('admin.users.create', 'locataire') }}"
               class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium px-4 py-2 rounded-lg transition">
                + Nouveau locataire
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
            <div class="grid grid-cols-3 gap-4">
                <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4 text-center">
                    <div class="text-2xl font-bold text-purple-600">{{ $stats['total'] }}</div>
                    <div class="text-xs text-gray-400 mt-1">Locataires total</div>
                </div>
                <div class="bg-emerald-50 border border-emerald-100 rounded-xl p-4 text-center">
                    <div class="text-2xl font-bold text-emerald-700">{{ $stats['actifs'] }}</div>
                    <div class="text-xs text-emerald-500 mt-1">Avec contrat actif</div>
                </div>
                <div class="bg-amber-50 border border-amber-100 rounded-xl p-4 text-center">
                    <div class="text-2xl font-bold text-amber-700">{{ $stats['sans_contrat'] }}</div>
                    <div class="text-xs text-amber-500 mt-1">Sans contrat</div>
                </div>
            </div>

            {{-- Liste --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-100">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-400 uppercase">Nom</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-400 uppercase">Email</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-400 uppercase">Téléphone</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-400 uppercase">Profession</th>
                                <th class="px-4 py-3 text-center text-xs font-medium text-gray-400 uppercase">Contrat</th>
                                <th class="px-4 py-3 text-center text-xs font-medium text-gray-400 uppercase">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @forelse($locataires as $user)
                            @php
                                $contratActif = $user->contrats->firstWhere('statut', 'actif');
                            @endphp
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-4 py-3">
                                    <div class="font-medium text-sm text-gray-800">{{ $user->name }}</div>
                                    <div class="text-xs text-gray-400">
                                        Inscrit le {{ $user->created_at->format('d/m/Y') }}
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-600">{{ $user->email }}</td>
                                <td class="px-4 py-3 text-sm text-gray-600">{{ $user->telephone ?? '—' }}</td>
                                <td class="px-4 py-3 text-sm text-gray-600">
                                    {{ $user->locataire?->profession ?? '—' }}
                                </td>
                                <td class="px-4 py-3 text-center">
                                    @if($contratActif)
                                    <span class="px-2 py-1 text-xs rounded-full font-medium bg-emerald-100 text-emerald-700">
                                        {{ $contratActif->bien->reference }}
                                    </span>
                                    @else
                                    <span class="px-2 py-1 text-xs rounded-full font-medium bg-gray-100 text-gray-500">
                                        Sans contrat
                                    </span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <div class="flex justify-center gap-2">
                                        @if($contratActif)
                                        <a href="{{ route('admin.contrats.show', $contratActif) }}"
                                           class="text-indigo-600 hover:text-indigo-800 text-xs font-medium">
                                            Voir contrat
                                        </a>
                                        @else
                                        <a href="{{ route('admin.contrats.create', ['locataire_id' => $user->id]) }}"
                                           class="text-emerald-600 hover:text-emerald-800 text-xs font-medium">
                                            Créer contrat
                                        </a>
                                        @endif
                                        @if(! $contratActif)
                                        <form method="POST" action="{{ route('admin.users.destroy', $user) }}"
                                              onsubmit="return confirm('Supprimer ce locataire ?')">
                                            @csrf @method('DELETE')
                                            <button type="submit"
                                                    class="text-red-500 hover:text-red-700 text-xs font-medium">
                                                Supprimer
                                            </button>
                                        </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-gray-400">
                                    <div class="text-4xl mb-2">👤</div>
                                    <div class="text-sm">Aucun locataire enregistré</div>
                                    <a href="{{ route('admin.users.create', 'locataire') }}"
                                       class="mt-3 inline-block text-indigo-600 text-sm hover:underline">
                                        Ajouter le premier locataire →
                                    </a>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($locataires->hasPages())
                <div class="px-6 py-4 border-t border-gray-100">
                    {{ $locataires->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>