{{--
    Composant : <x-empty-state />
    Usage :
        <x-empty-state
            title="Aucun bien enregistré"
            description="Commencez par ajouter votre premier bien immobilier."
            action-label="Ajouter un bien"
            :action-url="route('biens.create')"
        />

    Props :
        title       (string) — titre de l'état vide
        description (string) — description explicative (optionnel)
        actionLabel (string) — libellé du bouton CTA (optionnel)
        actionUrl   (string) — URL du bouton CTA (optionnel)
--}}

@props([
    'title'       => 'Aucun résultat',
    'description' => null,
    'actionLabel' => null,
    'actionUrl'   => null,
])

<div style="
    text-align: center;
    padding: 4rem 2rem;
    background: #fafafa;
    border-radius: 12px;
    border: 1.5px dashed #d0d7de;
">
    <div style="
        width: 48px; height: 48px;
        background: rgba(201,168,76,.1);
        border-radius: 12px;
        display: flex; align-items: center; justify-content: center;
        margin: 0 auto 1rem;
    ">
        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#c9a84c" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
        </svg>
    </div>

    <h3 style="
        font-family: 'Syne', sans-serif;
        font-size: 16px;
        font-weight: 600;
        color: #0d1117;
        margin: 0 0 6px;
    ">
        {{ $title }}
    </h3>

    @if($description)
        <p style="
            font-family: 'DM Sans', sans-serif;
            font-size: 13.5px;
            color: #8b949e;
            margin: 0 0 1.5rem;
            max-width: 340px;
            margin-left: auto; margin-right: auto;
        ">
            {{ $description }}
        </p>
    @endif

    @if($actionLabel && $actionUrl)
        <a href="{{ $actionUrl }}" style="
            display: inline-block;
            background: #c9a84c;
            color: #0d1117;
            font-family: 'DM Sans', sans-serif;
            font-size: 13.5px;
            font-weight: 600;
            padding: 10px 22px;
            border-radius: 8px;
            text-decoration: none;
            transition: opacity .15s;
        "
        onmouseover="this.style.opacity='.85'"
        onmouseout="this.style.opacity='1'"
        >
            {{ $actionLabel }}
        </a>
    @endif
</div>