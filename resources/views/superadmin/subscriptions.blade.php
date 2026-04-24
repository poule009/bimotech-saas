@extends('layouts.app')
@section('title', 'Abonnements')
@section('breadcrumb', 'Abonnements')

@section('content')
<div style="padding:0 0 48px">

    <div style="margin-bottom:20px">
        <div style="font-family:'Syne',sans-serif;font-size:18px;font-weight:700;color:#0d1117">Gestion des abonnements</div>
        <div style="font-size:12px;color:#9ca3af;margin-top:2px">Toutes les agences de la plateforme</div>
    </div>

        {{-- Message succès --}}
        @if (session('success'))
            <div class="mb-6 bg-green-50 border border-green-200 text-green-800 rounded-lg px-4 py-3 text-sm">
                ✅ {{ session('success') }}
            </div>
        @endif

        {{-- ── Stats globales abonnements ── --}}
        <div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-8">

            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5 text-center">
                <p class="text-3xl font-bold text-blue-600">{{ $stats['nb_essai'] }}</p>
                <p class="text-sm text-gray-500 mt-1">En essai</p>
            </div>

            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5 text-center">
                <p class="text-3xl font-bold text-green-600">{{ $stats['nb_actifs'] }}</p>
                <p class="text-sm text-gray-500 mt-1">Abonnés actifs</p>
            </div>

            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5 text-center">
                <p class="text-3xl font-bold text-red-500">{{ $stats['nb_expires'] }}</p>
                <p class="text-sm text-gray-500 mt-1">Expirés</p>
            </div>

            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5 text-center">
                <p class="text-xl font-bold text-gray-800">
                    {{ number_format($stats['revenus_total'], 0, ',', ' ') }}
                </p>
                <p class="text-xs text-gray-400">FCFA</p>
                <p class="text-sm text-gray-500 mt-1">Revenus encaissés</p>
            </div>

            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5 text-center">
                <p class="text-xl font-bold text-indigo-600">
                    {{ number_format($stats['revenus_mensuel_equiv'], 0, ',', ' ') }}
                </p>
                <p class="text-xs text-gray-400">FCFA</p>
                <p class="text-sm text-gray-500 mt-1">MRR estimé</p>
            </div>

        </div>

        {{-- ── Table des abonnements ── --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">

            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-800">Toutes les agences</h2>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 text-gray-600 uppercase text-xs">
                        <tr>
                            <th class="px-6 py-3 text-left">Agence</th>
                            <th class="px-6 py-3 text-center">Statut</th>
                            <th class="px-6 py-3 text-center">Plan</th>
                            <th class="px-6 py-3 text-center">Début</th>
                            <th class="px-6 py-3 text-center">Expiration</th>
                            <th class="px-6 py-3 text-center">Jours restants</th>
                            <th class="px-6 py-3 text-right">Montant payé</th>
                            <th class="px-6 py-3 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($subscriptions as $sub)
                            <tr class="hover:bg-gray-50 transition">

                                {{-- Agence --}}
                                <td class="px-6 py-4">
                                    <p class="font-semibold text-gray-900">{{ $sub->agency->name }}</p>
                                    <p class="text-xs text-gray-400">{{ $sub->agency->email }}</p>
                                </td>

                                {{-- Statut --}}
                                <td class="px-6 py-4 text-center">
                                    @if ($sub->estEnEssai())
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            🕐 Essai
                                        </span>
                                    @elseif ($sub->estActif())
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            ✅ Actif
                                        </span>
                                    @elseif ($sub->statut === 'expiré')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                            ❌ Expiré
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                            {{ $sub->statut }}
                                        </span>
                                    @endif
                                </td>

                                {{-- Plan --}}
                                <td class="px-6 py-4 text-center text-gray-600">
                                    {{ \App\Models\Subscription::LABELS[$sub->plan] ?? '—' }}
                                </td>

                                {{-- Début --}}
                                <td class="px-6 py-4 text-center text-gray-500">
                                    @if ($sub->estEnEssai())
                                        {{ $sub->date_debut_essai?->format('d/m/Y') }}
                                    @else
                                        {{ $sub->date_debut_abonnement?->format('d/m/Y') ?? '—' }}
                                    @endif
                                </td>

                                {{-- Expiration --}}
                                <td class="px-6 py-4 text-center text-gray-500">
                                    @if ($sub->estEnEssai())
                                        {{ $sub->date_fin_essai?->format('d/m/Y') }}
                                    @elseif ($sub->estActif())
                                        {{ $sub->date_fin_abonnement?->format('d/m/Y') }}
                                    @else
                                        —
                                    @endif
                                </td>

                                {{-- Jours restants --}}
                                <td class="px-6 py-4 text-center">
                                    @if ($sub->estEnEssai())
                                        @php $jours = $sub->joursRestantsEssai() @endphp
                                        <span class="font-semibold {{ $jours <= 7 ? 'text-amber-600' : 'text-blue-600' }}">
                                            {{ $jours }}j
                                        </span>
                                    @elseif ($sub->estActif())
                                        @php $jours = $sub->joursRestantsAbonnement() @endphp
                                        <span class="font-semibold {{ $jours <= 7 ? 'text-amber-600' : 'text-green-600' }}">
                                            {{ $jours }}j
                                        </span>
                                    @else
                                        <span class="text-gray-400">—</span>
                                    @endif
                                </td>

                                {{-- Montant payé --}}
                                <td class="px-6 py-4 text-right font-medium text-gray-800">
                                    @if ($sub->montant_paye)
                                        {{ number_format($sub->montant_paye, 0, ',', ' ') }} F
                                    @else
                                        <span class="text-gray-400">—</span>
                                    @endif
                                </td>

                                {{-- Actions --}}
                                <td class="px-6 py-4">
                                    <div class="flex items-center justify-center gap-2 flex-wrap">

                                        {{-- Activer un abonnement --}}
                                        <div x-data="{ open: false }" class="relative">
                                            <button
                                                @click="open = !open"
                                                class="text-xs bg-green-100 hover:bg-green-200 text-green-800 px-3 py-1 rounded transition font-medium"
                                            >
                                                ✅ Activer
                                            </button>

                                            {{-- Dropdown choix du plan --}}
                                            <div
                                                x-show="open"
                                                @click.outside="open = false"
                                                class="absolute right-0 mt-1 w-48 bg-white border border-gray-200 rounded-lg shadow-lg z-10"
                                            >
                                                @foreach (\App\Models\Subscription::LABELS as $plan => $label)
                                                    <form
                                                        method="POST"
                                                        action="{{ route('superadmin.agencies.abonnement.activer', $sub->agency) }}"
                                                        onsubmit="return confirm('Activer le plan {{ $label }} pour {{ $sub->agency->name }} ?')"
                                                    >
                                                        @csrf
                                                        <input type="hidden" name="plan" value="{{ $plan }}">
                                                        <button
                                                            type="submit"
                                                            class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition"
                                                        >
                                                            {{ $label }}
                                                            <span class="text-gray-400 text-xs">
                                                                — {{ number_format(\App\Models\Subscription::TARIFS[$plan], 0, ',', ' ') }} F
                                                            </span>
                                                        </button>
                                                    </form>
                                                @endforeach
                                            </div>
                                        </div>

                                        {{-- Réinitialiser l'essai --}}
                                        <form
                                            method="POST"
                                            action="{{ route('superadmin.agencies.essai.reinitialiser', $sub->agency) }}"
                                            onsubmit="return confirm('Réinitialiser l\'essai de {{ $sub->agency->name }} ?')"
                                        >
                                            @csrf
                                            <button
                                                type="submit"
                                                class="text-xs bg-blue-100 hover:bg-blue-200 text-blue-800 px-3 py-1 rounded transition font-medium"
                                            >
                                                🔄 Essai
                                            </button>
                                        </form>

                                    </div>
                                </td>

                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-6 py-8 text-center text-gray-400">
                                    Aucun abonnement enregistré.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>

</div>
@endsection