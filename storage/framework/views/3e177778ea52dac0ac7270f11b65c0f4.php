
<?php $__env->startSection('title', 'Modifier contrat — ' . ($contrat->reference_bail ?? '#'.$contrat->id)); ?>
<?php $__env->startSection('breadcrumb', 'Contrats › Modifier'); ?>

<?php $__env->startSection('content'); ?>
<style>
.form-grid { display:grid; grid-template-columns:1fr 300px; gap:24px; align-items:start; }
.card { background:#fff; border:1px solid #e5e7eb; border-radius:14px; overflow:hidden; margin-bottom:16px; }
.card-hd { padding:14px 20px; border-bottom:1px solid #e5e7eb; display:flex; align-items:center; gap:10px; }
.card-icon { width:30px;height:30px;border-radius:8px;display:flex;align-items:center;justify-content:center;flex-shrink:0; }
.card-icon svg { width:15px;height:15px; }
.card-icon.gold   { background:#f5e9c9;color:#8a6e2f; }
.card-icon.blue   { background:#dbeafe;color:#1d4ed8; }
.card-icon.green  { background:#dcfce7;color:#16a34a; }
.card-icon.purple { background:#ede9fe;color:#7c3aed; }
.card-title { font-family:'Syne',sans-serif;font-size:13px;font-weight:700;color:#0d1117; }
.card-body { padding:18px 20px; }
.form-row { display:grid;grid-template-columns:1fr 1fr;gap:14px; }
.form-row-3 { display:grid;grid-template-columns:1fr 1fr 1fr;gap:14px; }
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
.form-input.error { border-color:#dc2626; }
.form-error { font-size:11px;color:#dc2626;margin-top:3px; }
.form-textarea { resize:vertical;min-height:80px; }
.form-hint { font-size:11px;color:#9ca3af;margin-top:3px; }
.form-input[readonly] { background:#f9fafb;color:#6b7280;cursor:not-allowed; }

.ref-badge { display:inline-flex;align-items:center;gap:6px;padding:4px 12px;background:#f5e9c9;border:1px solid #e9d5a0;border-radius:7px;font-family:'Syne',sans-serif;font-size:11px;font-weight:600;color:#8a6e2f; }
.info-banner { background:#fefce8;border:1px solid #fde68a;border-left:3px solid #f59e0b;border-radius:8px;padding:10px 14px;font-size:12px;color:#92400e;margin-bottom:16px;display:flex;align-items:center;gap:8px; }

.submit-bar { display:flex;justify-content:flex-end;gap:10px;padding:14px 20px;border-top:1px solid #e5e7eb;background:#f9fafb; }
.btn-cancel { padding:8px 16px;border-radius:8px;border:1px solid #e5e7eb;background:#fff;color:#6b7280;font-size:13px;font-family:'DM Sans',sans-serif;cursor:pointer;text-decoration:none;display:inline-flex;align-items:center; }
.btn-submit { padding:8px 18px;border-radius:8px;border:none;background:#2a4a7f;color:#fff;font-size:13px;font-weight:600;font-family:'DM Sans',sans-serif;cursor:pointer;display:inline-flex;align-items:center;gap:6px; }

/* Sidebar */
.side-card { background:#0d1117;border-radius:14px;overflow:hidden;margin-bottom:14px;position:sticky;top:24px; }
.side-hd { padding:12px 16px;border-bottom:1px solid rgba(255,255,255,.07); }
.side-title { font-family:'Syne',sans-serif;font-size:12px;font-weight:700;color:#fff; }
.side-body { padding:14px 16px; }
.side-row { display:flex;justify-content:space-between;padding:6px 0;border-bottom:1px solid rgba(255,255,255,.06);font-size:12px; }
.side-row:last-child { border-bottom:none; }
.side-lbl { color:rgba(255,255,255,.4); }
.side-val { color:#e6edf3;font-weight:500; }
.side-val.gold { color:#c9a84c; }

/* Recap live */
.rp-row { display:flex;justify-content:space-between;padding:6px 0;border-bottom:1px solid rgba(255,255,255,.06); }
.rp-row:last-child { border-bottom:none; }
.rp-lbl { font-size:12px;color:rgba(255,255,255,.4); }
.rp-val { font-family:'Syne',sans-serif;font-size:12px;font-weight:600;color:#fff; }
.rp-val.gold { color:#c9a84c; }
.rp-total { background:rgba(201,168,76,.1);border:1px solid rgba(201,168,76,.2);border-radius:9px;padding:12px 14px;margin-top:12px; }
.rp-total-lbl { font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.8px;color:rgba(201,168,76,.6);margin-bottom:4px; }
.rp-total-val { font-family:'Syne',sans-serif;font-size:18px;font-weight:700;color:#c9a84c; }
</style>

<div style="padding:0 0 48px">

    
    <div style="display:flex;align-items:center;gap:8px;font-size:13px;color:#6b7280;margin-bottom:16px">
        <a href="<?php echo e(route('admin.contrats.index')); ?>" style="color:#6b7280;text-decoration:none">Contrats</a>
        <svg style="width:12px;height:12px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
        <a href="<?php echo e(route('admin.contrats.show', $contrat)); ?>" style="color:#6b7280;text-decoration:none">
            <?php echo e($contrat->reference_bail ?? 'Contrat #'.$contrat->id); ?>

        </a>
        <svg style="width:12px;height:12px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
        <span style="color:#0d1117;font-weight:500">Modifier</span>
    </div>

    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;flex-wrap:wrap;gap:10px">
        <div>
            <h1 style="font-family:'Syne',sans-serif;font-size:22px;font-weight:700;color:#0d1117;letter-spacing:-.4px">
                Modifier le contrat
            </h1>
            <p style="font-size:13px;color:#6b7280;margin-top:3px">
                Le bien et le locataire ne peuvent pas être changés.
            </p>
        </div>
        <div class="ref-badge">
            <?php echo e($contrat->reference_bail ?? 'BAIL-'.$contrat->id); ?>

        </div>
    </div>

    <?php if($errors->any()): ?>
    <div style="background:#fef2f2;border:1px solid #fecaca;border-left:3px solid #dc2626;border-radius:8px;padding:12px 16px;margin-bottom:18px;font-size:13px;color:#dc2626">
        <strong>Veuillez corriger les erreurs :</strong>
        <ul style="margin-top:4px;padding-left:16px">
            <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $e): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><li><?php echo e($e); ?></li><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </ul>
    </div>
    <?php endif; ?>

    
    <div class="info-banner">
        <svg style="width:14px;height:14px;flex-shrink:0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
        Toute modification du loyer prendra effet sur les prochains paiements. Les paiements déjà validés ne seront pas recalculés.
    </div>

    <form method="POST" action="<?php echo e(route('admin.contrats.update', $contrat)); ?>">
        <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>
        <div class="form-grid">

            
            <div>

                
                <div class="card">
                    <div class="card-hd">
                        <div class="card-icon gold">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                        </div>
                        <div class="card-title">Bien & Locataire</div>
                    </div>
                    <div class="card-body">
                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">Bien (non modifiable)</label>
                                <input type="text" class="form-input" readonly
                                       value="<?php echo e($contrat->bien?->reference); ?> — <?php echo e($contrat->bien?->adresse); ?>, <?php echo e($contrat->bien?->ville); ?>">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Locataire (non modifiable)</label>
                                <input type="text" class="form-input" readonly
                                       value="<?php echo e($contrat->locataire?->name); ?>">
                            </div>
                        </div>
                    </div>
                </div>

                
                <div class="card">
                    <div class="card-hd">
                        <div class="card-icon blue">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                        </div>
                        <div class="card-title">Durée & Type de bail</div>
                    </div>
                    <div class="card-body">
                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">Date de début <span class="req">*</span></label>
                                <input type="date" name="date_debut"
                                       class="form-input <?php echo e($errors->has('date_debut') ? 'error':''); ?>"
                                       value="<?php echo e(old('date_debut', $contrat->date_debut?->format('Y-m-d'))); ?>">
                                <?php $__errorArgs = ['date_debut'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="form-error"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Date de fin <span class="opt">(optionnel)</span></label>
                                <input type="date" name="date_fin" class="form-input"
                                       value="<?php echo e(old('date_fin', $contrat->date_fin?->format('Y-m-d'))); ?>">
                                <div class="form-hint">Laisser vide = contrat ouvert</div>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">Type de bail <span class="req">*</span></label>
                                <select name="type_bail" class="form-select">
                                    <?php $__currentLoopData = $typesBail; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $val => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($val); ?>"
                                            <?php echo e(old('type_bail', $contrat->type_bail) === $val ? 'selected':''); ?>>
                                            <?php echo e($label); ?>

                                        </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Référence bail</label>
                                <input type="text" name="reference_bail" class="form-input"
                                       value="<?php echo e(old('reference_bail', $contrat->reference_bail)); ?>"
                                       placeholder="Ex: BAIL-2024-001">
                            </div>
                        </div>
                    </div>
                </div>

                
                <div class="card">
                    <div class="card-hd">
                        <div class="card-icon gold">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6"/></svg>
                        </div>
                        <div class="card-title">Ventilation du loyer</div>
                    </div>
                    <div class="card-body">
                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">Loyer nu (FCFA) <span class="req">*</span></label>
                                <input type="number" name="loyer_nu" id="loyer_nu"
                                       class="form-input <?php echo e($errors->has('loyer_nu') ? 'error':''); ?>"
                                       value="<?php echo e(old('loyer_nu', $contrat->loyer_nu)); ?>"
                                       min="0" step="500" oninput="mettreAJourRecap()">
                                <div class="form-hint">Hors charges et TOM</div>
                                <?php $__errorArgs = ['loyer_nu'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="form-error"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Charges mensuelles</label>
                                <input type="number" name="charges_mensuelles" id="charges_mensuelles"
                                       class="form-input"
                                       value="<?php echo e(old('charges_mensuelles', $contrat->charges_mensuelles ?? 0)); ?>"
                                       min="0" step="500" oninput="mettreAJourRecap()">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">TOM (Taxe ordures ménagères)</label>
                                <input type="number" name="tom_amount" id="tom_amount"
                                       class="form-input"
                                       value="<?php echo e(old('tom_amount', $contrat->tom_amount ?? 0)); ?>"
                                       min="0" step="100" oninput="mettreAJourRecap()">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Indexation annuelle (%)</label>
                                <input type="number" name="indexation_annuelle" class="form-input"
                                       value="<?php echo e(old('indexation_annuelle', $contrat->indexation_annuelle ?? 0)); ?>"
                                       min="0" max="20" step="0.5">
                            </div>
                        </div>
                    </div>
                </div>

                
                <div class="card">
                    <div class="card-hd">
                        <div class="card-icon green">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                        </div>
                        <div class="card-title">Caution & Frais</div>
                    </div>
                    <div class="card-body">
                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">Caution (FCFA) <span class="req">*</span></label>
                                <input type="number" name="caution" class="form-input"
                                       value="<?php echo e(old('caution', $contrat->caution)); ?>" min="0" step="500">
                                <?php $__errorArgs = ['caution'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="form-error"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Nombre de mois de caution</label>
                                <select name="nombre_mois_caution" class="form-select">
                                    <?php $__currentLoopData = [1,2,3,6]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $n): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($n); ?>"
                                            <?php echo e(old('nombre_mois_caution', $contrat->nombre_mois_caution ?? 1) == $n ? 'selected':''); ?>>
                                            <?php echo e($n); ?> mois
                                        </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Frais d'agence (FCFA)</label>
                            <input type="number" name="frais_agence" class="form-input"
                                   value="<?php echo e(old('frais_agence', $contrat->frais_agence ?? 0)); ?>" min="0" step="500">
                        </div>
                    </div>
                </div>

                
                <div class="card">
                    <div class="card-hd">
                        <div class="card-icon purple">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75"/></svg>
                        </div>
                        <div class="card-title">Garant <span class="opt">(optionnel)</span></div>
                    </div>
                    <div class="card-body">
                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">Nom du garant</label>
                                <input type="text" name="garant_nom" class="form-input"
                                       value="<?php echo e(old('garant_nom', $contrat->garant_nom)); ?>">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Téléphone</label>
                                <input type="text" name="garant_telephone" class="form-input"
                                       value="<?php echo e(old('garant_telephone', $contrat->garant_telephone)); ?>">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Adresse</label>
                            <input type="text" name="garant_adresse" class="form-input"
                                   value="<?php echo e(old('garant_adresse', $contrat->garant_adresse)); ?>">
                        </div>
                    </div>
                </div>

                
                <div class="card">
                    <div class="card-hd">
                        <div class="card-icon purple">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
                        </div>
                        <div class="card-title">Observations</div>
                    </div>
                    <div class="card-body">
                        <textarea name="observations" class="form-textarea"><?php echo e(old('observations', $contrat->observations)); ?></textarea>
                    </div>
                    <div class="submit-bar">
                        <a href="<?php echo e(route('admin.contrats.show', $contrat)); ?>" class="btn-cancel">Annuler</a>
                        <button type="submit" class="btn-submit">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="width:13px;height:13px"><polyline points="20 6 9 17 4 12"/></svg>
                            Enregistrer les modifications
                        </button>
                    </div>
                </div>

            </div>

            
            <div>

                
                <div class="side-card">
                    <div class="side-hd"><div class="side-title">Contrat actuel</div></div>
                    <div class="side-body">
                        <div class="side-row">
                            <span class="side-lbl">Statut</span>
                            <span class="side-val"><?php echo e(\App\Models\Contrat::STATUTS[$contrat->statut] ?? $contrat->statut); ?></span>
                        </div>
                        <div class="side-row">
                            <span class="side-lbl">Loyer contractuel</span>
                            <span class="side-val gold"><?php echo e(number_format($contrat->loyer_contractuel, 0, ',', ' ')); ?> F</span>
                        </div>
                        <div class="side-row">
                            <span class="side-lbl">Loyer nu</span>
                            <span class="side-val"><?php echo e(number_format($contrat->loyer_nu, 0, ',', ' ')); ?> F</span>
                        </div>
                        <div class="side-row">
                            <span class="side-lbl">Charges</span>
                            <span class="side-val"><?php echo e(number_format($contrat->charges_mensuelles ?? 0, 0, ',', ' ')); ?> F</span>
                        </div>
                        <div class="side-row">
                            <span class="side-lbl">TOM</span>
                            <span class="side-val"><?php echo e(number_format($contrat->tom_amount ?? 0, 0, ',', ' ')); ?> F</span>
                        </div>
                        <div class="side-row">
                            <span class="side-lbl">Caution</span>
                            <span class="side-val"><?php echo e(number_format($contrat->caution, 0, ',', ' ')); ?> F</span>
                        </div>
                        <div class="side-row">
                            <span class="side-lbl">Créé le</span>
                            <span class="side-val"><?php echo e($contrat->created_at?->format('d/m/Y')); ?></span>
                        </div>
                    </div>
                </div>

                
                <div class="side-card">
                    <div class="side-hd"><div class="side-title">Nouveau récapitulatif</div></div>
                    <div class="side-body">
                        <div class="rp-row">
                            <div class="rp-lbl">Loyer nu</div>
                            <div class="rp-val" id="rp-loyer-nu">— F</div>
                        </div>
                        <div class="rp-row">
                            <div class="rp-lbl">+ Charges</div>
                            <div class="rp-val" id="rp-charges">— F</div>
                        </div>
                        <div class="rp-row">
                            <div class="rp-lbl">+ TOM</div>
                            <div class="rp-val" id="rp-tom">— F</div>
                        </div>
                        <div class="rp-row">
                            <div class="rp-lbl" style="color:rgba(255,255,255,.7);font-weight:600">= Loyer total</div>
                            <div class="rp-val gold" id="rp-total">— F</div>
                        </div>
                        <div class="rp-total">
                            <div class="rp-total-lbl">Commission HT</div>
                            <div class="rp-total-val" id="rp-comm">— F</div>
                        </div>
                    </div>
                </div>

            </div>

        </div>
    </form>
</div>

<script>
const tauxComm = <?php echo e($contrat->bien?->taux_commission ?? 10); ?>;

function fmt(n) { return Math.round(n).toLocaleString('fr-FR') + ' F'; }

function mettreAJourRecap() {
    const loyerNu = parseFloat(document.getElementById('loyer_nu').value)           || 0;
    const charges = parseFloat(document.getElementById('charges_mensuelles').value) || 0;
    const tom     = parseFloat(document.getElementById('tom_amount').value)          || 0;
    const total   = loyerNu + charges + tom;
    const commHt  = Math.round(loyerNu * tauxComm / 100);

    document.getElementById('rp-loyer-nu').textContent = fmt(loyerNu);
    document.getElementById('rp-charges').textContent  = fmt(charges);
    document.getElementById('rp-tom').textContent      = fmt(tom);
    document.getElementById('rp-total').textContent    = fmt(total);
    document.getElementById('rp-comm').textContent     = fmt(commHt);
}

mettreAJourRecap();
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\ph\bimotech-immo\resources\views/admin/contrats/edit.blade.php ENDPATH**/ ?>