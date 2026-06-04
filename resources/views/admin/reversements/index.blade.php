@extends('layouts.app')
@section('header', 'Reversements')

@section('content')
<div class="space-y-4 md:space-y-6">

    <div class="flex items-start justify-between gap-4">
        <div>
            <h1 class="font-display font-extrabold text-xl md:text-2xl text-bimo-navy tracking-tight leading-tight">Reversements propriétaires</h1>
            <p class="font-body text-sm text-bimo-navy/50 mt-1">Suivi des nets reversés et soldes mandants</p>
        </div>
        <a href="{{ route('admin.reversements.create') }}"
           class="flex-shrink-0 inline-flex items-center gap-2 bg-[var(--ac)] text-white font-display font-bold text-sm px-4 py-2.5 rounded-[10px] transition-opacity duration-150 hover:opacity-90">
            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            <span class="hidden sm:inline">Nouveau reversement</span>
        </a>
    </div>

    {{-- Soldes mandants --}}
    @if($soldesMandants->isNotEmpty())
    <div class="bg-white rounded-[14px] border border-bimo-navy/10 overflow-hidden">
        <div class="px-5 py-4 border-b border-bimo-navy/[5%] bg-bimo-bg2">
            <span class="font-display font-bold text-sm text-bimo-navy">Soldes en attente</span>
        </div>
        <div class="divide-y divide-bimo-navy/[5%]">
            @foreach($soldesMandants->where('solde', '>', 0)->sortByDesc('solde') as $solde)
            @php $prop = $proprietaires->firstWhere('id', $solde['proprietaire_id']); @endphp
            @if($prop)
            <div class="px-5 py-3.5 flex items-center justify-between gap-4">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-full bg-bimo-navy/10 flex items-center justify-center font-display font-bold text-xs text-bimo-navy/60 flex-shrink-0">
                        {{ strtoupper(substr($prop->name, 0, 1)) }}
                    </div>
                    <p class="font-body font-medium text-sm text-bimo-navy">{{ $prop->name }}</p>
                </div>
                <div class="flex items-center gap-3">
                    <p class="font-display font-bold text-sm text-bimo-red">{{ number_format($solde['solde'], 0, ',', ' ') }} F</p>
                    <a href="{{ route('admin.reversements.compte-mandant', $prop) }}" class="inline-flex items-center px-3 py-1 border border-bimo-navy/15 rounded-[7px] font-body text-xs text-bimo-navy/60 hover:border-bimo-navy/30 hover:text-bimo-navy transition-all duration-150">Compte</a>
                    <a href="{{ route('admin.reversements.create', ['proprietaire_id' => $prop->id]) }}" class="inline-flex items-center px-3 py-1 bg-[var(--ac)] rounded-[7px] font-body text-xs text-white hover:opacity-90 transition-opacity duration-150">Reverser</a>
                </div>
            </div>
            @endif
            @endforeach
        </div>
    </div>
    @endif

    {{-- Historique --}}
    <div class="bg-white rounded-[14px] border border-bimo-navy/10 overflow-hidden">
        <div class="px-5 py-4 border-b border-bimo-navy/[5%] bg-bimo-bg2">
            <span class="font-display font-bold text-sm text-bimo-navy">Historique des reversements</span>
        </div>

        {{-- Mobile --}}
        <div class="md:hidden divide-y divide-bimo-navy/[5%]">
            @forelse($reversements as $rev)
            <div class="px-5 py-4">
                <div class="flex items-center justify-between gap-2 mb-1.5">
                    <p class="font-body font-medium text-sm text-bimo-navy">{{ $rev->proprietaire?->name ?? '—' }}</p>
                    <p class="font-display font-bold text-sm text-bimo-gold">{{ number_format($rev->montant, 0, ',', ' ') }} F</p>
                </div>
                <div class="flex items-center gap-3 flex-wrap">
                    <span class="font-body text-xs text-bimo-navy/40">{{ $rev->date_reversement->format('d/m/Y') }}</span>
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-body font-medium bg-bimo-navy/10 border border-bimo-navy/15 text-bimo-navy/60">{{ $rev->mode_paiement_libelle }}</span>
                    @if($rev->reference)<span class="font-body text-[10px] text-bimo-navy/30">{{ $rev->reference }}</span>@endif
                </div>
            </div>
            @empty
            <div class="px-5 py-10 text-center font-body text-sm text-bimo-navy/30">Aucun reversement enregistré.</div>
            @endforelse
        </div>

        {{-- Desktop --}}
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-bimo-navy/[5%] bg-bimo-bg">
                        <th class="px-5 py-3 text-left font-body font-medium text-[10px] uppercase tracking-widest text-bimo-navy/40">Date</th>
                        <th class="px-5 py-3 text-left font-body font-medium text-[10px] uppercase tracking-widest text-bimo-navy/40">Propriétaire</th>
                        <th class="px-5 py-3 text-left font-body font-medium text-[10px] uppercase tracking-widest text-bimo-navy/40">Mode</th>
                        <th class="px-5 py-3 text-left font-body font-medium text-[10px] uppercase tracking-widest text-bimo-navy/40">Période</th>
                        <th class="px-5 py-3 text-right font-body font-medium text-[10px] uppercase tracking-widest text-bimo-navy/40">Montant</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-bimo-navy/[5%]">
                    @forelse($reversements as $rev)
                    <tr class="hover:bg-bimo-bg transition-colors duration-100">
                        <td class="px-5 py-3.5 font-body text-xs text-bimo-navy/50">{{ $rev->date_reversement->format('d/m/Y') }}</td>
                        <td class="px-5 py-3.5 font-body font-medium text-sm text-bimo-navy">{{ $rev->proprietaire?->name ?? '—' }}</td>
                        <td class="px-5 py-3.5"><span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-body font-medium bg-bimo-navy/10 border border-bimo-navy/15 text-bimo-navy/70">{{ $rev->mode_paiement_libelle }}</span></td>
                        <td class="px-5 py-3.5 font-body text-xs text-bimo-navy/50">{{ $rev->periode_debut && $rev->periode_fin ? $rev->periode_debut.' → '.$rev->periode_fin : '—' }}</td>
                        <td class="px-5 py-3.5 text-right font-display font-bold text-sm text-bimo-gold">{{ number_format($rev->montant, 0, ',', ' ') }} F</td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="px-5 py-10 text-center font-body text-sm text-bimo-navy/30">Aucun reversement enregistré.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($reversements->hasPages())
        <div class="px-5 py-4 border-t border-bimo-navy/[5%]">{{ $reversements->links() }}</div>
        @endif
    </div>

</div>
@endsection
