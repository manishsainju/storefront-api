<?php

namespace Fleetbase\Storefront\Models;

use Fleetbase\Models\Model;

class StorefrontModel extends Model
{
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        
        $this->connection = config('storefront.db.connection');
    }
}