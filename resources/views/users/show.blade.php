<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.users.proprietaires') }}"
                   class="text-gray-400 hover:text-gray-600 transition">←</a>
                <div>
                    <h2 class="font-semibold text-xl text-gray-800">{{ $user->name }}</h2>
                    <div class="text-sm text-gray-400">Propriétaire · {{ $user->email }}</div>
                </div>
            </div>
            <a href="{{ route('admin.contrats.create') }}"
               class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium px-4 py-2 rounded-lg transition">
                + Créer un contrat
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            {{-- KPI ──────────────────────────────────────────────────────── --}}
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
                    <div class="text-xs text-gray-400 uppercase tracking-wide mb-1">Biens</div>
                    <div class="text-3xl font-bold text-indigo-600">{{ $stats['nb_biens'] }}</div>
                    <div class="text-xs text-gray-400 mt-1">
                        {{ $stats['nb_biens_loues'] }} loué(s)
                    </div>
                </div>
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
                    <div class="text-xs text-gray-400 uppercase tracking-wide mb-1">Locataires actifs</div>
                    <div class="text-3xl font-bold text-purple-600">{{ $stats['nb_locataires'] }}</div>
                    <div class="text-xs text-gray-400 mt-1">En cours de bail</div>
                </div>
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
                    <div class="text-xs text-gray-400 uppercase tracking-wide mb-1">Loyers encaissés</div>
                    <div class="text-2xl font-bold text-gray-900">
                        {{ number_format($stats['total_loyers'], 0, ',', ' ') }}
                        <span class="text-sm font-normal text-gray-400">F</span>
                    </div>
                    <div class="text-xs text-gray-400 mt-1">{{ $stats['nb_paiements'] }} paiements</div>
                </div>
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
                    <div class="text-xs text-gray-400 uppercase tracking-wide mb-1">Net perçu</div>
                    <div class="text-2xl font-bold text-emerald-600">
                        {{ number_format($stats['total_net'], 0, ',', ' ') }}
                        <span class="text-sm font-normal text-gray-400">F</span>
                    </div>
                    <div class="text-xs text-gray-400 mt-1">
                        Commission : {{ number_format($stats['total_commission'], 0, ',', ' ') }} F
                    </div>
                </div>
            </div>

            {{-- Infos contact ─────────────────────────────────────────────── --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                <h3 class="font-semibold text-gray-800 mb-4">Coordonnées</h3>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 text-sm">
                    <div>
                        <div class="text-xs text-gray-400 uppercase mb-1">Téléphone</div>
                        <div class="font-medium text-gray-800">{{ $user->telephone ?? '—' }}</div>
                    </div>
                    <div>
                        <div class="text-xs text-gray-400 uppercase mb-1">Adresse</div>
                        <div class="font-medium text-gray-800">{{ $user->adresse ?? '—' }}</div>
                    </div>
                    <div>
                        <div class="text-xs text-gray-400 uppercase mb-1">Ville</div>
                        <div class="font-medium text-gray-800">{{ $user->proprietaire?->ville ?? '—' }}</div>
                    </div>
                    <div>
                        <div class="text-xs text-gray-400 uppercase mb-1">Mode paiement préféré</div>
                        <div class="font-medium text-gray-800">
                            {{ ucfirst(str_replace('_', ' ', $user->proprietaire?->mode_paiement_prefere ?? '—')) }}
                        </div>
                    </div>
                    @if($user->proprietaire?->numero_wave)
                    <div>
                        <div class="text-xs text-gray-400 uppercase mb-1">Wave</div>
                        <div class="font-medium text-gray-800">{{ $user->proprietaire->numero_wave }}</div>
                    </div>
                    @endif
                    @if($user->proprietaire?->numero_om)
                    <div>
                        <div class="text-xs text-gray-400 uppercase mb-1">Orange Money</div>
                        <div class="font-medium text-gray-800">{{ $user->proprietaire->numero_om }}</div>
                    </div>
                    @endif
                    @if($user->proprietaire?->banque)
                    <div>
                        <div class="text-xs text-gray-400 uppercase mb-1">Banque</div>
                        <div class="font-medium text-gray-800">{{ $user->proprietaire->banque }}</div>
                    </div>
                    @endif
                    @if($user->proprietaire?->ninea)
                    <div>
                        <div class="text-xs text-gray-400 uppercase mb-1">NINEA</div>
                        <div class="font-medium text-gray-800">{{ $user->proprietaire->ninea }}</div>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Ses biens + locataires actifs ───────────────────────────────── --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center">
                    <h3 class="font-semibold text-gray-800">Biens & Locataires</h3>
                    <a href="{{ route('biens.create') }}"
                       class="text-sm text-indigo-600 hover:underline">
                        + Ajouter un bien →
                    </a>
                </div>
                <div class="divide-y divide-gray-50">
                    @forelse($user->biens as $bien)
                    @php
                        $contratActif = $bien->contrats->firstWhere('statut', 'actif');
                    @endphp
                    <div class="px-6 py-4">
                        <div class="flex justify-between items-start">

                            {{-- Infos bien --}}
                            <div class="flex-1">
                                <div class="flex items-center gap-3 mb-1">
                                    <span class="font-semibold text-gray-800">{{ $bien->reference }}</span>
                                    <span class="text-xs text-gray-400">{{ $bien->type }}</span>
                                    <span class="px-2 py-0.5 text-xs rounded-full font-medium
                                        {{ $bien->statut === 'loue'       ? 'bg-emerald-100 text-emerald-700' : '' }}
                                        {{ $bien->statut === 'disponible' ? 'bg-blue-100 text-blue-700'       : '' }}
                                        {{ $bien->statut === 'en_travaux' ? 'bg-amber-100 text-amber-700'     : '' }}">
                                        {{ ucfirst(str_replace('_', ' ', $bien->statut)) }}
                                    </span>
                                </div>
                                <div class="text-sm text-gray-500">
                                    📍 {{ $bien->adresse }}, {{ $bien->ville }}
                                    @if($bien->surface_m2) · {{ $bien->surface_m2 }} m² @endif
                                </div>
                                <div class="text-sm font-semibold text-gray-900 mt-1">
                                    {{ number_format($bien->loyer_mensuel, 0, ',', ' ') }} FCFA/mois
                                    <span class="text-xs font-normal text-gray-400">
                                        · Commission {{ $bien->taux_commission }}%
                                    </span>
                                </div>
                            </div>

                            {{-- Locataire actif --}}
                            <div class="ml-6 min-w-48">
                                @if($contratActif)
                                <div class="bg-emerald-50 rounded-xl p-3 border border-emerald-100">
                                    <div class="text-xs text-emerald-500 uppercase tracking-wide mb-1">
                                        Locataire actuel
                                    </div>
                                    <div class="font-semibold text-emerald-800 text-sm">
                                        {{ $contratActif->locataire->name }}
                                    </div>
                                    <div class="text-xs text-emerald-600 mt-0.5">
                                        {{ $contratActif->locataire->telephone ?? $contratActif->locataire->email }}
                                    </div>
                                    <div class="text-xs text-emerald-500 mt-1">
                                        Depuis {{ \Carbon\Carbon::parse($contratActif->date_debut)->format('d/m/Y') }}
                                    </div>
                                    <div class="flex gap-2 mt-2">
                                        <a href="{{ route('admin.contrats.show', $contratActif) }}"
                                           class="text-xs text-emerald-700 hover:underline font-medium">
                                            Voir contrat →
                                        </a>
                                        <a href="{{ route('admin.paiements.create', ['contrat_id' => $contratActif->id]) }}"
                                           class="text-xs text-indigo-600 hover:underline font-medium">
                                            + Paiement
                                        </a>
                                    </div>
                                </div>
                                @else
                                <div class="bg-gray-50 rounded-xl p-3 border border-gray-100 text-center">
                                    <div class="text-xs text-gray-400 mb-2">Aucun locataire</div>
                                    <a href="{{ route('admin.contrats.create', ['bien_id' => $bien->id]) }}"
                                       class="text-xs text-indigo-600 hover:underline font-medium">
                                        + Créer un contrat →
                                    </a>
                                </div>
                                @endif
                            </div>

                            {{-- Actions bien --}}
                            <div class="ml-4 flex flex-col gap-1">
                                <a href="{{ route('biens.show', $bien) }}"
                                   class="text-xs text-indigo-600 hover:text-indigo-800 font-medium px-3 py-1.5 bg-indigo-50 rounded-lg text-center">
                                    Détail
                                </a>
                                <a href="{{ route('biens.edit', $bien) }}"
                                   class="text-xs text-gray-600 hover:text-gray-800 font-medium px-3 py-1.5 bg-gray-50 rounded-lg text-center">
                                    Modifier
                                </a>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="px-6 py-8 text-center text-gray-400 text-sm">
                        <div class="text-3xl mb-2">🏠</div>
                        Aucun bien enregistré pour ce propriétaire.
                        <div class="mt-2">
                            <a href="{{ route('biens.create') }}"
                               class="text-indigo-600 hover:underline">
                                Ajouter un bien →
                            </a>
                        </div>
                    </div>
                    @endforelse
                </div>
            </div>

            {{-- Historique paiements ─────────────────────────────────────────── --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h3 class="font-semibold text-gray-800">Historique des paiements</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-50">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-400 uppercase">Référence</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-400 uppercase">Bien</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-400 uppercase">Locataire</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-400 uppercase">Période</th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-400 uppercase">Loyer</th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-400 uppercase">Commission TTC</th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-400 uppercase">Net reçu</th>
                                <th class="px-4 py-3 text-center text-xs font-medium text-gray-400 uppercase">PDF</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @forelse($paiements as $p)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-4 py-3 text-xs font-mono text-gray-400">
                                    {{ $p->reference_paiement }}
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-800">
                                    {{ $p->contrat->bien->reference }}
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-600">
                                    {{ $p->contrat->locataire->name }}
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-600">
                                    {{ \Carbon\Carbon::parse($p->periode)->translatedFormat('F Y') }}
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
                                <td colspan="8" class="px-6 py-8 text-center text-gray-400 text-sm">
                                    Aucun paiement enregistré
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>