@extends('layouts.app')

@php
    $fmt = fn($n) => number_format((float) $n, 0, ',', ' ');
    $statutBadge = [
        'passee'   => ['Passée', 'bg-error/10 text-error'],
        'urgent'   => ['Urgent', 'bg-error/10 text-error'],
        'bientot'  => ['Bientôt', 'bg-gold/15 text-teal-deep'],
        'a_venir'  => ['À venir', 'bg-paper text-muted'],
        'recurrent'=> ['Récurrent', 'bg-teal/10 text-teal'],
        'hors_app' => ['Hors app', 'bg-paper text-muted'],
    ];
@endphp

@section('title', 'Échéances fiscales')
@section('page-title', 'Échéances fiscales')
@section('page-subtitle')<span class="text-muted">Calendrier des obligations fiscales de l'agence</span>@endsection

@section('content')
<div class="max-w-[1000px] space-y-5">

    {{-- Baux à enregistrer (DGID) — §5.2 --}}
    @if($bauxAEnregistrer->count())
        <div class="f-card border-l-4 border-gold">
            <h3 class="f-card-title mb-1">Baux à enregistrer à la DGID</h3>
            <p class="f-card-sub">Contrats dont la date limite d'enregistrement approche ou est dépassée.</p>
            <div class="space-y-2">
                @foreach($bauxAEnregistrer as $b)
                    <div class="flex items-center justify-between gap-3 py-2.5 border-b border-paper-dim last:border-0">
                        <div class="min-w-0">
                            <a href="{{ route('admin.contrats.show', $b['contrat']) }}" class="font-semibold text-teal hover:underline text-[14px]">Contrat de {{ $b['nom'] }}</a>
                            <div class="text-[12.5px] {{ $b['en_retard'] ? 'text-error font-semibold' : 'text-muted' }}">
                                {{ $b['en_retard'] ? 'En retard — ' : '' }}à enregistrer avant le {{ $b['date_limite']->format('d/m/Y') }} · {{ $fmt($b['total']) }} F
                            </div>
                        </div>
                        <a href="{{ route('admin.contrats.show', $b['contrat']) }}" class="shrink-0 text-[12.5px] font-bold text-teal hover:underline">Traiter</a>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Calendrier annuel --}}
    <div class="f-card p-0 overflow-hidden">
        <div class="px-5 py-4 border-b border-line"><h3 class="f-card-title mb-0">Calendrier fiscal</h3></div>
        <table class="w-full text-[14px]">
            <thead>
                <tr class="text-left text-[12px] uppercase tracking-wide text-muted border-b border-line">
                    <th class="px-5 py-3">Échéance</th><th class="px-5 py-3">Type</th>
                    <th class="px-5 py-3">Date</th><th class="px-5 py-3 text-right">Statut</th>
                </tr>
            </thead>
            <tbody>
                @foreach($echeances as $e)
                    @php [$lbl, $cls] = $statutBadge[$e['statut']] ?? [$e['statut'], 'bg-paper text-muted']; @endphp
                    <tr class="border-b border-paper-dim last:border-0">
                        <td class="px-5 py-3 font-semibold text-ink">
                            @if($e['lien'])<a href="{{ $e['lien'] }}" class="text-teal hover:underline">{{ $e['label'] }}</a>@else{{ $e['label'] }}@endif
                        </td>
                        <td class="px-5 py-3 text-muted">{{ $e['type'] }}</td>
                        <td class="px-5 py-3 text-muted">{{ $e['date'] ? $e['date']->locale('fr')->translatedFormat('D MMMM Y') : ($e['recurrent'] ? 'le 15 (mensuel)' : '—') }}</td>
                        <td class="px-5 py-3 text-right"><span class="inline-block px-2 py-0.5 rounded-md text-[11.5px] font-bold {{ $cls }}">{{ $lbl }}</span></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <p class="text-[11.5px] text-muted">L'app informe et calcule — aucune déclaration n'est envoyée automatiquement.</p>
</div>
@endsection
