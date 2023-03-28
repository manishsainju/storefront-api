<?php

namespace Fleetbase\Http\Controllers\Storefront;

use Fleetbase\Http\Controllers\RESTController;

class VoteController extends RESTController
{
    /**
     * The resource to query
     *
     * @var string
     */
    public string $resource = 'votes';

    /**
     * The namespace for the resource
     *
     * @var string
     */
    public string $namespace = 'Fleetbase\\Models\\Storefront\\';
}
