<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Abonnements — Super Admin</title>
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
                <h1 class="text-xl font-bold">💳 Gestion des abonnements</h1>
                <p class="text-gray-400 text-sm">Super Administration — BIMO-Tech</p>
            </div>
        </div>
        <form method="POST" action="<?php echo e(route('logout')); ?>">
            <?php echo csrf_field(); ?>
            <button class="text-sm bg-gray-700 hover:bg-gray-600 px-3 py-1 rounded transition">
                Déconnexion
            </button>
        </form>
    </header>

    <main class="max-w-7xl mx-auto px-4 py-8">

        
        <?php if(session('success')): ?>
            <div class="mb-6 bg-green-50 border border-green-200 text-green-800 rounded-lg px-4 py-3 text-sm">
                ✅ <?php echo e(session('success')); ?>

            </div>
        <?php endif; ?>

        
        <div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-8">

            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5 text-center">
                <p class="text-3xl font-bold text-blue-600"><?php echo e($stats['nb_essai']); ?></p>
                <p class="text-sm text-gray-500 mt-1">En essai</p>
            </div>

            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5 text-center">
                <p class="text-3xl font-bold text-green-600"><?php echo e($stats['nb_actifs']); ?></p>
                <p class="text-sm text-gray-500 mt-1">Abonnés actifs</p>
            </div>

            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5 text-center">
                <p class="text-3xl font-bold text-red-500"><?php echo e($stats['nb_expires']); ?></p>
                <p class="text-sm text-gray-500 mt-1">Expirés</p>
            </div>

            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5 text-center">
                <p class="text-xl font-bold text-gray-800">
                    <?php echo e(number_format($stats['revenus_total'], 0, ',', ' ')); ?>

                </p>
                <p class="text-xs text-gray-400">FCFA</p>
                <p class="text-sm text-gray-500 mt-1">Revenus encaissés</p>
            </div>

            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5 text-center">
                <p class="text-xl font-bold text-indigo-600">
                    <?php echo e(number_format($stats['revenus_mensuel_equiv'], 0, ',', ' ')); ?>

                </p>
                <p class="text-xs text-gray-400">FCFA</p>
                <p class="text-sm text-gray-500 mt-1">MRR estimé</p>
            </div>

        </div>

        
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">

            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-800">Toutes les agences</h2>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 text-gray-600 uppercase text-xs">
                        <tr>
                            <th class="px-6 py-3 text-left">Agence</th>
                            <th class="px-6 py-3 text-center">Statut</th>
                            <th class="px-6 py-3 text-center">Plan</th>
                            <th class="px-6 py-3 text-center">Début</th>
                            <th class="px-6 py-3 text-center">Expiration</th>
                            <th class="px-6 py-3 text-center">Jours restants</th>
                            <th class="px-6 py-3 text-right">Montant payé</th>
                            <th class="px-6 py-3 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <?php $__empty_1 = true; $__currentLoopData = $subscriptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sub): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr class="hover:bg-gray-50 transition">

                                
                                <td class="px-6 py-4">
                                    <p class="font-semibold text-gray-900"><?php echo e($sub->agency->name); ?></p>
                                    <p class="text-xs text-gray-400"><?php echo e($sub->agency->email); ?></p>
                                </td>

                                
                                <td class="px-6 py-4 text-center">
                                    <?php if($sub->estEnEssai()): ?>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            🕐 Essai
                                        </span>
                                    <?php elseif($sub->estActif()): ?>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            ✅ Actif
                                        </span>
                                    <?php elseif($sub->statut === 'expiré'): ?>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                            ❌ Expiré
                                        </span>
                                    <?php else: ?>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                            <?php echo e($sub->statut); ?>

                                        </span>
                                    <?php endif; ?>
                                </td>

                                
                                <td class="px-6 py-4 text-center text-gray-600">
                                    <?php echo e(\App\Models\Subscription::LABELS[$sub->plan] ?? '—'); ?>

                                </td>

                                
                                <td class="px-6 py-4 text-center text-gray-500">
                                    <?php if($sub->estEnEssai()): ?>
                                        <?php echo e($sub->date_debut_essai?->format('d/m/Y')); ?>

                                    <?php else: ?>
                                        <?php echo e($sub->date_debut_abonnement?->format('d/m/Y') ?? '—'); ?>

                                    <?php endif; ?>
                                </td>

                                
                                <td class="px-6 py-4 text-center text-gray-500">
                                    <?php if($sub->estEnEssai()): ?>
                                        <?php echo e($sub->date_fin_essai?->format('d/m/Y')); ?>

                                    <?php elseif($sub->estActif()): ?>
                                        <?php echo e($sub->date_fin_abonnement?->format('d/m/Y')); ?>

                                    <?php else: ?>
                                        —
                                    <?php endif; ?>
                                </td>

                                
                                <td class="px-6 py-4 text-center">
                                    <?php if($sub->estEnEssai()): ?>
                                        <?php $jours = $sub->joursRestantsEssai() ?>
                                        <span class="font-semibold <?php echo e($jours <= 7 ? 'text-amber-600' : 'text-blue-600'); ?>">
                                            <?php echo e($jours); ?>j
                                        </span>
                                    <?php elseif($sub->estActif()): ?>
                                        <?php $jours = $sub->joursRestantsAbonnement() ?>
                                        <span class="font-semibold <?php echo e($jours <= 7 ? 'text-amber-600' : 'text-green-600'); ?>">
                                            <?php echo e($jours); ?>j
                                        </span>
                                    <?php else: ?>
                                        <span class="text-gray-400">—</span>
                                    <?php endif; ?>
                                </td>

                                
                                <td class="px-6 py-4 text-right font-medium text-gray-800">
                                    <?php if($sub->montant_paye): ?>
                                        <?php echo e(number_format($sub->montant_paye, 0, ',', ' ')); ?> F
                                    <?php else: ?>
                                        <span class="text-gray-400">—</span>
                                    <?php endif; ?>
                                </td>

                                
                                <td class="px-6 py-4">
                                    <div class="flex items-center justify-center gap-2 flex-wrap">

                                        
                                        <div x-data="{ open: false }" class="relative">
                                            <button
                                                @click="open = !open"
                                                class="text-xs bg-green-100 hover:bg-green-200 text-green-800 px-3 py-1 rounded transition font-medium"
                                            >
                                                ✅ Activer
                                            </button>

                                            
                                            <div
                                                x-show="open"
                                                @click.outside="open = false"
                                                class="absolute right-0 mt-1 w-48 bg-white border border-gray-200 rounded-lg shadow-lg z-10"
                                            >
                                                <?php $__currentLoopData = \App\Models\Subscription::LABELS; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $plan => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <form
                                                        method="POST"
                                                        action="<?php echo e(route('superadmin.agencies.abonnement.activer', $sub->agency)); ?>"
                                                        onsubmit="return confirm('Activer le plan <?php echo e($label); ?> pour <?php echo e($sub->agency->name); ?> ?')"
                                                    >
                                                        <?php echo csrf_field(); ?>
                                                        <input type="hidden" name="plan" value="<?php echo e($plan); ?>">
                                                        <button
                                                            type="submit"
                                                            class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition"
                                                        >
                                                            <?php echo e($label); ?>

                                                            <span class="text-gray-400 text-xs">
                                                                — <?php echo e(number_format(\App\Models\Subscription::TARIFS[$plan], 0, ',', ' ')); ?> F
                                                            </span>
                                                        </button>
                                                    </form>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            </div>
                                        </div>

                                        
                                        <form
                                            method="POST"
                                            action="<?php echo e(route('superadmin.agencies.essai.reinitialiser', $sub->agency)); ?>"
                                            onsubmit="return confirm('Réinitialiser l\'essai de <?php echo e($sub->agency->name); ?> ?')"
                                        >
                                            <?php echo csrf_field(); ?>
                                            <button
                                                type="submit"
                                                class="text-xs bg-blue-100 hover:bg-blue-200 text-blue-800 px-3 py-1 rounded transition font-medium"
                                            >
                                                🔄 Essai
                                            </button>
                                        </form>

                                    </div>
                                </td>

                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="8" class="px-6 py-8 text-center text-gray-400">
                                    Aucun abonnement enregistré.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </main>

</body>
</html><?php /**PATH C:\Users\ph\bimotech-immo\resources\views/superadmin/subscriptions.blade.php ENDPATH**/ ?>