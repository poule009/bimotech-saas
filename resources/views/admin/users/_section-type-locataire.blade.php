{{--
    ════════════════════════════════════════════════════════════════════
    PARTIEL — Section "Type de locataire" pour users/create.blade.php
    et users/edit.blade.php (quand role = 'locataire').

    UTILISATION :
      @if($role === 'locataire' || $user->isLocataire())
        @include('admin.users._section-type-locataire', ['user' => $user ?? null])
      @endif

    Note charte : couleurs rouge (BRS) conservées comme sémantique fiscale
    légale (exception documentée). Accent de sélection = var(--ac).
    ════════════════════════════════════════════════════════════════════
--}}

@php
    $locataire     = $user?->locataire ?? null;
    $estEntreprise = old('est_entreprise', $locataire?->est_entreprise ?? false);
    $typeLocataire = old('type_locataire', $locataire?->type_locataire ?? 'particulier');
@endphp

<div class="font-body font-bold text-xs uppercase tracking-wider text-bimo-text/70 mb-3.5 pb-2 border-b border-bimo-navy/10 mt-6">
    ⚖️ Statut fiscal du locataire
</div>

{{-- ── Type de locataire ── --}}
<div class="mb-4">
    <label class="block font-body font-semibold text-xs text-bimo-text/70 mb-2">
        Type de locataire <span class="text-bimo-red">*</span>
    </label>

    <div id="type-locataire-grid" class="grid grid-cols-2 min-[380px]:grid-cols-3 sm:grid-cols-5 gap-2">
        @foreach([
            'particulier' => ['label' => 'Particulier',  'icon' => '👤', 'brs' => false],
            'entreprise'  => ['label' => 'Entreprise',   'icon' => '🏢', 'brs' => true],
            'association' => ['label' => 'Association',  'icon' => '🤝', 'brs' => true],
            'ambassade'   => ['label' => 'Ambassade',    'icon' => '🏛️', 'brs' => false],
            'ong'         => ['label' => 'ONG / Org.',   'icon' => '🌍', 'brs' => false],
        ] as $key => $info)
        @php $actif = $typeLocataire === $key; @endphp
        <label class="block cursor-pointer">
            <input type="radio" name="type_locataire" value="{{ $key }}"
                   {{ $actif ? 'checked' : '' }}
                   onchange="onTypeLocataireChange(this)"
                   class="hidden">
            <div class="type-loc-pill p-2.5 rounded-[9px] text-center transition-all duration-150"
                 data-brs="{{ $info['brs'] ? '1' : '0' }}"
                 style="border:1.5px solid {{ $actif ? 'var(--ac)' : 'rgba(123,30,58,0.12)' }};background:{{ $actif ? 'rgba(var(--ac-r),var(--ac-g),var(--ac-b),0.12)' : '#fff' }}">
                <div class="text-base mb-1">{{ $info['icon'] }}</div>
                <div class="font-body text-[11px] {{ $actif ? 'font-semibold' : 'font-medium' }}"
                     style="color:{{ $actif ? 'var(--ac)' : '#6b7280' }}">{{ $info['label'] }}</div>
                @if($info['brs'])
                <div class="text-[9px] text-bimo-red mt-0.5 font-semibold">BRS 5%</div>
                @endif
            </div>
        </label>
        @endforeach
    </div>

    {{-- Champ caché est_entreprise synchronisé avec le type ── --}}
    <input type="hidden" name="est_entreprise" id="est_entreprise"
           value="{{ in_array($typeLocataire, ['entreprise','association']) ? '1' : '0' }}">
</div>

