{{-- Panneau latéral d'aide — dans le scope x-data="importDrawer" de la page. --}}

{{-- Overlay --}}
<div x-on:click="closePanel" x-bind:class="overlayClass"
     class="fixed inset-0 z-40 bg-teal-deep/45 transition-opacity duration-200"></div>

{{-- Panneau --}}
<aside x-bind:class="panelClass"
       class="fixed top-0 right-0 z-50 h-full w-full max-w-[440px] bg-paper shadow-2xl p-8 overflow-y-auto transition-transform duration-250">
    <div class="flex items-center justify-between mb-6">
        <h3 class="font-display font-semibold text-[20px]">Comment ça marche</h3>
        <button type="button" x-on:click="closePanel" class="w-8 h-8 rounded-[9px] bg-white border border-line text-muted flex items-center justify-center hover:text-ink">
            <x-icon name="x" size="16" />
        </button>
    </div>

    @php
        $guide = [
            ['1', 'Propriétaires', "Toujours en premier, rien n'en dépend. Vous recevez un code unique par propriétaire (P-0001…)."],
            ['2', 'Biens', "Utilise les codes propriétaires pour relier chaque bien sans ambiguïté de nom. Génère des codes biens (B-0001…)."],
            ['3', 'Locataires', "Indépendant — juste leurs coordonnées. Génère des codes locataires (L-0001…)."],
            ['4', 'Contrats', "Combine les codes biens et locataires pour recréer chaque bail en cours."],
        ];
    @endphp

    @foreach($guide as $g)
        <div class="flex gap-3.5 mb-5">
            <span class="w-7 h-7 rounded-full bg-gold text-teal-deep flex items-center justify-center font-bold text-[12.5px] shrink-0">{{ $g[0] }}</span>
            <div>
                <h4 class="text-[13.5px] font-bold mb-1">{{ $g[1] }}</h4>
                <p class="text-[12.5px] text-muted leading-relaxed">{{ $g[2] }}</p>
            </div>
        </div>
    @endforeach

    <div class="bg-gold/10 border border-gold/20 rounded-xl px-4 py-3.5 text-[12px] text-gold leading-relaxed mt-2">
        <strong class="block mb-1">Contrats déjà anciens</strong>
        Aucun historique de retard n'est recréé pour les mois passés — les quittances automatiques démarrent seulement le mois suivant l'import.
    </div>

    <div class="mt-6 space-y-4">
        <div>
            <h4 class="text-[13.5px] font-bold mb-1">Une ligne a une erreur ?</h4>
            <p class="text-[12.5px] text-muted leading-relaxed">Les lignes correctes s'importent quand même. Vous obtenez un rapport précis (n° de ligne + raison) pour corriger et réimporter séparément les lignes en erreur.</p>
        </div>
        <div>
            <h4 class="text-[13.5px] font-bold mb-1">Je me suis trompé de fichier ?</h4>
            <p class="text-[12.5px] text-muted leading-relaxed">Chaque import peut être annulé en bloc depuis l'historique, tant que les données n'ont pas été modifiées ou utilisées depuis.</p>
        </div>
    </div>
</aside>
