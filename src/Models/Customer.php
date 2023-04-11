<?php

namespace Fleetbase\Storefront\Models;

use Fleetbase\FleetOps\Models\Contact;

class Customer extends Contact
{
    /**
     * The singular name of this model.
     *
     * @var string
     */
    protected string $singularName = 'customer';

    /**
     * The plural name of this model.
     *
     * @var string
     */
    protected string $pluralName = 'customers';
}
