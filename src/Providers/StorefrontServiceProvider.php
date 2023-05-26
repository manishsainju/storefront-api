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
     * The observers registered with the service provider.
     *
     * @var array
     */
    public $observers = [
        \Fleetbase\Storefront\Models\Product::class => \Fleetbase\Storefront\Observers\ProductObserver::class,
        \Fleetbase\Storefront\Models\Network::class => \Fleetbase\Storefront\Observers\NetworkObserver::class,
    ];

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
        $this->registerObservers();
        $this->registerExpansionsFrom(__DIR__ . '/../Expansions');
        $this->loadRoutesFrom(__DIR__ . '/../routes.php');
        $this->loadMigrationsFrom(__DIR__ . '/../../migrations');
        $this->mergeConfigFrom(__DIR__ . '/../../config/database.connections.php', 'database.connections');
        $this->mergeConfigFrom(__DIR__ . '/../../config/storefront.php', 'storefront');
        $this->mergeConfigFrom(__DIR__ . '/../../config/api.php', 'storefront.api');
        $this->mergeConfigFrom(__DIR__ . '/../../config/twilio-notification-channel.php', 'twilio-notification-channel');
    }
}
