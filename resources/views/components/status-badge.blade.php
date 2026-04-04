{{--
    Composant : <x-status-badge />
    Usage :
        <x-status-badge status="actif" />
        <x-status-badge status="en_attente" type="paiement" />

    Props :
        status  (string)  — valeur du statut
        type    (string)  — contrat | paiement | bien (défaut: contrat)
--}}

@props([
    'status' => '',
    'type'   => 'contrat',
])

@php
    $maps = [
        'contrat' => [
            'actif'    => ['label' => 'Actif',    'bg' => 'rgba(59,109,17,.1)',   'color' => '#3B6D11'],
            'expire'   => ['label' => 'Expiré',   'bg' => 'rgba(88,86,80,.1)',    'color' => '#5F5E5A'],
            'resilie'  => ['label' => 'Résilié',  'bg' => 'rgba(226,75,74,.1)',   'color' => '#A32D2D'],
            'brouillon'=> ['label' => 'Brouillon','bg' => 'rgba(186,117,23,.1)',  'color' => '#854F0B'],
        ],
        'paiement' => [
            'valide'     => ['label' => 'Payé',        'bg' => 'rgba(59,109,17,.1)',  'color' => '#3B6D11'],
            'en_attente' => ['label' => 'En attente',  'bg' => 'rgba(186,117,23,.1)','color' => '#854F0B'],
            'en_retard'  => ['label' => 'En retard',   'bg' => 'rgba(226,75,74,.1)', 'color' => '#A32D2D'],
        ],
        'bien' => [
            'disponible' => ['label' => 'Disponible',   'bg' => 'rgba(55,138,221,.1)', 'color' => '#185FA5'],
            'loue'       => ['label' => 'Loué',          'bg' => 'rgba(59,109,17,.1)',  'color' => '#3B6D11'],
            'en_travaux' => ['label' => 'En travaux',    'bg' => 'rgba(186,117,23,.1)','color' => '#854F0B'],
            'archive'    => ['label' => 'Archivé',       'bg' => 'rgba(88,86,80,.1)',   'color' => '#5F5E5A'],
        ],
    ];

    $map     = $maps[$type] ?? $maps['contrat'];
    $config  = $map[$status] ?? ['label' => ucfirst(str_replace('_', ' ', $status)), 'bg' => 'rgba(88,86,80,.1)', 'color' => '#5F5E5A'];
@endphp

<span style="
    display: inline-block;
    padding: 3px 10px;
    border-radius: 99px;
    font-family: 'DM Sans', sans-serif;
    font-size: 11.5px;
    font-weight: 500;
    background: {{ $config['bg'] }};
    color: {{ $config['color'] }};
    white-space: nowrap;
">
    {{ $config['label'] }}
</span>