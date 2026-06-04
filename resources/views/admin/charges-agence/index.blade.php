@extends('layouts.app')
@section('header', 'Charges agence')

@section('content')
<div class="space-y-4 md:space-y-6">

    <div class="flex items-start justify-between gap-4">
        <div>
            <h1 class="font-display font-extrabold text-xl md:text-2xl text-bimo-navy tracking-tight leading-tight">Charges agence</h1>
            <p class="font-body text-sm text-bimo-navy/50 mt-1">Dépenses opérationnelles de l'agence</p>
        </div>
        <a href="{{ route('admin.charges-agence.create') }}"
           class="flex-shrink-0 inline-flex items-center gap-2 bg-[var(--ac)] text-white font-display font-bold text-sm px-4 py-2.5 rounded-[10px] transition-opacity duration-150 hover:opacity-90">
            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            <span class="hidden sm:inline">Nouvelle charge</span>
        </a>
    </div>

    {{-- Filtres --}}
    <form method="GET" class="flex flex-wrap items-center gap-2">
        <input type="month" name="mois" value="{{ $mois }}" onchange="this.form.submit()"
               class="px-3 py-2 rounded-[8px] bg-white border border-bimo-navy/20 font-body text-sm text-bimo-navy focus:outline-none focus:border-bimo-gold focus:ring-2 focus:ring-bimo-gold/15 transition-all duration-150">
        <select name="categorie" onchange="this.form.submit()" class="px-3 py-2 rounded-[8px] bg-white border border-bimo-navy/20 font-body text-sm text-bimo-navy focus:outline-none focus:border-bimo-gold focus:ring-2 focus:ring-bimo-gold/15 transition-all duration-150 appearance-none cursor-pointer">
            <option value="">Toutes catégories</option>
            @foreach(\App\Models\ChargeAgence::CATEGORIES as $key => $label)
            <option value="{{ $key }}" {{ $categorie === $key ? 'selected' : '' }}>{{ $label }}</option>
            @endforeach
        </select>
    </form>

    {{-- KPI total + répartition --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
        <div class="col-span-2 bg-white rounded-[14px] border border-bimo-navy/10 p-4">
            <p class="font-body font-medium text-[9.5px] uppercase tracking-widest text-bimo-navy/50 mb-2">Total {{ $mois ? \Carbon\Carbon::createFromFormat('Y-m', $mois)->locale('fr')->translatedFormat('F Y') : '' }}</p>
            <p class="font-display font-extrabold text-2xl text-bimo-navy">{{ number_format($total, 0, ',', ' ') }} <span class="text-sm font-body font-normal text-bimo-navy/40">FCFA</span></p>
        </div>
        @foreach($parCategorie->take(2) as $cat)
        <div class="bg-white rounded-[14px] border border-bimo-navy/10 p-4">
            <p class="font-body font-medium text-[9.5px] uppercase tracking-widest text-bimo-navy/50 mb-2">{{ \App\Models\ChargeAgence::CATEGORIES[$cat->categorie] ?? $cat->categorie }}</p>
            <p class="font-display font-bold text-lg text-bimo-navy">{{ number_format($cat->total, 0, ',', ' ') }} F</p>
        </div>
        @endforeach
    </div>

    {{-- Liste mobile --}}
    <div class="md:hidden space-y-3">
        @forelse($charges as $charge)
        <div class="bg-white rounded-[14px] border border-bimo-navy/10 p-4">
            <div class="flex items-start justify-between gap-2 mb-2">
                <p class="font-body font-medium text-sm text-bimo-navy">{{ $charge->libelle }}</p>
                <p class="font-display font-bold text-sm text-bimo-navy flex-shrink-0">{{ number_format($charge->montant, 0, ',', ' ') }} F</p>
            </div>
            <div class="flex items-center gap-2 flex-wrap">
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-body font-medium bg-bimo-navy/10 border border-bimo-navy/15 text-bimo-navy/60">{{ $charge->categorie_libelle }}</span>
                <span class="font-body text-[10px] text-bimo-navy/30">{{ $charge->date_charge->format('d/m/Y') }}</span>
            </div>
            <div class="flex items-center gap-2 mt-3">
                <a href="{{ route('admin.charges-agence.edit', $charge) }}" class="inline-flex items-center px-3 py-1 border border-bimo-navy/15 rounded-[7px] font-body text-xs text-bimo-navy/60 hover:border-bimo-navy/30 hover:text-bimo-navy transition-all duration-150">Modifier</a>
                <form method="POST" action="{{ route('admin.charges-agence.destroy', $charge) }}" data-confirm="Supprimer cette charge ?">
                    @csrf @method('DELETE')
                    <button type="submit" class="inline-flex items-center px-3 py-1 border border-bimo-red/20 rounded-[7px] font-body text-xs text-bimo-red hover:bg-bimo-red/10 transition-all duration-150 cursor-pointer">Supprimer</button>
                </form>
            </div>
        </div>
        @empty
        <div class="bg-white rounded-[14px] border border-bimo-navy/10 p-8 text-center font-body text-sm text-bimo-navy/30">Aucune charge pour cette période.</div>
        @endforelse
    </div>

    {{-- Table desktop --}}
    <div class="hidden md:block bg-white rounded-[14px] border border-bimo-navy/10 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-bimo-navy/[5%] bg-bimo-bg2">
                        <th class="px-5 py-3 text-left font-body font-medium text-[10px] uppercase tracking-widest text-bimo-navy/40">Date</th>
                        <th class="px-5 py-3 text-left font-body font-medium text-[10px] uppercase tracking-widest text-bimo-navy/40">Libellé</th>
                        <th class="px-5 py-3 text-left font-body font-medium text-[10px] uppercase tracking-widest text-bimo-navy/40">Catégorie</th>
                        <th class="px-5 py-3 text-left font-body font-medium text-[10px] uppercase tracking-widest text-bimo-navy/40">Prestataire</th>
                        <th class="px-5 py-3 text-right font-body font-medium text-[10px] uppercase tracking-widest text-bimo-navy/40">Montant</th>
                        <th class="px-5 py-3 text-center font-body font-medium text-[10px] uppercase tracking-widest text-bimo-navy/40">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-bimo-navy/[5%]">
                    @forelse($charges as $charge)
                    <tr class="hover:bg-bimo-bg transition-colors duration-100">
                        <td class="px-5 py-3.5 font-body text-xs text-bimo-navy/50">{{ $charge->date_charge->format('d/m/Y') }}</td>
                        <td class="px-5 py-3.5 font-body font-medium text-sm text-bimo-navy">{{ $charge->libelle }}</td>
                        <td class="px-5 py-3.5">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-body font-medium bg-bimo-navy/10 border border-bimo-navy/15 text-bimo-navy/70">{{ $charge->categorie_libelle }}</span>
                        </td>
                        <td class="px-5 py-3.5 font-body text-xs text-bimo-navy/50">{{ $charge->prestataire ?: '—' }}</td>
                        <td class="px-5 py-3.5 text-right font-display font-bold text-sm text-bimo-navy">{{ number_format($charge->montant, 0, ',', ' ') }} F</td>
                        <td class="px-5 py-3.5 text-center">
                            <div class="flex items-center justify-center gap-2">
                                <a href="{{ route('admin.charges-agence.edit', $charge) }}" class="inline-flex items-center px-3 py-1 border border-bimo-navy/15 rounded-[7px] font-body text-xs text-bimo-navy/60 hover:border-bimo-navy/30 hover:text-bimo-navy transition-all duration-150">Modifier</a>
                                <form method="POST" action="{{ route('admin.charges-agence.destroy', $charge) }}" data-confirm="Supprimer cette charge ?">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="inline-flex items-center px-3 py-1 border border-bimo-red/20 rounded-[7px] font-body text-xs text-bimo-red hover:bg-bimo-red/10 transition-all duration-150 cursor-pointer">Supprimer</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="px-5 py-10 text-center font-body text-sm text-bimo-navy/30">Aucune charge pour cette période.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($charges->hasPages())
        <div class="px-5 py-4 border-t border-bimo-navy/[5%]">{{ $charges->links() }}</div>
        @endif
    </div>

</div>
@endsection
