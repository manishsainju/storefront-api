<?php

namespace Fleetbase\Storefront\Models\Storefront;

use Fleetbase\Models\BaseModel;
use Fleetbase\Traits\HasUuid;
use Fleetbase\Traits\HasApiModelBehavior;
use Fleetbase\Traits\HasPublicid;

class Vote extends BaseModel
{
    use HasUuid, HasPublicid, HasApiModelBehavior;

    /**
     * The type of public Id to generate
     *
     * @var string
     */
    protected $publicIdType = 'vote';

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
    protected $table = 'votes';

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
        'created_by_uuid',
        'customer_uuid',
        'subject_uuid',
        'subject_type',
        'type'
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
    public function createdBy()
    {
        return $this->setConnection('mysql')->belongsTo(User::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function customer()
    {
        return $this->setConnection('mysql')->belongsTo(Contact::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function subject()
    {
        return $this->morphTo(__FUNCTION__, 'subject_type', 'subject_uuid')->withoutGlobalScopes();
    }
}
