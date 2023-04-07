<?php

namespace Fleetbase\Storefront\Http\Controllers\Storefront;

use Fleetbase\Http\Controllers\RESTController;

class ProductAddonCategoryController extends RESTController
{
    /**
     * The resource to query
     *
     * @var string
     */
    public string $resource = 'product_addon_category';

    /**
     * The namespace for the resource
     *
     * @var string
     */
    public string $namespace = 'Fleetbase\\Models\\Storefront\\';
}
