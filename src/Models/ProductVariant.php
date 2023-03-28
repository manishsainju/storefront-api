<?php

namespace Fleetbase\Models\Storefront;

use Fleetbase\Casts\Json;
use Fleetbase\Models\BaseModel;
use Fleetbase\Models\Category;
use Fleetbase\Models\User;
use Fleetbase\Traits\HasMetaAttributes;
use Fleetbase\Traits\HasUuid;
use Fleetbase\Traits\HasApiModelBehavior;
use Fleetbase\Traits\HasPublicid;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class ProductVariant extends BaseModel
{
    use HasUuid, HasPublicid, HasApiModelBehavior, HasMetaAttributes, HasSlug;

    /**
     * The type of public Id to generate
     *
     * @var string
     */
    protected $publicIdType = 'variant';

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
    protected $table = 'product_variants';

    /**
     * These attributes that can be queried
     *
     * @var array
     */
    protected $searchableColumns = ['name', 'description'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'public_id',
        'product_uuid',
        'name',
        'description',
        'translations',
        'meta',
        'is_multiselect',
        'is_required',
        'min',
        'max'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'is_multiselect' => 'boolean',
        'is_required' => 'boolean',
        'meta' => Json::class,
        'translations' => Json::class
    ];

    /**
     * Dynamic attributes that are appended to object
     *
     * @var array
     */
    protected $appends = [];

    /**
     * Relationships to auto load with driver
     *
     * @var array
     */
    protected $with = [];

    /**
     * @var \Spatie\Sluggable\SlugOptions
     */
    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('name')
            ->saveSlugsTo('slug');
    }

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
    public function category()
    {
        return $this->setConnection('mysql')->belongsTo(Category::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function options()
    {
        return $this->hasMany(ProductVariantOption::class);
    }
}
