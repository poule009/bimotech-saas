@props([
    'show'          => null,   // URL « Voir la fiche »
    'edit'          => null,   // URL « Modifier »
    'delete'        => null,   // URL « Supprimer » (envoyée en DELETE)
    'deleteBlocked' => null,   // Raison du blocage — si renseignée, le bouton est désactivé
    'deleteLabel'   => 'Supprimer',
    'confirm'       => 'Supprimer définitivement ?',
])

{{-- Colonne Actions standard des listes (Propriétaires, Locataires, Biens, Contrats).
     Source unique des gestes Voir / Modifier / Supprimer — mêmes icônes, même style partout.
     Quand la suppression est impossible, le bouton reste visible mais désactivé : la raison
     est portée par le title (l'absence de bouton ne s'explique pas toute seule). --}}
<div class="flex items-center justify-end gap-1">
    @if($show)
        <a href="{{ $show }}" title="Voir la fiche" aria-label="Voir la fiche"
           class="w-8 h-8 flex items-center justify-center rounded-lg text-muted hover:text-teal hover:bg-paper-dim transition-colors">
            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
        </a>
    @endif
    @if($edit)
        <a href="{{ $edit }}" title="Modifier" aria-label="Modifier"
           class="w-8 h-8 flex items-center justify-center rounded-lg text-muted hover:text-teal hover:bg-paper-dim transition-colors">
            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.12 2.12 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
        </a>
    @endif
    @if($delete)
        @if($deleteBlocked)
            <span title="{{ $deleteBlocked }}" aria-label="{{ $deleteBlocked }}" aria-disabled="true"
                  class="w-8 h-8 flex items-center justify-center rounded-lg text-muted/35 cursor-not-allowed">
                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 6h18"/><path d="M8 6V4a2 2 0 012-2h4a2 2 0 012 2v2"/><path d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6"/></svg>
            </span>
        @else
            <form method="POST" action="{{ $delete }}" class="flex"
                  x-data="confirmForm" x-on:submit="submit" data-confirm="{{ $confirm }}">
                @csrf
                @method('DELETE')
                <button type="submit" title="{{ $deleteLabel }}" aria-label="{{ $deleteLabel }}"
                        class="w-8 h-8 flex items-center justify-center rounded-lg text-muted hover:text-error hover:bg-error/10 transition-colors">
                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 6h18"/><path d="M8 6V4a2 2 0 012-2h4a2 2 0 012 2v2"/><path d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6"/></svg>
                </button>
            </form>
        @endif
    @endif
</div>
