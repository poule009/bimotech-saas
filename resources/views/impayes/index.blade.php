<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-xl text-gray-800">Gestion des impayés</h2>
                <p class="text-sm text-gray-400 mt-0.5">
                    {{ $periode->translatedFormat('F Y') }}
                </p>
            </div>
            {{-- Filtre --}}
            <form method="GET" action="{{ route('admin.impayes.index') }}"
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
                    @foreach(range(now()->year - 1, now()->year) as $a)
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
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            @if(session('success'))
            <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-lg text-sm">
                ✅ {{ session('success') }}
            </div>
            @endif

            @if($errors->has('general'))
            <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg text-sm">
                ❌ {{ $errors->first('general') }}
            </div>
            @endif

            {{-- KPI ──────────────────────────────────────────────────────── --}}
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                <div class="bg-red-50 border border-red-100 rounded-2xl p-5">
                    <div class="text-xs text-red-400 uppercase tracking-wide mb-1">Impayés</div>
                    <div class="text-3xl font-bold text-red-700">{{ $stats['nb_impayes'] }}</div>
                    <div class="text-xs text-red-400 mt-1">contrats en retard</div>
                </div>
                <div class="bg-emerald-50 border border-emerald-100 rounded-2xl p-5">
                    <div class="text-xs text-emerald-400 uppercase tracking-wide mb-1">Payés</div>
                    <div class="text-3xl font-bold text-emerald-700">{{ $stats['nb_payes'] }}</div>
                    <div class="text-xs text-emerald-400 mt-1">paiements reçus</div>
                </div>
                <div class="bg-amber-50 border border-amber-100 rounded-2xl p-5">
                    <div class="text-xs text-amber-400 uppercase tracking-wide mb-1">Montant dû</div>
                    <div class="text-2xl font-bold text-amber-700">
                        {{ number_format($stats['montant_du'], 0, ',', ' ') }}
                        <span class="text-sm font-normal">F</span>
                    </div>
                </div>
                <div class="bg-white border border-gray-100 rounded-2xl p-5">
                    <div class="text-xs text-gray-400 uppercase tracking-wide mb-1">Taux recouvrement</div>
                    <div class="text-3xl font-bold
                        {{ $stats['taux_recouvrement'] >= 80 ? 'text-emerald-600' : '' }}
                        {{ $stats['taux_recouvrement'] >= 50 && $stats['taux_recouvrement'] < 80 ? 'text-amber-600' : '' }}
                        {{ $stats['taux_recouvrement'] < 50 ? 'text-red-600' : '' }}">
                        {{ $stats['taux_recouvrement'] }}%
                    </div>
                    <div class="w-full bg-gray-100 rounded-full h-2 mt-2">
                        <div class="h-2 rounded-full
                            {{ $stats['taux_recouvrement'] >= 80 ? 'bg-emerald-500' : '' }}
                            {{ $stats['taux_recouvrement'] >= 50 && $stats['taux_recouvrement'] < 80 ? 'bg-amber-500' : '' }}
                            {{ $stats['taux_recouvrement'] < 50 ? 'bg-red-500' : '' }}"
                             style="width: {{ $stats['taux_recouvrement'] }}%">
                        </div>
                    </div>
                </div>
            </div>

            {{-- IMPAYÉS ───────────────────────────────────────────────────── --}}
            @if($impayes->isNotEmpty())
            <div class="bg-white rounded-2xl border border-red-100 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-red-100 bg-red-50 flex justify-between items-center">
                    <h3 class="font-semibold text-red-800">
                        ⚠️ Loyers impayés — {{ $impayes->count() }} contrat(s)
                    </h3>
                    <span class="text-sm text-red-600 font-medium">
                        Total dû : {{ number_format($stats['montant_du'], 0, ',', ' ') }} FCFA
                    </span>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-50">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-400 uppercase">Bien</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-400 uppercase">Locataire</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-400 uppercase">Téléphone</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-400 uppercase">Propriétaire</th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-400 uppercase">Montant dû</th>
                                <th class="px-4 py-3 text-center text-xs font-medium text-gray-400 uppercase">Retard</th>
                                <th class="px-4 py-3 text-center text-xs font-medium text-gray-400 uppercase">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @foreach($impayes as $item)
                            <tr class="hover:bg-red-50 transition">
                                <td class="px-4 py-3">
                                    <div class="font-medium text-sm text-gray-800">
                                        {{ $item['contrat']->bien->reference }}
                                    </div>
                                    <div class="text-xs text-gray-400">
                                        {{ $item['contrat']->bien->ville }}
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="text-sm font-medium text-gray-800">
                                        {{ $item['contrat']->locataire->name }}
                                    </div>
                                    <div class="text-xs text-gray-400">
                                        {{ $item['contrat']->locataire->email }}
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-600">
                                    {{ $item['contrat']->locataire->telephone ?? '—' }}
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-600">
                                    {{ $item['contrat']->bien->proprietaire->name }}
                                </td>
                                <td class="px-4 py-3 text-sm font-bold text-red-600 text-right">
                                    {{ number_format($item['montant_du'], 0, ',', ' ') }} F
                                </td>
                                <td class="px-4 py-3 text-center">
                                    @if($item['jours_retard'] > 0)
                                    <span class="px-2 py-1 text-xs rounded-full font-medium
                                        {{ $item['jours_retard'] > 15 ? 'bg-red-100 text-red-700' : 'bg-amber-100 text-amber-700' }}">
                                        {{ $item['jours_retard'] }}j de retard
                                    </span>
                                    @else
                                    <span class="px-2 py-1 text-xs rounded-full font-medium bg-gray-100 text-gray-500">
                                        Pas encore dû
                                    </span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <div class="flex justify-center gap-2">
                                        {{-- Enregistrer paiement --}}
                                        <a href="{{ route('admin.paiements.create', ['contrat_id' => $item['contrat']->id]) }}"
                                           class="text-xs text-emerald-600 hover:text-emerald-800 font-medium px-2 py-1 bg-emerald-50 rounded-lg">
                                            + Paiement
                                        </a>
                                        {{-- Envoyer relance --}}
                                        <form method="POST"
                                              action="{{ route('admin.impayes.relance', $item['contrat']) }}">
                                            @csrf
                                            <input type="hidden" name="mois" value="{{ $mois }}">
                                            <input type="hidden" name="annee" value="{{ $annee }}">
                                            <button type="submit"
                                                    class="text-xs text-indigo-600 hover:text-indigo-800 font-medium px-2 py-1 bg-indigo-50 rounded-lg">
                                                📧 Relance
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @else
            <div class="bg-emerald-50 border border-emerald-200 rounded-2xl p-8 text-center">
                <div class="text-4xl mb-2">🎉</div>
                <div class="font-semibold text-emerald-800 text-lg">Tous les loyers sont payés !</div>
                <div class="text-sm text-emerald-600 mt-1">
                    Aucun impayé pour {{ $periode->translatedFormat('F Y') }}
                </div>
            </div>
            @endif

            {{-- PAYÉS ────────────────────────────────────────────────────── --}}
            @if($payes->isNotEmpty())
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center">
                    <h3 class="font-semibold text-gray-800">
                        ✅ Loyers reçus — {{ $payes->count() }} paiement(s)
                    </h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-50">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-400 uppercase">Bien</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-400 uppercase">Locataire</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-400 uppercase">Mode</th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-400 uppercase">Montant</th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-400 uppercase">Net proprio</th>
                                <th class="px-4 py-3 text-center text-xs font-medium text-gray-400 uppercase">PDF</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @foreach($payes as $item)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-4 py-3 text-sm font-medium text-gray-800">
                                    {{ $item['contrat']->bien->reference }}
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-600">
                                    {{ $item['contrat']->locataire->name }}
                                </td>
                                <td class="px-4 py-3">
                                    <span class="px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-600">
                                        {{ ucfirst(str_replace('_', ' ', $item['paiement']->mode_paiement)) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-sm font-semibold text-gray-900 text-right">
                                    {{ number_format($item['paiement']->montant_encaisse, 0, ',', ' ') }} F
                                </td>
                                <td class="px-4 py-3 text-sm font-bold text-emerald-600 text-right">
                                    {{ number_format($item['paiement']->net_proprietaire, 0, ',', ' ') }} F
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <a href="{{ route('admin.paiements.pdf', $item['paiement']) }}"
                                       target="_blank"
                                       class="text-indigo-600 hover:text-indigo-800 text-xs font-medium">
                                        📄 PDF
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endif

        </div>
    </div>
</x-app-layout>