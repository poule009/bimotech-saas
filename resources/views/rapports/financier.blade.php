<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-xl text-gray-800">Rapport Financier</h2>
                <p class="text-sm text-gray-400 mt-0.5">
                    {{ $debutMois->translatedFormat('F Y') }}
                </p>
            </div>
            {{-- Filtre mois/année --}}
            <form method="GET" action="{{ route('admin.rapports.financier') }}"
                  class="flex items-center gap-2">
                <select name="mois"
                        class="border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500">
                    @foreach(range(1, 12) as $m)
                    <option value="{{ $m }}" {{ $mois == $m ? 'selected' : '' }}>
                        {{ \Carbon\Carbon::create(null, $m)->translatedFormat('F') }}
                    </option>
                    @endforeach
                </select>
                <select name="annee"
                        class="border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500">
                    @foreach($anneesDisponibles as $a)
                    <option value="{{ $a }}" {{ $annee == $a ? 'selected' : '' }}>{{ $a }}</option>
                    @endforeach
                </select>
                <button type="submit"
                        class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium px-4 py-2 rounded-lg transition">
                    Filtrer
                </button>
            </form>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">

            {{-- ── KPI DU MOIS ──────────────────────────────────────────── --}}
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-4">
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
                    <div class="text-xs text-gray-400 uppercase tracking-wide mb-1">Loyers encaissés</div>
                    <div class="text-2xl font-bold text-gray-900">
                        {{ number_format($kpiMois['total_loyers'], 0, ',', ' ') }}
                        <span class="text-sm font-normal text-gray-400">F</span>
                    </div>
                    <div class="text-xs text-gray-400 mt-1">{{ $kpiMois['nb_paiements'] }} paiements</div>
                </div>

                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
                    <div class="text-xs text-gray-400 uppercase tracking-wide mb-1">Commission HT</div>
                    <div class="text-2xl font-bold text-indigo-600">
                        {{ number_format($kpiMois['total_commission'], 0, ',', ' ') }}
                        <span class="text-sm font-normal text-gray-400">F</span>
                    </div>
                </div>

                <div class="bg-amber-50 rounded-2xl border border-amber-100 shadow-sm p-5">
                    <div class="text-xs text-amber-500 uppercase tracking-wide mb-1">TVA à reverser DGI</div>
                    <div class="text-2xl font-bold text-amber-700">
                        {{ number_format($kpiMois['total_tva'], 0, ',', ' ') }}
                        <span class="text-sm font-normal text-amber-400">F</span>
                    </div>
                    <div class="text-xs text-amber-500 mt-1">18% sur commission</div>
                </div>

                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
                    <div class="text-xs text-gray-400 uppercase tracking-wide mb-1">Commission TTC</div>
                    <div class="text-2xl font-bold text-gray-900">
                        {{ number_format($kpiMois['total_ttc'], 0, ',', ' ') }}
                        <span class="text-sm font-normal text-gray-400">F</span>
                    </div>
                </div>

                <div class="bg-emerald-50 rounded-2xl border border-emerald-100 shadow-sm p-5">
                    <div class="text-xs text-emerald-500 uppercase tracking-wide mb-1">Net propriétaires</div>
                    <div class="text-2xl font-bold text-emerald-700">
                        {{ number_format($kpiMois['total_net_proprio'], 0, ',', ' ') }}
                        <span class="text-sm font-normal text-emerald-400">F</span>
                    </div>
                </div>
            </div>

            {{-- ── ALERTE IMPAYÉS ───────────────────────────────────────── --}}
            @if($biensImpayés->isNotEmpty())
            <div class="bg-red-50 border border-red-200 rounded-2xl p-6">
                <div class="flex items-center gap-2 mb-4">
                    <span class="text-red-600 text-lg">⚠️</span>
                    <h3 class="font-semibold text-red-800">
                        {{ $biensImpayés->count() }} contrat(s) sans paiement ce mois
                    </h3>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                    @foreach($biensImpayés as $contrat)
                    <div class="bg-white rounded-xl border border-red-100 p-3">
                        <div class="font-medium text-sm text-gray-800">{{ $contrat->bien->reference }}</div>
                        <div class="text-xs text-gray-500">{{ $contrat->locataire->name }}</div>
                        <div class="text-xs text-gray-400">{{ $contrat->locataire->telephone ?? $contrat->locataire->email }}</div>
                        <div class="text-sm font-semibold text-red-600 mt-1">
                            {{ number_format($contrat->loyer_contractuel, 0, ',', ' ') }} F attendu
                        </div>
                        <a href="{{ route('admin.paiements.create', ['contrat_id' => $contrat->id]) }}"
                           class="mt-2 inline-block text-xs text-indigo-600 hover:underline font-medium">
                            Enregistrer paiement →
                        </a>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- ── ÉVOLUTION 12 MOIS ────────────────────────────────────── --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h3 class="font-semibold text-gray-800">Évolution sur 12 mois</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-50">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-400 uppercase">Mois</th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-400 uppercase">Paiements</th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-400 uppercase">Loyers</th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-400 uppercase">Commission HT</th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-400 uppercase">TVA</th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-400 uppercase">Commission TTC</th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-400 uppercase">Net proprio</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @forelse($evolution as $ligne)
                            <tr class="hover:bg-gray-50 transition
                                {{ $ligne->mois === now()->format('Y-m') ? 'bg-indigo-50' : '' }}">
                                <td class="px-4 py-3 text-sm font-medium text-gray-800">
                                    {{ $ligne->mois_label }}
                                    @if($ligne->mois === now()->format('Y-m'))
                                    <span class="ml-1 px-1.5 py-0.5 text-xs bg-indigo-100 text-indigo-600 rounded">
                                        En cours
                                    </span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-600 text-right">{{ $ligne->nb_paiements }}</td>
                                <td class="px-4 py-3 text-sm font-semibold text-gray-900 text-right">
                                    {{ number_format($ligne->loyers, 0, ',', ' ') }} F
                                </td>
                                <td class="px-4 py-3 text-sm text-indigo-600 text-right">
                                    {{ number_format($ligne->commissions, 0, ',', ' ') }} F
                                </td>
                                <td class="px-4 py-3 text-sm text-amber-600 text-right">
                                    {{ number_format($ligne->tva, 0, ',', ' ') }} F
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-700 text-right">
                                    {{ number_format($ligne->ttc, 0, ',', ' ') }} F
                                </td>
                                <td class="px-4 py-3 text-sm font-bold text-emerald-600 text-right">
                                    {{ number_format($ligne->net, 0, ',', ' ') }} F
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="px-6 py-8 text-center text-gray-400 text-sm">
                                    Aucune donnée disponible
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                        {{-- Totaux --}}
                        @if($evolution->isNotEmpty())
                        <tfoot>
                            <tr class="bg-gray-800">
                                <td class="px-4 py-3 text-sm font-bold text-white">TOTAL</td>
                                <td class="px-4 py-3 text-sm font-bold text-white text-right">
                                    {{ $evolution->sum('nb_paiements') }}
                                </td>
                                <td class="px-4 py-3 text-sm font-bold text-white text-right">
                                    {{ number_format($evolution->sum('loyers'), 0, ',', ' ') }} F
                                </td>
                                <td class="px-4 py-3 text-sm font-bold text-white text-right">
                                    {{ number_format($evolution->sum('commissions'), 0, ',', ' ') }} F
                                </td>
                                <td class="px-4 py-3 text-sm font-bold text-amber-300 text-right">
                                    {{ number_format($evolution->sum('tva'), 0, ',', ' ') }} F
                                </td>
                                <td class="px-4 py-3 text-sm font-bold text-white text-right">
                                    {{ number_format($evolution->sum('ttc'), 0, ',', ' ') }} F
                                </td>
                                <td class="px-4 py-3 text-sm font-bold text-emerald-300 text-right">
                                    {{ number_format($evolution->sum('net'), 0, ',', ' ') }} F
                                </td>
                            </tr>
                        </tfoot>
                        @endif
                    </table>
                </div>
            </div>

            {{-- ── PAR PROPRIÉTAIRE ─────────────────────────────────────── --}}
            @if($parProprietaire->isNotEmpty())
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h3 class="font-semibold text-gray-800">
                        Détail par propriétaire —
                        {{ $debutMois->translatedFormat('F Y') }}
                    </h3>
                </div>
                <div class="divide-y divide-gray-50">
                    @foreach($parProprietaire as $data)
                    <div class="px-6 py-4">
                        <div class="flex justify-between items-start mb-3">
                            <div>
                                <div class="font-semibold text-gray-800">{{ $data['proprio']->name }}</div>
                                <div class="text-xs text-gray-400">{{ $data['proprio']->email }}</div>
                            </div>
                            <div class="text-right">
                                <div class="font-bold text-emerald-600">
                                    Net : {{ number_format($data['net'], 0, ',', ' ') }} F
                                </div>
                                <div class="text-xs text-gray-400">
                                    Commission TTC : {{ number_format($data['commission'], 0, ',', ' ') }} F
                                </div>
                            </div>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="min-w-full text-sm">
                                <thead>
                                    <tr class="text-xs text-gray-400">
                                        <th class="text-left pb-1">Bien</th>
                                        <th class="text-left pb-1">Locataire</th>
                                        <th class="text-right pb-1">Loyer</th>
                                        <th class="text-right pb-1">Commission TTC</th>
                                        <th class="text-right pb-1">Net</th>
                                        <th class="text-center pb-1">PDF</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($data['paiements'] as $p)
                                    <tr class="border-t border-gray-50">
                                        <td class="py-1.5 text-gray-700">{{ $p->contrat->bien->reference }}</td>
                                        <td class="py-1.5 text-gray-600">{{ $p->contrat->locataire->name }}</td>
                                        <td class="py-1.5 text-right font-medium text-gray-900">
                                            {{ number_format($p->montant_encaisse, 0, ',', ' ') }} F
                                        </td>
                                        <td class="py-1.5 text-right text-amber-600">
                                            {{ number_format($p->commission_ttc, 0, ',', ' ') }} F
                                        </td>
                                        <td class="py-1.5 text-right font-bold text-emerald-600">
                                            {{ number_format($p->net_proprietaire, 0, ',', ' ') }} F
                                        </td>
                                        <td class="py-1.5 text-center">
                                            <a href="{{ route('admin.paiements.pdf', $p) }}"
                                               target="_blank"
                                               class="text-indigo-600 hover:text-indigo-800 text-xs">
                                                📄
                                            </a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- ── STATS GÉNÉRALES ──────────────────────────────────────── --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                <h3 class="font-semibold text-gray-800 mb-4">État général du parc</h3>
                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4">
                    <div class="text-center">
                        <div class="text-2xl font-bold text-gray-900">{{ $statsGenerales['nb_biens'] }}</div>
                        <div class="text-xs text-gray-400 mt-1">Biens total</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-emerald-600">{{ $statsGenerales['nb_biens_loues'] }}</div>
                        <div class="text-xs text-gray-400 mt-1">Biens loués</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-indigo-600">{{ $statsGenerales['taux_occupation'] }}%</div>
                        <div class="text-xs text-gray-400 mt-1">Taux occupation</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-blue-600">{{ $statsGenerales['nb_contrats'] }}</div>
                        <div class="text-xs text-gray-400 mt-1">Contrats actifs</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-purple-600">{{ $statsGenerales['nb_proprietaires'] }}</div>
                        <div class="text-xs text-gray-400 mt-1">Propriétaires</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-pink-600">{{ $statsGenerales['nb_locataires'] }}</div>
                        <div class="text-xs text-gray-400 mt-1">Locataires</div>
                    </div>
                </div>
                {{-- Barre taux occupation --}}
                <div class="mt-4">
                    <div class="flex justify-between text-xs text-gray-400 mb-1">
                        <span>Taux d'occupation</span>
                        <span>{{ $statsGenerales['taux_occupation'] }}%</span>
                    </div>
                    <div class="w-full bg-gray-100 rounded-full h-3">
                        <div class="h-3 rounded-full transition-all
                            {{ $statsGenerales['taux_occupation'] >= 80 ? 'bg-emerald-500' : '' }}
                            {{ $statsGenerales['taux_occupation'] >= 50 && $statsGenerales['taux_occupation'] < 80 ? 'bg-amber-500' : '' }}
                            {{ $statsGenerales['taux_occupation'] < 50 ? 'bg-red-500' : '' }}"
                             style="width: {{ $statsGenerales['taux_occupation'] }}%">
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>