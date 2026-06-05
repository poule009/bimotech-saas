@extends('layouts.app')
@section('header', 'Abonnements')

@section('content')

@php
$niveauLabels = config('plans.labels', ['starter'=>'Starter','pro'=>'Pro','agence'=>'Agence','legacy'=>'Pro']);
@endphp

<div class="space-y-4 md:space-y-5">

    {{-- Header --}}
    <div class="flex items-center justify-between gap-4">
        <div>
            <h1 class="font-display font-extrabold text-xl md:text-2xl text-bimo-text tracking-tight">Abonnements</h1>
            <p class="font-body text-sm text-bimo-text/50 mt-1">Gestion des accès agences</p>
        </div>
        <a href="{{ route('superadmin.dashboard') }}"
           class="inline-flex items-center gap-2 px-4 py-2 border border-bimo-navy/15 rounded-[9px] font-body text-sm text-bimo-text/60 hover:border-bimo-navy/30 hover:text-bimo-text transition-all duration-150">
            ← Retour
        </a>
    </div>

    {{-- KPIs --}}
    <div class="grid grid-cols-2 md:grid-cols-5 gap-3">
        <div class="bg-white rounded-[14px] border border-bimo-navy/10 p-4 text-center">
            <div class="font-display font-extrabold text-2xl text-bimo-text leading-none">{{ $stats['nb_essai'] }}</div>
            <div class="font-body text-xs text-bimo-text/40 mt-2">En essai</div>
        </div>
        <div class="bg-white rounded-[14px] border border-bimo-navy/10 p-4 text-center">
            <div class="font-display font-extrabold text-2xl text-bimo-text leading-none">{{ $stats['nb_actifs'] }}</div>
            <div class="font-body text-xs text-bimo-text/40 mt-2">Abonnés actifs</div>
        </div>
        <div class="bg-white rounded-[14px] border {{ $stats['nb_expires'] > 0 ? 'border-bimo-red/20' : 'border-bimo-navy/10' }} p-4 text-center">
            <div class="font-display font-extrabold text-2xl {{ $stats['nb_expires'] > 0 ? 'text-bimo-red' : 'text-bimo-text' }} leading-none">{{ $stats['nb_expires'] }}</div>
            <div class="font-body text-xs text-bimo-text/40 mt-2">Expirés</div>
        </div>
        <div class="bg-bimo-gold/[8%] rounded-[14px] border border-bimo-gold/25 p-4 text-center">
            <div class="font-display font-extrabold text-xl text-bimo-gold leading-none">{{ number_format($stats['revenus_total'],0,',','') }}</div>
            <div class="font-body text-xs text-bimo-gold/60 mt-2">FCFA encaissés</div>
        </div>
        <div class="bg-white rounded-[14px] border border-bimo-navy/10 p-4 text-center">
            <div class="font-display font-extrabold text-xl text-bimo-text leading-none">{{ number_format($stats['revenus_mensuel_equiv'],0,',','') }}</div>
            <div class="font-body text-xs text-bimo-text/40 mt-2">MRR estimé (FCFA)</div>
        </div>
    </div>

    {{-- Table --}}
    <div class="bg-white rounded-[14px] border border-bimo-navy/10 overflow-hidden">
        <div class="px-5 py-4 border-b border-bimo-navy/[5%] bg-bimo-bg2">
            <span class="font-display font-bold text-sm text-bimo-text">Toutes les agences</span>
        </div>

        {{-- Mobile --}}
        <div class="md:hidden divide-y divide-bimo-navy/[5%]">
            @forelse($subscriptions as $sub)
            @php $niv = $sub->plan_niveau ?? 'legacy'; $nivLabel = $niveauLabels[$niv] ?? ucfirst($niv); @endphp
            <div class="px-4 py-3.5">
                <div class="flex items-center justify-between gap-2 mb-1">
                    <div class="font-body font-semibold text-sm text-bimo-text">{{ $sub->agency->name }}</div>
                    @if($sub->estEnEssai()) <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-body font-semibold bg-bimo-navy/10 border border-bimo-navy/15 text-bimo-text/70">Essai</span>
                    @elseif($sub->estActif()) <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-body font-semibold bg-bimo-gold/10 border border-bimo-gold/20 text-bimo-gold">Actif</span>
                    @else <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-body font-semibold bg-bimo-red/10 border border-bimo-red/20 text-bimo-red">Expiré</span>
                    @endif
                </div>
                <div class="font-body text-xs text-bimo-text/40">{{ $nivLabel }} · {{ \App\Models\Subscription::LABELS[$sub->plan] ?? '—' }}</div>
            </div>
            @empty
            <div class="px-5 py-10 text-center font-body text-sm text-bimo-text/30">Aucun abonnement.</div>
            @endforelse
        </div>

        {{-- Desktop --}}
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-bimo-navy/[5%] bg-bimo-bg">
                        <th class="px-5 py-3 text-left font-body font-medium text-[10px] uppercase tracking-widest text-bimo-text/40">Agence</th>
                        <th class="px-5 py-3 text-center font-body font-medium text-[10px] uppercase tracking-widest text-bimo-text/40">Statut</th>
                        <th class="px-5 py-3 text-center font-body font-medium text-[10px] uppercase tracking-widest text-bimo-text/40">Durée</th>
                        <th class="px-5 py-3 text-center font-body font-medium text-[10px] uppercase tracking-widest text-bimo-text/40">Niveau</th>
                        <th class="px-5 py-3 text-center font-body font-medium text-[10px] uppercase tracking-widest text-bimo-text/40">Début</th>
                        <th class="px-5 py-3 text-center font-body font-medium text-[10px] uppercase tracking-widest text-bimo-text/40">Expiration</th>
                        <th class="px-5 py-3 text-center font-body font-medium text-[10px] uppercase tracking-widest text-bimo-text/40">Jours rest.</th>
                        <th class="px-5 py-3 text-right font-body font-medium text-[10px] uppercase tracking-widest text-bimo-text/40">Montant payé</th>
                        <th class="px-5 py-3 text-center font-body font-medium text-[10px] uppercase tracking-widest text-bimo-text/40">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-bimo-navy/[5%]">
                    @forelse($subscriptions as $sub)
                    @php
                        $niv = $sub->plan_niveau ?? 'legacy';
                        $nivLabel = $niveauLabels[$niv] ?? ucfirst($niv);
                        $nivBadge = match($niv) { 'starter'=>'bg-bimo-navy/[5%] text-bimo-text/40', 'agence'=>'bg-bimo-gold/10 border border-bimo-gold/20 text-bimo-gold', default=>'bg-bimo-navy/10 border border-bimo-navy/15 text-bimo-text/70' };
                    @endphp
                    <tr class="hover:bg-bimo-bg transition-colors duration-100">
                        <td class="px-5 py-3.5">
                            <div class="font-body font-semibold text-sm text-bimo-text">{{ $sub->agency->name }}</div>
                            <div class="font-body text-xs text-bimo-text/40">{{ $sub->agency->email }}</div>
                        </td>
                        <td class="px-5 py-3.5 text-center">
                            @if($sub->estEnEssai())
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-body font-semibold bg-bimo-navy/10 border border-bimo-navy/15 text-bimo-text/70"><span class="w-1.5 h-1.5 rounded-full bg-bimo-navy/50 mr-1"></span>Essai</span>
                            @elseif($sub->estActif())
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-body font-semibold bg-bimo-gold/10 border border-bimo-gold/20 text-bimo-gold"><span class="w-1.5 h-1.5 rounded-full bg-bimo-gold mr-1"></span>Actif</span>
                            @elseif($sub->statut === 'expiré')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-body font-semibold bg-bimo-red/10 border border-bimo-red/20 text-bimo-red"><span class="w-1.5 h-1.5 rounded-full bg-bimo-red mr-1"></span>Expiré</span>
                            @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-body font-medium bg-bimo-navy/[5%] text-bimo-text/40">{{ $sub->statut }}</span>
                            @endif
                        </td>
                        <td class="px-5 py-3.5 text-center font-body text-xs text-bimo-text/60">{{ \App\Models\Subscription::LABELS[$sub->plan] ?? '—' }}</td>
                        <td class="px-5 py-3.5 text-center">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-display font-bold {{ $nivBadge }}">{{ $nivLabel }}</span>
                        </td>
                        <td class="px-5 py-3.5 text-center font-body text-xs text-bimo-text/60">
                            @if($sub->estEnEssai()) {{ $sub->date_debut_essai?->format('d/m/Y') }}
                            @else {{ $sub->date_debut_abonnement?->format('d/m/Y') ?? '—' }} @endif
                        </td>
                        <td class="px-5 py-3.5 text-center font-body text-xs text-bimo-text/60">
                            @if($sub->estEnEssai()) {{ $sub->date_fin_essai?->format('d/m/Y') }}
                            @elseif($sub->estActif()) {{ $sub->date_fin_abonnement?->format('d/m/Y') }}
                            @else — @endif
                        </td>
                        <td class="px-5 py-3.5 text-center">
                            @if($sub->estEnEssai()) @php $j = $sub->joursRestantsEssai(); @endphp
                            <span class="font-display font-bold text-sm {{ $j <= 7 ? 'text-bimo-red' : 'text-bimo-text' }}">{{ $j }}j</span>
                            @elseif($sub->estActif()) @php $j = $sub->joursRestantsAbonnement(); @endphp
                            <span class="font-display font-bold text-sm {{ $j <= 7 ? 'text-bimo-red' : 'text-bimo-gold' }}">{{ $j }}j</span>
                            @else <span class="text-bimo-text/20">—</span> @endif
                        </td>
                        <td class="px-5 py-3.5 text-right font-display font-semibold text-sm text-bimo-text/70">{{ $sub->montant_paye ? number_format($sub->montant_paye,0,',','').' F' : '—' }}</td>
                        <td class="px-5 py-3.5 text-center">
                            <div class="flex items-center justify-center gap-2">
                                {{-- Dropdown Activer --}}
                                <div class="relative" id="drop-{{ $sub->id }}">
                                    <button onclick="toggleDrop({{ $sub->id }}, event)"
                                            class="inline-flex items-center gap-1 px-3 py-1.5 border border-bimo-navy/15 rounded-[7px] font-body text-xs text-bimo-text/60 hover:border-bimo-gold hover:text-bimo-text transition-all duration-150 cursor-pointer bg-white">
                                        Activer <svg class="w-3 h-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg>
                                    </button>
                                    <div id="menu-{{ $sub->id }}"
                                         class="hidden absolute right-0 top-full mt-1 bg-white border border-bimo-navy/10 rounded-[12px] shadow-lg z-50 p-4 w-56">
                                        <form method="POST"
                                              action="{{ route('superadmin.agencies.abonnement.activer', $sub->agency) }}"
                                              onsubmit="return confirm('Activer cet abonnement pour {{ addslashes($sub->agency->name) }} ?')">
                                            @csrf
                                            <div class="space-y-3">
                                                <div class="space-y-1">
                                                    <label class="font-body font-medium text-[10px] uppercase tracking-widest text-bimo-text/40">Niveau d'accès</label>
                                                    <select name="plan_niveau"
                                                            class="w-full px-3 py-2 border border-bimo-navy/15 rounded-[7px] font-body text-sm text-bimo-text bg-bimo-bg focus:outline-none focus:border-bimo-gold cursor-pointer appearance-none">
                                                        <option value="starter">Starter</option>
                                                        <option value="pro" selected>Pro</option>
                                                        <option value="agence">Agence</option>
                                                    </select>
                                                </div>
                                                <div class="space-y-1">
                                                    <label class="font-body font-medium text-[10px] uppercase tracking-widest text-bimo-text/40">Durée</label>
                                                    <select name="plan"
                                                            class="w-full px-3 py-2 border border-bimo-navy/15 rounded-[7px] font-body text-sm text-bimo-text bg-bimo-bg focus:outline-none focus:border-bimo-gold cursor-pointer appearance-none">
                                                        @foreach(\App\Models\Subscription::LABELS as $plan => $label)
                                                        <option value="{{ $plan }}">{{ $label }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <button type="submit"
                                                        class="w-full py-2 bg-bimo-navy text-white rounded-[8px] font-display font-bold text-sm hover:bg-bimo-navy-dk transition-colors duration-150 cursor-pointer">
                                                    Activer l'abonnement
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                                {{-- Réinitialiser essai --}}
                                <form method="POST"
                                      action="{{ route('superadmin.agencies.essai.reinitialiser', $sub->agency) }}"
                                      onsubmit="return confirm('Réinitialiser l\'essai de {{ addslashes($sub->agency->name) }} ?')">
                                    @csrf
                                    <button type="submit"
                                            class="inline-flex items-center px-3 py-1.5 border border-bimo-navy/15 rounded-[7px] font-body text-xs text-bimo-text/60 hover:border-bimo-navy/30 hover:text-bimo-text transition-all duration-150 cursor-pointer">
                                        Essai
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="9" class="px-5 py-10 text-center font-body text-sm text-bimo-text/30">Aucun abonnement.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($subscriptions->hasPages())
        <div class="px-5 py-3.5 border-t border-bimo-navy/[5%]">
            {{ $subscriptions->links() }}
        </div>
        @endif
    </div>

</div>

@push('scripts')
<script>
function toggleDrop(id, e) {
    e.stopPropagation();
    var menu = document.getElementById('menu-' + id);
    var isOpen = !menu.classList.contains('hidden');
    document.querySelectorAll('[id^="menu-"]').forEach(function(m){ m.classList.add('hidden'); });
    if (!isOpen) {
        menu.classList.remove('hidden');
        setTimeout(function(){
            document.addEventListener('click', function close(ev) {
                if (!document.getElementById('drop-' + id).contains(ev.target)) {
                    menu.classList.add('hidden');
                    document.removeEventListener('click', close);
                }
            });
        }, 0);
    }
}
</script>
@endpush
@endsection
