<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div class="flex items-center gap-3">
                <a href="{{ route('biens.index') }}" class="text-gray-400 hover:text-gray-600 transition">←</a>
                <h2 class="font-semibold text-xl text-gray-800">{{ $bien->reference }}</h2>
                <span class="px-2 py-1 text-xs rounded-full font-medium
                    {{ $bien->statut === 'loue'       ? 'bg-emerald-100 text-emerald-700' : '' }}
                    {{ $bien->statut === 'disponible' ? 'bg-blue-100 text-blue-700'       : '' }}
                    {{ $bien->statut === 'en_travaux' ? 'bg-amber-100 text-amber-700'     : '' }}">
                    {{ ucfirst(str_replace('_', ' ', $bien->statut)) }}
                </span>
            </div>
            @can('update', $bien)
            <a href="{{ route('biens.edit', $bien) }}"
               class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium px-4 py-2 rounded-lg transition">
                Modifier
            </a>
            @endcan
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            {{-- Infos principales --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                <h3 class="font-semibold text-gray-800 mb-4">Informations du bien</h3>
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-6">
                    <div>
                        <div class="text-xs text-gray-400 uppercase tracking-wide mb-1">Type</div>
                        <div class="font-semibold text-gray-800">{{ $bien->type }}</div>
                    </div>
                    <div>
                        <div class="text-xs text-gray-400 uppercase tracking-wide mb-1">Adresse</div>
                        <div class="font-semibold text-gray-800">{{ $bien->adresse }}</div>
                        <div class="text-sm text-gray-500">{{ $bien->ville }}</div>
                    </div>
                    <div>
                        <div class="text-xs text-gray-400 uppercase tracking-wide mb-1">Surface</div>
                        <div class="font-semibold text-gray-800">
                            {{ $bien->surface_m2 ? $bien->surface_m2 . ' m²' : '—' }}
                        </div>
                    </div>
                    <div>
                        <div class="text-xs text-gray-400 uppercase tracking-wide mb-1">Pièces</div>
                        <div class="font-semibold text-gray-800">
                            {{ $bien->nombre_pieces ?? '—' }}
                        </div>
                    </div>
                    <div>
                        <div class="text-xs text-gray-400 uppercase tracking-wide mb-1">Loyer mensuel</div>
                        <div class="font-bold text-gray-900">
                            {{ number_format($bien->loyer_mensuel, 0, ',', ' ') }} FCFA
                        </div>
                    </div>
                    <div>
                        <div class="text-xs text-gray-400 uppercase tracking-wide mb-1">Commission agence</div>
                        <div class="font-semibold text-indigo-600">{{ $bien->taux_commission }}%</div>
                        <div class="text-xs text-gray-400">
                            TTC : {{ number_format($bien->loyer_mensuel * ($bien->taux_commission / 100) * 1.18, 0, ',', ' ') }} F
                        </div>
                    </div>
                    @if(auth()->user()->isAdmin())
                    <div>
                        <div class="text-xs text-gray-400 uppercase tracking-wide mb-1">Propriétaire</div>
                        <div class="font-semibold text-gray-800">{{ $bien->proprietaire->name }}</div>
                        <div class="text-sm text-gray-500">{{ $bien->proprietaire->telephone ?? '' }}</div>
                    </div>
                    @endif
                    <div>
                        <div class="text-xs text-gray-400 uppercase tracking-wide mb-1">Total encaissé</div>
                        <div class="font-bold text-emerald-600">
                            {{ number_format($totalEncaisse, 0, ',', ' ') }} FCFA
                        </div>
                    </div>
                </div>
                @if($bien->description)
                <div class="mt-4 pt-4 border-t border-gray-100">
                    <div class="text-xs text-gray-400 uppercase tracking-wide mb-1">Description</div>
                    <div class="text-sm text-gray-600">{{ $bien->description }}</div>
                </div>
                @endif
            </div>

            {{-- ── GALERIE PHOTOS ──────────────────────────────────────────────── --}}
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center">
        <h3 class="font-semibold text-gray-800">
            Photos du bien
            <span class="text-sm font-normal text-gray-400">
                ({{ $bien->photos->count() }} photo(s))
            </span>
        </h3>
    </div>

    {{-- Galerie --}}
    @if($bien->photos->isNotEmpty())
    <div class="p-6">
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4">
            @foreach($bien->photos as $photo)
            <div class="relative group rounded-xl overflow-hidden border-2
                {{ $photo->est_principale ? 'border-indigo-500' : 'border-gray-100' }}">

                {{-- Image --}}
                <img src="{{ $photo->url }}"
                     alt="{{ $bien->reference }}"
                     class="w-full h-40 object-cover">

                {{-- Badge principale --}}
                @if($photo->est_principale)
                <div class="absolute top-2 left-2 bg-indigo-600 text-white text-xs
                            font-medium px-2 py-0.5 rounded-full">
                    Principale
                </div>
                @endif

                {{-- Actions au hover --}}
                <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-40
                            transition-all flex items-center justify-center gap-2 opacity-0
                            group-hover:opacity-100">

                    {{-- Définir comme principale --}}
                    @if(! $photo->est_principale)
                    <form method="POST"
                          action="{{ route('biens.photos.principale', [$bien, $photo]) }}">
                        @csrf @method('PATCH')
                        <button type="submit"
                                class="bg-white text-indigo-600 text-xs font-medium
                                       px-2 py-1 rounded-lg hover:bg-indigo-50 transition">
                            ⭐ Principale
                        </button>
                    </form>
                    @endif

                    {{-- Supprimer --}}
                    <form method="POST"
                          action="{{ route('biens.photos.destroy', [$bien, $photo]) }}"
                          onsubmit="return confirm('Supprimer cette photo ?')">
                        @csrf @method('DELETE')
                        <button type="submit"
                                class="bg-white text-red-600 text-xs font-medium
                                       px-2 py-1 rounded-lg hover:bg-red-50 transition">
                            🗑 Supprimer
                        </button>
                    </form>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Zone upload --}}
    @can('update', $bien)
    <div class="px-6 pb-6 {{ $bien->photos->isNotEmpty() ? 'border-t border-gray-100 pt-6' : 'pt-2' }}">
        <form method="POST"
              action="{{ route('biens.photos.store', $bien) }}"
              enctype="multipart/form-data">
            @csrf

            @if(session('success'))
            <div class="mb-4 bg-emerald-50 border border-emerald-200 text-emerald-700
                        px-4 py-3 rounded-lg text-sm">
                ✅ {{ session('success') }}
            </div>
            @endif

            @if($errors->has('photos') || $errors->has('photos.*'))
            <div class="mb-4 bg-red-50 border border-red-200 text-red-700
                        px-4 py-3 rounded-lg text-sm">
                @foreach($errors->get('photos.*') as $messages)
                    @foreach($messages as $msg)
                    <div>❌ {{ $msg }}</div>
                    @endforeach
                @endforeach
            </div>
            @endif

            {{-- Zone drag & drop --}}
            <label for="photos-input"
                   class="flex flex-col items-center justify-center w-full h-36
                          border-2 border-dashed border-gray-200 rounded-xl
                          cursor-pointer hover:border-indigo-400 hover:bg-indigo-50
                          transition-all group">
                <div class="text-center">
                    <div class="text-3xl mb-2 group-hover:scale-110 transition-transform">
                        📷
                    </div>
                    <div class="text-sm font-medium text-gray-600">
                        Cliquer pour ajouter des photos
                    </div>
                    <div class="text-xs text-gray-400 mt-1">
                        JPG, PNG, WEBP · Max 3 MB par photo · 10 photos max
                    </div>
                </div>
                <input id="photos-input"
                       type="file"
                       name="photos[]"
                       multiple
                       accept="image/jpeg,image/png,image/webp"
                       class="hidden"
                       onchange="previewPhotos(this)">
            </label>

            {{-- Prévisualisation avant upload --}}
            <div id="preview-container"
                 class="hidden grid grid-cols-4 gap-3 mt-4">
            </div>

            <button type="submit"
                    id="btn-upload"
                    class="hidden mt-4 w-full bg-indigo-600 hover:bg-indigo-700
                           text-white text-sm font-medium py-2.5 rounded-xl transition">
                Uploader les photos
            </button>
        </form>
    </div>
    @endcan
