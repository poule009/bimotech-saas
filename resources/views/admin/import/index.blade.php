@extends('layouts.app')

@php
    // Métadonnées d'étape (ordre = ImportManager::ETAPES).
    $steps = [
        'proprietaires' => [
            'num' => 1, 'label' => 'Propriétaires',
            'sub' => "Première étape — rien n'en dépend, on commence toujours par là.",
            'cols' => 'Nom, Téléphone, Email (facultatif), Adresse, NINEA',
        ],
        'biens' => [
            'num' => 2, 'label' => 'Biens',
            'sub' => "Nécessite les codes propriétaires obtenus à l'étape précédente.",
            'cols' => 'Nom du bien, Type, Adresse, Loyer, Code propriétaire*',
        ],
        'locataires' => [
            'num' => 3, 'label' => 'Locataires',
            'sub' => 'Indépendant des étapes précédentes — juste leurs coordonnées.',
            'cols' => 'Nom, Téléphone, Email (facultatif)',
        ],
        'contrats' => [
            'num' => 4, 'label' => 'Contrats',
            'sub' => 'Dernière étape — recrée vos baux en cours à partir des codes biens et locataires.',
            'cols' => 'Code bien*, Code locataire*, Loyer, Date de début',
        ],
    ];
    $ordre     = array_keys($steps);
    $next      = ['proprietaires' => 'biens', 'biens' => 'locataires', 'locataires' => 'contrats', 'contrats' => null];

    $c    = $current;
    $st   = $etat[$c];
    $prev = $previews[$c];
    $cols = $meta[$c]['colonnes'];
@endphp

@section('title', 'Import de données')
@section('page-title', 'Import de données')
@section('page-subtitle', 'Récupérez vos propriétaires, biens et locataires déjà existants, étape par étape.')

