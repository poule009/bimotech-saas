@php
    $labels = [
        'proprietaires' => 'propriétaire', 'biens' => 'bien',
        'locataires' => 'locataire', 'contrats' => 'contrat',
    ];
    $mot      = $labels[$type] ?? $type;
    $nb       = $batch->nb_crees;
    $codes    = $batch->codes ?? [];
    $premier  = $codes[0]['code'] ?? null;
    $dernier  = $codes[count($codes) - 1]['code'] ?? null;
    $nextLabel = [
        'biens' => 'les Biens', 'locataires' => 'les Locataires', 'contrats' => 'les Contrats',
    ];
@endphp

<div class="rounded-2xl p-7 mb-5 text-white bg-gradient-to-br from-green to-teal shadow-sm">
    <div class="font-display font-semibold text-[17px] mb-1.5 flex items-center gap-2">
        <x-icon name="check-circle" size="20" />
        @if($type === 'contrats')
            Import terminé — vos baux en cours sont enregistrés
        @else
            {{ $nb }} {{ $mot }}{{ $nb > 1 ? 's' : '' }} importé{{ $nb > 1 ? 's' : '' }}
        @endif
    </div>

    <div class="text-[13px] text-white/85 leading-relaxed">
        @if($type === 'contrats')
            Aucune quittance n'a été générée pour les mois passés : la facturation automatique démarre le mois prochain.
        @elseif($premier && $dernier)
            Un fichier de codes ({{ $premier }} à {{ $dernier }}) a été généré — vous en aurez besoin à l'étape suivante pour relier vos données.
        @else
            Import enregistré.
        @endif
    </div>

    <div class="flex flex-wrap items-center gap-3 mt-5">
        @if(! empty($codes))
            <a href="{{ route('admin.import.codes', $batch) }}" class="bg-white text-teal px-4 py-2.5 rounded-[9px] text-[13px] font-bold flex items-center gap-1.5">
                <x-icon name="download" size="15" /> Télécharger les codes
            </a>
        @endif

        @if(! empty($next) && isset($nextLabel[$next]))
            <a href="{{ route('admin.import.index', ['step' => $next]) }}" class="bg-white/15 hover:bg-white/25 text-white px-4 py-2.5 rounded-[9px] text-[13px] font-bold flex items-center gap-1.5 transition-colors">
                Passer à {{ $nextLabel[$next] }} →
            </a>
        @endif

        <a href="{{ route('admin.import.historique') }}" class="text-white/80 hover:text-white text-[12.5px] font-bold">Gérer / annuler cet import</a>
    </div>
</div>
