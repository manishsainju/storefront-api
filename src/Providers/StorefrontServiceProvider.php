<?php

namespace Fleetbase\Providers;

use Exception;

// require __DIR__ . '/../../vendor/autoload.php';

if (!class_exists(CoreServiceProvider::class)) {
    throw new Exception('Storefront cannot be loaded without `fleetbase/core-api` installed!');
}

// if (!class_exists(FleetOpsServiceProvider::class)) {
//     throw new Exception('Storefront cannot be loaded without `fleetbase/storefront-api` installed!');
// }

/**
 * CoreServiceProvider
 */
class StorefrontServiceProvider extends CoreServiceProvider
{
    /**
     * Bootstrap any package services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerExpansionsFrom(__DIR__ . '/../Expansions');
        $this->loadRoutesFrom(__DIR__ . '/../routes.php');
        $this->mergeConfigFrom(__DIR__ . '/../../config/storefront.php', 'storefront');
    }
}
