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
     <?php $__env->slot('header', null, []); ?> 
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Paramètres de l'agence
        </h2>
     <?php $__env->endSlot(); ?>

    <div class="max-w-3xl mx-auto py-8 px-4">

        
        <?php if(session('success')): ?>
            <div class="mb-6 bg-green-50 border border-green-200 text-green-800 rounded-lg px-4 py-3 text-sm">
                <?php echo e(session('success')); ?>

            </div>
        <?php endif; ?>

        
        <?php if($errors->any()): ?>
            <div class="mb-6 bg-red-50 border border-red-200 rounded-lg p-4">
                <ul class="list-disc list-inside text-sm text-red-700 space-y-1">
                    <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li><?php echo e($error); ?></li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </ul>
            </div>
        <?php endif; ?>

        
        
        
        <?php if($agency->logo_path): ?>
            <form
                id="form-delete-logo"
                method="POST"
                action="<?php echo e(route('admin.agency.logo.delete')); ?>"
                onsubmit="return confirm('Supprimer le logo ?')"
            >
                <?php echo csrf_field(); ?>
                <?php echo method_field('DELETE'); ?>
            </form>
        <?php endif; ?>

        
        
        
        <form
            method="POST"
            action="<?php echo e(route('admin.agency.settings.update')); ?>"
            enctype="multipart/form-data"
        >
            <?php echo csrf_field(); ?>
            <?php echo method_field('PATCH'); ?>

            
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 mb-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-5">🖼️ Logo de l'agence</h2>

                <div class="flex items-center gap-6">

                    
                    <div class="flex-shrink-0">
                        <?php if($agency->logo_path): ?>
                            <img
                                src="<?php echo e(Storage::url($agency->logo_path)); ?>"
                                alt="Logo <?php echo e($agency->name); ?>"
                                class="h-20 w-20 object-contain rounded-lg border border-gray-200 p-1"
                            >
                        <?php else: ?>
                            <div class="h-20 w-20 rounded-lg border-2 border-dashed border-gray-300 flex items-center justify-center">
                                <span class="text-3xl">🏢</span>
                            </div>
                        <?php endif; ?>
                    </div>

                    
                    <div class="flex-1">
                        <label for="logo" class="block text-sm font-medium text-gray-700 mb-1">
                            Changer le logo
                        </label>
                        <input
                            type="file"
                            id="logo"
                            name="logo"
                            accept="image/png,image/jpeg,image/jpg,image/svg+xml,image/webp"
                            class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100"
                        >
                        <p class="mt-1 text-xs text-gray-400">PNG, JPG, SVG ou WEBP — max 2 Mo</p>

                        
                        <?php if($agency->logo_path): ?>
                            <button
                                type="submit"
                                form="form-delete-logo"
                                class="mt-2 text-xs text-red-500 hover:text-red-700"
                            >
                                Supprimer le logo actuel
                            </button>
                        <?php endif; ?>
                    </div>

                </div>
            </div>

            
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 mb-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-5">🎨 Couleur principale</h2>

                <div class="flex items-center gap-4">
                    <input
                        type="color"
                        id="couleur_primaire"
                        name="couleur_primaire"
                        value="<?php echo e($agency->couleur_primaire ?? '#1a3c5e'); ?>"
                        class="h-12 w-20 rounded-lg border border-gray-300 cursor-pointer p-1"
                    >
                    <div>
                        <label for="couleur_primaire" class="block text-sm font-medium text-gray-700">
                            Couleur de l'interface
                        </label>
                        <p class="text-xs text-gray-400 mt-0.5">
                            Utilisée pour la barre de navigation et les boutons principaux.
                        </p>
                    </div>
                </div>
            </div>

            
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 mb-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-5">📋 Informations de l'agence</h2>

                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">

                    
                    <div class="sm:col-span-2">
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-1">
                            Nom de l'agence <span class="text-red-500">*</span>
                        </label>
                        <input
                            type="text"
                            id="name"
                            name="name"
                            value="<?php echo e(old('name', $agency->name)); ?>"
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            required
                        >
                    </div>

                    
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">
                            Email <span class="text-red-500">*</span>
                        </label>
                        <input
                            type="email"
                            id="email"
                            name="email"
                            value="<?php echo e(old('email', $agency->email)); ?>"
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            required
                        >
                    </div>

                    
                    <div>
                        <label for="telephone" class="block text-sm font-medium text-gray-700 mb-1">
                            Téléphone
                        </label>
                        <input
                            type="text"
                            id="telephone"
                            name="telephone"
                            value="<?php echo e(old('telephone', $agency->telephone)); ?>"
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        >
                    </div>

                    
                    <div class="sm:col-span-2">
                        <label for="adresse" class="block text-sm font-medium text-gray-700 mb-1">
                            Adresse
                        </label>
                        <input
                            type="text"
                            id="adresse"
                            name="adresse"
                            value="<?php echo e(old('adresse', $agency->adresse)); ?>"
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        >
                    </div>

                    
                    <div>
                        <label for="ninea" class="block text-sm font-medium text-gray-700 mb-1">
                            NINEA
                            <span class="ml-1 text-xs text-gray-400 font-normal">(Numéro d'identification fiscal)</span>
                        </label>
                        <input
                            type="text"
                            id="ninea"
                            name="ninea"
                            value="<?php echo e(old('ninea', $agency->ninea)); ?>"
                            placeholder="ex: 00123456789"
                            maxlength="30"
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        >
                        <?php if(! $agency->ninea): ?>
                            <p class="mt-1 text-xs text-amber-600">
                                ⚠️ Le NINEA est requis pour compléter la configuration de votre agence.
                            </p>
                        <?php endif; ?>
                    </div>

                </div>
            </div>

            
            <div class="flex justify-end">
                <button
                    type="submit"
                    class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-6 py-3 rounded-lg shadow-sm transition"
                >
                    Sauvegarder les paramètres
                </button>
            </div>

        </form>

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
<?php endif; ?><?php /**PATH C:\Users\ph\bimotech-immo\resources\views/admin/agency-settings.blade.php ENDPATH**/ ?>