{{--
    Composant : <x-kpi-card />
    Usage :
        <x-kpi-card
            label="Loyers perçus"
            :value="number_format($total, 0, ',', ' ') . ' FCFA'"
            color="gold"
            icon="trending-up"
            :trend="+12"
        />

    Props :
        label   (string)  — libellé de la carte
        value   (string)  — valeur principale affichée
        color   (string)  — gold | blue | green | red | purple (défaut: gold)
        icon    (string)  — nom d'icône Feather (optionnel)
        trend   (int)     — variation en % (positif/négatif, null = masqué)
        subtitle(string)  — texte secondaire sous la valeur (optionnel)
--}}

@props([
    'label'    => '',
    'value'    => '',
    'color'    => 'gold',
    'icon'     => null,
    'trend'    => null,
    'subtitle' => null,
])

@php
    $colors = [
        'gold'   => ['border' => '#c9a84c', 'bg' => 'rgba(201,168,76,.08)',  'text' => '#c9a84c'],
        'blue'   => ['border' => '#378ADD', 'bg' => 'rgba(55,138,221,.08)',  'text' => '#378ADD'],
        'green'  => ['border' => '#3B6D11', 'bg' => 'rgba(59,109,17,.08)',   'text' => '#3B6D11'],
        'red'    => ['border' => '#E24B4A', 'bg' => 'rgba(226,75,74,.08)',   'text' => '#E24B4A'],
        'purple' => ['border' => '#534AB7', 'bg' => 'rgba(83,74,183,.08)',   'text' => '#534AB7'],
    ];
    $c = $colors[$color] ?? $colors['gold'];
@endphp

<div style="
    background: #fff;
    border-radius: 12px;
    padding: 1.25rem 1.25rem 1rem;
    border-top: 3px solid {{ $c['border'] }};
    box-shadow: 0 1px 3px rgba(0,0,0,.06);
    display: flex;
    flex-direction: column;
    gap: 4px;
    position: relative;
    overflow: hidden;
">
    {{-- Icône de fond décorative --}}
    @if($icon)
        <div style="
            position: absolute; top: 12px; right: 14px;
            opacity: .08;
        ">
            <span style="font-size: 20px; color: {{ $c['border'] }};">◆</span>
        </div>
    @endif

    {{-- Label --}}
    <div style="
        font-family: 'DM Sans', sans-serif;
        font-size: 11.5px;
        font-weight: 500;
        color: #6e7681;
        text-transform: uppercase;
        letter-spacing: .6px;
    ">
        {{ $label }}
    </div>

    {{-- Valeur principale --}}
    <div style="
        font-family: 'Syne', sans-serif;
        font-size: 22px;
        font-weight: 700;
        color: #0d1117;
        line-height: 1.1;
        margin-top: 4px;
    ">
        {{ $value }}
    </div>

    {{-- Sous-titre optionnel --}}
    @if($subtitle)
        <div style="font-size: 12px; color: #8b949e; font-family: 'DM Sans', sans-serif;">
            {{ $subtitle }}
        </div>
    @endif

    {{-- Tendance --}}
    @if($trend !== null)
        @php $positive = $trend >= 0; @endphp
        <div style="
            display: inline-flex;
            align-items: center;
            gap: 3px;
            margin-top: 6px;
            font-size: 12px;
            font-weight: 500;
            font-family: 'DM Sans', sans-serif;
            color: {{ $positive ? '#3B6D11' : '#E24B4A' }};
            background: {{ $positive ? 'rgba(59,109,17,.08)' : 'rgba(226,75,74,.08)' }};
            padding: 2px 8px;
            border-radius: 99px;
            width: fit-content;
        ">
            {{ $positive ? '↑' : '↓' }} {{ abs($trend) }}%
        </div>
    @endif
</div>