<?php

namespace Fleetbase\Storefront\Models;

use Fleetbase\FleetOps\Models\Order as FleetOpsOrder;

class Order extends FleetOpsOrder
{
    /**
     * The key to use in the payload responses
     *
     * @var string
     */
    protected string $payloadKey = 'storefront_order';
}
