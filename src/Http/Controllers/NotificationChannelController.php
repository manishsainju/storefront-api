<?php

namespace Fleetbase\Http\Controllers\Storefront;

use Fleetbase\Http\Controllers\RESTController;
use Fleetbase\Models\Storefront\NotificationChannel;
use Fleetbase\Support\Utils;
use Illuminate\Http\Request;

class NotificationChannelController extends RESTController
{
    /**
     * The resource to query
     *
     * @var string
     */
    public string $resource = 'notification_channel';

    /**
     * The namespace for the resource
     *
     * @var string
     */
    public string $namespace = 'Fleetbase\\Models\\Storefront\\';
}
