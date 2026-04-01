<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo e($agency->name); ?> — Super Admin</title>
    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>
</head>
<body class="bg-gray-100 min-h-screen">

    
    <header class="bg-gray-900 text-white px-6 py-4 flex items-center justify-between shadow">
        <div class="flex items-center gap-4">
            <a href="<?php echo e(route('superadmin.dashboard')); ?>"
               class="text-gray-400 hover:text-white transition text-sm">
                ← Retour
            </a>
            <div>
                <h1 class="text-xl font-bold"><?php echo e($agency->name); ?></h1>
                <p class="text-gray-400 text-sm">Détail de l'agence — Super Administration</p>
            </div>
        </div>
        <div class="flex items-center gap-4">
            
            <?php if($agency->actif): ?>
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                    ● Active
                </span>
            <?php else: ?>
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                    ● Suspendue
                </span>
            <?php endif; ?>

            
            <form
                method="POST"
                action="<?php echo e(route('superadmin.agencies.toggle', $agency)); ?>"
                onsubmit="return confirm('<?php echo e($agency->actif ? 'Suspendre cette agence ?' : 'Activer cette agence ?'); ?>')"
            >
                <?php echo csrf_field(); ?>
                <?php echo method_field('PATCH'); ?>
                <button
                    type="submit"
                    class="text-sm px-4 py-2 rounded-lg transition font-medium
                        <?php echo e($agency->actif
                            ? 'bg-red-600 hover:bg-red-700 text-white'
                            : 'bg-green-600 hover:bg-green-700 text-white'); ?>"
                >
                    <?php echo e($agency->actif ? '⏸ Suspendre' : '▶ Activer'); ?>

                </button>
            </form>

            
            <form method="POST" action="<?php echo e(route('logout')); ?>">
                <?php echo csrf_field(); ?>
                <button class="text-sm bg-gray-700 hover:bg-gray-600 px-3 py-1 rounded transition">
                    Déconnexion
                </button>
            </form>
        </div>
    </header>

    <main class="max-w-7xl mx-auto px-4 py-8">

        
        <?php if(session('success')): ?>
            <div class="mb-6 bg-green-50 border border-green-200 text-green-800 rounded-lg px-4 py-3 text-sm">
                <?php echo e(session('success')); ?>

            </div>
        <?php endif; ?>

        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">

            
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                <h2 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-4">
                    Identité
                </h2>
                <div class="space-y-3">
                    
                    <div class="flex items-center gap-3">
                        <?php if($agency->logo_path): ?>
                            <img
                                src="<?php echo e(Storage::url($agency->logo_path)); ?>"
                                alt="<?php echo e($agency->name); ?>"
                                class="h-12 w-12 object-contain rounded-lg border border-gray-200 p-1"
                            >
                        <?php else: ?>
                            <div class="h-12 w-12 rounded-lg border-2 border-dashed border-gray-300 flex items-center justify-center">
                                <span class="text-2xl">🏢</span>
                            </div>
                        <?php endif; ?>
                        <div>
                            <p class="font-semibold text-gray-900"><?php echo e($agency->name); ?></p>
                            <p class="text-xs text-gray-400"><?php echo e($agency->slug); ?></p>
                        </div>
                    </div>

                    
                    <?php if($agency->couleur_primaire): ?>
                        <div class="flex items-center gap-2">
                            <div
                                class="h-5 w-5 rounded-full border border-gray-200"
                                style="background-color: <?php echo e($agency->couleur_primaire); ?>"
                            ></div>
                            <span class="text-sm text-gray-600"><?php echo e($agency->couleur_primaire); ?></span>
                        </div>
                    <?php endif; ?>

                    <div class="text-sm text-gray-600 space-y-1 pt-2 border-t border-gray-100">
                        <p>📧 <?php echo e($agency->email); ?></p>
                        <?php if($agency->telephone): ?>
                            <p>📞 <?php echo e($agency->telephone); ?></p>
                        <?php endif; ?>
                        <?php if($agency->adresse): ?>
                            <p>📍 <?php echo e($agency->adresse); ?></p>
                        <?php endif; ?>
                        <p>🧾 TVA : <?php echo e($agency->taux_tva); ?>%</p>
                        <p class="text-xs text-gray-400 pt-1">
                            Inscrite le <?php echo e($agency->created_at->format('d/m/Y à H:i')); ?>

                        </p>
                    </div>
                </div>
            </div>

            
            <div class="md:col-span-2 grid grid-cols-2 sm:grid-cols-3 gap-4">

                <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5 text-center">
                    <p class="text-3xl font-bold text-gray-800"><?php echo e($stats['nb_users']); ?></p>
                    <p class="text-sm text-gray-500 mt-1">Utilisateurs</p>
                    <p class="text-xs text-gray-400 mt-1">
                        <?php echo e($stats['nb_proprietaires']); ?> proprio · <?php echo e($stats['nb_locataires']); ?> locataires
                    </p>
                </div>

                <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5 text-center">
                    <p class="text-3xl font-bold text-gray-800"><?php echo e($stats['nb_biens']); ?></p>
                    <p class="text-sm text-gray-500 mt-1">Biens</p>
                    <p class="text-xs text-green-600 mt-1"><?php echo e($stats['nb_biens_loues']); ?> loués</p>
                </div>

                <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5 text-center">
                    <p class="text-3xl font-bold text-gray-800"><?php echo e($stats['nb_contrats']); ?></p>
                    <p class="text-sm text-gray-500 mt-1">Contrats actifs</p>
                </div>

                <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5 text-center">
                    <p class="text-2xl font-bold text-gray-800">
                        <?php echo e(number_format($stats['total_loyers'], 0, ',', ' ')); ?>

                    </p>
                    <p class="text-xs text-gray-400">FCFA</p>
                    <p class="text-sm text-gray-500 mt-1">Loyers encaissés</p>
                </div>

                <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5 text-center col-span-2 sm:col-span-2">
                    <p class="text-2xl font-bold text-blue-700">
                        <?php echo e(number_format($stats['total_commissions'], 0, ',', ' ')); ?>

                    </p>
                    <p class="text-xs text-gray-400">FCFA TTC</p>
                    <p class="text-sm text-gray-500 mt-1">Commissions générées</p>
                </div>

            </div>
        </div>

        
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden mb-8">

            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-800">
                    👥 Utilisateurs (<?php echo e($users->count()); ?>)
                </h2>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 text-gray-600 uppercase text-xs">
                        <tr>
                            <th class="px-6 py-3 text-left">Nom</th>
                            <th class="px-6 py-3 text-left">Email</th>
                            <th class="px-6 py-3 text-center">Rôle</th>
                            <th class="px-6 py-3 text-center">Téléphone</th>
                            <th class="px-6 py-3 text-center">Inscrit le</th>
                            <th class="px-6 py-3 text-center">Statut</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <?php $__empty_1 = true; $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-4 font-medium text-gray-900">
                                    <?php echo e($user->name); ?>

                                </td>
                                <td class="px-6 py-4 text-gray-600">
                                    <?php echo e($user->email); ?>

                                </td>
                                <td class="px-6 py-4 text-center">
                                    <?php
                                        $roleColors = [
                                            'admin'        => 'bg-purple-100 text-purple-800',
                                            'proprietaire' => 'bg-blue-100 text-blue-800',
                                            'locataire'    => 'bg-green-100 text-green-800',
                                        ];
                                        $roleLabels = [
                                            'admin'        => 'Admin',
                                            'proprietaire' => 'Propriétaire',
                                            'locataire'    => 'Locataire',
                                        ];
                                    ?>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?php echo e($roleColors[$user->role] ?? 'bg-gray-100 text-gray-800'); ?>">
                                        <?php echo e($roleLabels[$user->role] ?? $user->role); ?>

                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center text-gray-600">
                                    <?php echo e($user->telephone ?? '—'); ?>

                                </td>
                                <td class="px-6 py-4 text-center text-gray-500">
                                    <?php echo e($user->created_at->format('d/m/Y')); ?>

                                </td>
                                <td class="px-6 py-4 text-center">
                                    <?php if($user->deleted_at): ?>
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-700">
                                            Supprimé
                                        </span>
                                    <?php else: ?>
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-700">
                                            Actif
                                        </span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="6" class="px-6 py-8 text-center text-gray-400">
                                    Aucun utilisateur dans cette agence.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">

            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-800">
                    🏠 Biens (<?php echo e($biens->count()); ?>)
                </h2>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 text-gray-600 uppercase text-xs">
                        <tr>
                            <th class="px-6 py-3 text-left">Référence</th>
                            <th class="px-6 py-3 text-left">Type</th>
                            <th class="px-6 py-3 text-left">Adresse</th>
                            <th class="px-6 py-3 text-center">Propriétaire</th>
                            <th class="px-6 py-3 text-right">Loyer</th>
                            <th class="px-6 py-3 text-center">Statut</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <?php $__empty_1 = true; $__currentLoopData = $biens; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $bien): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-4 font-mono text-xs text-gray-600">
                                    <?php echo e($bien->reference); ?>

                                </td>
                                <td class="px-6 py-4 text-gray-800">
                                    <?php echo e($bien->type); ?>

                                </td>
                                <td class="px-6 py-4 text-gray-600">
                                    <?php echo e($bien->adresse); ?>, <?php echo e($bien->ville); ?>

                                </td>
                                <td class="px-6 py-4 text-center text-gray-600">
                                    <?php echo e($bien->proprietaire?->name ?? '—'); ?>

                                </td>
                                <td class="px-6 py-4 text-right font-medium text-gray-800">
                                    <?php echo e(number_format($bien->loyer_mensuel, 0, ',', ' ')); ?> F
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <?php
                                        $statutColors = [
                                            'loue'        => 'bg-green-100 text-green-800',
                                            'disponible'  => 'bg-blue-100 text-blue-800',
                                            'en_travaux'  => 'bg-yellow-100 text-yellow-800',
                                        ];
                                        $statutLabels = [
                                            'loue'        => 'Loué',
                                            'disponible'  => 'Disponible',
                                            'en_travaux'  => 'En travaux',
                                        ];
                                    ?>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?php echo e($statutColors[$bien->statut] ?? 'bg-gray-100 text-gray-800'); ?>">
                                        <?php echo e($statutLabels[$bien->statut] ?? $bien->statut); ?>

                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="6" class="px-6 py-8 text-center text-gray-400">
                                    Aucun bien dans cette agence.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </main>

</body>
</html>
<?php /**PATH C:\Users\ph\bimotech-immo\resources\views/superadmin/agency-detail.blade.php ENDPATH**/ ?>