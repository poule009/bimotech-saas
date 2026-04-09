{{--
    Composant : <x-dark-bilan />
    Usage :
        <x-dark-bilan title="Bilan mensuel — Juin 2025">
            <x-dark-bilan-row label="Loyers encaissés"   value="1 450 000 FCFA" />
            <x-dark-bilan-row label="Commissions HT"     value="145 000 FCFA"   muted />
            <x-dark-bilan-row label="TVA (18%)"          value="26 100 FCFA"    muted />
            <x-dark-bilan-row label="Net propriétaires"  value="1 246 500 FCFA" highlight />
        </x-dark-bilan>

    Props :
        title  (string) — titre de la section
--}}

@props(['title' => 'Bilan financier'])

<div style="
    background: #0d1117;
    border-radius: 12px;
    padding: 1.5rem;
    margin: 1.5rem 0;
">
    <div style="
        font-family: 'Syne', sans-serif;
        font-size: 14px;
        font-weight: 600;
        color: #c9a84c;
        letter-spacing: .3px;
        margin-bottom: 1.25rem;
        text-transform: uppercase;
        font-size: 11px;
        letter-spacing: 1px;
    ">
        {{ $title }}
    </div>

    <div style="display: flex; flex-direction: column; gap: 0;">
        {{ $slot }}
    </div>
</div>