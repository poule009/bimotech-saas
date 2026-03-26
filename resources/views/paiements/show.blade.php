<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.paiements.index') }}" class="text-gray-400 hover:text-gray-600 transition">←</a>
                <h2 class="font-semibold text-xl text-gray-800">
                    Paiement — {{ $paiement->reference_paiement }}
                </h2>
                <span class="px-2 py-1 text-xs rounded-full font-medium
                    {{ $paiement->statut === 'valide'     ? 'bg-emerald-100 text-emerald-700' : '' }}
                    {{ $paiement->statut === 'en_attente' ? 'bg-amber-100 text-amber-700'    : '' }}
                    {{ $paiement->statut === 'annule'     ? 'bg-red-100 text-red-600'        : '' }}">
                    {{ ucfirst($paiement->statut) }}
                </span>
            </div>
            @if($paiement->statut === 'valide')
            <a href="{{ route('admin.paiements.pdf', $paiement) }}"
               target="_blank"
               class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium px-4 py-2 rounded-lg transition">
                📄 Télécharger quittance
            </a>
            @endif
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            @if(session('success'))
            <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-lg text-sm">
                ✅ {{ session('success') }}
            </div>
            @endif

            {{-- Parties --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                <h3 class="font-semibold text-gray-800 mb-4">Parties concernées</h3>
                <div class="grid grid-cols-2 gap-6">
                    <div>
                        <div class="text-xs text-gray-400 uppercase tracking-wide mb-1">Propriétaire</div>
                        <div class="font-semibold text-gray-800">{{ $paiement->contrat->bien->proprietaire->name }}</div>
                        <div class="text-sm text-gray-500">{{ $paiement->contrat->bien->proprietaire->telephone ?? '' }}</div>
                    </div>
                    <div>
                        <div class="text-xs text-gray-400 uppercase tracking-wide mb-1">Locataire</div>
                        <div class="font-semibold text-gray-800">{{ $paiement->contrat->locataire->name }}</div>
                        <div class="text-sm text-gray-500">{{ $paiement->contrat->locataire->telephone ?? '' }}</div>
                    </div>
                    <div>
                        <div class="text-xs text-gray-400 uppercase tracking-wide mb-1">Bien</div>
                        <div class="font-semibold text-gray-800">{{ $paiement->contrat->bien->reference }}</div>
                        <div class="text-sm text-gray-500">{{ $paiement->contrat->bien->adresse }}, {{ $paiement->contrat->bien->ville }}</div>
                    </div>
                    <div>
                        <div class="text-xs text-gray-400 uppercase tracking-wide mb-1">Période</div>
                        <div class="font-semibold text-gray-800">
                            {{ \Carbon\Carbon::parse($paiement->periode)->translatedFormat('F Y') }}
                        </div>
                        <div class="text-sm text-gray-500">
                            Réglé le {{ \Carbon\Carbon::parse($paiement->date_paiement)->format('d/m/Y') }}
                        </div>
                    </div>
                </div>
            </div>

            {{-- Montants --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                <h3 class="font-semibold text-gray-800 mb-4">Détail des montants</h3>
                <div class="space-y-3">
                    <div class="flex justify-between items-center py-2 border-b border-gray-50">
                        <span class="text-sm text-gray-600">Loyer encaissé</span>
                        <span class="font-semibold text-gray-900">
                            {{ number_format($paiement->montant_encaisse, 0, ',', ' ') }} FCFA
                        </span>
                    </div>
                    <div class="flex justify-between items-center py-2 border-b border-gray-50">
                        <span class="text-sm text-gray-600">
                            Commission HT ({{ $paiement->taux_commission_applique }}%)
                        </span>
                        <span class="text-amber-600">
                            - {{ number_format($paiement->commission_agence, 0, ',', ' ') }} FCFA
                        </span>
                    </div>
                    <div class="flex justify-between items-center py-2 border-b border-gray-50">
                        <span class="text-sm text-gray-500 text-xs">TVA 18% sur commission</span>
                        <span class="text-gray-500 text-xs">
                            - {{ number_format($paiement->tva_commission, 0, ',', ' ') }} FCFA
                        </span>
                    </div>
                    <div class="flex justify-between items-center py-2 border-b border-gray-50">
                        <span class="text-sm text-gray-600">Commission TTC</span>
                        <span class="text-amber-700 font-medium">
                            - {{ number_format($paiement->commission_ttc, 0, ',', ' ') }} FCFA
                        </span>
                    </div>
                    <div class="flex justify-between items-center py-3 bg-emerald-50 rounded-lg px-3">
                        <span class="font-bold text-gray-800">Net propriétaire</span>
                        <span class="font-bold text-emerald-600 text-lg">
                            {{ number_format($paiement->net_proprietaire, 0, ',', ' ') }} FCFA
                        </span>
                    </div>
                    @if($paiement->est_premier_paiement && $paiement->caution_percue > 0)
                    <div class="flex justify-between items-center py-2 bg-indigo-50 rounded-lg px-3">
                        <span class="text-sm text-indigo-700">Caution perçue</span>
                        <span class="font-semibold text-indigo-700">
                            {{ number_format($paiement->caution_percue, 0, ',', ' ') }} FCFA
                        </span>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Infos règlement --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                <h3 class="font-semibold text-gray-800 mb-4">Règlement</h3>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <div class="text-xs text-gray-400 uppercase tracking-wide mb-1">Mode</div>
                        <div class="font-semibold text-gray-800">
                            {{ ucfirst(str_replace('_', ' ', $paiement->mode_paiement)) }}
                        </div>
                    </div>
                    <div>
                        <div class="text-xs text-gray-400 uppercase tracking-wide mb-1">Date</div>
                        <div class="font-semibold text-gray-800">
                            {{ \Carbon\Carbon::parse($paiement->date_paiement)->format('d/m/Y') }}
                        </div>
                    </div>
                </div>
                @if($paiement->notes)
                <div class="mt-3 pt-3 border-t border-gray-100">
                    <div class="text-xs text-gray-400 uppercase tracking-wide mb-1">Notes</div>
                    <div class="text-sm text-gray-600">{{ $paiement->notes }}</div>
                </div>
                @endif
            </div>

            {{-- Annulation --}}
            @if($paiement->statut === 'valide')
            <div class="bg-red-50 rounded-2xl border border-red-100 p-6">
                <h3 class="font-semibold text-red-800 mb-2">Annuler ce paiement</h3>
                <form method="POST" action="{{ route('admin.paiements.annuler', $paiement) }}"
                      onsubmit="return confirm('Annuler ce paiement ?')">
                    @csrf @method('PATCH')
                    <div class="flex gap-3 items-center">
                        <input type="text" name="motif" placeholder="Motif de l'annulation..."
                               class="flex-1 border border-red-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-red-400">
                        <button type="submit"
                                class="bg-red-600 hover:bg-red-700 text-white text-sm font-medium px-4 py-2 rounded-lg transition">
                            Annuler
                        </button>
                    </div>
                </form>
            </div>
            @endif

        </div>
    </div>
</x-app-layout>