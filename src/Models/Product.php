<?php

namespace Fleetbase\Storefront\Models\Storefront;

use Fleetbase\Casts\Json;
use Fleetbase\Models\BaseModel;
use Fleetbase\Models\Category;
use Fleetbase\Models\File;
use Fleetbase\Models\User;
use Fleetbase\Support\Utils;
use Fleetbase\Traits\HasMetaAttributes;
use Fleetbase\Traits\HasUuid;
use Fleetbase\Traits\HasApiModelBehavior;
use Fleetbase\Traits\HasPublicid;
use Fleetbase\Traits\Searchable;
use Illuminate\Support\Str;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;
use Milon\Barcode\Facades\DNS2DFacade as DNS2D;
// use GeneaLabs\LaravelModelCaching\Traits\Cachable;

class ProductStatus
{
    public const AVAILABLE = 'available';
    public const DRAFT = 'draft';
}

class Product extends BaseModel
{
    use HasUuid, HasPublicid, HasApiModelBehavior, HasMetaAttributes, HasSlug, Searchable;

    /**
     * The type of public Id to generate
     *
     * @var string
     */
    protected $publicIdType = 'product';

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
    protected $table = 'products';

    /**
     * The cache prefix.
     *
     * @var string
     */
    protected $cachePrefix = 'storefront-products';

    /**
     * The cache cool down period.
     *
     * @var string
     */
    protected $cacheCooldownSeconds = 600; // 10 minutes

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
        'company_uuid',
        'primary_image_uuid',
        'created_by_uuid',
        'store_uuid',
        'category_uuid',
        'name',
        'description',
        'tags',
        'translations',
        'meta',
        'qr_code',
        'barcode',
        'youtube_urls',
        'sku',
        'price',
        'currency',
        'sale_price',
        'is_service',
        'is_bookable',
        'is_available',
        'is_on_sale',
        'is_recommended',
        'can_pickup',
        'status',
        'slug'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'is_service' => 'boolean',
        'is_bookable' => 'boolean',
        'is_on_sale' => 'boolean',
        'is_available' => 'boolean',
        'is_recommended' => 'boolean',
        'can_pickup' => 'boolean',
        'tags' => 'array',
        'meta' => Json::class,
        'translations' => Json::class,
        'youtube_urls' => Json::class
    ];

    /**
     * Dynamic attributes that are appended to object
     *
     * @var array
     */
    protected $appends = ['primary_image_url', 'store_id', 'meta_array'];

    /**
     * Attributes that is filterable on this model
     *
     * @var array
     */
    protected $filterParams = ['category_slug', 'category'];

    /**
     * Generates QR Code & Barcode on creation.
     *
     * @return void
     */
    public static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            $model->qr_code = DNS2D::getBarcodePNG($model->uuid, 'QRCODE');
            $model->barcode = DNS2D::getBarcodePNG($model->uuid, 'PDF417');
        });
    }

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
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function addonCategories()
    {
        return $this->setConnection('mysql')->hasMany(ProductAddonCategory::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function variants()
    {
        return $this->setConnection('mysql')->hasMany(ProductVariant::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function primaryImage()
    {
        return $this->setConnection('mysql')->belongsTo(File::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function files()
    {
        return $this->setConnection('mysql')->hasMany(File::class, 'key_uuid');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function reviews()
    {
        return $this->hasMany(Review::class, 'subject_uuid');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function votes()
    {
        return $this->hasMany(Vote::class, 'subject_uuid');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function hours()
    {
        return $this->hasMany(ProductHour::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    /**
     * @return array
     */
    public function getMetaArrayAttribute()
    {
        $_meta = [];

        if (empty($this->meta)) {
            return $_meta;
        }

        foreach ($this->meta as $key => $value) {
            $_meta[] = [
                'key' => Str::snake($key),
                'label' => Utils::smartHumanize($key),
                'value' => $value
            ];
        }

        return $_meta;
    }

    /**
     * @return string
     */
    public function getPrimaryImageUrlAttribute()
    {
        $default = $this->primaryImage->s3url ?? null; //static::attributeFromCache($this, 'primaryImage.s3url', null);
        $secondary = $this->files->first()->s3url ?? null;
        $backup = 'https://flb-assets.s3.ap-southeast-1.amazonaws.com/static/image-file-icon.png';

        return $default ?? $secondary ?? $backup;
    }

    /**
     * @return string
     */
    public function getStoreIdAttribute()
    {
        return static::attributeFromCache($this, 'store.public_id', function () {
            return $this->store()->select(['public_id'])->first()->public_id;
        });
    }

    /**
     * Set the price as only numbers
     *
     * @void
     */
    public function setPriceAttribute($value)
    {
        $this->attributes['price'] = Utils::numbersOnly($value);
    }

    /**
     * Set the sale price as only numbers
     *
     * @void
     */
    public function setSalePriceAttribute($value)
    {
        $this->attributes['sale_price'] = Utils::numbersOnly($value);
    }

    public static function findFromNetwork($search, $store = null, $limit = 20, $network = null)
    {
        $network = $network ?? session('storefront_network');

        $results = static::whereHas('store', function ($query) use ($network, $store) {
            if ($store) {
                $query->where('public_id', $store);
            }

            $query->whereHas('networks', function ($networksQuery) use ($network) {
                $networksQuery->where('network_uuid', $network);
            });
        })
            ->search($search)
            ->whereIsAvailable(1)
            ->whereStatus('published')
            ->limit($limit)
            ->get();

        return $results;
    }
}
