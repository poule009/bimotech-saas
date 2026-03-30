<x-app-layout>
    <x-slot name="header">Enregistrer un paiement</x-slot>

    {{-- Header navigation --}}
    <div style="display:flex;align-items:center;gap:12px;" class="section-gap">
        <a href="{{ route('admin.paiements.index') }}"
           style="display:flex;align-items:center;justify-content:center;width:32px;height:32px;border-radius:var(--radius-sm);border:1px solid var(--border);color:var(--text-2);transition:background .15s;"
           onmouseenter="this.style.background='var(--bg)'"
           onmouseleave="this.style.background='transparent'">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:16px;height:16px;">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <div>
            <h1 style="font-size:20px;font-weight:700;color:var(--text);letter-spacing:-.3px;">Enregistrer un paiement</h1>
            <p style="font-size:13px;color:var(--text-3);margin-top:2px;">Saisir un nouveau loyer encaissé</p>
        </div>
    </div>

    <div style="max-width:680px;">
        <div class="card">
            <div class="card-body">

                {{-- Erreurs --}}
                @if($errors->any())
                    <div class="alert alert-error" style="margin-bottom:20px;">
                        @foreach($errors->all() as $error)
                            <div>❌ {{ $error }}</div>
                        @endforeach
                    </div>
                @endif

                <form method="POST" action="{{ route('admin.paiements.store') }}">
                    @csrf

                    {{-- Contrat — Recherche par nom (datalist) --}}
                    <div style="margin-bottom:20px;">
                        <label class="form-label">Contrat <span style="color:#ef4444;">*</span></label>
                        <div style="position:relative;">
                            <span style="position:absolute;left:10px;top:50%;transform:translateY(-50%);font-size:15px;pointer-events:none;">🔍</span>
                            <input type="text"
                                   id="recherche-contrat"
                                   list="contrats-datalist"
                                   class="input"
                                   style="padding-left:32px;"
                                   placeholder="Tapez 2 lettres du nom du locataire ou du bien…"
                                   autocomplete="off"
                                   oninput="onRechercheContrat(this.value)"
                                   value="@if($contratPreselectionne){{ $contratPreselectionne->locataire->name }} — {{ $contratPreselectionne->bien->reference }} ({{ number_format($contratPreselectionne->loyer_contractuel, 0, ',', ' ') }} F/mois)@elseif(old('contrat_id')){{ old('contrat_id') }}@endif">
                        </div>
                        <datalist id="contrats-datalist">
                            @foreach($contrats as $contrat)
                                <option value="{{ $contrat->locataire->name }} — {{ $contrat->bien->reference }} ({{ number_format($contrat->loyer_contractuel, 0, ',', ' ') }} F/mois)"></option>
                            @endforeach
                        </datalist>
                        {{-- Champ caché qui porte la vraie valeur soumise --}}
                        <input type="hidden" name="contrat_id" id="contrat_id"
                               value="{{ old('contrat_id', $contratPreselectionne?->id) }}">
                        <p style="font-size:11px;color:var(--text-3);margin-top:4px;">
                            💡 Tapez 2 lettres pour filtrer — le montant et la période se rempliront automatiquement.
                        </p>
                        @error('contrat_id')
                            <div style="font-size:12px;color:#dc2626;margin-top:4px;">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Infos contrat sélectionné --}}
                    <div id="infos-contrat"
                         style="display:none;background:#eff6ff;border:1px solid #bfdbfe;border-radius:var(--radius-sm);padding:14px;margin-bottom:20px;">
                        <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                            <div>
                                <div style="font-size:10px;font-weight:600;color:#3b82f6;text-transform:uppercase;letter-spacing:.5px;margin-bottom:4px;">Bien</div>
                                <div style="font-weight:700;font-size:13px;color:#1e40af;" id="info-bien">—</div>
                            </div>
                            <div>
                                <div style="font-size:10px;font-weight:600;color:#3b82f6;text-transform:uppercase;letter-spacing:.5px;margin-bottom:4px;">Locataire</div>
                                <div style="font-weight:700;font-size:13px;color:#1e40af;" id="info-locataire">—</div>
                            </div>
                            <div>
                                <div style="font-size:10px;font-weight:600;color:#3b82f6;text-transform:uppercase;letter-spacing:.5px;margin-bottom:4px;">Loyer mensuel</div>
                                <div style="font-weight:700;font-size:13px;color:#1e40af;" id="info-loyer">—</div>
                            </div>
                            <div>
                                <div style="font-size:10px;font-weight:600;color:#3b82f6;text-transform:uppercase;letter-spacing:.5px;margin-bottom:4px;">Commission</div>
                                <div style="font-weight:700;font-size:13px;color:#1e40af;" id="info-commission">—</div>
                            </div>
                        </div>
                    </div>

                    {{-- Période — TÂCHE 2 : saisie prédictive --}}
                    <div style="margin-bottom:20px;">
                        <label class="form-label">Période (mois concerné) <span style="color:#ef4444;">*</span></label>
                        <input type="month" name="periode" id="periode"
                               value="{{ old('periode', now()->format('Y-m')) }}"
                               class="input">
                        {{-- Hint AJAX : affiché après sélection d'un contrat --}}
                        <div id="periode-hint" style="display:none;margin-top:6px;padding:8px 12px;background:#f0fdf4;border:1px solid #bbf7d0;border-radius:var(--radius-sm);font-size:12px;color:#15803d;">
                            <span id="periode-hint-text"></span>
                        </div>
                        @error('periode')
                            <div style="font-size:12px;color:#dc2626;margin-top:4px;">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Montant --}}
                    <div style="margin-bottom:20px;">
                        <label class="form-label">Montant encaissé (FCFA) <span style="color:#ef4444;">*</span></label>
                        <input type="number" name="montant_encaisse" id="montant_encaisse"
                               value="{{ old('montant_encaisse') }}"
                               min="1" placeholder="250 000"
                               oninput="calculerApercu()"
                               class="input">
                        @error('montant_encaisse')
                            <div style="font-size:12px;color:#dc2626;margin-top:4px;">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Aperçu calcul automatique --}}
                    <div id="apercu-calcul"
                         style="display:none;background:var(--bg);border:1px solid var(--border);border-radius:var(--radius-sm);padding:16px;margin-bottom:20px;">
                        <div style="font-size:10px;font-weight:600;color:var(--text-3);text-transform:uppercase;letter-spacing:.5px;margin-bottom:12px;">
                            Aperçu du calcul
                        </div>
                        <div style="display:flex;flex-direction:column;gap:8px;">
                            <div style="display:flex;justify-content:space-between;font-size:13px;padding-bottom:8px;border-bottom:1px solid var(--border);">
                                <span style="color:var(--text-2);">Montant encaissé</span>
                                <span style="font-weight:700;color:var(--text);" id="ap-montant">—</span>
                            </div>
                            <div style="display:flex;justify-content:space-between;font-size:13px;">
                                <span style="color:var(--text-2);">Commission HT</span>
                                <span style="color:#d97706;" id="ap-commission-ht">—</span>
                            </div>
                            <div style="display:flex;justify-content:space-between;font-size:12px;">
                                <span style="color:var(--text-3);">TVA 18% sur commission</span>
                                <span style="color:var(--text-3);" id="ap-tva">—</span>
                            </div>
                            <div style="display:flex;justify-content:space-between;font-size:13px;padding-bottom:8px;border-bottom:1px solid var(--border);">
                                <span style="color:var(--text-2);">Commission TTC</span>
                                <span style="color:#b45309;font-weight:600;" id="ap-commission-ttc">—</span>
                            </div>
                            <div style="display:flex;justify-content:space-between;font-size:14px;padding:10px 12px;background:#f0fdf4;border-radius:var(--radius-sm);">
                                <span style="font-weight:700;color:#15803d;">Net propriétaire</span>
                                <span style="font-weight:800;color:#16a34a;" id="ap-net">—</span>
                            </div>
                        </div>
                    </div>

                    {{-- Mode de paiement --}}
                    <div style="margin-bottom:20px;">
                        <label class="form-label">Mode de paiement <span style="color:#ef4444;">*</span></label>
                        <select name="mode_paiement" class="input">
                            <option value="">— Choisir —</option>
                            <option value="especes"      {{ old('mode_paiement') === 'especes'      ? 'selected' : '' }}>Espèces</option>
                            <option value="virement"     {{ old('mode_paiement') === 'virement'     ? 'selected' : '' }}>Virement bancaire</option>
                            <option value="mobile_money" {{ old('mode_paiement') === 'mobile_money' ? 'selected' : '' }}>Mobile Money (Wave / OM)</option>
                            <option value="cheque"       {{ old('mode_paiement') === 'cheque'       ? 'selected' : '' }}>Chèque</option>
                        </select>
                        @error('mode_paiement')
                            <div style="font-size:12px;color:#dc2626;margin-top:4px;">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Date de paiement --}}
                    <div style="margin-bottom:20px;">
                        <label class="form-label">Date du règlement <span style="color:#ef4444;">*</span></label>
                        <input type="date" name="date_paiement"
                               value="{{ old('date_paiement', now()->format('Y-m-d')) }}"
                               class="input">
                        @error('date_paiement')
                            <div style="font-size:12px;color:#dc2626;margin-top:4px;">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Premier paiement + Caution --}}
                    <div style="background:#eff6ff;border:1px solid #bfdbfe;border-radius:var(--radius-sm);padding:16px;margin-bottom:20px;">
                        <label style="display:flex;align-items:center;gap:10px;cursor:pointer;margin-bottom:0;">
                            <input type="checkbox" name="est_premier_paiement" value="1"
                                   id="premier_paiement"
                                   onchange="toggleCaution(this.checked)"
                                   {{ old('est_premier_paiement') ? 'checked' : '' }}
                                   style="width:16px;height:16px;accent-color:var(--agency);cursor:pointer;">
                            <span style="font-size:13px;font-weight:600;color:#1e40af;">
                                Premier paiement — inclure la caution
                            </span>
                        </label>

                        <div id="bloc-caution" style="{{ old('est_premier_paiement') ? '' : 'display:none;' }}margin-top:14px;">
                            <label class="form-label" style="color:#1d4ed8;">Caution perçue (FCFA)</label>
                            <input type="number" name="caution_percue"
                                   value="{{ old('caution_percue') }}"
                                   min="0" placeholder="250 000"
                                   class="input">
                        </div>
                    </div>

                    {{-- Notes --}}
                    <div style="margin-bottom:24px;">
                        <label class="form-label">Notes</label>
                        <textarea name="notes" rows="2"
                                  placeholder="Observations particulières..."
                                  class="input" style="resize:vertical;">{{ old('notes') }}</textarea>
                    </div>

                    {{-- Boutons --}}
                    <div style="display:flex;justify-content:flex-end;gap:10px;padding-top:20px;border-top:1px solid var(--border);">
                        <a href="{{ route('admin.paiements.index') }}" class="btn btn-secondary">
                            Annuler
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:15px;height:15px;">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            Valider le paiement
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>

    <script>
        let tauxCommission = 0;

        // ── Données des contrats pour la recherche datalist ──────────────────
        const contratsData = {
            @foreach($contrats as $contrat)
            {{ json_encode($contrat->locataire->name . ' — ' . $contrat->bien->reference . ' (' . number_format($contrat->loyer_contractuel, 0, ',', ' ') . ' F/mois)') }}: {
                id:         {{ $contrat->id }},
                loyer:      {{ $contrat->loyer_contractuel }},
                commission: {{ $contrat->bien->taux_commission }},
                bien:       {{ json_encode($contrat->bien->reference) }},
                locataire:  {{ json_encode($contrat->locataire->name) }}
            },
            @endforeach
        };

        // URL de base pour l'endpoint AJAX période suggérée
        const urlDernierePeriode = '{{ url("admin/paiements/dernier-periode") }}';

        function onRechercheContrat(valeur) {
            const data = contratsData[valeur];
            if (data) {
                document.getElementById('contrat_id').value = data.id;
                chargerInfosContratData(data);
                // ── TÂCHE 2 : Appel AJAX pour suggérer la prochaine période ──
                chargerPeriodeSuggeree(data.id);
            } else {
                document.getElementById('contrat_id').value = '';
                document.getElementById('infos-contrat').style.display  = 'none';
                document.getElementById('apercu-calcul').style.display  = 'none';
                document.getElementById('periode-hint').style.display   = 'none';
                tauxCommission = 0;
            }
        }

        /**
         * TÂCHE 2 — Saisie prédictive de la période.
         * Appelle GET /admin/paiements/dernier-periode/{contratId}
         * et pré-remplit le champ "période" avec le mois suivant le dernier payé.
         */
        async function chargerPeriodeSuggeree(contratId) {
            const hint     = document.getElementById('periode-hint');
            const hintText = document.getElementById('periode-hint-text');
            const periodeInput = document.getElementById('periode');

            try {
                const response = await fetch(urlDernierePeriode + '/' + contratId, {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                    }
                });

                if (!response.ok) return;

                const json = await response.json();

                // Pré-remplir le champ période avec la suggestion
                periodeInput.value = json.periode_suggeree;

                // Afficher le message d'aide contextuel
                if (json.dernier_paye) {
                    hintText.innerHTML = '✅ Dernier mois payé : <strong>' + json.dernier_paye + '</strong>'
                        + ' — Période suggérée : <strong>' + json.label + '</strong>';
                } else {
                    hintText.innerHTML = '🆕 Aucun paiement enregistré — Premier mois suggéré : <strong>' + json.label + '</strong>';
                }
                hint.style.display = 'block';

            } catch (e) {
                // En cas d'erreur réseau, on laisse la valeur actuelle sans bloquer
                hint.style.display = 'none';
            }
        }

        function chargerInfosContratData(data) {
            tauxCommission = parseFloat(data.commission) || 0;

            document.getElementById('info-bien').textContent       = data.bien;
            document.getElementById('info-locataire').textContent  = data.locataire;
            document.getElementById('info-loyer').textContent      = parseInt(data.loyer).toLocaleString('fr-FR') + ' FCFA';
            document.getElementById('info-commission').textContent = data.commission + '%';
            document.getElementById('infos-contrat').style.display = 'block';

            const montantInput = document.getElementById('montant_encaisse');
            if (!montantInput.value) {
                montantInput.value = data.loyer;
            }
            calculerApercu();
        }

        // Compatibilité ancienne fonction (non utilisée mais conservée par sécurité)
        function chargerInfosContrat(select) {
            const option = select.options[select.selectedIndex];
            const infos  = document.getElementById('infos-contrat');
            const apercu = document.getElementById('apercu-calcul');

            if (!option.value) {
                infos.style.display  = 'none';
                apercu.style.display = 'none';
                tauxCommission = 0;
                return;
            }

            tauxCommission = parseFloat(option.dataset.commission) || 0;

            document.getElementById('info-bien').textContent      = option.dataset.bien;
            document.getElementById('info-locataire').textContent = option.dataset.locataire;
            document.getElementById('info-loyer').textContent     = parseInt(option.dataset.loyer).toLocaleString('fr-FR') + ' FCFA';
            document.getElementById('info-commission').textContent = option.dataset.commission + '%';
            infos.style.display = 'block';

            const montantInput = document.getElementById('montant_encaisse');
            if (!montantInput.value) {
                montantInput.value = option.dataset.loyer;
            }
            calculerApercu();
        }

        function calculerApercu() {
            const montant = parseFloat(document.getElementById('montant_encaisse').value) || 0;
            if (!montant || !tauxCommission) return;

            const commissionHT  = Math.round(montant * tauxCommission / 100);
            const tva           = Math.round(commissionHT * 0.18);
            const commissionTTC = commissionHT + tva;
            const net           = montant - commissionTTC;

            document.getElementById('ap-montant').textContent        = montant.toLocaleString('fr-FR') + ' F';
            document.getElementById('ap-commission-ht').textContent  = '− ' + commissionHT.toLocaleString('fr-FR') + ' F';
            document.getElementById('ap-tva').textContent            = '− ' + tva.toLocaleString('fr-FR') + ' F';
            document.getElementById('ap-commission-ttc').textContent = '− ' + commissionTTC.toLocaleString('fr-FR') + ' F';
            document.getElementById('ap-net').textContent            = net.toLocaleString('fr-FR') + ' F';

            document.getElementById('apercu-calcul').style.display = 'block';
        }

        function toggleCaution(checked) {
            const bloc = document.getElementById('bloc-caution');
            bloc.style.display = checked ? 'block' : 'none';
        }

        window.addEventListener('DOMContentLoaded', () => {
            // Si un contrat est pré-sélectionné (via URL ou old()), charger ses infos + période
            const inputRecherche = document.getElementById('recherche-contrat');
            if (inputRecherche.value) {
                onRechercheContrat(inputRecherche.value);
            }
        });
    </script>

</x-app-layout>