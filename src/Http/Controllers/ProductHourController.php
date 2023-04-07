<?php

namespace Fleetbase\Storefront\Http\Controllers\Storefront;

use Fleetbase\Http\Controllers\RESTController;

class ProductHourController extends RESTController
{
    /**
     * The resource to query
     *
     * @var string
     */
    public string $resource = 'product_hours';

    /**
     * The namespace for the resource
     *
     * @var string
     */
    public string $namespace = 'Fleetbase\\Models\\Storefront\\';
}
