@extends('layouts.app')
@section('header', 'Calendrier fiscal')

@section('content')

<div class="space-y-4 md:space-y-5">

    <div>
        <h1 class="font-display font-extrabold text-xl md:text-2xl text-bimo-text tracking-tight">Calendrier des échéances fiscales</h1>
        <p class="font-body text-sm text-bimo-text/50 mt-1">Obligation déclaratives et de paiement</p>
    </div>

    {{-- Synthèse urgences --}}
    <div class="bg-white rounded-[14px] border border-bimo-navy/10 p-5">
        <div class="font-display font-bold text-sm text-bimo-text mb-4">
            @if(count($echeancesUrgentes) > 0)
                {{ count($echeancesUrgentes) }} échéance{{ count($echeancesUrgentes) > 1 ? 's' : '' }} dans les 30 prochains jours
            @else
                Aucune échéance dans les 30 prochains jours
            @endif
        </div>
        @if(count($echeancesUrgentes) > 0)
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
            @foreach($echeancesUrgentes as $e)
            @php
                $isUrgent = $e['statut'] === 'urgent';
                $cardCls = $isUrgent ? 'bg-bimo-red/[4%] border-bimo-red/20' : 'bg-bimo-gold/[4%] border-bimo-gold/20';
                $j = (int)$today->diffInDays($e['date']);
            @endphp
            <div class="flex items-center gap-3 px-4 py-3 border {{ $cardCls }} rounded-[10px]">
                <span class="text-lg flex-shrink-0">{{ $isUrgent ? '🔴' : '🟠' }}</span>
                <div class="min-w-0">
                    <div class="font-body font-semibold text-sm {{ $isUrgent ? 'text-bimo-red' : 'text-bimo-gold' }} truncate">{{ $e['label'] }}</div>
                    <div class="font-body text-xs {{ $isUrgent ? 'text-bimo-red/60' : 'text-bimo-gold/60' }} mt-0.5">{{ $e['date']?->format('d/m/Y') }} — J-{{ $j }}</div>
                </div>
            </div>
            @endforeach
        </div>
        @else
        <p class="font-body text-sm text-bimo-text/40">Toutes les prochaines échéances sont à plus de 30 jours.</p>
        @endif
    </div>

    {{-- Table principale --}}
    @php
        $fixes     = array_filter($echeances, fn($e) => !$e['recurrent'] && $e['statut'] !== 'hors_app');
        $recurrents = array_filter($echeances, fn($e) => $e['recurrent'] && $e['statut'] !== 'hors_app');
        $horsApp   = array_filter($echeances, fn($e) => $e['statut'] === 'hors_app');
        $moisLabels = [1=>'Janvier',2=>'Février',3=>'Mars',4=>'Avril',5=>'Mai',6=>'Juin',7=>'Juillet',8=>'Août',9=>'Septembre',10=>'Octobre',11=>'Novembre',12=>'Décembre'];
    @endphp
    <div class="bg-white rounded-[14px] border border-bimo-navy/10 overflow-hidden">
        {{-- Mobile --}}
        <div class="md:hidden">
            {{-- Section annuelles --}}
            <div class="px-5 py-2.5 bg-bimo-bg border-b border-bimo-navy/[5%]">
                <span class="font-body font-medium text-[10px] uppercase tracking-widest text-bimo-text/40">Échéances annuelles</span>
            </div>
            @foreach($fixes as $e)
            <div class="px-4 py-3.5 border-b border-bimo-navy/[5%] flex items-start justify-between gap-3">
                <div class="min-w-0">
                    <div class="font-body font-semibold text-sm text-bimo-text truncate">{{ $e['label'] }}</div>
                    <div class="font-body text-xs text-bimo-text/40 mt-0.5">{{ $moisLabels[$e['mois_num']] ?? '' }} {{ $e['jour'] }}</div>
                </div>
                @php
                    $sLbl = match($e['statut']) { 'urgent'=>'🔴 Urgent', 'bientot'=>'🟠 Bientôt', 'a_venir'=>'À venir', 'passee'=>'Passée', default=>$e['statut'] };
                    $sCls = match($e['statut']) { 'urgent'=>'bg-bimo-red/10 border-bimo-red/20 text-bimo-red', 'bientot'=>'bg-bimo-gold/10 border-bimo-gold/20 text-bimo-gold', 'passee'=>'bg-bimo-navy/[5%] text-bimo-text/20', default=>'bg-bimo-navy/[5%] text-bimo-text/40' };
                @endphp
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-body font-semibold border {{ $sCls }} whitespace-nowrap flex-shrink-0">{{ $sLbl }}</span>
            </div>
            @endforeach
            {{-- Section récurrentes --}}
            <div class="px-5 py-2.5 bg-bimo-bg border-b border-bimo-navy/[5%]">
                <span class="font-body font-medium text-[10px] uppercase tracking-widest text-bimo-text/40">Récurrentes (mensuel / trimestriel)</span>
            </div>
            @foreach($recurrents as $e)
            <div class="px-4 py-3.5 border-b border-bimo-navy/[5%] flex items-start justify-between gap-3">
                <div class="min-w-0">
                    <div class="font-body font-semibold text-sm text-bimo-text truncate">{{ $e['label'] }}</div>
                    <div class="font-body text-xs text-bimo-text/40 mt-0.5">{{ $e['type'] }}</div>
                </div>
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-body font-semibold bg-bimo-navy/10 border border-bimo-navy/15 text-bimo-text/70 whitespace-nowrap flex-shrink-0">En cours</span>
            </div>
            @endforeach
        </div>

        {{-- Desktop --}}
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-bimo-navy">
                        <th class="px-5 py-3 text-left font-body font-medium text-[10px] uppercase tracking-widest text-white/50">Mois / Date</th>
                        <th class="px-5 py-3 text-left font-body font-medium text-[10px] uppercase tracking-widest text-white/50">Échéance</th>
                        <th class="px-5 py-3 text-left font-body font-medium text-[10px] uppercase tracking-widest text-white/50">Type</th>
                        <th class="px-5 py-3 text-left font-body font-medium text-[10px] uppercase tracking-widest text-white/50">Statut</th>
                        <th class="px-5 py-3 text-left font-body font-medium text-[10px] uppercase tracking-widest text-white/50">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-bimo-navy/[5%]">
                    {{-- Section annuelles --}}
                    <tr class="bg-bimo-bg">
                        <td colspan="5" class="px-5 py-2 font-body font-medium text-[10px] uppercase tracking-widest text-bimo-text/40">Échéances annuelles</td>
                    </tr>
                    @foreach($fixes as $e)
                    @php
                        $typeCls = match($e['type']) { 'Déclaration'=>'bg-bimo-navy/10 text-bimo-text/60', 'Paiement'=>'bg-bimo-gold/10 text-bimo-gold', default=>'bg-bimo-navy/[5%] text-bimo-text/40' };
                        $sLbl = match($e['statut']) { 'urgent'=>'🔴 Urgent', 'bientot'=>'🟠 Bientôt', 'a_venir'=>'⬜ À venir', 'passee'=>'✔ Passée', default=>$e['statut'] };
                        $sCls = match($e['statut']) { 'urgent'=>'bg-bimo-red/10 border-bimo-red/20 text-bimo-red', 'bientot'=>'bg-bimo-gold/10 border-bimo-gold/20 text-bimo-gold', 'passee'=>'bg-bimo-navy/[5%] text-bimo-text/20 border-transparent', default=>'bg-bimo-navy/[5%] text-bimo-text/40 border-transparent' };
                    @endphp
                    <tr class="hover:bg-bimo-bg transition-colors duration-100">
                        <td class="px-5 py-3.5 font-body font-semibold text-sm text-bimo-text whitespace-nowrap">
                            {{ $moisLabels[$e['mois_num']] ?? '' }} <span class="font-normal text-bimo-text/50">{{ $e['jour'] }}</span>
                        </td>
                        <td class="px-5 py-3.5 font-body font-medium text-sm text-bimo-text/70">{{ $e['label'] }}</td>
                        <td class="px-5 py-3.5">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-[5px] text-[10px] font-body font-semibold uppercase tracking-wide {{ $typeCls }}">{{ $e['type'] }}</span>
                        </td>
                        <td class="px-5 py-3.5">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-body font-semibold border {{ $sCls }}">{{ $sLbl }}</span>
                            @if($e['date'] && in_array($e['statut'], ['urgent','bientot','a_venir']))
                            <span class="font-body text-[10px] text-bimo-text/30 ml-2">{{ $e['date']->format('d/m/Y') }}</span>
                            @endif
                        </td>
                        <td class="px-5 py-3.5">
                            @if($e['lien'])
                            <a href="{{ $e['lien'] }}" class="inline-flex items-center gap-1 px-3 py-1.5 bg-bimo-navy text-white rounded-[7px] font-body text-xs hover:bg-bimo-navy-dk transition-colors duration-150">{{ $e['lien_label'] }} →</a>
                            @else
                            <span class="font-body text-xs text-bimo-text/25">{{ $e['lien_label'] }}</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach

                    {{-- Section récurrentes --}}
                    <tr class="bg-bimo-bg">
                        <td colspan="5" class="px-5 py-2 font-body font-medium text-[10px] uppercase tracking-widest text-bimo-text/40">Récurrentes (mensuel / trimestriel)</td>
                    </tr>
                    @foreach($recurrents as $e)
                    @php
                        $typeCls = match($e['type']) { 'Déclaration'=>'bg-bimo-navy/10 text-bimo-text/60', 'Paiement'=>'bg-bimo-gold/10 text-bimo-gold', default=>'bg-bimo-navy/[5%] text-bimo-text/40' };
                    @endphp
                    <tr class="hover:bg-bimo-bg transition-colors duration-100">
                        <td class="px-5 py-3.5 font-body text-sm text-bimo-text/50">
                            @if(in_array($e['label'], ['TVA mensuelle','BRS mensuel'])) Mensuel — 15
                            @elseif($e['label'] === 'BRS trimestriel') Trimestriel — 15
                            @else — @endif
                        </td>
                        <td class="px-5 py-3.5 font-body font-medium text-sm text-bimo-text/70">{{ $e['label'] }}</td>
                        <td class="px-5 py-3.5">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-[5px] text-[10px] font-body font-semibold uppercase tracking-wide {{ $typeCls }}">{{ $e['type'] }}</span>
                        </td>
                        <td class="px-5 py-3.5">
                            @if($e['statut'] === 'recurrent')
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-body font-semibold bg-bimo-navy/10 border border-bimo-navy/15 text-bimo-text/70">↗ En cours</span>
                            @else
                            @php $j = $e['date'] ? (int)$today->diffInDays($e['date']) : null; @endphp
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-body font-semibold {{ match($e['statut']) { 'urgent'=>'bg-bimo-red/10 border border-bimo-red/20 text-bimo-red', 'bientot'=>'bg-bimo-gold/10 border border-bimo-gold/20 text-bimo-gold', default=>'bg-bimo-navy/[5%] text-bimo-text/40' } }}">
                                {{ match($e['statut']) { 'urgent'=>'🔴 Urgent J-'.$j, 'bientot'=>'🟠 Bientôt J-'.$j, 'a_venir'=>'⬜ À venir', default=>$e['statut'] } }}
                            </span>
                            @endif
                        </td>
                        <td class="px-5 py-3.5">
                            @if($e['lien'])
                            <a href="{{ $e['lien'] }}" class="inline-flex items-center gap-1 px-3 py-1.5 bg-bimo-navy text-white rounded-[7px] font-body text-xs hover:bg-bimo-navy-dk transition-colors duration-150">{{ $e['lien_label'] }} →</a>
                            @else
                            <span class="font-body text-xs text-bimo-text/25">{{ $e['lien_label'] }}</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach

                    {{-- Hors périmètre --}}
                    @if(count($horsApp) > 0)
                    <tr class="bg-bimo-bg">
                        <td colspan="5" class="px-5 py-2 font-body font-medium text-[10px] uppercase tracking-widest text-bimo-text/30">Hors périmètre Bimotech</td>
                    </tr>
                    @foreach($horsApp as $e)
                    <tr class="opacity-50 hover:bg-bimo-bg transition-colors duration-100">
                        <td class="px-5 py-3 font-body text-sm text-bimo-text/40">Annuel</td>
                        <td class="px-5 py-3 font-body text-sm text-bimo-text/40">{{ $e['label'] }}</td>
                        <td class="px-5 py-3"><span class="inline-flex items-center px-2.5 py-0.5 rounded-[5px] text-[10px] font-body font-semibold uppercase bg-bimo-navy/[5%] text-bimo-text/30">{{ $e['type'] }}</span></td>
                        <td class="px-5 py-3"><span class="font-body text-xs text-bimo-text/30">ℹ Hors app</span></td>
                        <td class="px-5 py-3"><span class="font-body text-xs text-bimo-text/25">{{ $e['lien_label'] }}</span></td>
                    </tr>
                    @endforeach
                    @endif
                </tbody>
            </table>
        </div>
    </div>

    <div class="flex items-start gap-2 bg-bimo-navy/[4%] border border-bimo-navy/10 rounded-[10px] px-4 py-3">
        <svg class="w-4 h-4 text-bimo-text/40 flex-shrink-0 mt-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
        <p class="font-body text-xs text-bimo-text/50 leading-relaxed"><strong>Note :</strong> Ce calendrier est fourni à titre indicatif. Les dates peuvent varier selon les décisions de la DGID. Les agences immobilières sont exclues de la CGU et relèvent de la CEL (Art. 320-338 CGI SN). Consultez un expert-comptable pour votre situation spécifique.</p>
    </div>

</div>
@endsection
