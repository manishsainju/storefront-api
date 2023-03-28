<?php

namespace Fleetbase\Http\Controllers\Storefront;

use Fleetbase\Http\Controllers\RESTController;

class ProductAddonController extends RESTController
{
    /**
     * The resource to query
     *
     * @var string
     */
    public string $resource = 'product_addons';

    /**
     * The namespace for the resource
     *
     * @var string
     */
    public string $namespace = 'Fleetbase\\Models\\Storefront\\';
}
