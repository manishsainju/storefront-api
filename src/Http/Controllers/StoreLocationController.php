<?php

namespace Fleetbase\Storefront\Http\Controllers\Storefront;

use Fleetbase\Http\Controllers\RESTController;

class StoreLocationController extends RESTController
{
    /**
     * The resource to query
     *
     * @var string
     */
    public string $resource = 'store_locations';

    /**
     * The namespace for the resource
     *
     * @var string
     */
    public string $namespace = 'Fleetbase\\Models\\Storefront\\';
}
