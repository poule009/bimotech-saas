@extends('layouts.app')

@section('title', 'Historique des imports')
@section('page-title', 'Historique des imports')
@section('page-subtitle', "Chaque import peut être annulé en bloc tant qu'aucune donnée n'en dépend et que rien n'a été modifié depuis.")

@section('content')
<div class="max-w-[960px]">

    <div class="mb-6">
        <a href="{{ route('admin.import.index') }}" class="text-[13px] font-bold text-teal hover:underline flex items-center gap-1.5">
            ← Retour à l'import
        </a>
    </div>

    @if(session('import_success'))
        <div class="mb-5 rounded-lg bg-green/10 border border-green/25 px-4 py-3 text-[13px] text-green flex items-center gap-2"><x-icon name="check-circle" size="16" /> {{ session('import_success') }}</div>
    @endif
    @if(session('import_error'))
        <div class="mb-5 rounded-lg bg-error/10 border border-error/25 px-4 py-3 text-[13px] text-error flex items-center gap-2"><x-icon name="alert-triangle" size="16" /> {{ session('import_error') }}</div>
    @endif

    @if($batches->isEmpty())
        <div class="bg-white border border-line rounded-2xl p-10 text-center text-muted">
            <x-icon name="clock" size="28" class="mx-auto mb-3 text-muted/60" />
            <p class="text-[14px]">Aucun import effectué pour le moment.</p>
        </div>
    @else
        <div class="space-y-3">
            @foreach($batches as $b)
                @php
                    $badge = match($b->statut) {
                        'committed' => ['bg-green/12 text-green', 'Importé'],
                        'annule'    => ['bg-muted/15 text-muted', 'Annulé'],
                        default     => ['bg-amber/15 text-amber', $b->statut],
                    };
                @endphp
                <div class="bg-white border border-line rounded-xl px-5 py-4 flex items-center gap-4 flex-wrap">
                    <div class="flex-1 min-w-[200px]">
                        <div class="flex items-center gap-2.5">
                            <span class="font-bold text-[14px]">{{ $b->type_label }}</span>
                            <span class="text-[11px] font-bold px-2.5 py-0.5 rounded-full {{ $badge[0] }}">{{ $badge[1] }}</span>
                        </div>
                        <div class="text-[12px] text-muted mt-1">
                            {{ $b->nb_crees }} créé{{ $b->nb_crees > 1 ? 's' : '' }}
                            @if($b->nb_erreurs || $b->nb_doublons)· {{ $b->nb_erreurs }} erreur(s), {{ $b->nb_doublons }} doublon(s) ignoré(s)@endif
                            · {{ $b->original_filename }}
                            · {{ optional($b->committed_at ?? $b->created_at)->format('d/m/Y H:i') }}
                        </div>
                    </div>

                    <div class="flex items-center gap-3 shrink-0">
                        @if($b->statut === 'committed' && ! empty($b->codes))
                            <a href="{{ route('admin.import.codes', $b) }}" class="text-[12.5px] font-bold text-teal hover:underline flex items-center gap-1.5">
                                <x-icon name="download" size="14" /> Codes
                            </a>
                        @endif

                        @if($b->statut === 'committed')
                            @if($annulables[$b->id])
                                <form method="POST" action="{{ route('admin.import.undo', $b) }}"
                                      x-data="confirmForm" x-on:submit="submit"
                                      data-confirm="Annuler cet import ? Les {{ $b->nb_crees }} enregistrement(s) créé(s) seront définitivement supprimés.">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-[12.5px] font-bold text-error hover:underline flex items-center gap-1.5">
                                        <x-icon name="trash" size="14" /> Annuler
                                    </button>
                                </form>
                            @else
                                <span class="text-[12px] text-muted/70 italic">Non annulable (données dépendantes ou modifiées)</span>
                            @endif
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-6">{{ $batches->links() }}</div>
    @endif

</div>
@endsection
