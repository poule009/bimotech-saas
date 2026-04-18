<?php

return [
    App\Providers\AppServiceProvider::class,
    App\Providers\AuthServiceProvider::class,
    // Telescope est en require-dev — chargé uniquement si le package est installé
    ...(class_exists(\Laravel\Telescope\TelescopeApplicationServiceProvider::class)
        ? [App\Providers\TelescopeServiceProvider::class]
        : []),
];
