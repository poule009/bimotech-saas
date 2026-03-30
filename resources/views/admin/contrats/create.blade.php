<x-app-layout>
    <x-slot name="header">Nouveau contrat de bail</x-slot>

    {{-- Header navigation --}}
    <div style="display:flex;align-items:center;gap:12px;" class="section-gap">
        <a href="{{ route('admin.contrats.index') }}"
           style="display:flex;align-items:center;justify-content:center;width:32px;height:32px;border-radius:var(--radius-sm);border:1px solid var(--border);color:var(--text-2);transition:background .15s;"
           onmouseenter="this.style.background='var(--bg)'"
           onmouseleave="this.style.background='transparent'">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:16px;height:16px;">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <div>
            <h1 style="font-size:20px;font-weight:700;color:var(--text);letter-spacing:-.3px;">Nouveau contrat de bail</h1>
            <p style="font-size:13px;color:var(--text-3);margin-top:2px;">Renseignez les informations du contrat</p>
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

                <form method="POST" action="{{ route('admin.contrats.store') }}">
                    @csrf

                    {{-- Bien --}}
                    <div style="margin-bottom:20px;">
                        <label class="form-label">Bien à louer <span style="color:#ef4444;">*</span></label>
                        <select name="bien_id" id="bien_id"
                                onchange="chargerInfosBien(this)"
                                class="input">
                            <option value="">— Sélectionner un bien disponible —</option>
                            @foreach($biens as $bien)
                                <option value="{{ $bien->id }}"
                                        data-loyer="{{ $bien->loyer_mensuel }}"
                                        data-commission="{{ $bien->taux_commission }}"
                                        data-proprietaire="{{ $bien->proprietaire->name }}"
                                        {{ old('bien_id', $bienPreselectionne?->id) == $bien->id ? 'selected' : '' }}>
                                    {{ $bien->reference }} — {{ $bien->type }}, {{ $bien->adresse }}
                                    ({{ number_format($bien->loyer_mensuel, 0, ',', ' ') }} F/mois)
                                    — {{ $bien->proprietaire->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('bien_id')
                            <div style="font-size:12px;color:#dc2626;margin-top:4px;">{{ $message }}</div>
                        @enderror
                        @if($biens->isEmpty())
                            <div style="font-size:12px;color:#d97706;margin-top:6px;display:flex;align-items:center;gap:6px;">
                                ⚠️ Aucun bien disponible.
                                <a href="{{ route('biens.index') }}" style="color:var(--agency);font-weight:600;">Vérifier les biens →</a>
                            </div>
                        @endif
                    </div>

                    {{-- Infos bien sélectionné --}}
                    <div id="infos-bien"
                         style="display:none;background:#eff6ff;border:1px solid #bfdbfe;border-radius:var(--radius-sm);padding:14px;margin-bottom:20px;">
                        <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:12px;">
                            <div>
                                <div style="font-size:10px;font-weight:600;color:#3b82f6;text-transform:uppercase;letter-spacing:.5px;margin-bottom:4px;">Loyer mensuel</div>
                                <div style="font-weight:700;font-size:14px;color:#1e40af;" id="info-loyer">—</div>
                            </div>
                            <div>
                                <div style="font-size:10px;font-weight:600;color:#3b82f6;text-transform:uppercase;letter-spacing:.5px;margin-bottom:4px;">Commission</div>
                                <div style="font-weight:700;font-size:14px;color:#1e40af;" id="info-commission">—</div>
                            </div>
                            <div>
                                <div style="font-size:10px;font-weight:600;color:#3b82f6;text-transform:uppercase;letter-spacing:.5px;margin-bottom:4px;">Propriétaire</div>
                                <div style="font-weight:700;font-size:14px;color:#1e40af;" id="info-proprietaire">—</div>
                            </div>
                        </div>
                    </div>

                    {{-- Locataire --}}
                    <div style="margin-bottom:20px;">
                        <label class="form-label">Locataire <span style="color:#ef4444;">*</span></label>
                        <div style="display:flex;gap:8px;align-items:center;">
                            <select name="locataire_id" id="locataire_id" class="input" style="flex:1;">
                                <option value="">— Sélectionner un locataire —</option>
                                @foreach($locataires as $locataire)
                                    <option value="{{ $locataire->id }}"
                                            {{ old('locataire_id', request('locataire_id')) == $locataire->id ? 'selected' : '' }}>
                                        {{ $locataire->name }} — {{ $locataire->email }}
                                    </option>
                                @endforeach
                            </select>
                            <button type="button" onclick="ouvrirModaleLocataire()"
                                    title="Ajouter un nouveau locataire"
                                    style="flex-shrink:0;display:flex;align-items:center;justify-content:center;width:38px;height:38px;border-radius:var(--radius-sm);border:1.5px solid var(--agency);background:#f5f3ff;color:var(--agency);cursor:pointer;font-size:20px;font-weight:700;transition:background .15s;"
                                    onmouseenter="this.style.background='#ede9fe'"
                                    onmouseleave="this.style.background='#f5f3ff'">
                                +
                            </button>
                        </div>
                        <p style="font-size:11px;color:var(--text-3);margin-top:4px;">
                            💡 Le locataire n'existe pas encore ? Cliquez sur <strong>+</strong> pour le créer directement.
                        </p>
                        @error('locataire_id')
                            <div style="font-size:12px;color:#dc2626;margin-top:4px;">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Dates --}}
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:20px;">
                        <div>
                            <label class="form-label">Date de début <span style="color:#ef4444;">*</span></label>
                            <input type="date" name="date_debut"
                                   value="{{ old('date_debut', now()->format('Y-m-d')) }}"
                                   class="input">
                            @error('date_debut')
                                <div style="font-size:12px;color:#dc2626;margin-top:4px;">{{ $message }}</div>
                            @enderror
                        </div>
                        <div>
                            <label class="form-label">
                                Date de fin
                                <span style="font-size:11px;font-weight:400;color:var(--text-3);">(vide = indéterminée)</span>
                            </label>
                            <input type="date" name="date_fin"
                                   value="{{ old('date_fin') }}"
                                   class="input">
                            @error('date_fin')
                                <div style="font-size:12px;color:#dc2626;margin-top:4px;">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    {{-- Caution --}}
                    <div style="margin-bottom:20px;">
                        <label class="form-label">Caution (FCFA) <span style="color:#ef4444;">*</span></label>
                        <input type="number" name="caution"
                               value="{{ old('caution') }}"
                               min="0" id="caution"
                               placeholder="Généralement 1 ou 2 mois de loyer"
                               class="input">
                        <div style="font-size:11px;color:var(--text-3);margin-top:4px;" id="caution-hint"></div>
                        @error('caution')
                            <div style="font-size:12px;color:#dc2626;margin-top:4px;">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Observations --}}
                    <div style="margin-bottom:24px;">
                        <label class="form-label">Observations</label>
                        <textarea name="observations" rows="3"
                                  placeholder="Conditions particulières, préavis, état des lieux..."
                                  class="input" style="resize:vertical;">{{ old('observations') }}</textarea>
                    </div>

                    {{-- Info loyer automatique --}}
                    <div class="alert alert-info" style="margin-bottom:24px;">
                        ℹ️ Le loyer contractuel sera automatiquement repris depuis la fiche du bien sélectionné.
                    </div>

                    {{-- Boutons --}}
                    <div style="display:flex;justify-content:flex-end;gap:10px;padding-top:20px;border-top:1px solid var(--border);">
                        <a href="{{ route('admin.contrats.index') }}" class="btn btn-secondary">
                            Annuler
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:15px;height:15px;">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            Créer le contrat
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════════════════════
         MODALE — Création rapide d'un locataire
    ═══════════════════════════════════════════════════════════════════ --}}
    <div id="modal-locataire" style="display:none;position:fixed;inset:0;z-index:9999;background:rgba(0,0,0,.45);backdrop-filter:blur(2px);align-items:center;justify-content:center;padding:16px;">
        <div style="background:#fff;border-radius:var(--radius);box-shadow:0 20px 60px rgba(0,0,0,.25);width:100%;max-width:460px;overflow:hidden;">

            {{-- En-tête modale --}}
            <div style="padding:18px 20px;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between;background:#f5f3ff;">
                <div>
                    <div style="font-size:15px;font-weight:700;color:var(--text);">👤 Nouveau locataire</div>
                    <div style="font-size:12px;color:var(--text-3);margin-top:2px;">Création rapide — sera ajouté au menu déroulant</div>
                </div>
                <button type="button" onclick="fermerModaleLocataire()"
                        style="width:28px;height:28px;border-radius:50%;border:none;background:#e5e7eb;color:var(--text-2);cursor:pointer;font-size:16px;display:flex;align-items:center;justify-content:center;">
                    ×
                </button>
            </div>

            {{-- Corps modale --}}
            <div style="padding:20px;">
                <div id="modal-erreur" style="display:none;background:#fef2f2;border:1px solid #fecaca;border-radius:var(--radius-sm);padding:10px 14px;margin-bottom:16px;font-size:13px;color:#dc2626;"></div>

                <div style="margin-bottom:14px;">
                    <label class="form-label">Nom complet <span style="color:#ef4444;">*</span></label>
                    <input type="text" id="modal-name" class="input" placeholder="Ex : Fatou Diallo" autocomplete="off">
                </div>
                <div style="margin-bottom:14px;">
                    <label class="form-label">Email <span style="color:#ef4444;">*</span></label>
                    <input type="email" id="modal-email" class="input" placeholder="fatou@exemple.sn" autocomplete="off">
                </div>
                <div style="margin-bottom:14px;">
                    <label class="form-label">Téléphone</label>
                    <input type="tel" id="modal-telephone" class="input" placeholder="+221 77 000 00 00">
                </div>
                <div style="margin-bottom:6px;">
                    <label class="form-label">Mot de passe <span style="color:#ef4444;">*</span></label>
                    <input type="password" id="modal-password" class="input" placeholder="Minimum 8 caractères" autocomplete="new-password">
                </div>
            </div>

            {{-- Pied modale --}}
            <div style="padding:14px 20px;border-top:1px solid var(--border);display:flex;justify-content:flex-end;gap:10px;background:#fafafa;">
                <button type="button" onclick="fermerModaleLocataire()" class="btn btn-secondary">
                    Annuler
                </button>
                <button type="button" onclick="soumettreLocataireRapide()" id="btn-modal-submit" class="btn btn-primary">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:14px;height:14px;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Créer le locataire
                </button>
            </div>
        </div>
    </div>

    <script>
        let loyerMensuel = 0;

        function chargerInfosBien(select) {
            const option = select.options[select.selectedIndex];
            const infos  = document.getElementById('infos-bien');

            if (!option.value) {
                infos.style.display = 'none';
                loyerMensuel = 0;
                document.getElementById('caution-hint').textContent = '';
                return;
            }

            loyerMensuel = parseFloat(option.dataset.loyer) || 0;

            document.getElementById('info-loyer').textContent =
                parseInt(loyerMensuel).toLocaleString('fr-FR') + ' FCFA';
            document.getElementById('info-commission').textContent =
                option.dataset.commission + '%';
            document.getElementById('info-proprietaire').textContent =
                option.dataset.proprietaire;

            infos.style.display = 'block';

            // Suggérer caution = 2 mois de loyer
            const cautionInput = document.getElementById('caution');
            if (!cautionInput.value) {
                cautionInput.value = Math.round(loyerMensuel * 2);
            }
            document.getElementById('caution-hint').textContent =
                '💡 Suggestion : ' + Math.round(loyerMensuel).toLocaleString('fr-FR') +
                ' F (1 mois) ou ' + Math.round(loyerMensuel * 2).toLocaleString('fr-FR') + ' F (2 mois)';
        }

        window.addEventListener('DOMContentLoaded', () => {
            const select = document.getElementById('bien_id');
            if (select.value) chargerInfosBien(select);
        });

        // ── Modale locataire rapide ──────────────────────────────────────────

        function ouvrirModaleLocataire() {
            const modal = document.getElementById('modal-locataire');
            modal.style.display = 'flex';
            document.getElementById('modal-name').focus();
            document.getElementById('modal-erreur').style.display = 'none';
            // Vider les champs
            ['modal-name','modal-email','modal-telephone','modal-password'].forEach(id => {
                document.getElementById(id).value = '';
            });
        }

        function fermerModaleLocataire() {
            document.getElementById('modal-locataire').style.display = 'none';
        }

        // Fermer en cliquant sur le fond
        document.getElementById('modal-locataire').addEventListener('click', function(e) {
            if (e.target === this) fermerModaleLocataire();
        });

        // Fermer avec Échap
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') fermerModaleLocataire();
        });

        async function soumettreLocataireRapide() {
            const btn    = document.getElementById('btn-modal-submit');
            const errDiv = document.getElementById('modal-erreur');
            const name   = document.getElementById('modal-name').value.trim();
            const email  = document.getElementById('modal-email').value.trim();
            const tel    = document.getElementById('modal-telephone').value.trim();
            const pwd    = document.getElementById('modal-password').value;

            errDiv.style.display = 'none';

            if (!name || !email || !pwd) {
                errDiv.textContent = '❌ Veuillez remplir tous les champs obligatoires.';
                errDiv.style.display = 'block';
                return;
            }

            btn.disabled = true;
            btn.textContent = '⏳ Création…';

            try {
                const response = await fetch('{{ route('admin.contrats.locataire-rapide') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ name, email, telephone: tel, password: pwd }),
                });

                const data = await response.json();

                if (!response.ok) {
                    // Erreurs de validation Laravel
                    const msgs = data.errors
                        ? Object.values(data.errors).flat().join(' — ')
                        : (data.message || 'Erreur inconnue.');
                    errDiv.textContent = '❌ ' + msgs;
                    errDiv.style.display = 'block';
                    return;
                }

                // Ajouter l'option dans le select et la sélectionner
                const select = document.getElementById('locataire_id');
                const option = new Option(data.name + ' — ' + email, data.id, true, true);
                select.add(option);
                select.value = data.id;

                fermerModaleLocataire();

                // Feedback visuel
                select.style.borderColor = '#22c55e';
                setTimeout(() => { select.style.borderColor = ''; }, 2000);

            } catch (err) {
                errDiv.textContent = '❌ Erreur réseau. Veuillez réessayer.';
                errDiv.style.display = 'block';
            } finally {
                btn.disabled = false;
                btn.innerHTML = '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:14px;height:14px;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> Créer le locataire';
            }
        }
    </script>

</x-app-layout>