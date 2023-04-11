<?php

namespace Fleetbase\Storefront\Models;

use Fleetbase\Models\Category;

class AddonCategory extends Category
{
    /**
     * Relationships to auto load with driver
     *
     * @var array
     */
    protected $with = ['addons'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function addons()
    {
        return $this->setConnection(config('fleetbase.connection.db'))->hasMany(ProductAddon::class, 'category_uuid');
    }
}