{{-- ── Champs entreprise (affichés dynamiquement) ── --}}
<div id="bloc-entreprise" class="{{ in_array($typeLocataire, ['entreprise','association']) ? '' : 'hidden' }}">
    <div class="bg-bimo-navy/[4%] border border-bimo-navy/15 rounded-[10px] p-4 mb-3.5">
        <div class="font-body font-semibold text-xs text-bimo-text/70 mb-3 flex items-center gap-1.5">
            <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M13 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V9z"/><polyline points="13 2 13 9 20 9"/></svg>
            Informations de la personne morale
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            <div class="sm:col-span-2">
                <label class="block font-body font-semibold text-xs text-bimo-text/70 mb-1.5">
                    Raison sociale (nom officiel de l'entreprise)
                </label>
                <input type="text" name="nom_entreprise" id="nom_entreprise"
                       value="{{ old('nom_entreprise', $locataire?->nom_entreprise) }}"
                       placeholder="Ex: Société Immobilière Dakar SARL"
                       class="w-full px-3 py-2.5 rounded-[8px] bg-white border border-bimo-navy/15 font-body text-[13px] text-bimo-text placeholder:text-bimo-text/35 focus:outline-none focus:border-[var(--ac)] focus:ring-2 focus:ring-[var(--ac)]/15 transition-all duration-150">
                <div class="font-body text-[11px] text-bimo-text/40 mt-1">Sera utilisé à la place du nom du représentant sur les quittances</div>
            </div>

            <div>
                <label class="block font-body font-semibold text-xs text-bimo-text/70 mb-1.5">
                    NINEA <span class="font-normal text-bimo-text/40">(Numéro fiscal)</span>
                </label>
                <input type="text" name="ninea_locataire" id="ninea_locataire"
                       value="{{ old('ninea_locataire', $locataire?->ninea_locataire) }}"
                       placeholder="Ex: 00123456789"
                       maxlength="30"
                       class="w-full px-3 py-2.5 rounded-[8px] bg-white border border-bimo-navy/15 font-body text-[13px] text-bimo-text placeholder:text-bimo-text/35 focus:outline-none focus:border-[var(--ac)] focus:ring-2 focus:ring-[var(--ac)]/15 transition-all duration-150">
                <div class="font-body text-[11px] text-bimo-text/40 mt-1">Apparaît sur la quittance PDF pour justifier la BRS</div>
            </div>

            <div>
                <label class="block font-body font-semibold text-xs text-bimo-text/70 mb-1.5">
                    RCCM <span class="font-normal text-bimo-text/40">(Registre de commerce)</span>
                </label>
                <input type="text" name="rccm_locataire" id="rccm_locataire"
                       value="{{ old('rccm_locataire', $locataire?->rccm_locataire) }}"
                       placeholder="Ex: SN-DKR-2024-B-12345"
                       maxlength="60"
                       class="w-full px-3 py-2.5 rounded-[8px] bg-white border border-bimo-navy/15 font-body text-[13px] text-bimo-text placeholder:text-bimo-text/35 focus:outline-none focus:border-[var(--ac)] focus:ring-2 focus:ring-[var(--ac)]/15 transition-all duration-150">
            </div>

            <div>
                <label class="block font-body font-semibold text-xs text-bimo-text/70 mb-1.5">
                    Taux BRS personnalisé <span class="font-normal text-bimo-text/40">(%)</span>
                </label>
                <input type="number" name="taux_brs_override" id="taux_brs_override"
                       value="{{ old('taux_brs_override', $locataire?->taux_brs_override) }}"
                       placeholder="15" min="0" max="20" step="0.5"
                       class="w-full px-3 py-2.5 rounded-[8px] bg-white border border-bimo-navy/15 font-body text-[13px] text-bimo-text placeholder:text-bimo-text/35 focus:outline-none focus:border-[var(--ac)] focus:ring-2 focus:ring-[var(--ac)]/15 transition-all duration-150">
                <div class="font-body text-[11px] text-bimo-text/40 mt-1">Laisser vide = 15% légal. Saisir si convention fiscale spéciale (ex: 5%)</div>
            </div>
        </div>

        <div class="mt-3 px-3 py-2.5 bg-bimo-red/10 rounded-[7px] font-body text-[11px] text-bimo-red leading-relaxed">
            <strong>⚠ BRS automatique :</strong> Tous les <strong>paiements futurs</strong> de ce locataire
            incluront une Retenue à la Source de <strong>5%</strong> sur le loyer TTC (Art. 201 §3 CGI SN).
            Les paiements passés ne sont pas modifiés (immutabilité comptable).
        </div>
    </div>
</div>

<script>
function onTypeLocataireChange(radio) {
    const val     = radio.value;
    const estEntr = ['entreprise', 'association'].includes(val);

    // Mettre à jour le champ caché
    document.getElementById('est_entreprise').value = estEntr ? '1' : '0';

    // Montrer/cacher bloc entreprise
    document.getElementById('bloc-entreprise').classList.toggle('hidden', !estEntr);

    // Styler les pills (accent = couleur agence var(--ac))
    document.querySelectorAll('#type-locataire-grid .type-loc-pill').forEach(pill => {
        const isActive = pill.closest('label').querySelector('input').value === val;
        pill.style.border     = isActive ? '1.5px solid var(--ac)' : '1.5px solid rgba(123,30,58,0.12)';
        pill.style.background = isActive ? 'rgba(var(--ac-r),var(--ac-g),var(--ac-b),0.12)' : '#fff';
    });
}
</script>
