<?php

namespace Fleetbase\Storefront\Models\Storefront;

use Fleetbase\Models\BaseModel;
use Fleetbase\Traits\HasUuid;
use Fleetbase\Traits\HasApiModelBehavior;

class StoreHour extends BaseModel
{
    use HasUuid, HasApiModelBehavior;

    /**
     * The database connection to use.
     *
     * @var string
     */
    protected $connection = 'storefront';

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'store_hours';

    /**
     * These attributes that can be queried
     *
     * @var array
     */
    protected $searchableColumns = [];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'store_location_uuid',
        'day_of_week',
        'start',
        'end'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [];

    /**
     * Dynamic attributes that are appended to object
     *
     * @var array
     */
    protected $appends = [];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function storeLocation()
    {
        return $this->belongsTo(StoreLocation::class);
    }
}
