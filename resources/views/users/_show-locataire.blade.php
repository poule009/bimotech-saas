@php
    $profil     = $user->locataire;
    $entreprise = (bool) ($profil?->est_entreprise);
    $fmt        = fn ($n) => number_format((float) $n, 0, ',', ' ');
    // Lien WhatsApp (wa.me/221XXXXXXXX)
    $telDigits = preg_replace('/\D/', '', (string) $user->telephone);
    if ($telDigits !== '' && ! str_starts_with($telDigits, '221')) {
        $telDigits = '221' . ltrim($telDigits, '0');
    }
    $waLink = $telDigits !== '' ? 'https://wa.me/' . $telDigits : null;
    $bienActif = $contratActif?->bien;
    $bienNom   = $bienActif?->reference ?? $bienActif?->titre ?? $bienActif?->ville;
@endphp

<div class="max-w-[1100px]" x-data="tabs">

    @if(session('success'))
        <div class="mb-5 rounded-lg bg-green/10 border border-green/25 px-4 py-3 text-[13px] text-green">{{ session('success') }}</div>
    @endif

    {{-- En-tête --}}
    <div class="bg-white border border-line rounded-xl p-6 md:p-7 flex flex-col sm:flex-row sm:items-center gap-5 mb-4">
        <div @class(['w-[64px] h-[64px] rounded-2xl flex items-center justify-center font-bold text-[21px] shrink-0','bg-gold text-teal-deep'=>$entreprise,'bg-teal text-paper'=>!$entreprise])>{{ mb_strtoupper(mb_substr($user->name, 0, 2)) }}</div>
        <div class="min-w-0 flex-1">
            <h2 class="font-display font-semibold text-[24px] mb-1.5">{{ $user->name }}</h2>
            <div class="flex flex-wrap items-center gap-x-4 gap-y-1.5 text-[13.5px] text-muted">
                @if($user->telephone)<span>📞 {{ $user->telephone }}</span>@endif
                @if($entreprise)<span class="text-[12px] font-bold px-3 py-1 rounded-full bg-gold/15 text-gold">Bureau</span>
                @else<span class="text-[12px] font-bold px-3 py-1 rounded-full bg-green/10 text-green">Particulier</span>@endif
            </div>
        </div>
        <a href="{{ route('admin.users.edit', $user) }}" class="px-5 py-3 rounded-[10px] border-[1.5px] border-line bg-white text-ink text-[14px] font-bold hover:border-teal transition-colors shrink-0">Modifier</a>
    </div>

    {{-- ★ BANNIÈRE DE PAIEMENT (élément central, statut calculé) ★ --}}
    @if($paie['etat'] === 'retard')
        <div class="bg-error text-white rounded-xl p-5 md:px-7 flex flex-col sm:flex-row sm:items-center gap-4 mb-5">
            <div class="w-[52px] h-[52px] rounded-[14px] bg-white/20 flex items-center justify-center text-[24px] shrink-0">⚠️</div>
            <div class="flex-1 min-w-0">
                <div class="font-display font-semibold text-[16.5px] mb-0.5">En retard de {{ $paie['jours'] }} jour{{ $paie['jours'] > 1 ? 's' : '' }}@if($bienNom) — {{ $bienNom }}@endif</div>
                <div class="text-[13px] text-white/90">Loyer de {{ $fmt($contratActif->loyer_contractuel) }} F attendu pour {{ $paie['periode']->locale('fr')->isoFormat('MMMM Y') }}.</div>
            </div>
            @if($waLink)
                <a href="{{ $waLink }}" target="_blank" rel="noopener" class="inline-flex items-center gap-2 bg-[#25D366] text-white px-5 py-3 rounded-[11px] text-[14px] font-bold hover:opacity-90 transition-opacity shrink-0"><x-icon-whatsapp :size="16" /> Relancer</a>
            @endif
        </div>
    @elseif($paie['etat'] === 'ok')
        <div class="bg-green text-white rounded-xl p-5 md:px-7 flex flex-col sm:flex-row sm:items-center gap-4 mb-5">
            <div class="w-[52px] h-[52px] rounded-[14px] bg-white/20 flex items-center justify-center text-[24px] shrink-0">✓</div>
            <div class="flex-1 min-w-0">
                <div class="font-display font-semibold text-[16.5px] mb-0.5">À jour @if($bienNom)— {{ $bienNom }}@endif</div>
                <div class="text-[13px] text-white/90">Loyer de {{ $fmt($contratActif->loyer_contractuel) }} F · {{ $paie['periode']->locale('fr')->isoFormat('MMMM Y') }}.</div>
            </div>
            @if($waLink)
                <a href="{{ $waLink }}" target="_blank" rel="noopener" class="inline-flex items-center gap-2 bg-white/20 text-white px-5 py-3 rounded-[11px] text-[14px] font-bold hover:bg-white/30 transition-colors shrink-0"><x-icon-whatsapp :size="16" /> WhatsApp</a>
            @endif
        </div>
    @else
        <div class="bg-paper-dim border border-line rounded-xl px-6 py-4 mb-5 text-[13.5px] text-muted">
            Aucun contrat actif — ce locataire n'a pas de loyer en cours.
        </div>
    @endif

    {{-- Onglets --}}
    <div class="flex gap-2 border-b-2 border-line mb-5 overflow-x-auto">
        <button type="button" x-on:click="showInfo" x-bind:class="infoTabClass" class="px-4 py-3 text-[14.5px] font-bold border-b-[3px] -mb-0.5 whitespace-nowrap transition-colors">Informations</button>
        <button type="button" x-on:click="showContrats" x-bind:class="contratsTabClass" class="px-4 py-3 text-[14.5px] font-bold border-b-[3px] -mb-0.5 whitespace-nowrap transition-colors">Contrats · {{ $contrats->count() }}</button>
        <button type="button" x-on:click="showGarant" x-bind:class="garantTabClass" class="px-4 py-3 text-[14.5px] font-bold border-b-[3px] -mb-0.5 whitespace-nowrap transition-colors">Garant</button>
        <button type="button" x-on:click="showDocs" x-bind:class="docsTabClass" class="px-4 py-3 text-[14.5px] font-bold border-b-[3px] -mb-0.5 whitespace-nowrap transition-colors">Documents</button>
    </div>

    {{-- Informations --}}
    <div x-show="isInfo">
        <div class="f-card mb-5">
            <h3 class="f-card-title mb-4">Coordonnées</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div><div class="text-[11.5px] uppercase tracking-wide text-muted font-bold mb-1">Téléphone</div><div class="text-[15px] font-semibold">{{ $user->telephone ?? '—' }}</div></div>
                <div><div class="text-[11.5px] uppercase tracking-wide text-muted font-bold mb-1">Email</div><div class="text-[15px] font-semibold">{{ $user->email ?? '—' }}</div></div>
                <div><div class="text-[11.5px] uppercase tracking-wide text-muted font-bold mb-1">Type</div><div class="text-[15px] font-semibold">{{ $profil?->label_type ?? 'Particulier' }}</div></div>
                <div><div class="text-[11.5px] uppercase tracking-wide text-muted font-bold mb-1">{{ $entreprise ? 'NINEA' : 'N° CNI' }}</div><div class="text-[15px] font-semibold">{{ ($entreprise ? $profil?->ninea_locataire : $profil?->cni) ?? '—' }}</div></div>
                @if($profil?->profession)<div><div class="text-[11.5px] uppercase tracking-wide text-muted font-bold mb-1">Profession</div><div class="text-[15px] font-semibold">{{ $profil->profession }}</div></div>@endif
                @if($profil?->revenu_mensuel)<div><div class="text-[11.5px] uppercase tracking-wide text-muted font-bold mb-1">Revenu mensuel</div><div class="text-[15px] font-semibold">{{ $fmt($profil->revenu_mensuel) }} F</div></div>@endif
            </div>
        </div>
    </div>

    {{-- Contrats (actif + historique) --}}
    <div x-show="isContrats" x-cloak>
        @if($contrats->isEmpty())
            <div class="bg-white border border-line rounded-xl py-12 text-center text-muted text-[14px]">Aucun contrat pour ce locataire.</div>
        @else
            <div class="space-y-3">
                @foreach($contrats as $c)
                    <div class="flex items-center gap-4 p-4 border rounded-xl bg-white {{ $c->statut === 'actif' ? 'border-teal/40' : 'border-line' }}">
                        <span class="w-[46px] h-[46px] rounded-[11px] bg-teal text-paper flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 21h18M5 21V7l7-4 7 4v14M10 21v-5h4v5"/></svg>
                        </span>
                        <div class="min-w-0 flex-1">
                            <div class="font-bold text-[14.5px] truncate">{{ $c->bien?->reference ?? $c->bien?->titre ?? 'Bien' }}</div>
                            <div class="text-[12.5px] text-muted">
                                {{ optional($c->date_debut)->locale('fr')->isoFormat('D MMM Y') }}
                                @if($c->date_fin) → {{ optional($c->date_fin)->locale('fr')->isoFormat('D MMM Y') }}@endif
                                · <span class="{{ $c->statut === 'actif' ? 'text-green font-semibold' : '' }}">{{ ucfirst($c->statut ?? '') }}</span>
                            </div>
                        </div>
                        <div class="ml-auto text-right shrink-0"><strong class="block text-[15px]">{{ $fmt($c->loyer_contractuel) }} F</strong><span class="text-[11px] text-muted">par mois</span></div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    {{-- Garant (depuis le contrat actif) --}}
    <div x-show="isGarant" x-cloak>
        <div class="f-card">
            @if($contratActif && $contratActif->garant_nom)
                <h3 class="f-card-title mb-4">Garant du bail en cours</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div><div class="text-[11.5px] uppercase tracking-wide text-muted font-bold mb-1">Nom</div><div class="text-[15px] font-semibold">{{ $contratActif->garant_nom }}</div></div>
                    <div><div class="text-[11.5px] uppercase tracking-wide text-muted font-bold mb-1">Téléphone</div><div class="text-[15px] font-semibold">{{ $contratActif->garant_telephone ?? '—' }}</div></div>
                    @if($contratActif->garant_adresse)<div class="sm:col-span-2"><div class="text-[11.5px] uppercase tracking-wide text-muted font-bold mb-1">Adresse</div><div class="text-[15px] font-semibold">{{ $contratActif->garant_adresse }}</div></div>@endif
                </div>
            @else
                <div class="text-center py-10 text-muted">
                    <span class="text-[28px] block mb-2">🤝</span>
                    <p class="text-[14px]">Aucun garant renseigné pour ce locataire.</p>
                    <p class="text-[12.5px] mt-1">Le garant se renseigne sur le contrat, à la signature.</p>
                </div>
            @endif
        </div>
    </div>

    {{-- Documents --}}
    <div x-show="isDocs" x-cloak class="space-y-3">
        <div class="flex items-center gap-3.5 p-4 border border-line rounded-xl bg-white">
            <span class="text-[19px]">📄</span>
            <span class="font-bold flex-1 text-[14.5px]">Pièce d'identité (CNI)</span>
            <span class="text-[12px] font-bold px-2.5 py-1 rounded-full {{ $profil?->cni_verified ? 'bg-green/10 text-green' : 'bg-gold/15 text-gold' }}">{{ $profil?->cni_verified ? 'Vérifiée' : 'À vérifier' }}</span>
        </div>
        <div class="flex items-center gap-3.5 p-4 border border-line rounded-xl bg-white">
            <span class="text-[19px] {{ $profil?->justif_revenus_fourni ? '' : 'opacity-60' }}">📑</span>
            <span class="font-bold flex-1 text-[14.5px] {{ $profil?->justif_revenus_fourni ? '' : 'text-error' }}">Justificatif de revenus</span>
            <span class="text-[12px] font-bold px-2.5 py-1 rounded-full {{ $profil?->justif_revenus_fourni ? 'bg-green/10 text-green' : 'bg-error/10 text-error' }}">{{ $profil?->justif_revenus_fourni ? 'Fourni' : 'Manquant' }}</span>
        </div>
    </div>
</div>
