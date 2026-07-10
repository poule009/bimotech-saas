@php
    $rejets = collect($prev->rows)->filter(fn ($r) => $r['status'] !== 'valid');
@endphp

<div class="bg-white border border-line rounded-2xl p-7 mb-5 shadow-sm">
    <div class="flex items-center gap-2.5 text-[11.5px] font-bold uppercase tracking-wider text-gold mb-3.5">
        <span class="w-5 h-5 rounded-full bg-gold/15 text-gold flex items-center justify-center text-[11px]">2</span>
        Vérifier avant de valider
    </div>
    <h3 class="font-display font-semibold text-[18px] mb-1">Aperçu — {{ $prev->nb_total }} ligne{{ $prev->nb_total > 1 ? 's' : '' }} détectée{{ $prev->nb_total > 1 ? 's' : '' }}</h3>
    <p class="text-[13.5px] text-muted mb-5">Rien n'est encore enregistré. Vérifiez, puis confirmez.</p>

    <div class="overflow-x-auto -mx-2 px-2">
        <table class="w-full border-collapse text-[13px]">
            <thead>
                <tr class="text-left">
                    <th class="px-3 py-2.5 bg-paper-dim text-[11px] uppercase text-muted font-bold rounded-l-lg">Ligne</th>
                    @foreach($cols as $key => $label)
                        <th class="px-3 py-2.5 bg-paper-dim text-[11px] uppercase text-muted font-bold">{{ $label }}</th>
                    @endforeach
                    <th class="px-3 py-2.5 bg-paper-dim text-[11px] uppercase text-muted font-bold rounded-r-lg">Statut</th>
                </tr>
            </thead>
            <tbody>
                @foreach($prev->rows as $r)
                    @php
                        $pill = match($r['status']) {
                            'valid'     => ['bg-green/12 text-green', 'Valide'],
                            'duplicate' => ['bg-amber/15 text-amber', 'Déjà présent'],
                            default     => ['bg-error text-white', 'Erreur'],
                        };
                    @endphp
                    <tr @class(['border-b border-paper-dim', 'bg-error/5' => $r['status'] === 'error', 'bg-amber/5' => $r['status'] === 'duplicate'])>
                        <td class="px-3 py-2.5 text-muted">{{ $r['line'] }}</td>
                        @foreach($cols as $key => $label)
                            <td class="px-3 py-2.5">{{ $r['display'][$key] ?? '—' }}</td>
                        @endforeach
                        <td class="px-3 py-2.5"><span class="text-[11px] font-bold px-2.5 py-1 rounded-full whitespace-nowrap {{ $pill[0] }}">{{ $pill[1] }}</span></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- Rapport des lignes non importées --}}
    @if($rejets->isNotEmpty())
        <div class="bg-error/6 border border-error/15 rounded-xl px-5 py-4 mt-5">
            <h4 class="text-[13.5px] font-bold text-error mb-2.5">{{ $rejets->count() }} ligne{{ $rejets->count() > 1 ? 's' : '' }} ne ser{{ $rejets->count() > 1 ? 'ont' : 'a' }} pas importée{{ $rejets->count() > 1 ? 's' : '' }}</h4>
            <ul class="space-y-1.5">
                @foreach($rejets as $r)
                    <li class="text-[12.5px] text-ink pl-4 relative before:content-['—'] before:absolute before:left-0 before:text-error">
                        Ligne {{ $r['line'] }} — {{ $r['message'] }}
                    </li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Action de confirmation --}}
    <div class="mt-6 flex items-center gap-4">
        @if($prev->nb_valides > 0)
            <form method="POST" action="{{ route('admin.import.commit', $prev) }}">
                @csrf
                <button type="submit" class="bg-teal hover:bg-teal-deep text-white px-6 py-3 rounded-[10px] text-[14px] font-bold">
                    Importer les {{ $prev->nb_valides }} ligne{{ $prev->nb_valides > 1 ? 's' : '' }} valide{{ $prev->nb_valides > 1 ? 's' : '' }}
                </button>
            </form>
        @else
            <div class="text-[13px] text-muted">Aucune ligne valide à importer. Corrigez le fichier puis réimportez.</div>
        @endif
        <form method="POST" action="{{ route('admin.import.discard', $type) }}">
            @csrf @method('DELETE')
            <button type="submit" class="text-[13px] text-muted hover:text-error font-bold">Annuler cet aperçu</button>
        </form>
    </div>
</div>
