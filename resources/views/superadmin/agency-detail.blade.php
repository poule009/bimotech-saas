@extends('layouts.app')
@section('title', $agency->name)
@section('breadcrumb', 'Agence — '.$agency->name)

@section('content')
<div style="padding:0 0 48px">

    {{-- Breadcrumb + actions --}}
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;flex-wrap:wrap;gap:10px">
        <div style="display:flex;align-items:center;gap:8px;font-size:13px;color:#6b7280">
            <a href="{{ route('superadmin.dashboard') }}" style="color:#6b7280;text-decoration:none">Agences</a>
            <svg style="width:12px;height:12px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
            <span style="color:#0d1117;font-weight:600">{{ $agency->name }}</span>
        </div>
        <div style="display:flex;align-items:center;gap:8px">
            {{-- Statut --}}
            @if ($agency->actif)
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                    ● Active
                </span>
            @else
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                    ● Suspendue
                </span>
            @endif

            {{-- Bouton activer/suspendre --}}
            <form
                method="POST"
                action="{{ route('superadmin.agencies.toggle', $agency) }}"
                onsubmit="return confirm('{{ $agency->actif ? 'Suspendre cette agence ?' : 'Activer cette agence ?' }}')"
            >
                @csrf
                @method('PATCH')
                <button
                    type="submit"
                    class="text-sm px-4 py-2 rounded-lg transition font-medium
                        {{ $agency->actif
                            ? 'bg-red-600 hover:bg-red-700 text-white'
                            : 'bg-green-600 hover:bg-green-700 text-white' }}"
                >
                    {{ $agency->actif ? '⏸ Suspendre' : '▶ Activer' }}
                </button>
            </form>

            {{-- Déconnexion --}}
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="text-sm bg-gray-700 hover:bg-gray-600 px-3 py-1 rounded transition">
                    Déconnexion
                </button>
            </form>
        </div>
    </div>

        {{-- Message succès --}}
        @if (session('success'))
            <div class="mb-6 bg-green-50 border border-green-200 text-green-800 rounded-lg px-4 py-3 text-sm">
                {{ session('success') }}
            </div>
        @endif

        {{-- ── Infos générales ── --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">

            {{-- Carte identité --}}
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                <h2 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-4">
                    Identité
                </h2>
                <div class="space-y-3">
                    {{-- Logo --}}
                    <div class="flex items-center gap-3">
                        @if ($agency->logo_path)
                            <img
                                src="{{ Storage::url($agency->logo_path) }}"
                                alt="{{ $agency->name }}"
                                class="h-12 w-12 object-contain rounded-lg border border-gray-200 p-1"
                            >
                        @else
                            <div class="h-12 w-12 rounded-lg border-2 border-dashed border-gray-300 flex items-center justify-center">
                                <span class="text-2xl">🏢</span>
                            </div>
                        @endif
                        <div>
                            <p class="font-semibold text-gray-900">{{ $agency->name }}</p>
                            <p class="text-xs text-gray-400">{{ $agency->slug }}</p>
                        </div>
                    </div>

                    {{-- Couleur --}}
                    @if ($agency->couleur_primaire)
                        <div class="flex items-center gap-2">
                            <div
                                class="h-5 w-5 rounded-full border border-gray-200"
                                style="background-color: {{ $agency->couleur_primaire }}"
                            ></div>
                            <span class="text-sm text-gray-600">{{ $agency->couleur_primaire }}</span>
                        </div>
                    @endif

                    <div class="text-sm text-gray-600 space-y-1 pt-2 border-t border-gray-100">
                        <p>📧 {{ $agency->email }}</p>
                        @if ($agency->telephone)
                            <p>📞 {{ $agency->telephone }}</p>
                        @endif
                        @if ($agency->adresse)
                            <p>📍 {{ $agency->adresse }}</p>
                        @endif
                        <p>🧾 TVA : {{ $agency->taux_tva }}%</p>
                        <p class="text-xs text-gray-400 pt-1">
                            Inscrite le {{ $agency->created_at->format('d/m/Y à H:i') }}
                        </p>
                    </div>
                </div>
            </div>

            {{-- Stats chiffres clés --}}
            <div class="md:col-span-2 grid grid-cols-2 sm:grid-cols-3 gap-4">

                <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5 text-center">
                    <p class="text-3xl font-bold text-gray-800">{{ $stats['nb_users'] }}</p>
                    <p class="text-sm text-gray-500 mt-1">Utilisateurs</p>
                    <p class="text-xs text-gray-400 mt-1">
                        {{ $stats['nb_proprietaires'] }} proprio · {{ $stats['nb_locataires'] }} locataires
                    </p>
                </div>

                <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5 text-center">
                    <p class="text-3xl font-bold text-gray-800">{{ $stats['nb_biens'] }}</p>
                    <p class="text-sm text-gray-500 mt-1">Biens</p>
                    <p class="text-xs text-green-600 mt-1">{{ $stats['nb_biens_loues'] }} loués</p>
                </div>

                <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5 text-center">
                    <p class="text-3xl font-bold text-gray-800">{{ $stats['nb_contrats'] }}</p>
                    <p class="text-sm text-gray-500 mt-1">Contrats actifs</p>
                </div>

                <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5 text-center">
                    <p class="text-2xl font-bold text-gray-800">
                        {{ number_format($stats['total_loyers'], 0, ',', ' ') }}
                    </p>
                    <p class="text-xs text-gray-400">FCFA</p>
                    <p class="text-sm text-gray-500 mt-1">Loyers encaissés</p>
                </div>

                <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5 text-center col-span-2 sm:col-span-2">
                    <p class="text-2xl font-bold text-blue-700">
                        {{ number_format($stats['total_commissions'], 0, ',', ' ') }}
                    </p>
                    <p class="text-xs text-gray-400">FCFA TTC</p>
                    <p class="text-sm text-gray-500 mt-1">Commissions générées</p>
                </div>

            </div>
        </div>

        {{-- ── Liste des utilisateurs ── --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden mb-8">

            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-800">
                    👥 Utilisateurs ({{ $users->count() }})
                </h2>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 text-gray-600 uppercase text-xs">
                        <tr>
                            <th class="px-6 py-3 text-left">Nom</th>
                            <th class="px-6 py-3 text-left">Email</th>
                            <th class="px-6 py-3 text-center">Rôle</th>
                            <th class="px-6 py-3 text-center">Téléphone</th>
                            <th class="px-6 py-3 text-center">Inscrit le</th>
                            <th class="px-6 py-3 text-center">Statut</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($users as $user)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-4 font-medium text-gray-900">
                                    {{ $user->name }}
                                </td>
                                <td class="px-6 py-4 text-gray-600">
                                    {{ $user->email }}
                                </td>
                                <td class="px-6 py-4 text-center">
                                    @php
                                        $roleColors = [
                                            'admin'        => 'bg-purple-100 text-purple-800',
                                            'proprietaire' => 'bg-blue-100 text-blue-800',
                                            'locataire'    => 'bg-green-100 text-green-800',
                                        ];
                                        $roleLabels = [
                                            'admin'        => 'Admin',
                                            'proprietaire' => 'Propriétaire',
                                            'locataire'    => 'Locataire',
                                        ];
                                    @endphp
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $roleColors[$user->role] ?? 'bg-gray-100 text-gray-800' }}">
                                        {{ $roleLabels[$user->role] ?? $user->role }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center text-gray-600">
                                    {{ $user->telephone ?? '—' }}
                                </td>
                                <td class="px-6 py-4 text-center text-gray-500">
                                    {{ $user->created_at->format('d/m/Y') }}
                                </td>
                                <td class="px-6 py-4 text-center">
                                    @if ($user->deleted_at)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-700">
                                            Supprimé
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-700">
                                            Actif
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-8 text-center text-gray-400">
                                    Aucun utilisateur dans cette agence.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- ── Liste des biens ── --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">

            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-800">
                    🏠 Biens ({{ $biens->count() }})
                </h2>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 text-gray-600 uppercase text-xs">
                        <tr>
                            <th class="px-6 py-3 text-left">Référence</th>
                            <th class="px-6 py-3 text-left">Type</th>
                            <th class="px-6 py-3 text-left">Adresse</th>
                            <th class="px-6 py-3 text-center">Propriétaire</th>
                            <th class="px-6 py-3 text-right">Loyer</th>
                            <th class="px-6 py-3 text-center">Statut</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($biens as $bien)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-4 font-mono text-xs text-gray-600">
                                    {{ $bien->reference }}
                                </td>
                                <td class="px-6 py-4 text-gray-800">
                                    {{ $bien->type }}
                                </td>
                                <td class="px-6 py-4 text-gray-600">
                                    {{ $bien->adresse }}, {{ $bien->ville }}
                                </td>
                                <td class="px-6 py-4 text-center text-gray-600">
                                    {{ $bien->proprietaire?->name ?? '—' }}
                                </td>
                                <td class="px-6 py-4 text-right font-medium text-gray-800">
                                    {{ number_format($bien->loyer_mensuel, 0, ',', ' ') }} F
                                </td>
                                <td class="px-6 py-4 text-center">
                                    @php
                                        $statutColors = [
                                            'loue'        => 'bg-green-100 text-green-800',
                                            'disponible'  => 'bg-blue-100 text-blue-800',
                                            'en_travaux'  => 'bg-yellow-100 text-yellow-800',
                                        ];
                                        $statutLabels = [
                                            'loue'        => 'Loué',
                                            'disponible'  => 'Disponible',
                                            'en_travaux'  => 'En travaux',
                                        ];
                                    @endphp
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statutColors[$bien->statut] ?? 'bg-gray-100 text-gray-800' }}">
                                        {{ $statutLabels[$bien->statut] ?? $bien->statut }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-8 text-center text-gray-400">
                                    Aucun bien dans cette agence.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

</div>
@endsection

---

Teste en cliquant sur le bouton **"Détail"** d'une agence depuis :
```
http://127.0.0.1:8000/superadmin/dashboard --}}