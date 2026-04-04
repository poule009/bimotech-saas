{{--
    Composant : <x-dark-bilan-row />
    Utilisé à l'intérieur de <x-dark-bilan>.

    Props :
        label     (string)  — libellé de la ligne
        value     (string)  — montant / valeur
        muted     (bool)    — texte atténué pour les sous-totaux
        highlight (bool)    — mise en évidence gold pour le total net
        separator (bool)    — ajoute une ligne de séparation au-dessus
--}}

@props([
    'label'     => '',
    'value'     => '',
    'muted'     => false,
    'highlight' => false,
    'separator' => false,
])

<div style="
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 8px 0;
    {{ $separator ? 'border-top: 1px solid rgba(255,255,255,.08); margin-top: 4px; padding-top: 12px;' : '' }}
    border-bottom: 1px solid rgba(255,255,255,.04);
">
    <span style="
        font-family: 'DM Sans', sans-serif;
        font-size: {{ $highlight ? '13.5px' : '13px' }};
        color: {{ $highlight ? '#e6edf3' : ($muted ? '#484f58' : '#8b949e') }};
        font-weight: {{ $highlight ? '500' : '400' }};
    ">
        {{ $label }}
    </span>
    <span style="
        font-family: 'DM Sans', sans-serif;
        font-size: {{ $highlight ? '15px' : '13px' }};
        font-weight: {{ $highlight ? '600' : '400' }};
        color: {{ $highlight ? '#c9a84c' : ($muted ? '#484f58' : '#8b949e') }};
    ">
        {{ $value }}
    </span>
</div>