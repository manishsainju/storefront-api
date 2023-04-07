<?php

namespace Fleetbase\Storefront\Http\Controllers\Storefront;

use Fleetbase\Http\Controllers\RESTController;

class ReviewController extends RESTController
{
    /**
     * The resource to query
     *
     * @var string
     */
    public string $resource = 'reviews';

    /**
     * The namespace for the resource
     *
     * @var string
     */
    public string $namespace = 'Fleetbase\\Models\\Storefront\\';
}
