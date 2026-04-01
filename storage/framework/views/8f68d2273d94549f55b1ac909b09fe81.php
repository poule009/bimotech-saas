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
     <?php $__env->slot('header', null, []); ?> Nouveau bien <?php $__env->endSlot(); ?>

    
    <div style="display:flex;align-items:center;gap:12px;" class="section-gap">
        <a href="<?php echo e(route('biens.index')); ?>"
           style="display:flex;align-items:center;justify-content:center;width:32px;height:32px;border-radius:var(--radius-sm);border:1px solid var(--border);color:var(--text-2);transition:background .15s;"
           onmouseenter="this.style.background='var(--bg)'"
           onmouseleave="this.style.background='transparent'">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:16px;height:16px;">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <div>
            <h1 style="font-size:20px;font-weight:700;color:var(--text);letter-spacing:-.3px;">Nouveau bien</h1>
            <p style="font-size:13px;color:var(--text-3);margin-top:2px;">Renseignez les informations du bien immobilier</p>
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

                <form method="POST" action="<?php echo e(route('biens.store')); ?>">
                    <?php echo csrf_field(); ?>

                    
                    <?php if(auth()->user()->isAdmin()): ?>
                        <div style="margin-bottom:20px;">
                            <label class="form-label">Propriétaire <span style="color:#ef4444;">*</span></label>
                            <select name="proprietaire_id" class="input">
                                <option value="">— Sélectionner un propriétaire —</option>
                                <?php $__currentLoopData = $proprietaires; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $proprio): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($proprio->id); ?>"
                                            <?php echo e(old('proprietaire_id') == $proprio->id ? 'selected' : ''); ?>>
                                        <?php echo e($proprio->name); ?> — <?php echo e($proprio->email); ?>

                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                            <?php $__errorArgs = ['proprietaire_id'];
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
                    <?php else: ?>
                        <input type="hidden" name="proprietaire_id" value="<?php echo e(auth()->id()); ?>">
                    <?php endif; ?>

                    
                    <div style="font-size:12px;font-weight:700;color:var(--text-2);text-transform:uppercase;letter-spacing:.5px;margin-bottom:12px;padding-bottom:8px;border-bottom:1px solid var(--border);">
                        🏠 Identification
                    </div>

                    
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:20px;">
                        <div>
                            <label class="form-label">Type de bien <span style="color:#ef4444;">*</span></label>
                            <select name="type" class="input">
                                <option value="">— Choisir —</option>
                                <?php $__currentLoopData = ['Appartement', 'Villa', 'Studio', 'Chambre', 'Bureau', 'Local commercial', 'Entrepôt', 'Terrain', 'Maison', 'Duplex']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $type): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($type); ?>" <?php echo e(old('type') === $type ? 'selected' : ''); ?>><?php echo e($type); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                            <?php $__errorArgs = ['type'];
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
                            <label class="form-label">Statut <span style="color:#ef4444;">*</span></label>
                            <select name="statut" class="input">
                                <option value="disponible" <?php echo e(old('statut', 'disponible') === 'disponible' ? 'selected' : ''); ?>>Disponible</option>
                                <option value="loue"       <?php echo e(old('statut') === 'loue'       ? 'selected' : ''); ?>>Loué</option>
                                <option value="en_travaux" <?php echo e(old('statut') === 'en_travaux' ? 'selected' : ''); ?>>En travaux</option>
                            </select>
                            <?php $__errorArgs = ['statut'];
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

                    
                    <div style="font-size:12px;font-weight:700;color:var(--text-2);text-transform:uppercase;letter-spacing:.5px;margin-bottom:12px;padding-bottom:8px;border-bottom:1px solid var(--border);">
                        📍 Localisation
                    </div>

                    
                    <div style="margin-bottom:20px;">
                        <label class="form-label">Adresse <span style="color:#ef4444;">*</span></label>
                        <input type="text" name="adresse" value="<?php echo e(old('adresse')); ?>"
                               placeholder="Ex : 25 Rue de Thiong" class="input">
                        <?php $__errorArgs = ['adresse'];
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

                    
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:20px;">
                        <div>
                            <label class="form-label">Région / Ville <span style="color:#ef4444;">*</span></label>
                            <select name="ville" class="input">
                                <?php $__currentLoopData = [
                                    'Dakar', 'Thiès', 'Saint-Louis', 'Ziguinchor', 'Kaolack',
                                    'Fatick', 'Louga', 'Tambacounda', 'Kolda', 'Matam',
                                    'Kaffrine', 'Kédougou', 'Sédhiou', 'Diourbel'
                                ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ville): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($ville); ?>" <?php echo e(old('ville', 'Dakar') === $ville ? 'selected' : ''); ?>>
                                        <?php echo e($ville); ?>

                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                            <?php $__errorArgs = ['ville'];
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
                            <label class="form-label">Quartier</label>
                            <input type="text" name="quartier" value="<?php echo e(old('quartier')); ?>"
                                   placeholder="Ex : Almadies, Mermoz, Plateau…" class="input">
                            <?php $__errorArgs = ['quartier'];
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
                        <label class="form-label">Commune</label>
                        <input type="text" name="commune" value="<?php echo e(old('commune')); ?>"
                               placeholder="Ex : Dakar-Plateau, Guédiawaye, Pikine…" class="input">
                        <?php $__errorArgs = ['commune'];
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
                        📐 Caractéristiques
                    </div>

                    
                    <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:16px;margin-bottom:20px;">
                        <div>
                            <label class="form-label">Surface (m²)</label>
                            <input type="number" name="surface_m2" value="<?php echo e(old('surface_m2')); ?>"
                                   min="1" placeholder="85" class="input">
                            <?php $__errorArgs = ['surface_m2'];
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
                            <label class="form-label">Nombre de pièces</label>
                            <input type="number" name="nombre_pieces" value="<?php echo e(old('nombre_pieces')); ?>"
                                   min="1" placeholder="3" class="input">
                            <?php $__errorArgs = ['nombre_pieces'];
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
                            <label class="form-label">Meublé</label>
                            <div style="display:flex;align-items:center;gap:10px;height:38px;margin-top:2px;">
                                <label style="display:flex;align-items:center;gap:6px;cursor:pointer;font-size:13px;color:var(--text-2);">
                                    <input type="checkbox" name="meuble" value="1"
                                           <?php echo e(old('meuble') ? 'checked' : ''); ?>

                                           style="width:16px;height:16px;accent-color:var(--agency);cursor:pointer;">
                                    Oui, meublé
                                </label>
                            </div>
                            <?php $__errorArgs = ['meuble'];
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

                    
                    <div style="font-size:12px;font-weight:700;color:var(--text-2);text-transform:uppercase;letter-spacing:.5px;margin-bottom:12px;padding-bottom:8px;border-bottom:1px solid var(--border);">
                        💰 Conditions financières
                    </div>

                    
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:20px;">
                        <div>
                            <label class="form-label">Loyer mensuel (FCFA) <span style="color:#ef4444;">*</span></label>
                            <input type="number" name="loyer_mensuel" value="<?php echo e(old('loyer_mensuel')); ?>"
                                   min="1" placeholder="250 000" class="input">
                            <?php $__errorArgs = ['loyer_mensuel'];
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
                            <label class="form-label">Taux commission (%) <span style="color:#ef4444;">*</span></label>
                            <input type="number" name="taux_commission" value="<?php echo e(old('taux_commission', 10)); ?>"
                                   min="1" max="20" step="0.5" placeholder="10" class="input">
                            <div style="font-size:11px;color:var(--text-3);margin-top:4px;">Entre 1% et 20%</div>
                            <?php $__errorArgs = ['taux_commission'];
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
                        <label class="form-label">Description</label>
                        <textarea name="description" rows="3"
                                  placeholder="Appartement F3 climatisé, 2ème étage, vue sur mer…"
                                  class="input" style="resize:vertical;"><?php echo e(old('description')); ?></textarea>
                    </div>

                    
                    <div style="display:flex;justify-content:flex-end;gap:10px;padding-top:20px;border-top:1px solid var(--border);">
                        <a href="<?php echo e(route('biens.index')); ?>" class="btn btn-secondary">
                            Annuler
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:15px;height:15px;">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            Enregistrer le bien
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>

 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54)): ?>
<?php $attributes = $__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54; ?>
<?php unset($__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9ac128a9029c0e4701924bd2d73d7f54)): ?>
<?php $component = $__componentOriginal9ac128a9029c0e4701924bd2d73d7f54; ?>
<?php unset($__componentOriginal9ac128a9029c0e4701924bd2d73d7f54); ?>
<?php endif; ?><?php /**PATH C:\Users\ph\bimotech-immo\resources\views/biens/create.blade.php ENDPATH**/ ?>