@section('content')
<div class="max-w-[960px]" x-data="importDrawer">

    {{-- Barre d'actions --}}
    <div class="flex items-center justify-between gap-3 mb-6 -mt-2">
        <a href="{{ route('admin.import.historique') }}" class="text-[13px] font-bold text-teal hover:underline flex items-center gap-1.5">
            <x-icon name="clock" size="15" /> Historique des imports
        </a>
        <button type="button" x-on:click="openPanel" class="bg-white border border-line hover:border-teal text-teal px-4 py-2.5 rounded-[10px] text-[13px] font-bold flex items-center gap-2 shadow-sm">
            <x-icon name="info" size="15" /> Comment ça marche
        </button>
    </div>

    {{-- Flashes --}}
    @if(session('import_success'))
        <div class="mb-5 rounded-lg bg-green/10 border border-green/25 px-4 py-3 text-[13px] text-green flex items-center gap-2"><x-icon name="check-circle" size="16" /> {{ session('import_success') }}</div>
    @endif
    @if(session('import_error') || $errors->has('fichier'))
        <div class="mb-5 rounded-lg bg-error/10 border border-error/25 px-4 py-3 text-[13px] text-error flex items-center gap-2"><x-icon name="alert-triangle" size="16" /> {{ session('import_error') ?? $errors->first('fichier') }}</div>
    @endif

    {{-- ─────────────── STEPPER ─────────────── --}}
    <div class="flex items-center mb-8">
        @foreach($ordre as $i => $type)
            @php
                $s        = $etat[$type];
                $isCur    = $type === $c;
                $stateCls = $s['done'] ? 'done' : ($isCur ? 'current' : ($s['unlocked'] ? 'todo' : 'locked'));
                $circle   = match($stateCls) {
                    'done'    => 'bg-green border-green text-white',
                    'current' => 'bg-teal border-teal text-white ring-4 ring-teal/15',
                    'locked'  => 'bg-paper-dim border-paper-dim text-muted/50',
                    default   => 'bg-white border-line text-muted',
                };
                $labelCls = match($stateCls) {
                    'done'    => 'text-green',
                    'current' => 'text-teal',
                    'locked'  => 'text-muted/50',
                    default   => 'text-muted',
                };
            @endphp

            @if($s['unlocked'])
                <a href="{{ route('admin.import.index', ['step' => $type]) }}" class="flex items-center gap-3 rounded-lg p-1">
                    @if($s['done'])<span class="w-9 h-9 rounded-full border-2 flex items-center justify-center shrink-0 {{ $circle }}"><x-icon name="check" size="16" /></span>@else<span class="w-9 h-9 rounded-full border-2 flex items-center justify-center text-[13px] font-bold shrink-0 {{ $circle }}">{{ $steps[$type]['num'] }}</span>@endif
                    <span class="text-[13px] font-bold {{ $labelCls }}">{{ $steps[$type]['label'] }}</span>
                </a>
            @else
                <div class="flex items-center gap-3 p-1 cursor-not-allowed" title="Terminez l'étape précédente pour débloquer">
                    <span class="w-9 h-9 rounded-full border-2 flex items-center justify-center text-[13px] font-bold shrink-0 {{ $circle }}">{{ $steps[$type]['num'] }}</span>
                    <span class="text-[13px] font-bold {{ $labelCls }}">{{ $steps[$type]['label'] }}</span>
                </div>
            @endif

            @if($i < count($ordre) - 1)
                <div class="w-11 h-0.5 mx-2 {{ $etat[$type]['done'] ? 'bg-green' : 'bg-line' }}"></div>
            @endif
        @endforeach
    </div>

    {{-- ─────────────── ÉTAPE COURANTE ─────────────── --}}

    {{-- Carte 1 : préparer & importer --}}
    <div class="bg-white border border-line rounded-2xl p-7 mb-5 shadow-sm">
        <div class="flex items-center gap-2.5 text-[11.5px] font-bold uppercase tracking-wider text-gold mb-3.5">
            <span class="w-5 h-5 rounded-full bg-gold/15 text-gold flex items-center justify-center text-[11px]">1</span>
            Préparer et importer le fichier
        </div>
        <h3 class="font-display font-semibold text-[18px] mb-1">{{ $steps[$c]['label'] }}</h3>
        <p class="text-[13.5px] text-muted mb-5">{{ $steps[$c]['sub'] }}</p>

        {{-- Ligne modèle --}}
        <div class="flex items-center gap-4 bg-paper border border-line rounded-xl px-5 py-4 mb-5">
            <span class="w-10 h-10 rounded-[10px] bg-green/12 text-green flex items-center justify-center shrink-0"><x-icon name="file-text" size="18" /></span>
            <div class="flex-1 min-w-0">
                <div class="font-bold text-[14px]">modele_{{ $c }}.xlsx</div>
                <div class="text-[12px] text-muted mt-0.5">Colonnes : {{ $steps[$c]['cols'] }}</div>
            </div>
            <a href="{{ route('admin.import.template', $c) }}" class="bg-teal hover:bg-teal-deep text-white px-4 py-2.5 rounded-[9px] text-[13px] font-bold flex items-center gap-1.5 shrink-0 whitespace-nowrap">
                <x-icon name="download" size="15" /> Télécharger
            </a>
        </div>

        {{-- Zone d'upload OU fichier en attente --}}
        @if($prev)
            <div class="flex items-center gap-3 bg-green/8 border border-green/25 rounded-xl px-5 py-4">
                <x-icon name="file-text" size="18" class="text-green" />
                <span class="font-bold text-[13.5px] flex-1 truncate">{{ $prev->original_filename }}</span>
                <form method="POST" action="{{ route('admin.import.discard', $c) }}">
                    @csrf @method('DELETE')
                    <button type="submit" class="text-[12px] text-muted hover:text-error font-bold">Retirer</button>
                </form>
            </div>
        @else
            <form method="POST" action="{{ route('admin.import.preview', $c) }}" enctype="multipart/form-data"
                  x-data="importUpload" x-ref="form">
                @csrf
                <label class="block border-2 border-dashed border-line hover:border-teal rounded-xl p-9 text-center cursor-pointer bg-paper hover:bg-white transition-colors">
                    <input type="file" name="fichier" accept=".xlsx,.xls,.csv" class="hidden" x-on:change="pick">
                    <span class="flex flex-col items-center">
                        <x-icon name="upload" size="28" class="text-teal mb-2.5" />
                        <span class="font-bold text-[14.5px] mb-1" x-text="label"></span>
                        <span class="text-[12.5px] text-muted" x-show="idle">Formats acceptés : .xlsx, .xls, .csv</span>
                        <span class="text-[12.5px] text-teal font-bold" x-show="submitting" x-cloak>Lecture du fichier…</span>
                    </span>
                </label>
            </form>
        @endif

        @if($c === 'biens')
            <div class="mt-4 bg-paper border border-line rounded-[10px] px-4 py-3 text-[12.5px] text-muted leading-relaxed flex gap-2">
                <x-icon name="lightbulb" size="15" class="text-gold shrink-0 mt-0.5" />
                <span>La colonne <code class="bg-paper-dim px-1.5 py-0.5 rounded text-ink text-[11.5px]">code_proprietaire</code> doit contenir un code du fichier reçu à l'étape Propriétaires (ex. <code class="bg-paper-dim px-1.5 py-0.5 rounded text-ink text-[11.5px]">P-0001</code>).</span>
            </div>
        @elseif($c === 'contrats')
            <div class="mt-4 bg-gold/10 border border-gold/25 rounded-[10px] px-4 py-3 text-[12.5px] text-gold leading-relaxed flex gap-2">
                <x-icon name="alert-triangle" size="15" class="shrink-0 mt-0.5" />
                <span>Les quittances automatiques ne démarreront qu'à partir du mois prochain — aucun historique de retard n'est recréé pour les mois passés.</span>
            </div>
        @endif
    </div>

    {{-- Carte 2 : aperçu avant validation --}}
    @if($prev)
        @include('admin.import._apercu', ['prev' => $prev, 'cols' => $cols, 'type' => $c])
    {{-- Bannière résultat (dernier lot committé, plus d'aperçu en attente) --}}
    @elseif($st['batch'])
        @include('admin.import._resultat', ['batch' => $st['batch'], 'type' => $c, 'next' => $next[$c]])
    @endif

    {{-- ─────────────── DRAWER AIDE ─────────────── --}}
    @include('admin.import._aide')

</div>
@endsection
