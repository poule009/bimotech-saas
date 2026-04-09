{{--
    Composant : <x-page-header />
    Usage :
        <x-page-header
            title="Biens immobiliers"
            subtitle="12 biens enregistrés"
        >
            <x-slot:actions>
                @can('create', App\Models\Bien::class)
                    <a href="{{ route('biens.create') }}" class="btn-primary">+ Nouveau bien</a>
                @endcan
            </x-slot:actions>
        </x-page-header>

    Props :
        title    (string) — titre principal
        subtitle (string) — sous-titre / compteur (optionnel)
--}}

@props([
    'title'    => '',
    'subtitle' => null,
])

<div style="
    display: flex;
    justify-content: space-between;
    align-items: flex-end;
    margin-bottom: 1.5rem;
    gap: 1rem;
    flex-wrap: wrap;
">
    <div>
        <h1 style="
            font-family: 'Syne', sans-serif;
            font-size: 22px;
            font-weight: 700;
            color: #0d1117;
            margin: 0;
            line-height: 1.2;
        ">
            {{ $title }}
        </h1>
        @if($subtitle)
            <p style="
                font-family: 'DM Sans', sans-serif;
                font-size: 13px;
                color: #8b949e;
                margin: 4px 0 0;
            ">
                {{ $subtitle }}
            </p>
        @endif
    </div>

    @isset($actions)
        <div style="display: flex; align-items: center; gap: 8px;">
            {{ $actions }}
        </div>
    @endisset
</div>