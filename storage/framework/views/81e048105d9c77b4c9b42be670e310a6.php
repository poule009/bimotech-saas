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

    
    <div style="display:flex;align-items:center;gap:12px;margin-bottom:30px;">
        <a href="<?php echo e(route('admin.paiements.index')); ?>"
           style="display:flex;align-items:center;justify-content:center;width:40px;height:40px;border-radius:12px;border:1px solid rgba(181, 140, 90, 0.2);background:white;color:var(--agency);transition:all .2s;box-shadow: var(--shadow);"
           onmouseenter="this.style.background='#f7f4ec';this.style.transform='translateX(-3px)'"
           onmouseleave="this.style.background='white';this.style.transform='translateX(0)'">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:20px;height:20px;stroke-width:2.5;">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <div>
            <h1 style="font-size:24px;font-weight:900;color:var(--agency);letter-spacing:-.8px;">Enregistrer un règlement</h1>
            <p style="font-size:13px;color:#b58c5a;font-weight:600;text-transform:uppercase;letter-spacing:1px;margin-top:2px;">Saisie certifiée BIMO-Tech</p>
        </div>
    </div>

    <div style="max-width:720px;">
        <div class="card" style="background: rgba(255, 255, 255, 0.6);backdrop-filter: blur(12px);border: 1px solid rgba(255, 255, 255, 0.4);border-radius: 28px;box-shadow: var(--shadow-lg);overflow: hidden;">
            <div class="card-body" style="padding: 35px;">

                <?php if($errors->any()): ?>
                    <div class="alert alert-error" style="background:#fef2f2;border:1px solid #fecaca;border-radius:16px;padding:16px;margin-bottom:25px;color:#dc2626;font-weight:600;font-size:13px;">
                        <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div style="display:flex;align-items:center;gap:8px;"><span>✕</span> <?php echo e($error); ?></div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                <?php endif; ?>

                <form method="POST" action="<?php echo e(route('admin.paiements.store')); ?>">
                    <?php echo csrf_field(); ?>

                    
                    <div style="font-size:11px;font-weight:800;color:#b58c5a;text-transform:uppercase;letter-spacing:1.5px;margin-bottom:18px;padding-bottom:10px;border-bottom:1px solid rgba(181, 140, 90, 0.1);">
                        📋 Contrat & Bail
                    </div>

                    <div style="margin-bottom:25px;">
                        <label class="form-label" style="display:block;font-size:13px;font-weight:700;color:var(--agency);margin-bottom:10px;">Locataire / Bien <span style="color:#dc2626;">*</span></label>
                        <input type="text"
                               id="recherche-contrat"
                               list="contrats-datalist"
                               autocomplete="off"
                               placeholder="Tapez le nom ou la référence..."
                               class="input"
                               style="width:100%;padding:16px;background:#fcfaf5;border:2px solid #e2e8f0;border-radius:16px;font-size:15px;color:var(--agency);transition:all .3s;outline:none;"
                               onfocus="this.style.borderColor='#b58c5a';this.style.background='#fff';this.style.boxShadow='0 0 0 4px rgba(181, 140, 90, 0.1)';"
                               onblur="this.style.borderColor='#e2e8f0';this.style.background='#fcfaf5';this.style.boxShadow='none';"
                               oninput="onRechercheContrat(this.value)"
                               value="<?php if($contratPreselectionne): ?><?php echo e($contratPreselectionne->locataire->name); ?> — <?php echo e($contratPreselectionne->bien->reference); ?> (<?php echo e(number_format($contratPreselectionne->loyer_contractuel, 0, ',', ' ')); ?> F/mois)<?php elseif(old('contrat_id')): ?><?php echo e(old('contrat_id')); ?><?php endif; ?>">
                        <datalist id="contrats-datalist">
                            <?php $__currentLoopData = $contrats; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $contrat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($contrat->locataire->name); ?> — <?php echo e($contrat->bien->reference); ?> (<?php echo e(number_format($contrat->loyer_contractuel, 0, ',', ' ')); ?> F/mois)"></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </datalist>
                        <input type="hidden" name="contrat_id" id="contrat_id"
                               value="<?php echo e(old('contrat_id', $contratPreselectionne?->id)); ?>">
                        <p style="font-size:11px;color:#a0aec0;margin-top:8px;font-style:italic;">
                            💡 Sélectionnez un contrat dans la liste pour pré-remplir les données financières.
                        </p>
                        <?php $__errorArgs = ['contrat_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <div style="font-size:12px;color:#dc2626;margin-top:6px;font-weight:700;"><?php echo e($message); ?></div>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    
                    <div id="infos-contrat"
                         style="display:none;background:#1a202c;border-radius:20px;padding:22px;margin-bottom:25px;color:white;box-shadow: 0 15px 30px rgba(0,0,0,0.15);">
                        <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;">
                            <div>
                                <div style="font-size:10px;font-weight:700;color:#718096;text-transform:uppercase;letter-spacing:1px;margin-bottom:4px;">Référence Bien</div>
                                <div style="font-weight:700;font-size:14px;color:#fff;" id="info-bien">—</div>
                            </div>
                            <div>
                                <div style="font-size:10px;font-weight:700;color:#718096;text-transform:uppercase;letter-spacing:1px;margin-bottom:4px;">Locataire Titulaire</div>
                                <div style="font-weight:700;font-size:14px;color:#fff;" id="info-locataire">—</div>
                            </div>
                            <div>
                                <div style="font-size:10px;font-weight:700;color:#718096;text-transform:uppercase;letter-spacing:1px;margin-bottom:4px;">Loyer Mensuel</div>
                                <div style="font-weight:800;font-size:16px;color:#b58c5a;" id="info-loyer">—</div>
                            </div>
                            <div>
                                <div style="font-size:10px;font-weight:700;color:#718096;text-transform:uppercase;letter-spacing:1px;margin-bottom:4px;">Taux Gestion</div>
                                <div style="font-weight:800;font-size:16px;color:#b58c5a;" id="info-commission">—</div>
                            </div>
                            <div style="grid-column: span 2; border-top:1px solid rgba(255,255,255,0.1); padding-top:12px;">
                                <div style="font-size:10px;font-weight:700;color:#718096;text-transform:uppercase;letter-spacing:1px;margin-bottom:4px;">Identifiant du Bail</div>
                                <div style="font-weight:700;font-size:13px;color:#fff;font-family:monospace;" id="info-ref-bail">—</div>
                            </div>
                        </div>
                    </div>

                    
                    <div style="margin-bottom:30px;">
                        <label class="form-label" style="display:block;font-size:13px;font-weight:700;color:var(--agency);margin-bottom:10px;">Période du loyer <span style="color:#dc2626;">*</span></label>
                        <input type="month" name="periode" id="periode"
                               value="<?php echo e(old('periode', now()->format('Y-m'))); ?>"
                               class="input" style="width:100%;padding:14px;border:2px solid #e2e8f0;border-radius:14px;font-weight:700;color:var(--agency);">
                        <div id="periode-hint" style="display:none;margin-top:10px;padding:12px 16px;background:rgba(22,163,74,0.08);border-radius:12px;font-size:13px;color:#16a34a;font-weight:700;border: 1px solid rgba(22,163,74,0.2);">
                            <span id="periode-hint-text"></span>
                        </div>
                        <?php $__errorArgs = ['periode'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <div style="font-size:12px;color:#dc2626;margin-top:6px;font-weight:700;"><?php echo e($message); ?></div>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    
                    <div style="font-size:11px;font-weight:800;color:#b58c5a;text-transform:uppercase;letter-spacing:1.5px;margin-bottom:18px;padding-bottom:10px;border-bottom:1px solid rgba(181, 140, 90, 0.1);">
                        💰 Détails des versements
                    </div>

                    <div style="background:#fcfaf5;border:1px solid #fde68a;border-radius:16px;padding:15px 20px;margin-bottom:25px;font-size:12px;color:#92400e;line-height:1.5;">
                        <b>Note de gestion :</b> La commission agence est calculée exclusivement sur le <strong>loyer nu</strong>.
                    </div>

                    
                    <div style="margin-bottom:25px;">
                        <label class="form-label" style="display:block;font-size:13px;font-weight:700;color:var(--agency);margin-bottom:10px;">Loyer net de charges (FCFA) <span style="color:#dc2626;">*</span></label>
                        <input type="number" name="loyer_nu" id="loyer_nu"
                               value="<?php echo e(old('loyer_nu')); ?>"
                               min="1" step="500"
                               placeholder="Montant du loyer nu"
                               class="input" style="width:100%;padding:18px;border:2px solid #e2e8f0;border-radius:18px;font-size:20px;font-weight:900;color:#16a34a;background:#fff;outline:none;transition:all 0.3s;"
                               onfocus="this.style.borderColor='#16a34a';this.style.boxShadow='0 10px 20px rgba(22,163,74,0.1)';"
                               onblur="this.style.borderColor='#e2e8f0';this.style.boxShadow='none';"
                               oninput="calculerApercu()">
                        <?php $__errorArgs = ['loyer_nu'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <div style="font-size:12px;color:#dc2626;margin-top:6px;font-weight:700;"><?php echo e($message); ?></div>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;margin-bottom:25px;">
                        
                        <div>
                            <label class="form-label" style="display:block;font-size:13px;font-weight:700;color:var(--agency);margin-bottom:8px;">Provision Charges</label>
                            <input type="number" name="charges_amount" id="charges_amount"
                                   value="<?php echo e(old('charges_amount', 0)); ?>"
                                   min="0" step="500"
                                   class="input" style="width:100%;padding:14px;border:2px solid #e2e8f0;border-radius:14px;font-weight:600;"
                                   oninput="calculerApercu()">
                        </div>

                        
                        <div>
                            <label class="form-label" style="display:block;font-size:13px;font-weight:700;color:var(--agency);margin-bottom:8px;">T.O.M</label>
                            <input type="number" name="tom_amount" id="tom_amount"
                                   value="<?php echo e(old('tom_amount', 0)); ?>"
                                   min="0" step="100"
                                   class="input" style="width:100%;padding:14px;border:2px solid #e2e8f0;border-radius:14px;font-weight:600;"
                                   oninput="calculerApercu()">
                        </div>
                    </div>

                    
                    <div id="apercu-calcul"
                         style="display:none;background: linear-gradient(145deg, #fcfaf5, #f8f4e9);border:1px solid #fde68a;border-radius:24px;padding:25px;margin-bottom:35px;box-shadow: 0 10px 25px rgba(181, 140, 90, 0.1);">
                        <div style="font-size:10px;font-weight:900;color:#b58c5a;text-transform:uppercase;letter-spacing:2px;margin-bottom:18px;text-align:center;">
                            Bordereau de calcul financier
                        </div>
                        <div style="display:flex;flex-direction:column;gap:12px;">
                            <div style="display:flex;justify-content:space-between;font-size:14px;color:#718096;padding-bottom:10px;border-bottom:1px dashed #e2e8f0;">
                                <span style="font-weight:600;">Loyer nu encaissé</span>
                                <span style="font-weight:800;color:var(--agency);" id="ap-loyer-nu">—</span>
                            </div>
                            <div style="display:flex;justify-content:space-between;font-size:14px;color:#718096;padding-bottom:10px;border-bottom:1px dashed #e2e8f0;">
                                <span style="font-weight:600;">+ Charges & TOM</span>
                                <span style="font-weight:800;color:var(--agency);" id="ap-charges">—</span>
                            </div>
                            <div style="display:flex;justify-content:space-between;font-size:14px;color:#718096;padding-bottom:10px;border-bottom:1px dashed #e2e8f0;">
                                <span style="font-weight:600;">+ TOM (Taxe)</span>
                                <span style="font-weight:800;color:var(--agency);" id="ap-tom">—</span>
                            </div>
                            <div style="display:flex;justify-content:space-between;font-size:15px;padding-bottom:15px;margin-top:5px;">
                                <span style="font-weight:900;color:var(--agency);text-transform:uppercase;">= Total encaissé</span>
                                <span style="font-weight:900;color:var(--agency);font-size:18px;" id="ap-montant">—</span>
                            </div>
                            
                            <div style="display:flex;justify-content:space-between;font-size:12px;color:#d97706;font-weight:700;">
                                <span>Commission Agence (HT)</span>
                                <span id="ap-commission-ht">—</span>
                            </div>
                            <div style="display:flex;justify-content:space-between;font-size:12px;color:#a0aec0;font-weight:700;">
                                <span>TVA sur commission (18%)</span>
                                <span id="ap-tva">—</span>
                            </div>
                            <div style="display:flex;justify-content:space-between;font-size:13px;color:#b58c5a;font-weight:800;padding-bottom:12px;border-bottom:1px solid rgba(181, 140, 90, 0.2);">
                                <span>TOTAL COMMISSION (TTC)</span>
                                <span id="ap-commission-ttc">—</span>
                            </div>

                            <div style="display:flex;justify-content:space-between;font-size:18px;background:#16a34a;color:white;padding:20px;border-radius:18px;margin-top:10px;box-shadow: 0 10px 20px rgba(22, 163, 74, 0.2);">
                                <span style="font-weight:700;letter-spacing:-0.5px;">NET PROPRIÉTAIRE</span>
                                <span style="font-weight:900;font-size:22px;" id="ap-net">—</span>
                            </div>
                        </div>
                    </div>

                    
                    <div style="font-size:12px;font-weight:700;color:#b58c5a;text-transform:uppercase;letter-spacing:1.5px;margin-bottom:18px;padding-bottom:10px;border-bottom:1px solid rgba(181, 140, 90, 0.1);">
                        📋 Administration
                    </div>

                    <div style="margin-bottom:30px;">
                        <label class="form-label" style="display:block;font-size:13px;font-weight:700;color:var(--agency);margin-bottom:10px;">Référence bail <span style="font-weight:400;color:#a0aec0;font-size:11px;">(Optionnel)</span></label>
                        <input type="text" name="reference_bail" id="reference_bail"
                               value="<?php echo e(old('reference_bail')); ?>"
                               placeholder="Ex: BAIL-DKR-2024"
                               class="input" style="width:100%;padding:14px;border:2px solid #e2e8f0;border-radius:14px;color:var(--agency);font-family:monospace;font-weight:600;">
                        <?php $__errorArgs = ['reference_bail'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <div style="font-size:12px;color:#dc2626;margin-top:6px;font-weight:700;"><?php echo e($message); ?></div>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    
                    <div style="font-size:12px;font-weight:700;color:#b58c5a;text-transform:uppercase;letter-spacing:1.5px;margin-bottom:18px;padding-bottom:10px;border-bottom:1px solid rgba(181, 140, 90, 0.1);">
                        🏦 Modalités de règlement
                    </div>

                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;margin-bottom:30px;">
                        <div>
                            <label class="form-label" style="display:block;font-size:13px;font-weight:700;color:var(--agency);margin-bottom:8px;">Mode de paiement <span style="color:#dc2626;">*</span></label>
                            <select name="mode_paiement" class="input" style="width:100%;padding:14px;border:2px solid #e2e8f0;border-radius:14px;background:#fff;font-weight:600;color:var(--agency);cursor:pointer;">
                                <?php $__currentLoopData = \App\Models\Paiement::MODES_PAIEMENT; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($key); ?>" <?php echo e(old('mode_paiement') === $key ? 'selected' : ''); ?>>
                                        <?php echo e($label); ?>

                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                        <div>
                            <label class="form-label" style="display:block;font-size:13px;font-weight:700;color:var(--agency);margin-bottom:8px;">Date de perception <span style="color:#dc2626;">*</span></label>
                            <input type="date" name="date_paiement"
                                   value="<?php echo e(old('date_paiement', now()->format('Y-m-d'))); ?>"
                                   class="input" style="width:100%;padding:14px;border:2px solid #e2e8f0;border-radius:14px;font-weight:600;color:var(--agency);">
                        </div>
                    </div>

                    
                    <div style="background:rgba(37, 99, 235, 0.05);border:1px solid rgba(37, 99, 235, 0.15);border-radius:24px;padding:25px;margin-bottom:30px;transition:all 0.3s;">
                        <label style="display:flex;align-items:center;gap:15px;cursor:pointer;margin-bottom:0;">
                            <input type="checkbox" name="est_premier_paiement" value="1"
                                   id="premier_paiement"
                                   onchange="toggleCaution(this.checked)"
                                   <?php echo e(old('est_premier_paiement') ? 'checked' : ''); ?>

                                   style="width:22px;height:22px;accent-color:var(--agency);cursor:pointer;border-radius:6px;">
                            <span style="font-size:15px;font-weight:800;color:#1e40af;letter-spacing:-0.3px;">
                                Premier paiement (Inclure le dépôt de garantie / caution)
                            </span>
                        </label>
                        <div id="bloc-caution" style="<?php echo e(old('est_premier_paiement') ? '' : 'display:none;'); ?>margin-top:20px;padding-top:20px;border-top:1px dashed rgba(37, 99, 235, 0.2);">
                            <label class="form-label" style="display:block;font-size:13px;font-weight:700;color:#1e40af;margin-bottom:10px;">Montant de la caution (FCFA)</label>
                            <input type="number" name="caution_percue"
                                   value="<?php echo e(old('caution_percue', 0)); ?>"
                                   min="0" step="500"
                                   class="input" style="width:100%;padding:16px;border:2px solid #bfdbfe;border-radius:14px;font-weight:800;color:#1e40af;background:white;">
                            <?php $__errorArgs = ['caution_percue'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div style="font-size:12px;color:#dc2626;margin-top:6px;font-weight:700;"><?php echo e($message); ?></div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                    </div>

                    
                    <div style="margin-bottom:40px;">
                        <label class="form-label" style="display:block;font-size:13px;font-weight:700;color:var(--agency);margin-bottom:10px;">Notes & Observations internes</label>
                        <textarea name="notes" rows="3"
                                  placeholder="Détails du chèque, référence virement, situation particulière..."
                                  class="input" style="width:100%;padding:18px;border:2px solid #e2e8f0;border-radius:18px;resize:vertical;min-height:100px;font-size:14px;line-height:1.6;outline:none;transition:all 0.3s;"
                                  onfocus="this.style.borderColor='#b58c5a';"><?php echo e(old('notes')); ?></textarea>
                    </div>

                    
                    <div style="display:flex;justify-content:flex-end;gap:15px;padding-top:25px;border-top:1px solid rgba(181, 140, 90, 0.15);">
                        <a href="<?php echo e(route('admin.paiements.index')); ?>" 
                           style="padding:16px 28px;font-weight:800;color:#718096;font-size:14px;text-transform:uppercase;letter-spacing:1px;display:flex;align-items:center;">Annuler</a>
                        <button type="submit" class="btn btn-primary" 
                                style="background:var(--agency);color:white;padding:18px 35px;border-radius:16px;font-weight:900;font-size:15px;box-shadow:0 15px 30px rgba(26, 32, 44, 0.25);border:none;display:flex;align-items:center;gap:10px;transition:all 0.3s;"
                                onmouseenter="this.style.transform='translateY(-3px)';this.style.boxShadow='0 20px 40px rgba(26, 32, 44, 0.3)';"
                                onmouseleave="this.style.transform='translateY(0)';this.style.boxShadow='0 15px 30px rgba(26, 32, 44, 0.25)';">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:20px;height:20px;stroke-width:3;">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                            </svg>
                            Valider l'encaissement
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>

    <script>
        // ── Données contrats injectées côté serveur (Structure inchangée) ───────────────────────────
        const contratsData = {
            <?php $__currentLoopData = $contrats; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $contrat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php echo e(json_encode($contrat->locataire->name . ' — ' . $contrat->bien->reference . ' (' . number_format($contrat->loyer_contractuel, 0, ',', ' ') . ' F/mois)')); ?>: {
                id:           <?php echo e($contrat->id); ?>,
                loyer:        <?php echo e($contrat->loyer_contractuel); ?>,
                loyer_nu:     <?php echo e($contrat->loyer_nu ?? $contrat->loyer_contractuel); ?>,
                charges:      <?php echo e($contrat->charges_mensuelles ?? 0); ?>,
                tom:          <?php echo e($contrat->tom_amount ?? 0); ?>,
                commission:   <?php echo e($contrat->bien->taux_commission); ?>,
                bien:         <?php echo e(json_encode($contrat->bien->reference)); ?>,
                locataire:    <?php echo e(json_encode($contrat->locataire->name)); ?>,
                ref_bail:     <?php echo e(json_encode($contrat->reference_bail_affichee ?? '')); ?>,
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
            document.getElementById('info-loyer').textContent       = parseInt(data.loyer).toLocaleString('fr-FR') + ' FCFA';
            document.getElementById('info-commission').textContent = data.commission + '%';
            document.getElementById('info-ref-bail').textContent   = data.ref_bail || '—';
            document.getElementById('infos-contrat').style.display = 'block';

            const loyerNuInput = document.getElementById('loyer_nu');
            if (!loyerNuInput.value || loyerNuInput.value == '0') {
                loyerNuInput.value = data.loyer_nu || data.loyer;
            }

            const chargesInput = document.getElementById('charges_amount');
            if (!chargesInput.value || chargesInput.value == '0') {
                chargesInput.value = data.charges || 0;
            }

            const tomInput = document.getElementById('tom_amount');
            if (!tomInput.value || tomInput.value == '0') {
                tomInput.value = data.tom || 0;
            }

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
            } catch (e) { }
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