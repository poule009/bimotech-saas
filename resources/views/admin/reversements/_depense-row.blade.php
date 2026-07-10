@php
    use Illuminate\Support\Facades\Storage;
    $fmt = fn ($n) => number_format((float) $n, 0, ',', ' ');
@endphp
<div class="flex items-center gap-3.5 py-3 border-b border-paper-dim">
    <span class="w-9 h-9 rounded-[9px] bg-error/10 text-error flex items-center justify-center text-[14px] shrink-0">↓</span>
    <div class="min-w-0">
        <div class="font-semibold text-[14px] truncate">{{ $d->libelle }}</div>
        <div class="text-[12px] text-muted truncate">{{ $contexte }} · {{ $d->categorie_libelle }} · {{ optional($d->date_depense)->locale('fr')->isoFormat('D MMM') }}</div>
    </div>
    <div class="ml-auto flex items-center gap-2.5 shrink-0">
        @if($d->justificatif_path)
            <a href="{{ Storage::url($d->justificatif_path) }}" target="_blank" rel="noopener" class="inline-flex items-center gap-1 text-[11.5px] font-bold text-teal hover:underline whitespace-nowrap"><x-icon name="paperclip" size="12" /> Justificatif</a>
        @else
            <span class="text-[11.5px] font-bold text-error whitespace-nowrap">Justif. manquant</span>
        @endif
        <span class="font-bold text-[14.5px] text-error whitespace-nowrap">−{{ $fmt($d->montant) }} F</span>
        <form method="POST" action="{{ route('admin.comptabilite.depenses.destroy', $d) }}" x-data="confirmForm" x-on:submit="submit" data-confirm="Supprimer cette dépense ?">
            @csrf @method('DELETE')
            <button type="submit" class="text-muted hover:text-error transition-colors text-[16px] px-1" aria-label="Supprimer">×</button>
        </form>
    </div>
</div>
