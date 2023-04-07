<?php

namespace Fleetbase\Storefront\Providers;

use Fleetbase\Providers\CoreServiceProvider;
use Fleetbase\FleetOps\Providers\FleetOpsServiceProvider;

if (!class_exists(CoreServiceProvider::class)) {
    throw new \Exception('Storefront cannot be loaded without `fleetbase/core-api` installed!');
}

if (!class_exists(FleetOpsServiceProvider::class)) {
    throw new \Exception('Storefront cannot be loaded without `fleetbase/fleetops-api` installed!');
}

/**
 * Storefront service provider.
 *
 * @package \Fleetbase\Storefront\Providers
 */
class StorefrontServiceProvider extends CoreServiceProvider
{
    /**
     * Bootstrap any package services.
     *
     * @return void
     *
     * @throws \Exception If the `fleetbase/core-api` package is not installed.
     * @throws \Exception If the `fleetbase/fleetops-api` package is not installed.
     */
    public function boot()
    {
        $this->registerExpansionsFrom(__DIR__ . '/../Expansions');
        $this->loadRoutesFrom(__DIR__ . '/../routes.php');
        $this->mergeConfigFrom(__DIR__ . '/../../config/storefront.php', 'storefront');
    }
}