</div>

<script>
function previewPhotos(input) {
    const container = document.getElementById('preview-container');
    const btn       = document.getElementById('btn-upload');
    container.innerHTML = '';

    if (input.files.length === 0) {
        container.classList.add('hidden');
        btn.classList.add('hidden');
        return;
    }

    container.classList.remove('hidden');
    container.classList.add('grid');
    btn.classList.remove('hidden');
    btn.textContent = `Uploader ${input.files.length} photo(s)`;

    Array.from(input.files).forEach(file => {
        const reader = new FileReader();
        reader.onload = e => {
            const div = document.createElement('div');
            div.className = 'relative rounded-lg overflow-hidden border border-gray-100';
            div.innerHTML = `
                <img src="${e.target.result}"
                     class="w-full h-24 object-cover">
                <div class="absolute bottom-0 left-0 right-0 bg-black bg-opacity-40
                            text-white text-xs px-1 py-0.5 truncate">
                    ${file.name}
                </div>`;
            container.appendChild(div);
        };
        reader.readAsDataURL(file);
    });
}
</script>

            {{-- Contrat actif --}}
            @if($contratActif)
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                <h3 class="font-semibold text-gray-800 mb-4">Contrat actif</h3>
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
                    <div>
                        <div class="text-xs text-gray-400 uppercase tracking-wide mb-1">Locataire</div>
                        <div class="font-semibold text-gray-800">{{ $contratActif->locataire->name }}</div>
                        <div class="text-sm text-gray-500">{{ $contratActif->locataire->telephone ?? '' }}</div>
                    </div>
                    <div>
                        <div class="text-xs text-gray-400 uppercase tracking-wide mb-1">Début du bail</div>
                        <div class="font-semibold text-gray-800">
                            {{ \Carbon\Carbon::parse($contratActif->date_debut)->format('d/m/Y') }}
                        </div>
                    </div>
                    <div>
                        <div class="text-xs text-gray-400 uppercase tracking-wide mb-1">Loyer contractuel</div>
                        <div class="font-bold text-gray-900">
                            {{ number_format($contratActif->loyer_contractuel, 0, ',', ' ') }} FCFA
                        </div>
                    </div>
                    <div>
                        <div class="text-xs text-gray-400 uppercase tracking-wide mb-1">Caution</div>
                        <div class="font-semibold text-gray-800">
                            {{ number_format($contratActif->caution, 0, ',', ' ') }} FCFA
                        </div>
                    </div>
                </div>
            </div>
            @endif

            {{-- Historique paiements --}}
            @if($bien->contrats->isNotEmpty())
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h3 class="font-semibold text-gray-800">Historique des paiements</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-50">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-400 uppercase">Période</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-400 uppercase">Locataire</th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-400 uppercase">Montant</th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-400 uppercase">Net proprio</th>
                                <th class="px-4 py-3 text-center text-xs font-medium text-gray-400 uppercase">Statut</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @forelse($bien->contrats->flatMap->paiements->sortByDesc('periode')->take(10) as $p)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-4 py-3 text-sm text-gray-800">
                                    {{ \Carbon\Carbon::parse($p->periode)->translatedFormat('F Y') }}
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-600">
                                    {{ $p->contrat->locataire->name }}
                                </td>
                                <td class="px-4 py-3 text-sm font-semibold text-gray-900 text-right">
                                    {{ number_format($p->montant_encaisse, 0, ',', ' ') }} F
                                </td>
                                <td class="px-4 py-3 text-sm font-bold text-emerald-600 text-right">
                                    {{ number_format($p->net_proprietaire, 0, ',', ' ') }} F
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <span class="px-2 py-1 text-xs rounded-full font-medium
                                        {{ $p->statut === 'valide' ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-600' }}">
                                        {{ ucfirst($p->statut) }}
                                    </span>
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
            @endif

            {{-- Suppression --}}
            @can('delete', $bien)
            @if($bien->statut !== 'loue')
            <div class="bg-red-50 rounded-2xl border border-red-100 p-6">
                <h3 class="font-semibold text-red-800 mb-2">Zone dangereuse</h3>
                <p class="text-sm text-red-600 mb-4">
                    La suppression est irréversible. Impossible de supprimer un bien avec un contrat actif.
                </p>
                <form method="POST" action="{{ route('biens.destroy', $bien) }}"
                      onsubmit="return confirm('Supprimer définitivement ce bien ?')">
                    @csrf @method('DELETE')
                    <button type="submit"
                            class="bg-red-600 hover:bg-red-700 text-white text-sm font-medium px-4 py-2 rounded-lg transition">
                        Supprimer ce bien
                    </button>
                </form>
            </div>
            @endif
            @endcan

        </div>
    </div>
</x-app-layout>