<?php

namespace Fleetbase\Http\Controllers\Storefront;

use Fleetbase\Http\Controllers\RESTController;

class ProductVariantOptionController extends RESTController
{
    /**
     * The resource to query
     *
     * @var string
     */
    public string $resource = 'product_variant_option';

    /**
     * The namespace for the resource
     *
     * @var string
     */
    public string $namespace = 'Fleetbase\\Models\\Storefront\\';
}
