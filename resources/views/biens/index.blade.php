<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800">
                @if(auth()->user()->isAdmin()) Tous les biens
                @else Mes biens
                @endif
            </h2>
            @can('create', App\Models\Bien::class)
            <a href="{{ route('biens.create') }}"
               class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium px-4 py-2 rounded-lg transition">
                + Nouveau bien
            </a>
            @endcan
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            @if(session('success'))
            <div class="mb-4 bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-lg text-sm">
                ✅ {{ session('success') }}
            </div>
            @endif

            @if($errors->has('general'))
            <div class="mb-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg text-sm">
                ❌ {{ $errors->first('general') }}
            </div>
            @endif

            {{-- Grille des biens --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse($biens as $bien)
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm hover:shadow-md transition overflow-hidden">

                    {{-- Badge statut --}}
                    <div class="px-5 pt-5 flex justify-between items-start">
                        <div>
                            <div class="font-bold text-gray-900 text-lg">{{ $bien->reference }}</div>
                            <div class="text-sm text-gray-500 mt-0.5">{{ $bien->type }}</div>
                        </div>
                        <span class="px-2 py-1 text-xs rounded-full font-medium
                            {{ $bien->statut === 'loue'       ? 'bg-emerald-100 text-emerald-700' : '' }}
                            {{ $bien->statut === 'disponible' ? 'bg-blue-100 text-blue-700'       : '' }}
                            {{ $bien->statut === 'en_travaux' ? 'bg-amber-100 text-amber-700'     : '' }}">
                            {{ ucfirst(str_replace('_', ' ', $bien->statut)) }}
                        </span>
                    </div>

                    @if($bien->photoPrincipale)
<div class="w-full h-40 overflow-hidden">
    <img src="{{ $bien->photoPrincipale->url }}"
         alt="{{ $bien->reference }}"
         class="w-full h-full object-cover hover:scale-105 transition-transform duration-300">
</div>
@else
<div class="w-full h-40 bg-gradient-to-br from-indigo-50 to-gray-100
            flex items-center justify-center">
    <span class="text-4xl">🏠</span>
</div>
@endif

                    {{-- Infos --}}
                    <div class="px-5 py-4 space-y-1">
                        <div class="text-sm text-gray-600">
                            📍 {{ $bien->adresse }}, {{ $bien->ville }}
                        </div>
                        @if($bien->surface_m2 || $bien->nombre_pieces)
                        <div class="text-sm text-gray-500">
                            @if($bien->surface_m2) {{ $bien->surface_m2 }} m² @endif
                            @if($bien->nombre_pieces) · {{ $bien->nombre_pieces }} pièces @endif
                        </div>
                        @endif
                        @if(auth()->user()->isAdmin())
                        <div class="text-sm text-gray-500">
                            👤 {{ $bien->proprietaire->name }}
                        </div>
                        @endif
                    </div>

                    {{-- Loyer + commission --}}
                    <div class="px-5 pb-4 flex justify-between items-center border-t border-gray-50 pt-3">
                        <div>
                            <div class="font-bold text-gray-900">
                                {{ number_format($bien->loyer_mensuel, 0, ',', ' ') }} F
                                <span class="text-xs font-normal text-gray-400">/mois</span>
                            </div>
                            <div class="text-xs text-gray-400">
                                Commission : {{ $bien->taux_commission }}%
                            </div>
                        </div>
                        <div class="flex gap-2">
                            <a href="{{ route('biens.show', $bien) }}"
                               class="text-indigo-600 hover:text-indigo-800 text-sm font-medium px-3 py-1.5 bg-indigo-50 hover:bg-indigo-100 rounded-lg transition">
                                Voir
                            </a>
                            @can('update', $bien)
                            <a href="{{ route('biens.edit', $bien) }}"
                               class="text-gray-600 hover:text-gray-800 text-sm font-medium px-3 py-1.5 bg-gray-50 hover:bg-gray-100 rounded-lg transition">
                                Modifier
                            </a>
                            @endcan
                        </div>
                    </div>
                </div>
                @empty
                <div class="col-span-3 bg-white rounded-2xl border border-gray-100 p-12 text-center">
                    <div class="text-5xl mb-4">🏠</div>
                    <div class="text-lg font-semibold text-gray-800 mb-2">Aucun bien enregistré</div>
                    @can('create', App\Models\Bien::class)
                    <a href="{{ route('biens.create') }}"
                       class="mt-4 inline-block bg-indigo-600 text-white text-sm font-medium px-5 py-2.5 rounded-lg hover:bg-indigo-700 transition">
                        Ajouter le premier bien
                    </a>
                    @endcan
                </div>
                @endforelse
            </div>

            {{-- Pagination --}}
            @if($biens->hasPages())
            <div class="mt-6">{{ $biens->links() }}</div>
            @endif

        </div>
    </div>
</x-app-layout>