<?php

namespace Fleetbase\Models\Storefront;

use Fleetbase\Models\BaseModel;
use Fleetbase\Models\Category;
use Fleetbase\Traits\HasUuid;
use Fleetbase\Traits\HasApiModelBehavior;

class NetworkStore extends BaseModel
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
    protected $table = 'network_stores';

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
    protected $fillable = ['network_uuid', 'store_uuid', 'category_uuid'];

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
    public function network()
    {
        return $this->belongsTo(Network::class);
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
    public function category()
    {
        return $this->setConnection('mysql')->belongsTo(Category::class);
    }
}
