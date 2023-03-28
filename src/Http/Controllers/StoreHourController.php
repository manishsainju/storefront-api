<?php

namespace Fleetbase\Http\Controllers\Storefront;

use Fleetbase\Http\Controllers\RESTController;

class StoreHourController extends RESTController
{
    /**
     * The resource to query
     *
     * @var string
     */
    public string $resource = 'store_hours';

    /**
     * The namespace for the resource
     *
     * @var string
     */
    public string $namespace = 'Fleetbase\\Models\\Storefront\\';
}
