<?php

namespace Fleetbase\Storefront\Http\Controllers\Storefront;

use Fleetbase\Http\Controllers\RESTController;

class AddonCategoryController extends RESTController
{
    /**
     * The resource to query
     *
     * @var string
     */
    public string $resource = 'addon_category';

    /**
     * The namespace for the resource
     *
     * @var string
     */
    public string $namespace = 'Fleetbase\\Models\\Storefront\\';
}
