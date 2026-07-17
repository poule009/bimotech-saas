@props([
    'show' => null,   // URL « Voir la fiche »
    'edit' => null,   // URL « Modifier »
])

{{-- Colonne Actions standard des listes (Propriétaires, Locataires, Biens, Contrats).
     Source unique du geste Voir / Modifier — mêmes icônes, même style partout. --}}
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
</div>
