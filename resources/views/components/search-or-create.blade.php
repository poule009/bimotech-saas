@props([
    'name',                 // nom du champ caché soumis au formulaire parent (ex. proprietaire_id)
    'searchUrl',            // endpoint JSON de recherche
    'createUrl' => null,    // endpoint JSON de création rapide (facultatif si allowCreate=false)
    'type' => 'élément',    // libellé du type (ex. propriétaire) pour les messages
    'label' => null,
    'allowCreate' => true,  // false = recherche seule (pas d'option « Créer »)
    'fillField' => null,    // id d'un champ à pré-remplir depuis item.fill (ex. loyer_nu)
    'selectedId' => null,   // pré-sélection (édition)
    'selectedName' => null,
    'selectedSub' => null,
])

<div x-data="searchOrCreate"
     data-search-url="{{ $searchUrl }}"
     data-create-url="{{ $createUrl }}"
     data-type="{{ $type }}"
     data-allow-create="{{ $allowCreate ? 'true' : 'false' }}"
     @if($fillField) data-fill-field="{{ $fillField }}" @endif
     x-on:click.away="closeList"
     @if($selectedId)
         data-selected-id="{{ $selectedId }}"
         data-selected-name="{{ $selectedName }}"
         data-selected-sub="{{ $selectedSub }}"
     @endif
     class="relative">

    @if($label)<label class="f-label">{{ $label }}</label>@endif

    {{-- Valeur transmise au formulaire parent --}}
    <input type="hidden" name="{{ $name }}" x-bind:value="selectedId">

    {{-- État 6 : puce sélectionnée --}}
    <div x-show="hasSelected" x-cloak
         class="flex items-center gap-3 bg-green/10 border-[1.5px] border-green/30 rounded-[10px] px-4 py-3">
        <span class="w-[34px] h-[34px] rounded-[9px] bg-green text-white flex items-center justify-center text-[12px] font-bold" x-text="selectedInitials"></span>
        <div class="flex-1 min-w-0">
            <div class="font-bold text-[14px] text-ink truncate" x-text="selectedName"></div>
            <div class="text-[11.5px] text-muted truncate" x-text="selectedSub"></div>
        </div>
        <button type="button" x-on:click="clear" class="text-muted hover:text-ink px-1" aria-label="Retirer la sélection"><x-icon name="x" size="15" /></button>
    </div>

    {{-- États 0-3 : champ + résultats --}}
    <div x-show="showInput">
        <input type="text" x-model="query" x-on:input="onInput" x-on:focus="onFocus"
               placeholder="Rechercher un {{ $type }}…" autocomplete="off" class="f-input">

        <div x-show="showDropdown" x-cloak
             class="absolute z-20 left-0 right-0 mt-2 bg-white border border-line rounded-[10px] shadow-lg overflow-hidden">

            {{-- État 1 : chargement --}}
            <div x-show="loading" class="px-4 py-5 text-center text-[13px] text-muted">Recherche en cours…</div>

            {{-- État 2 : résultats --}}
            <template x-for="item in results" x-bind:key="item.id">
                <div x-bind:data-id="item.id" x-on:click="pick"
                     class="flex items-center gap-3 px-4 py-3 cursor-pointer border-b border-paper-dim hover:bg-paper">
                    <span class="w-9 h-9 rounded-[9px] bg-teal text-paper flex items-center justify-center text-[12.5px] font-bold" x-text="item.initials"></span>
                    <div class="min-w-0">
                        <div class="font-bold text-[14.5px] truncate" x-text="item.name"></div>
                        <div class="text-[12px] text-muted truncate" x-text="item.sub"></div>
                    </div>
                </div>
            </template>

            {{-- État 3 : aucun résultat --}}
            <div x-show="noResults" x-cloak class="px-4 py-4 text-center text-[13px] text-muted" x-text="emptyLabel"></div>

            {{-- Option « Créer » — visible uniquement si on a tapé du texte --}}
            <div x-show="showCreateRow" x-on:click="startCreate"
                 class="flex items-center gap-2.5 px-4 py-3 bg-paper cursor-pointer text-[13.5px] font-bold text-teal hover:bg-gold-soft transition-colors">
                <span class="w-[22px] h-[22px] rounded-md bg-teal text-white flex items-center justify-center text-[14px] shrink-0">+</span>
                <span x-text="createLabel"></span>
            </div>
        </div>
    </div>

    {{-- État 4 : mini-formulaire de création inline --}}
    <div x-show="showCreating" x-cloak class="bg-white border-[1.5px] border-teal rounded-xl p-5 shadow-lg">
        <div class="text-[14px] font-bold mb-1">Créer un {{ $type }} rapide</div>
        <div class="text-[12px] text-muted mb-4">Juste de quoi démarrer — le reste se complète plus tard depuis sa fiche.</div>
        <div class="space-y-2.5">
            <input type="text" x-model="createName" placeholder="Nom complet" class="f-input">
            <input type="text" x-model="createPhone" placeholder="Téléphone (+221)" class="f-input">
        </div>
        <div x-show="createError" x-cloak class="text-[12px] text-error mt-2" x-text="createError"></div>
        <div class="flex gap-2.5 mt-4">
            <button type="button" x-on:click="cancelCreate" class="flex-1 py-2.5 rounded-lg border-[1.5px] border-line bg-white text-ink text-[13.5px] font-bold hover:border-teal transition-colors">Annuler</button>
            <button type="button" x-on:click="submitCreate" class="flex-1 py-2.5 rounded-lg bg-teal text-paper text-[13.5px] font-bold hover:bg-teal-deep transition-colors">Créer et sélectionner</button>
        </div>
    </div>
</div>
