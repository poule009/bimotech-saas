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
     <?php $__env->slot('header', null, []); ?> Nouveau contrat de bail <?php $__env->endSlot(); ?>

    
    <div style="display:flex;align-items:center;gap:12px;" class="section-gap">
        <a href="<?php echo e(route('admin.contrats.index')); ?>"
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

                
                <?php if($errors->any()): ?>
                    <div class="alert alert-error" style="margin-bottom:20px;">
                        <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div>❌ <?php echo e($error); ?></div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                <?php endif; ?>

                <form method="POST" action="<?php echo e(route('admin.contrats.store')); ?>">
                    <?php echo csrf_field(); ?>

                    
                    <div style="font-size:12px;font-weight:700;color:var(--text-2);text-transform:uppercase;letter-spacing:.5px;margin-bottom:12px;padding-bottom:8px;border-bottom:1px solid var(--border);">
                        🏠 Bien & Locataire
                    </div>

                    
                    <div style="margin-bottom:20px;">
                        <label class="form-label">Bien à louer <span style="color:#ef4444;">*</span></label>
                        <select name="bien_id" id="bien_id"
                                onchange="chargerInfosBien(this)"
                                class="input">
                            <option value="">— Sélectionner un bien disponible —</option>
                            <?php $__currentLoopData = $biens; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $bien): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($bien->id); ?>"
                                        data-loyer="<?php echo e($bien->loyer_mensuel); ?>"
                                        data-commission="<?php echo e($bien->taux_commission); ?>"
                                        data-proprietaire="<?php echo e($bien->proprietaire->name); ?>"
                                        <?php echo e(old('bien_id', $bienPreselectionne?->id) == $bien->id ? 'selected' : ''); ?>>
                                    <?php echo e($bien->reference); ?> — <?php echo e($bien->type); ?>, <?php echo e($bien->adresse); ?>

                                    <?php if($bien->quartier): ?> (<?php echo e($bien->quartier); ?>)<?php endif; ?>
                                </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                        <?php $__errorArgs = ['bien_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <div style="font-size:12px;color:#dc2626;margin-top:4px;"><?php echo e($message); ?></div>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>

                        
                        <div id="infos-bien" style="display:none;margin-top:10px;padding:12px;background:var(--bg);border-radius:var(--radius-sm);border:1px solid var(--border);">
                            <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:12px;font-size:12px;">
                                <div>
                                    <div style="color:var(--text-3);margin-bottom:2px;">Loyer mensuel</div>
                                    <div style="font-weight:700;color:var(--text);" id="info-loyer">—</div>
                                </div>
                                <div>
                                    <div style="color:var(--text-3);margin-bottom:2px;">Commission</div>
                                    <div style="font-weight:700;color:var(--text);" id="info-commission">—</div>
                                </div>
                                <div>
                                    <div style="color:var(--text-3);margin-bottom:2px;">Propriétaire</div>
                                    <div style="font-weight:700;color:var(--text);" id="info-proprietaire">—</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    
                    <div style="margin-bottom:20px;">
                        <label class="form-label">Locataire <span style="color:#ef4444;">*</span></label>
                        <div style="display:flex;gap:8px;">
                            <select name="locataire_id" id="locataire_id" class="input" style="flex:1;">
                                <option value="">— Sélectionner un locataire —</option>
                                <?php $__currentLoopData = $locataires; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $locataire): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($locataire->id); ?>"
                                            <?php echo e(old('locataire_id') == $locataire->id ? 'selected' : ''); ?>>
                                        <?php echo e($locataire->name); ?>

                                        <?php if($locataire->telephone): ?> — <?php echo e($locataire->telephone); ?><?php endif; ?>
                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                            <button type="button" onclick="ouvrirModaleLocataire()"
                                    class="btn btn-secondary"
                                    style="white-space:nowrap;flex-shrink:0;">
                                + Nouveau
                            </button>
                        </div>
                        <?php $__errorArgs = ['locataire_id'];
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
                        📄 Type de bail & Durée
                    </div>

                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:20px;">
                        <div>
                            <label class="form-label">Type de bail <span style="color:#ef4444;">*</span></label>
                            <select name="type_bail" class="input">
                                <option value="habitation"  <?php echo e(old('type_bail', 'habitation') === 'habitation'  ? 'selected' : ''); ?>>Habitation</option>
                                <option value="commercial"  <?php echo e(old('type_bail') === 'commercial'  ? 'selected' : ''); ?>>Commercial</option>
                                <option value="mixte"       <?php echo e(old('type_bail') === 'mixte'       ? 'selected' : ''); ?>>Mixte</option>
                                <option value="saisonnier"  <?php echo e(old('type_bail') === 'saisonnier'  ? 'selected' : ''); ?>>Saisonnier</option>
                            </select>
                            <?php $__errorArgs = ['type_bail'];
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
                            <label class="form-label">
                                Date de fin
                                <span style="font-size:11px;font-weight:400;color:var(--text-3);">(vide = indéterminée)</span>
                            </label>
                            <input type="date" name="date_fin"
                                   value="<?php echo e(old('date_fin')); ?>"
                                   class="input">
                            <?php $__errorArgs = ['date_fin'];
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

                    
                    <div style="margin-bottom:20px;">
                        <label class="form-label">Date de début <span style="color:#ef4444;">*</span></label>
                        <input type="date" name="date_debut"
                               value="<?php echo e(old('date_debut', now()->format('Y-m-d'))); ?>"
                               class="input">
                        <?php $__errorArgs = ['date_debut'];
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
                        💰 Conditions financières
                    </div>

                    
                    <div class="alert alert-info" style="margin-bottom:16px;">
                        ℹ️ Le loyer contractuel sera automatiquement repris depuis la fiche du bien sélectionné.
                    </div>

                    
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:20px;">
                        <div>
                            <label class="form-label">Caution (FCFA) <span style="color:#ef4444;">*</span></label>
                            <input type="number" name="caution"
                                   value="<?php echo e(old('caution')); ?>"
                                   min="0" step="500" id="caution"
                                   placeholder="Ex : 500 000"
                                   class="input">
                            <div style="font-size:11px;color:var(--text-3);margin-top:4px;" id="caution-hint"></div>
                            <?php $__errorArgs = ['caution'];
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
                            <label class="form-label">Nombre de mois de caution</label>
                            <input type="number" name="nombre_mois_caution"
                                   value="<?php echo e(old('nombre_mois_caution', 1)); ?>"
                                   min="1" max="6" class="input">
                            <div style="font-size:11px;color:var(--text-3);margin-top:4px;">Entre 1 et 6 mois</div>
                            <?php $__errorArgs = ['nombre_mois_caution'];
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

                    
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:20px;">
                        <div>
                            <label class="form-label">Frais d'agence (FCFA)</label>
                            <input type="number" name="frais_agence"
                                   value="<?php echo e(old('frais_agence', 0)); ?>"
                                   min="0" step="500" placeholder="0"
                                   class="input">
                            <div style="font-size:11px;color:var(--text-3);margin-top:4px;">Perçus à la signature</div>
                            <?php $__errorArgs = ['frais_agence'];
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
                            <label class="form-label">Charges mensuelles (FCFA)</label>
                            <input type="number" name="charges_mensuelles"
                                   value="<?php echo e(old('charges_mensuelles', 0)); ?>"
                                   min="0" step="500" placeholder="0"
                                   class="input">
                            <div style="font-size:11px;color:var(--text-3);margin-top:4px;">Eau, électricité, gardiennage…</div>
                            <?php $__errorArgs = ['charges_mensuelles'];
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

                    
                    <div style="margin-bottom:20px;">
                        <label class="form-label">Taux d'indexation annuelle (%)</label>
                        <input type="number" name="indexation_annuelle"
                               value="<?php echo e(old('indexation_annuelle', 0)); ?>"
                               min="0" max="20" step="0.5" placeholder="0"
                               class="input" style="max-width:200px;">
                        <div style="font-size:11px;color:var(--text-3);margin-top:4px;">Taux de révision annuelle du loyer (0 = pas de révision)</div>
                        <?php $__errorArgs = ['indexation_annuelle'];
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
                        🛡️ Garant (optionnel)
                    </div>

                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:20px;">
                        <div>
                            <label class="form-label">Nom du garant</label>
                            <input type="text" name="garant_nom"
                                   value="<?php echo e(old('garant_nom')); ?>"
                                   placeholder="Ex : Mamadou Diop"
                                   class="input">
                            <?php $__errorArgs = ['garant_nom'];
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
                            <label class="form-label">Téléphone du garant</label>
                            <input type="tel" name="garant_telephone"
                                   value="<?php echo e(old('garant_telephone')); ?>"
                                   placeholder="+221 77 000 00 00"
                                   class="input">
                            <?php $__errorArgs = ['garant_telephone'];
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
                        <label class="form-label">Adresse du garant</label>
                        <input type="text" name="garant_adresse"
                               value="<?php echo e(old('garant_adresse')); ?>"
                               placeholder="Ex : Sacré-Cœur 3, Dakar"
                               class="input">
                        <?php $__errorArgs = ['garant_adresse'];
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
                        📝 Observations
                    </div>

                    <div style="margin-bottom:24px;">
                        <textarea name="observations" rows="3"
                                  placeholder="Conditions particulières, préavis, état des lieux…"
                                  class="input" style="resize:vertical;"><?php echo e(old('observations')); ?></textarea>
                    </div>

                    
                    <div style="display:flex;justify-content:flex-end;gap:10px;padding-top:20px;border-top:1px solid var(--border);">
                        <a href="<?php echo e(route('admin.contrats.index')); ?>" class="btn btn-secondary">
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

    
    <div id="modal-locataire" style="display:none;position:fixed;inset:0;z-index:9999;background:rgba(0,0,0,.45);backdrop-filter:blur(2px);align-items:center;justify-content:center;padding:16px;">
        <div style="background:var(--surface);border-radius:var(--radius);box-shadow:0 20px 60px rgba(0,0,0,.25);width:100%;max-width:460px;overflow:hidden;">

            
            <div style="padding:18px 20px;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between;background:var(--bg);">
                <div>
                    <div style="font-size:15px;font-weight:700;color:var(--text);">👤 Nouveau locataire</div>
                    <div style="font-size:12px;color:var(--text-3);margin-top:2px;">Création rapide — sera ajouté au menu déroulant</div>
                </div>
                <button type="button" onclick="fermerModaleLocataire()"
                        style="background:none;border:none;cursor:pointer;color:var(--text-3);font-size:20px;line-height:1;padding:4px;">
                    ×
                </button>
            </div>

            
            <div style="padding:20px;">
                <div id="modal-erreur" style="display:none;background:#fef2f2;border:1px solid #fecaca;color:#b91c1c;font-size:13px;padding:10px 14px;border-radius:var(--radius-sm);margin-bottom:16px;"></div>

                <div style="margin-bottom:16px;">
                    <label class="form-label">Nom complet <span style="color:#ef4444;">*</span></label>
                    <input type="text" id="modal-name" placeholder="Prénom et Nom" class="input">
                </div>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-bottom:16px;">
                    <div>
                        <label class="form-label">Email <span style="color:#ef4444;">*</span></label>
                        <input type="email" id="modal-email" placeholder="email@exemple.com" class="input">
                    </div>
                    <div>
                        <label class="form-label">Téléphone</label>
                        <input type="text" id="modal-telephone" placeholder="+221 77 000 00 00" class="input">
                    </div>
                </div>
                <div style="margin-bottom:20px;">
                    <label class="form-label">Mot de passe <span style="color:#ef4444;">*</span></label>
                    <input type="password" id="modal-password" placeholder="Min. 8 caractères" class="input">
                </div>
            </div>

            
            <div style="padding:14px 20px;border-top:1px solid var(--border);display:flex;justify-content:flex-end;gap:8px;background:var(--bg);">
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

        // ── Chargement des infos du bien sélectionné ─────────────────────────
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

            // Suggérer caution = 2 mois de loyer si le champ est vide
            const cautionInput = document.getElementById('caution');
            if (!cautionInput.value) {
                cautionInput.value = Math.round(loyerMensuel * 2);
            }
            document.getElementById('caution-hint').textContent =
                '💡 Suggestion : ' + Math.round(loyerMensuel).toLocaleString('fr-FR') +
                ' FCFA (1 mois) ou ' + Math.round(loyerMensuel * 2).toLocaleString('fr-FR') + ' FCFA (2 mois)';
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
            ['modal-name', 'modal-email', 'modal-telephone', 'modal-password'].forEach(id => {
                document.getElementById(id).value = '';
            });
        }

        function fermerModaleLocataire() {
            document.getElementById('modal-locataire').style.display = 'none';
        }

        document.getElementById('modal-locataire').addEventListener('click', function(e) {
            if (e.target === this) fermerModaleLocataire();
        });

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
                const response = await fetch('<?php echo e(route('admin.contrats.locataire-rapide')); ?>', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>',
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ name, email, telephone: tel, password: pwd }),
                });

                const data = await response.json();

                if (!response.ok) {
                    const msgs = data.errors
                        ? Object.values(data.errors).flat().join(' — ')
                        : (data.message || 'Erreur inconnue.');
                    errDiv.textContent = '❌ ' + msgs;
                    errDiv.style.display = 'block';
                    return;
                }

                // Ajouter au select et le sélectionner
                const select = document.getElementById('locataire_id');
                const opt    = document.createElement('option');
                opt.value    = data.id;
                opt.text     = data.name;
                opt.selected = true;
                select.add(opt);

                fermerModaleLocataire();

            } catch (err) {
                errDiv.textContent = '❌ Erreur réseau. Veuillez réessayer.';
                errDiv.style.display = 'block';
            } finally {
                btn.disabled    = false;
                btn.textContent = 'Créer le locataire';
            }
        }
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
<?php endif; ?><?php /**PATH C:\Users\ph\bimotech-immo\resources\views/admin/contrats/create.blade.php ENDPATH**/ ?>