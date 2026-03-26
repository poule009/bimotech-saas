<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800">Propriétaires</h2>
            <a href="{{ route('admin.users.create', 'proprietaire') }}"
               class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium px-4 py-2 rounded-lg transition">
                + Nouveau propriétaire
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
                    <div class="text-2xl font-bold text-indigo-600">{{ $stats['total'] }}</div>
                    <div class="text-xs text-gray-400 mt-1">Propriétaires</div>
                </div>
                <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4 text-center">
                    <div class="text-2xl font-bold text-gray-900">{{ $stats['total_biens'] }}</div>
                    <div class="text-xs text-gray-400 mt-1">Biens total</div>
                </div>
                <div class="bg-emerald-50 border border-emerald-100 rounded-xl p-4 text-center">
                    <div class="text-2xl font-bold text-emerald-700">{{ $stats['biens_loues'] }}</div>
                    <div class="text-xs text-emerald-500 mt-1">Biens loués</div>
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
                                <th class="px-4 py-3 text-center text-xs font-medium text-gray-400 uppercase">Nb biens</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-400 uppercase">Ville</th>
                                <th class="px-4 py-3 text-center text-xs font-medium text-gray-400 uppercase">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @forelse($proprietaires as $user)
                            <tr class="hover:bg-gray-50 transition cursor-pointer"
    onclick="window.location='{{ route('admin.users.show', $user) }}'">
                                <td class="px-4 py-3">
                                    <div class="font-medium text-sm text-gray-800">{{ $user->name }}</div>
                                    <div class="text-xs text-gray-400">
                                        Inscrit le {{ $user->created_at->format('d/m/Y') }}
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-600">{{ $user->email }}</td>
                                <td class="px-4 py-3 text-sm text-gray-600">{{ $user->telephone ?? '—' }}</td>
                                <td class="px-4 py-3 text-center">
                                    <span class="px-2 py-1 text-xs rounded-full font-medium
                                        {{ $user->biens_count > 0 ? 'bg-indigo-100 text-indigo-700' : 'bg-gray-100 text-gray-500' }}">
                                        {{ $user->biens_count }} bien(s)
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-600">
                                    {{ $user->proprietaire?->ville ?? '—' }}
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <div class="flex justify-center gap-2">
                                        <a href="{{ route('biens.index') }}?proprietaire={{ $user->id }}"
                                           class="text-indigo-600 hover:text-indigo-800 text-xs font-medium">
                                            Voir biens
                                        </a>
                                        @if($user->biens_count === 0)
                                        <form method="POST" action="{{ route('admin.users.destroy', $user) }}"
                                              onsubmit="return confirm('Supprimer ce propriétaire ?')">
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
                                    <div class="text-sm">Aucun propriétaire enregistré</div>
                                    <a href="{{ route('admin.users.create', 'proprietaire') }}"
                                       class="mt-3 inline-block text-indigo-600 text-sm hover:underline">
                                        Ajouter le premier propriétaire →
                                    </a>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($proprietaires->hasPages())
                <div class="px-6 py-4 border-t border-gray-100">
                    {{ $proprietaires->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>