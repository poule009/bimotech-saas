{{--
    ════════════════════════════════════════════════════════════════════
    PARTIEL — Section "Type de locataire" pour users/create.blade.php
    et users/edit.blade.php (quand role = 'locataire').

    UTILISATION :
      @if($role === 'locataire' || $user->isLocataire())
        @include('admin.users._section-type-locataire', ['user' => $user ?? null])
      @endif
    ════════════════════════════════════════════════════════════════════
--}}

@php
    $locataire     = $user?->locataire ?? null;
    $estEntreprise = old('est_entreprise', $locataire?->est_entreprise ?? false);
    $typeLocataire = old('type_locataire', $locataire?->type_locataire ?? 'particulier');
@endphp

<div style="font-size:12px;font-weight:700;color:#374151;text-transform:uppercase;letter-spacing:.5px;margin-bottom:14px;padding-bottom:8px;border-bottom:1px solid #e5e7eb;margin-top:24px">
    ⚖️ Statut fiscal du locataire
</div>

{{-- ── Type de locataire ── --}}
<div style="margin-bottom:16px">
    <label style="display:block;font-size:12px;font-weight:600;color:#374151;margin-bottom:8px">
        Type de locataire <span style="color:#dc2626">*</span>
    </label>

    <div style="display:grid;grid-template-columns:repeat(5,1fr);gap:8px" id="type-locataire-grid">
        @foreach([
            'particulier' => ['label' => 'Particulier',  'icon' => '👤', 'brs' => false],
            'entreprise'  => ['label' => 'Entreprise',   'icon' => '🏢', 'brs' => true],
            'association' => ['label' => 'Association',  'icon' => '🤝', 'brs' => true],
            'ambassade'   => ['label' => 'Ambassade',    'icon' => '🏛️', 'brs' => false],
            'ong'         => ['label' => 'ONG / Org.',   'icon' => '🌍', 'brs' => false],
        ] as $key => $info)
        <label style="display:block;cursor:pointer">
            <input type="radio" name="type_locataire" value="{{ $key }}"
                   {{ $typeLocataire === $key ? 'checked' : '' }}
                   onchange="onTypeLocataireChange(this)"
                   style="display:none">
            <div class="type-loc-pill {{ $typeLocataire === $key ? 'active' : '' }}"
                 data-brs="{{ $info['brs'] ? '1' : '0' }}"
                 style="padding:10px 8px;border:1.5px solid {{ $typeLocataire === $key ? '#c9a84c' : '#e5e7eb' }};border-radius:9px;text-align:center;background:{{ $typeLocataire === $key ? '#f5e9c9' : '#fff' }};transition:all .15s">
                <div style="font-size:16px;margin-bottom:4px">{{ $info['icon'] }}</div>
                <div style="font-size:11px;font-weight:{{ $typeLocataire === $key ? '600' : '500' }};color:{{ $typeLocataire === $key ? '#8a6e2f' : '#6b7280' }}">{{ $info['label'] }}</div>
                @if($info['brs'])
                <div style="font-size:9px;color:#dc2626;margin-top:2px;font-weight:600">BRS 5%</div>
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
<div id="bloc-entreprise" style="{{ in_array($typeLocataire, ['entreprise','association']) ? '' : 'display:none' }}">
    <div style="background:#fef9ec;border:1px solid #fde68a;border-radius:10px;padding:14px 16px;margin-bottom:14px">
        <div style="font-size:12px;font-weight:600;color:#92400e;margin-bottom:12px;display:flex;align-items:center;gap:6px">
            <svg style="width:14px;height:14px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M13 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V9z"/><polyline points="13 2 13 9 20 9"/></svg>
            Informations de la personne morale
        </div>

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px">
            <div style="grid-column:span 2">
                <label style="display:block;font-size:12px;font-weight:600;color:#374151;margin-bottom:5px">
                    Raison sociale (nom officiel de l'entreprise)
                </label>
                <input type="text" name="nom_entreprise" id="nom_entreprise"
                       value="{{ old('nom_entreprise', $locataire?->nom_entreprise) }}"
                       placeholder="Ex: Société Immobilière Dakar SARL"
                       style="width:100%;padding:9px 12px;border:1px solid #e5e7eb;border-radius:8px;font-size:13px;font-family:'DM Sans',sans-serif;background:#fff">
                <div style="font-size:11px;color:#9ca3af;margin-top:4px">Sera utilisé à la place du nom du représentant sur les quittances</div>
            </div>

            <div>
                <label style="display:block;font-size:12px;font-weight:600;color:#374151;margin-bottom:5px">
                    NINEA <span style="font-size:11px;font-weight:400;color:#9ca3af">(Numéro fiscal)</span>
                </label>
                <input type="text" name="ninea_locataire" id="ninea_locataire"
                       value="{{ old('ninea_locataire', $locataire?->ninea_locataire) }}"
                       placeholder="Ex: 00123456789"
                       maxlength="30"
                       style="width:100%;padding:9px 12px;border:1px solid #e5e7eb;border-radius:8px;font-size:13px;font-family:'DM Sans',sans-serif;background:#fff">
                <div style="font-size:11px;color:#9ca3af;margin-top:4px">Apparaît sur la quittance PDF pour justifier la BRS</div>
            </div>

            <div>
                <label style="display:block;font-size:12px;font-weight:600;color:#374151;margin-bottom:5px">
                    RCCM <span style="font-size:11px;font-weight:400;color:#9ca3af">(Registre de commerce)</span>
                </label>
                <input type="text" name="rccm_locataire" id="rccm_locataire"
                       value="{{ old('rccm_locataire', $locataire?->rccm_locataire) }}"
                       placeholder="Ex: SN-DKR-2024-B-12345"
                       maxlength="60"
                       style="width:100%;padding:9px 12px;border:1px solid #e5e7eb;border-radius:8px;font-size:13px;font-family:'DM Sans',sans-serif;background:#fff">
            </div>

            <div>
                <label style="display:block;font-size:12px;font-weight:600;color:#374151;margin-bottom:5px">
                    Taux BRS personnalisé <span style="font-size:11px;font-weight:400;color:#9ca3af">(%)</span>
                </label>
                <input type="number" name="taux_brs_override" id="taux_brs_override"
                       value="{{ old('taux_brs_override', $locataire?->taux_brs_override) }}"
                       placeholder="15" min="0" max="20" step="0.5"
                       style="width:100%;padding:9px 12px;border:1px solid #e5e7eb;border-radius:8px;font-size:13px;font-family:'DM Sans',sans-serif;background:#fff">
                <div style="font-size:11px;color:#9ca3af;margin-top:4px">Laisser vide = 15% légal. Saisir si convention fiscale spéciale (ex: 5%)</div>
            </div>
        </div>

        <div style="margin-top:12px;padding:9px 12px;background:#fee2e2;border-radius:7px;font-size:11px;color:#dc2626;line-height:1.6">
            <strong>⚠ BRS automatique :</strong> Tous les <strong>paiements futurs</strong> de ce locataire
            incluront une Retenue à la Source de <strong>5%</strong> sur le loyer TTC (Art. 201 §3 CGI SN).
            Les paiements passés ne sont pas modifiés (immutabilité comptable).
        </div>
    </div>
</div>

<script>
function onTypeLocataireChange(radio) {
    const val     = radio.value;
    const isBrs   = ['entreprise', 'association'].includes(val);
    const estEntr = ['entreprise', 'association'].includes(val);

    // Mettre à jour le champ caché
    document.getElementById('est_entreprise').value = estEntr ? '1' : '0';

    // Montrer/cacher bloc entreprise
    document.getElementById('bloc-entreprise').style.display = estEntr ? '' : 'none';

    // Styler les pills
    document.querySelectorAll('#type-locataire-grid .type-loc-pill').forEach(pill => {
        const isActive = pill.closest('label').querySelector('input').value === val;
        pill.style.border      = isActive ? '1.5px solid #c9a84c' : '1.5px solid #e5e7eb';
        pill.style.background  = isActive ? '#f5e9c9' : '#fff';
    });
}
</script>