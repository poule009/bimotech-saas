
<?php $__env->startSection('title', 'Modifier — ' . $user->name); ?>
<?php $__env->startSection('breadcrumb', ($user->isProprietaire() ? 'Propriétaires' : 'Locataires') . ' › Modifier'); ?>

<?php $__env->startSection('content'); ?>
<style>
.form-grid { display:grid;grid-template-columns:1fr 280px;gap:24px;align-items:start; }
.card { background:#fff;border:1px solid #e5e7eb;border-radius:14px;overflow:hidden;margin-bottom:16px; }
.card-hd { padding:14px 20px;border-bottom:1px solid #e5e7eb;display:flex;align-items:center;gap:10px; }
.card-icon { width:30px;height:30px;border-radius:8px;display:flex;align-items:center;justify-content:center;flex-shrink:0; }
.card-icon svg { width:15px;height:15px; }
.card-icon.gold   { background:#f5e9c9;color:#8a6e2f; }
.card-icon.blue   { background:#dbeafe;color:#1d4ed8; }
.card-icon.green  { background:#dcfce7;color:#16a34a; }
.card-icon.purple { background:#ede9fe;color:#7c3aed; }
.card-title { font-family:'Syne',sans-serif;font-size:13px;font-weight:700;color:#0d1117; }
.card-body { padding:18px 20px; }
.form-row { display:grid;grid-template-columns:1fr 1fr;gap:14px; }
.form-group { margin-bottom:14px; }
.form-group:last-child { margin-bottom:0; }
.form-label { display:block;font-size:12px;font-weight:600;color:#374151;margin-bottom:5px; }
.req { color:#dc2626; }
.opt { color:#9ca3af;font-weight:400; }
.form-input,.form-select,.form-textarea {
    width:100%;padding:9px 12px;border:1px solid #e5e7eb;border-radius:8px;
    font-size:13px;font-family:'DM Sans',sans-serif;color:#0d1117;background:#fff;
    outline:none;transition:border .15s;
}
.form-input:focus,.form-select:focus,.form-textarea:focus { border-color:#c9a84c;box-shadow:0 0 0 3px rgba(201,168,76,.1); }
.form-error { font-size:11px;color:#dc2626;margin-top:3px; }
.form-textarea { resize:vertical;min-height:80px; }

.role-badge { display:inline-flex;align-items:center;gap:6px;padding:4px 12px;border-radius:7px;font-size:11px;font-weight:600; }
.role-proprio { background:#f5e9c9;color:#8a6e2f;border:1px solid #e9d5a0; }
.role-locataire { background:#dbeafe;color:#1d4ed8;border:1px solid #bfdbfe; }

.submit-bar { display:flex;justify-content:flex-end;gap:10px;padding:14px 20px;border-top:1px solid #e5e7eb;background:#f9fafb; }
.btn-cancel { padding:8px 16px;border-radius:8px;border:1px solid #e5e7eb;background:#fff;color:#6b7280;font-size:13px;font-family:'DM Sans',sans-serif;cursor:pointer;text-decoration:none;display:inline-flex;align-items:center; }
.btn-submit { padding:8px 18px;border-radius:8px;border:none;background:#2a4a7f;color:#fff;font-size:13px;font-weight:600;font-family:'DM Sans',sans-serif;cursor:pointer;display:inline-flex;align-items:center;gap:6px; }

.side-card { background:#0d1117;border-radius:14px;overflow:hidden;position:sticky;top:24px; }
.side-hd { padding:12px 16px;border-bottom:1px solid rgba(255,255,255,.07); }
.side-title { font-family:'Syne',sans-serif;font-size:12px;font-weight:700;color:#fff; }
.side-body { padding:14px 16px; }
.side-row { display:flex;justify-content:space-between;padding:6px 0;border-bottom:1px solid rgba(255,255,255,.06);font-size:12px; }
.side-row:last-child { border-bottom:none; }
.side-lbl { color:rgba(255,255,255,.4); }
.side-val { color:#e6edf3;font-weight:500; }
</style>

<div style="padding:0 0 48px">

    
    <div style="display:flex;align-items:center;gap:8px;font-size:13px;color:#6b7280;margin-bottom:16px">
        <?php if($user->isProprietaire()): ?>
            <a href="<?php echo e(route('admin.users.proprietaires')); ?>" style="color:#6b7280;text-decoration:none">Propriétaires</a>
        <?php else: ?>
            <a href="<?php echo e(route('admin.users.locataires')); ?>" style="color:#6b7280;text-decoration:none">Locataires</a>
        <?php endif; ?>
        <svg style="width:12px;height:12px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
        <a href="<?php echo e(route('admin.users.show', $user)); ?>" style="color:#6b7280;text-decoration:none"><?php echo e($user->name); ?></a>
        <svg style="width:12px;height:12px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
        <span style="color:#0d1117;font-weight:500">Modifier</span>
    </div>

    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;flex-wrap:wrap;gap:10px">
        <div>
            <h1 style="font-family:'Syne',sans-serif;font-size:22px;font-weight:700;color:#0d1117;letter-spacing:-.4px">
                Modifier le profil
            </h1>
            <p style="font-size:13px;color:#6b7280;margin-top:3px">
                <?php echo e($user->name); ?> · <?php echo e($user->email); ?>

            </p>
        </div>
        <span class="role-badge <?php echo e($user->isProprietaire() ? 'role-proprio' : 'role-locataire'); ?>">
            <?php echo e($user->isProprietaire() ? 'Propriétaire' : 'Locataire'); ?>

        </span>
    </div>

    <?php if($errors->any()): ?>
    <div style="background:#fef2f2;border:1px solid #fecaca;border-left:3px solid #dc2626;border-radius:8px;padding:12px 16px;margin-bottom:18px;font-size:13px;color:#dc2626">
        <ul style="padding-left:16px;margin:0">
            <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $e): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><li><?php echo e($e); ?></li><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </ul>
    </div>
    <?php endif; ?>

    <form method="POST" action="<?php echo e(route('admin.users.update', $user)); ?>">
        <?php echo csrf_field(); ?> <?php echo method_field('PATCH'); ?>
        <div class="form-grid">

            
            <div>

                
                <div class="card">
                    <div class="card-hd">
                        <div class="card-icon gold">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                        </div>
                        <div class="card-title">Informations générales</div>
                    </div>
                    <div class="card-body">
                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">Nom complet <span class="req">*</span></label>
                                <input type="text" name="name" class="form-input"
                                       value="<?php echo e(old('name', $user->name)); ?>" required>
                                <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="form-error"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Email <span class="req">*</span></label>
                                <input type="email" name="email" class="form-input"
                                       value="<?php echo e(old('email', $user->email)); ?>" required>
                                <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="form-error"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">Téléphone</label>
                                <input type="text" name="telephone" class="form-input"
                                       value="<?php echo e(old('telephone', $user->telephone)); ?>"
                                       placeholder="+221 7X XXX XX XX">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Adresse</label>
                                <input type="text" name="adresse" class="form-input"
                                       value="<?php echo e(old('adresse', $user->adresse)); ?>"
                                       placeholder="Adresse complète">
                            </div>
                        </div>
                    </div>
                </div>

                
                <?php if($user->isProprietaire()): ?>
                <div class="card">
                    <div class="card-hd">
                        <div class="card-icon blue">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/></svg>
                        </div>
                        <div class="card-title">Profil propriétaire</div>
                    </div>
                    <div class="card-body">
                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">CNI / Passeport</label>
                                <input type="text" name="cni" class="form-input"
                                       value="<?php echo e(old('cni', $user->proprietaire?->cni)); ?>">
                            </div>
                            <div class="form-group">
                                <label class="form-label">NINEA</label>
                                <input type="text" name="ninea" class="form-input"
                                       value="<?php echo e(old('ninea', $user->proprietaire?->ninea)); ?>"
                                       placeholder="Ex: 123456789">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">Date de naissance</label>
                                <input type="date" name="date_naissance" class="form-input"
                                       value="<?php echo e(old('date_naissance', $user->proprietaire?->date_naissance?->format('Y-m-d'))); ?>">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Genre</label>
                                <select name="genre" class="form-select">
                                    <option value="">— Choisir —</option>
                                    <option value="homme" <?php echo e(old('genre', $user->proprietaire?->genre) === 'homme' ? 'selected':''); ?>>Homme</option>
                                    <option value="femme" <?php echo e(old('genre', $user->proprietaire?->genre) === 'femme' ? 'selected':''); ?>>Femme</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">Ville</label>
                                <input type="text" name="ville" class="form-input"
                                       value="<?php echo e(old('ville', $user->proprietaire?->ville ?? 'Dakar')); ?>">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Quartier</label>
                                <input type="text" name="quartier" class="form-input"
                                       value="<?php echo e(old('quartier', $user->proprietaire?->quartier)); ?>">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-hd">
                        <div class="card-icon green">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="1" y="4" width="22" height="16" rx="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>
                        </div>
                        <div class="card-title">Coordonnées bancaires</div>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label class="form-label">Mode de paiement préféré</label>
                            <select name="mode_paiement_prefere" class="form-select">
                                <?php $__currentLoopData = ['especes'=>'Espèces','virement'=>'Virement bancaire','wave'=>'Wave','orange_money'=>'Orange Money','free_money'=>'Free Money','cheque'=>'Chèque']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $val => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($val); ?>"
                                        <?php echo e(old('mode_paiement_prefere', $user->proprietaire?->mode_paiement_prefere) === $val ? 'selected':''); ?>>
                                        <?php echo e($label); ?>

                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">Banque</label>
                                <input type="text" name="banque" class="form-input"
                                       value="<?php echo e(old('banque', $user->proprietaire?->banque)); ?>"
                                       placeholder="Ex: CBAO, Ecobank...">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Numéro Wave</label>
                                <input type="text" name="numero_wave" class="form-input"
                                       value="<?php echo e(old('numero_wave', $user->proprietaire?->numero_wave)); ?>"
                                       placeholder="+221 7X XXX XX XX">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Numéro Orange Money</label>
                            <input type="text" name="numero_om" class="form-input"
                                   value="<?php echo e(old('numero_om', $user->proprietaire?->numero_om)); ?>"
                                   placeholder="+221 7X XXX XX XX">
                        </div>
                    </div>
                </div>

                
                <?php elseif($user->isLocataire()): ?>
                <div class="card">
                    <div class="card-hd">
                        <div class="card-icon blue">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                        </div>
                        <div class="card-title">Profil locataire</div>
                    </div>
                    <div class="card-body">
                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">CNI / Passeport</label>
                                <input type="text" name="cni" class="form-input"
                                       value="<?php echo e(old('cni', $user->locataire?->cni)); ?>">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Date de naissance</label>
                                <input type="date" name="date_naissance" class="form-input"
                                       value="<?php echo e(old('date_naissance', $user->locataire?->date_naissance?->format('Y-m-d'))); ?>">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">Profession</label>
                                <input type="text" name="profession" class="form-input"
                                       value="<?php echo e(old('profession', $user->locataire?->profession)); ?>">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Employeur</label>
                                <input type="text" name="employeur" class="form-input"
                                       value="<?php echo e(old('employeur', $user->locataire?->employeur)); ?>">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">Revenu mensuel (FCFA)</label>
                                <input type="number" name="revenu_mensuel" class="form-input"
                                       value="<?php echo e(old('revenu_mensuel', $user->locataire?->revenu_mensuel)); ?>"
                                       min="0" step="500">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Genre</label>
                                <select name="genre" class="form-select">
                                    <option value="">— Choisir —</option>
                                    <option value="homme" <?php echo e(old('genre', $user->locataire?->genre) === 'homme' ? 'selected':''); ?>>Homme</option>
                                    <option value="femme" <?php echo e(old('genre', $user->locataire?->genre) === 'femme' ? 'selected':''); ?>>Femme</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-hd">
                        <div class="card-icon purple">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75"/></svg>
                        </div>
                        <div class="card-title">Contact d'urgence <span class="opt">(optionnel)</span></div>
                    </div>
                    <div class="card-body">
                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">Nom</label>
                                <input type="text" name="contact_urgence_nom" class="form-input"
                                       value="<?php echo e(old('contact_urgence_nom', $user->locataire?->contact_urgence_nom)); ?>">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Téléphone</label>
                                <input type="text" name="contact_urgence_tel" class="form-input"
                                       value="<?php echo e(old('contact_urgence_tel', $user->locataire?->contact_urgence_tel)); ?>">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Lien de parenté</label>
                            <input type="text" name="contact_urgence_lien" class="form-input"
                                   value="<?php echo e(old('contact_urgence_lien', $user->locataire?->contact_urgence_lien)); ?>"
                                   placeholder="Ex: Père, Mère, Époux(se)...">
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                
                <div class="card">
                    <div class="submit-bar">
                        <a href="<?php echo e(route('admin.users.show', $user)); ?>" class="btn-cancel">Annuler</a>
                        <button type="submit" class="btn-submit">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="width:13px;height:13px"><polyline points="20 6 9 17 4 12"/></svg>
                            Enregistrer les modifications
                        </button>
                    </div>
                </div>

            </div>

            
            <div>
                <div class="side-card">
                    <div class="side-hd"><div class="side-title">Profil actuel</div></div>
                    <div class="side-body">
                        <div style="text-align:center;padding:14px 0 10px">
                            <div style="width:52px;height:52px;border-radius:50%;background:rgba(201,168,76,.15);border:2px solid rgba(201,168,76,.3);display:flex;align-items:center;justify-content:center;margin:0 auto 10px;font-family:'Syne',sans-serif;font-size:20px;font-weight:700;color:#c9a84c">
                                <?php echo e(strtoupper(substr($user->name, 0, 1))); ?>

                            </div>
                            <div style="font-size:13px;font-weight:600;color:#e6edf3"><?php echo e($user->name); ?></div>
                            <div style="font-size:11px;color:#484f58;margin-top:2px"><?php echo e($user->email); ?></div>
                        </div>
                        <div class="side-row">
                            <span class="side-lbl">Rôle</span>
                            <span class="side-val"><?php echo e($user->isProprietaire() ? 'Propriétaire' : 'Locataire'); ?></span>
                        </div>
                        <?php if($user->telephone): ?>
                        <div class="side-row">
                            <span class="side-lbl">Téléphone</span>
                            <span class="side-val"><?php echo e($user->telephone); ?></span>
                        </div>
                        <?php endif; ?>
                        <div class="side-row">
                            <span class="side-lbl">Membre depuis</span>
                            <span class="side-val"><?php echo e($user->created_at?->format('d/m/Y')); ?></span>
                        </div>
                        <?php if($user->isProprietaire()): ?>
                        <div class="side-row">
                            <span class="side-lbl">Biens</span>
                            <span class="side-val"><?php echo e($user->biens()->count()); ?></span>
                        </div>
                        <?php else: ?>
                        <div class="side-row">
                            <span class="side-lbl">Contrats</span>
                            <span class="side-val"><?php echo e($user->contrats()->count()); ?></span>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

        </div>
    </form>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\ph\bimotech-immo\resources\views/users/edit.blade.php ENDPATH**/ ?>