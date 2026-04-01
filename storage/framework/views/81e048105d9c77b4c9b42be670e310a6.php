<?php if (isset($component)) { $__componentOriginal9ac128a9029c0e4701924bd2d73d7f54 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54 = $attributes; } ?>
<?php $component = App\View\Components\AppLayout::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('app-layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\App\View\Components\AppLayout::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
     <?php $__env->slot('header', null, []); ?> Enregistrer un paiement <?php $__env->endSlot(); ?>

    
    <div style="display:flex;align-items:center;gap:12px;" class="section-gap">
        <a href="<?php echo e(route('admin.paiements.index')); ?>"
           style="display:flex;align-items:center;justify-content:center;width:32px;height:32px;border-radius:var(--radius-sm);border:1px solid var(--border);color:var(--text-2);transition:background .15s;"
           onmouseenter="this.style.background='var(--bg)'"
           onmouseleave="this.style.background='transparent'">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:16px;height:16px;">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <div>
            <h1 style="font-size:20px;font-weight:700;color:var(--text);letter-spacing:-.3px;">Enregistrer un paiement</h1>
            <p style="font-size:13px;color:var(--text-3);margin-top:2px;">Saisie manuelle d'un loyer perçu</p>
        </div>
    </div>

    <div style="max-width:680px;">
        <div class="card">
            <div class="card-body">

                <?php if($errors->any()): ?>
                    <div class="alert alert-error" style="margin-bottom:20px;">
                        <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div>❌ <?php echo e($error); ?></div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                <?php endif; ?>

                <form method="POST" action="<?php echo e(route('admin.paiements.store')); ?>">
                    <?php echo csrf_field(); ?>

                    
                    <div style="font-size:12px;font-weight:700;color:var(--text-2);text-transform:uppercase;letter-spacing:.5px;margin-bottom:12px;padding-bottom:8px;border-bottom:1px solid var(--border);">
                        📋 Contrat concerné
                    </div>

                    <div style="margin-bottom:20px;">
                        <label class="form-label">Locataire / Bien <span style="color:#ef4444;">*</span></label>
                        <input type="text"
                               id="recherche-contrat"
                               list="contrats-datalist"
                               autocomplete="off"
                               placeholder="Tapez 2 lettres pour filtrer…"
                               class="input"
                               oninput="onRechercheContrat(this.value)"
                               value="<?php if($contratPreselectionne): ?><?php echo e($contratPreselectionne->locataire->name); ?> — <?php echo e($contratPreselectionne->bien->reference); ?> (<?php echo e(number_format($contratPreselectionne->loyer_contractuel, 0, ',', ' ')); ?> F/mois)<?php elseif(old('contrat_id')): ?><?php echo e(old('contrat_id')); ?><?php endif; ?>">
                        <datalist id="contrats-datalist">
                            <?php $__currentLoopData = $contrats; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $contrat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($contrat->locataire->name); ?> — <?php echo e($contrat->bien->reference); ?> (<?php echo e(number_format($contrat->loyer_contractuel, 0, ',', ' ')); ?> F/mois)"></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </datalist>
                        <input type="hidden" name="contrat_id" id="contrat_id"
                               value="<?php echo e(old('contrat_id', $contratPreselectionne?->id)); ?>">
                        <p style="font-size:11px;color:var(--text-3);margin-top:4px;">
                            💡 Tapez 2 lettres pour filtrer — le loyer et la période se rempliront automatiquement.
                        </p>
                        <?php $__errorArgs = ['contrat_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <div style="font-size:12px;color:#dc2626;margin-top:4px;"><?php echo e($message); ?></div>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    
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
                                <div style="font-size:10px;font-weight:600;color:#3b82f6;text-transform:uppercase;letter-spacing:.5px;margin-bottom:4px;">Loyer contractuel</div>
                                <div style="font-weight:700;font-size:13px;color:#1e40af;" id="info-loyer">—</div>
                            </div>
                            <div>
                                <div style="font-size:10px;font-weight:600;color:#3b82f6;text-transform:uppercase;letter-spacing:.5px;margin-bottom:4px;">Commission</div>
                                <div style="font-weight:700;font-size:13px;color:#1e40af;" id="info-commission">—</div>
                            </div>
                            <div>
                                <div style="font-size:10px;font-weight:600;color:#3b82f6;text-transform:uppercase;letter-spacing:.5px;margin-bottom:4px;">Réf. bail</div>
                                <div style="font-weight:700;font-size:13px;color:#1e40af;" id="info-ref-bail">—</div>
                            </div>
                        </div>
                    </div>

                    
                    <div style="margin-bottom:20px;">
                        <label class="form-label">Période (mois concerné) <span style="color:#ef4444;">*</span></label>
                        <input type="month" name="periode" id="periode"
                               value="<?php echo e(old('periode', now()->format('Y-m'))); ?>"
                               class="input">
                        <div id="periode-hint" style="display:none;margin-top:6px;padding:8px 12px;background:#f0fdf4;border:1px solid #bbf7d0;border-radius:var(--radius-sm);font-size:12px;color:#15803d;">
                            <span id="periode-hint-text"></span>
                        </div>
                        <?php $__errorArgs = ['periode'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <div style="font-size:12px;color:#dc2626;margin-top:4px;"><?php echo e($message); ?></div>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    
                    <div style="font-size:12px;font-weight:700;color:var(--text-2);text-transform:uppercase;letter-spacing:.5px;margin-bottom:12px;padding-bottom:8px;border-bottom:1px solid var(--border);">
                        💰 Décomposition du loyer encaissé
                    </div>

                    <div style="background:#f0f4ff;border:1px solid #c7d2fe;border-radius:var(--radius-sm);padding:10px 14px;margin-bottom:16px;font-size:12px;color:#3730a3;">
                        La commission est calculée sur le <strong>loyer nu uniquement</strong>. Les charges et la TOM ne sont pas commissionnées.
                    </div>

                    
                    <div style="margin-bottom:16px;">
                        <label class="form-label">Loyer nu (FCFA) <span style="color:#ef4444;">*</span></label>
                        <input type="number" name="loyer_nu" id="loyer_nu"
                               value="<?php echo e(old('loyer_nu')); ?>"
                               min="1" step="500"
                               placeholder="Ex : 200 000"
                               class="input"
                               oninput="calculerApercu()">
                        <div style="font-size:11px;color:var(--text-3);margin-top:4px;">Loyer hors charges et hors TOM — base de calcul de la commission agence</div>
                        <?php $__errorArgs = ['loyer_nu'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <div style="font-size:12px;color:#dc2626;margin-top:4px;"><?php echo e($message); ?></div>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:16px;">
                        
                        <div>
                            <label class="form-label">Charges locatives (FCFA)</label>
                            <input type="number" name="charges_amount" id="charges_amount"
                                   value="<?php echo e(old('charges_amount', 0)); ?>"
                                   min="0" step="500"
                                   placeholder="0"
                                   class="input"
                                   oninput="calculerApercu()">
                            <div style="font-size:11px;color:var(--text-3);margin-top:4px;">Eau, électricité, gardiennage...</div>
                            <?php $__errorArgs = ['charges_amount'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div style="font-size:12px;color:#dc2626;margin-top:4px;"><?php echo e($message); ?></div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>

                        
                        <div>
                            <label class="form-label">TOM (FCFA)</label>
                            <input type="number" name="tom_amount" id="tom_amount"
                                   value="<?php echo e(old('tom_amount', 0)); ?>"
                                   min="0" step="100"
                                   placeholder="0"
                                   class="input"
                                   oninput="calculerApercu()">
                            <div style="font-size:11px;color:var(--text-3);margin-top:4px;">Taxe sur les Ordures Ménagères</div>
                            <?php $__errorArgs = ['tom_amount'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div style="font-size:12px;color:#dc2626;margin-top:4px;"><?php echo e($message); ?></div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                    </div>

                    
                    <div id="apercu-calcul"
                         style="display:none;background:var(--bg);border:1px solid var(--border);border-radius:var(--radius-sm);padding:16px;margin-bottom:20px;">
                        <div style="font-size:10px;font-weight:600;color:var(--text-3);text-transform:uppercase;letter-spacing:.5px;margin-bottom:12px;">
                            Aperçu du calcul (commission sur loyer nu)
                        </div>
                        <div style="display:flex;flex-direction:column;gap:8px;">
                            <div style="display:flex;justify-content:space-between;font-size:13px;padding-bottom:6px;border-bottom:1px solid var(--border);">
                                <span style="color:var(--text-3);">Loyer nu</span>
                                <span style="font-weight:600;color:var(--text);" id="ap-loyer-nu">—</span>
                            </div>
                            <div style="display:flex;justify-content:space-between;font-size:13px;padding-bottom:6px;border-bottom:1px solid var(--border);">
                                <span style="color:var(--text-3);">+ Charges</span>
                                <span style="font-weight:600;color:var(--text);" id="ap-charges">—</span>
                            </div>
                            <div style="display:flex;justify-content:space-between;font-size:13px;padding-bottom:8px;border-bottom:2px solid var(--border);">
                                <span style="color:var(--text-3);">+ TOM</span>
                                <span style="font-weight:600;color:var(--text);" id="ap-tom">—</span>
                            </div>
                            <div style="display:flex;justify-content:space-between;font-size:13px;padding-bottom:8px;border-bottom:1px solid var(--border);">
                                <span style="font-weight:700;color:var(--text);">= Total encaissé</span>
                                <span style="font-weight:800;color:var(--text);" id="ap-montant">—</span>
                            </div>
                            <div style="display:flex;justify-content:space-between;font-size:12px;">
                                <span style="color:#d97706;">Commission HT (sur loyer nu)</span>
                                <span style="color:#d97706;" id="ap-commission-ht">—</span>
                            </div>
                            <div style="display:flex;justify-content:space-between;font-size:12px;">
                                <span style="color:var(--text-3);">TVA 18%</span>
                                <span style="color:var(--text-3);" id="ap-tva">—</span>
                            </div>
                            <div style="display:flex;justify-content:space-between;font-size:12px;padding-bottom:8px;border-bottom:1px solid var(--border);">
                                <span style="color:#b45309;">Commission TTC</span>
                                <span style="color:#b45309;" id="ap-commission-ttc">—</span>
                            </div>
                            <div style="display:flex;justify-content:space-between;font-size:14px;background:#f0fdf4;padding:10px 12px;border-radius:6px;margin-top:4px;">
                                <span style="font-weight:700;color:#15803d;">Net propriétaire</span>
                                <span style="font-weight:900;color:#15803d;" id="ap-net">—</span>
                            </div>
                        </div>
                    </div>

                    
                    <div style="font-size:12px;font-weight:700;color:var(--text-2);text-transform:uppercase;letter-spacing:.5px;margin-bottom:12px;padding-bottom:8px;border-bottom:1px solid var(--border);">
                        📋 Référence du bail
                    </div>

                    <div style="margin-bottom:20px;">
                        <label class="form-label">
                            Référence bail
                            <span style="font-size:11px;font-weight:400;color:var(--text-3);">(pré-remplie depuis le contrat — modifiable)</span>
                        </label>
                        <input type="text" name="reference_bail" id="reference_bail"
                               value="<?php echo e(old('reference_bail')); ?>"
                               placeholder="BIMO-2026-00001"
                               class="input"
                               maxlength="60">
                        <?php $__errorArgs = ['reference_bail'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <div style="font-size:12px;color:#dc2626;margin-top:4px;"><?php echo e($message); ?></div>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    
                    <div style="font-size:12px;font-weight:700;color:var(--text-2);text-transform:uppercase;letter-spacing:.5px;margin-bottom:12px;padding-bottom:8px;border-bottom:1px solid var(--border);">
                        🏦 Mode de règlement
                    </div>

                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:20px;">
                        <div>
                            <label class="form-label">Mode de paiement <span style="color:#ef4444;">*</span></label>
                            <select name="mode_paiement" class="input">
                                <?php $__currentLoopData = \App\Models\Paiement::MODES_PAIEMENT; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($key); ?>" <?php echo e(old('mode_paiement') === $key ? 'selected' : ''); ?>>
                                        <?php echo e($label); ?>

                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                            <?php $__errorArgs = ['mode_paiement'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div style="font-size:12px;color:#dc2626;margin-top:4px;"><?php echo e($message); ?></div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                        <div>
                            <label class="form-label">Date du règlement <span style="color:#ef4444;">*</span></label>
                            <input type="date" name="date_paiement"
                                   value="<?php echo e(old('date_paiement', now()->format('Y-m-d'))); ?>"
                                   class="input">
                            <?php $__errorArgs = ['date_paiement'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div style="font-size:12px;color:#dc2626;margin-top:4px;"><?php echo e($message); ?></div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                    </div>

                    
                    <div style="background:#eff6ff;border:1px solid #bfdbfe;border-radius:var(--radius-sm);padding:16px;margin-bottom:20px;">
                        <label style="display:flex;align-items:center;gap:10px;cursor:pointer;margin-bottom:0;">
                            <input type="checkbox" name="est_premier_paiement" value="1"
                                   id="premier_paiement"
                                   onchange="toggleCaution(this.checked)"
                                   <?php echo e(old('est_premier_paiement') ? 'checked' : ''); ?>

                                   style="width:16px;height:16px;accent-color:var(--agency);cursor:pointer;">
                            <span style="font-size:13px;font-weight:600;color:#1e40af;">
                                Premier paiement — inclure la caution
                            </span>
                        </label>
                        <div id="bloc-caution" style="<?php echo e(old('est_premier_paiement') ? '' : 'display:none;'); ?>margin-top:14px;">
                            <label class="form-label">Montant de la caution (FCFA)</label>
                            <input type="number" name="caution_percue"
                                   value="<?php echo e(old('caution_percue', 0)); ?>"
                                   min="0" step="500"
                                   class="input">
                            <?php $__errorArgs = ['caution_percue'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div style="font-size:12px;color:#dc2626;margin-top:4px;"><?php echo e($message); ?></div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                    </div>

                    
                    <div style="margin-bottom:24px;">
                        <label class="form-label">Notes</label>
                        <textarea name="notes" rows="2"
                                  placeholder="Observations, numéro de chèque, référence virement..."
                                  class="input" style="resize:vertical;"><?php echo e(old('notes')); ?></textarea>
                    </div>

                    
                    <div style="display:flex;justify-content:flex-end;gap:10px;padding-top:20px;border-top:1px solid var(--border);">
                        <a href="<?php echo e(route('admin.paiements.index')); ?>" class="btn btn-secondary">Annuler</a>
                        <button type="submit" class="btn btn-primary">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:15px;height:15px;">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            Enregistrer le paiement
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>

    <script>
        // ── Données contrats injectées côté serveur ───────────────────────────
        const contratsData = {
            <?php $__currentLoopData = $contrats; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $contrat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php echo e(json_encode($contrat->locataire->name . ' — ' . $contrat->bien->reference . ' (' . number_format($contrat->loyer_contractuel, 0, ',', ' ') . ' F/mois)')); ?>: {
                id:          <?php echo e($contrat->id); ?>,
                loyer:       <?php echo e($contrat->loyer_contractuel); ?>,
                loyer_nu:    <?php echo e($contrat->loyer_nu ?? $contrat->loyer_contractuel); ?>,
                charges:     <?php echo e($contrat->charges_mensuelles ?? 0); ?>,
                tom:         <?php echo e($contrat->tom_amount ?? 0); ?>,
                commission:  <?php echo e($contrat->bien->taux_commission); ?>,
                bien:        <?php echo e(json_encode($contrat->bien->reference)); ?>,
                locataire:   <?php echo e(json_encode($contrat->locataire->name)); ?>,
                ref_bail:    <?php echo e(json_encode($contrat->reference_bail_affichee ?? '')); ?>,
            },
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        };

        let tauxCommission = 0;
        const urlDernierePeriode = '<?php echo e(url("admin/paiements/dernier-periode")); ?>';

        function onRechercheContrat(valeur) {
            const data = contratsData[valeur];
            if (data) {
                document.getElementById('contrat_id').value = data.id;
                chargerInfosContratData(data);
                chargerPeriodeSuggeree(data.id);
            } else {
                document.getElementById('contrat_id').value = '';
                document.getElementById('infos-contrat').style.display  = 'none';
                document.getElementById('apercu-calcul').style.display  = 'none';
                document.getElementById('periode-hint').style.display   = 'none';
                tauxCommission = 0;
            }
        }

        function chargerInfosContratData(data) {
            tauxCommission = parseFloat(data.commission) || 0;

            document.getElementById('info-bien').textContent       = data.bien;
            document.getElementById('info-locataire').textContent  = data.locataire;
            document.getElementById('info-loyer').textContent      = parseInt(data.loyer).toLocaleString('fr-FR') + ' FCFA';
            document.getElementById('info-commission').textContent = data.commission + '%';
            document.getElementById('info-ref-bail').textContent   = data.ref_bail || '—';
            document.getElementById('infos-contrat').style.display = 'block';

            // Pré-remplir loyer_nu depuis le contrat
            const loyerNuInput = document.getElementById('loyer_nu');
            if (!loyerNuInput.value || loyerNuInput.value == '0') {
                loyerNuInput.value = data.loyer_nu || data.loyer;
            }

            // Pré-remplir charges
            const chargesInput = document.getElementById('charges_amount');
            if (!chargesInput.value || chargesInput.value == '0') {
                chargesInput.value = data.charges || 0;
            }

            // Pré-remplir TOM
            const tomInput = document.getElementById('tom_amount');
            if (!tomInput.value || tomInput.value == '0') {
                tomInput.value = data.tom || 0;
            }

            // Pré-remplir référence bail
            const refBailInput = document.getElementById('reference_bail');
            if (!refBailInput.value && data.ref_bail) {
                refBailInput.value = data.ref_bail;
            }

            calculerApercu();
        }

        async function chargerPeriodeSuggeree(contratId) {
            try {
                const r = await fetch(`${urlDernierePeriode}/${contratId}`, {
                    headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>' }
                });
                if (!r.ok) return;
                const data = await r.json();
                if (data.prochaine_periode) {
                    document.getElementById('periode').value = data.prochaine_periode;
                    document.getElementById('periode-hint-text').textContent =
                        '✅ Période suggérée : ' + data.prochaine_periode + ' (mois suivant le dernier paiement)';
                    document.getElementById('periode-hint').style.display = 'block';
                }
            } catch (e) { /* silencieux */ }
        }

        function calculerApercu() {
            const loyerNu  = parseFloat(document.getElementById('loyer_nu').value) || 0;
            const charges  = parseFloat(document.getElementById('charges_amount').value) || 0;
            const tom      = parseFloat(document.getElementById('tom_amount').value) || 0;
            const total    = loyerNu + charges + tom;

            if (!loyerNu || !tauxCommission) return;

            const commissionHT  = Math.round(loyerNu * tauxCommission / 100);
            const tva           = Math.round(commissionHT * 0.18);
            const commissionTTC = commissionHT + tva;
            const net           = total - commissionTTC;

            document.getElementById('ap-loyer-nu').textContent      = loyerNu.toLocaleString('fr-FR') + ' F';
            document.getElementById('ap-charges').textContent       = charges.toLocaleString('fr-FR') + ' F';
            document.getElementById('ap-tom').textContent           = tom.toLocaleString('fr-FR') + ' F';
            document.getElementById('ap-montant').textContent       = total.toLocaleString('fr-FR') + ' F';
            document.getElementById('ap-commission-ht').textContent = '− ' + commissionHT.toLocaleString('fr-FR') + ' F';
            document.getElementById('ap-tva').textContent           = '− ' + tva.toLocaleString('fr-FR') + ' F';
            document.getElementById('ap-commission-ttc').textContent= '− ' + commissionTTC.toLocaleString('fr-FR') + ' F';
            document.getElementById('ap-net').textContent           = net.toLocaleString('fr-FR') + ' F';

            document.getElementById('apercu-calcul').style.display = 'block';
        }

        function toggleCaution(checked) {
            document.getElementById('bloc-caution').style.display = checked ? 'block' : 'none';
        }

        window.addEventListener('DOMContentLoaded', () => {
            const inputRecherche = document.getElementById('recherche-contrat');
            if (inputRecherche.value) {
                onRechercheContrat(inputRecherche.value);
            }
        });
    </script>

 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54)): ?>
<?php $attributes = $__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54; ?>
<?php unset($__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9ac128a9029c0e4701924bd2d73d7f54)): ?>
<?php $component = $__componentOriginal9ac128a9029c0e4701924bd2d73d7f54; ?>
<?php unset($__componentOriginal9ac128a9029c0e4701924bd2d73d7f54); ?>
<?php endif; ?><?php /**PATH C:\Users\ph\bimotech-immo\resources\views/paiements/create.blade.php ENDPATH**/ ?>