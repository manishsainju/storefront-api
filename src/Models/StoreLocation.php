<?php

namespace Fleetbase\Storefront\Models\Storefront;

use Fleetbase\Models\BaseModel;
use Fleetbase\Models\Place;
use Fleetbase\Models\User;
use Fleetbase\Traits\HasUuid;
use Fleetbase\Traits\HasApiModelBehavior;
use Fleetbase\Traits\HasPublicid;
use Grimzy\LaravelMysqlSpatial\Eloquent\SpatialTrait;

class StoreLocation extends BaseModel
{
    use HasUuid, HasPublicid, HasApiModelBehavior, SpatialTrait;

    /**
     * The type of public Id to generate
     *
     * @var string
     */
    protected $publicIdType = 'store_location';

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
    protected $table = 'store_locations';

    /**
     * These attributes that can be queried
     *
     * @var array
     */
    protected $searchableColumns = [];

    /**
     * The attributes that are spatial columns.
     *
     * @var array
     */
    protected $spatialFields = ['location'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'public_id',
        'store_uuid',
        'created_by_uuid',
        'place_uuid',
        'name'
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
    protected $appends = ['address'];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = ['place'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function createdBy()
    {
        return $this->setConnection('mysql')->belongsTo(User::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function place()
    {
        // ->withTrashed(); undefined
        return $this->setConnection('mysql')->belongsTo(Place::class, 'place_uuid')->where(function ($q) {
            $q->whereNull('deleted_at')->orWhereNotNull('deleted_at');
        });
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function hours()
    {
        return $this->hasMany(StoreHour::class);
    }

    /**
     * Get address for places
     * @return string
     */
    public function getAddressAttribute()
    {
        return static::attributeFromCache($this, 'place.address');
    }

    /**
     * @return \Grimzy\LaravelMysqlSpatial\Types\Point
     */
    public function getLocationAttribute()
    {
        return $this->place()->first()->location;
    }
}
