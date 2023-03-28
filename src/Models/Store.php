<?php

namespace Fleetbase\Models\Storefront;

use Fleetbase\Casts\Json;
use Fleetbase\Models\BaseModel;
use Fleetbase\Models\Category;
use Fleetbase\Models\User;
use Fleetbase\Models\Company;
use Fleetbase\Models\File;
use Fleetbase\Models\Place;
use Fleetbase\Support\Utils;
use Fleetbase\Traits\HasMetaAttributes;
use Fleetbase\Traits\HasOptionsAttributes;
use Fleetbase\Traits\HasUuid;
use Fleetbase\Traits\HasApiModelBehavior;
use Fleetbase\Traits\HasPublicid;
use Fleetbase\Traits\Searchable;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class Store extends BaseModel
{
    use HasUuid, HasPublicid, HasApiModelBehavior, HasOptionsAttributes, HasMetaAttributes, HasSlug, Searchable;

    /**
     * The type of public Id to generate
     *
     * @var string
     */
    protected $publicIdType = 'store';

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
    protected $table = 'stores';

    /**
     * These attributes that can be queried
     *
     * @var array
     */
    protected $searchableColumns = ['name', 'description', 'tags'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['created_by_uuid', 'company_uuid', 'logo_uuid', 'backdrop_uuid', 'key', 'online', 'name', 'description', 'translations', 'website', 'facebook', 'instagram', 'twitter', 'email', 'phone', 'tags', 'currency', 'meta', 'timezone', 'pod_method', 'options', 'alertable', 'slug'];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'options' => Json::class,
        'meta' => Json::class,
        'translations' => Json::class,
        'alertable' => Json::class,
        'tags' => 'array',
        'require_account' => 'boolean',
        'online' => 'boolean'
    ];

    /**
     * Dynamic attributes that are appended to object
     *
     * @var array
     */
    protected $appends = ['logo_url', 'backdrop_url', 'rating'];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = ['logo', 'backdrop', 'files'];

    /**
     * Attributes that is filterable on this model
     *
     * @var array
     */
    protected $filterParams = ['network', 'without_category', 'category'];

    /**
     * @var \Spatie\Sluggable\SlugOptions
     */
    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('name')
            ->saveSlugsTo('slug');
    }

    /** on boot generate key */
    public static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            $model->key = 'store_' . md5(Str::random(14) . time());
        });
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
    public function company()
    {
        return $this->setConnection('mysql')->belongsTo(Company::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function logo()
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
    public function media()
    {
        return $this->files()->where('type', 'storefront_store_media');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function backdrop()
    {
        return $this->setConnection('mysql')->belongsTo(File::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function categories()
    {
        return $this->setConnection('mysql')->hasMany(Category::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function products()
    {
        return $this->hasMany(Product::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function checkouts()
    {
        return $this->hasMany(Checkout::class);
    }

    /**
     * @return int
     */
    public function getThisMonthCheckoutsCountAttribute()
    {
        return $this->checkouts()->where('created_at', '>=', Carbon::now()->subMonth())->count();
    }

    /**
     * @return int
     */
    public function get24hCheckoutsCountAttribute()
    {
        return $this->checkouts()->where('created_at', '>=', Carbon::now()->subHours(24))->count();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function hours()
    {
        return $this->hasMany(StoreHour::class);
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
    public function notificationChannels()
    {
        return $this->hasMany(NotificationChannel::class, 'owner_uuid');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function paymentGateways()
    {
        return $this->hasMany(Gateway::class, 'owner_uuid');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function locations()
    {
        return $this->hasMany(StoreLocation::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function networkStores()
    {
        return $this->hasMany(NetworkStore::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function networks()
    {
        return $this->hasManyThrough(Network::class, NetworkStore::class, 'store_uuid', 'uuid', 'uuid', 'network_uuid');
    }

    /**
     * @var string
     */
    public function getLogoUrlAttribute()
    {
        // return static::attributeFromCache($this, 'logo.s3url', 'https://flb-assets.s3.ap-southeast-1.amazonaws.com/static/image-file-icon.png');
        return $this->logo->s3url ?? 'https://flb-assets.s3.ap-southeast-1.amazonaws.com/static/image-file-icon.png';
    }

    /**
     * @var string
     */
    public function getBackdropUrlAttribute()
    {
        // return static::attributeFromCache($this, 'backdrop.s3url', 'https://flb-assets.s3.ap-southeast-1.amazonaws.com/static/default-storefront-backdrop.png');
        return $this->backdrop->s3url ?? 'https://flb-assets.s3.ap-southeast-1.amazonaws.com/static/default-storefront-backdrop.png';
    }

    /**
     * @var float
     */
    public function getRatingAttribute()
    {
        return $this->reviews()->avg('rating') ?? 0;
    }

    /**
     *'Create a new product category for this store record
     *
     * @param string $name
     * @param string $description
     * @param array|null $meta
     * @param array|null $translations
     * @param Category|null $parent
     * @param File|string|null $icon
     * @param string $iconColor
     * @return Category
     */
    public function createCategory(string $name, string $description = '', ?array $meta = [],  ?array $translations = [], ?Category $parent = null, $icon = null, $iconColor = '#000000'): Category
    {
        $iconFile = null;
        $iconName = null;

        if ($icon instanceof File) {
            $iconFile = $icon;
        }

        if (is_string($icon)) {
            $iconName = $icon;
        }

        return Category::create([
            'company_uuid' => $this->company_uuid,
            'owner_uuid' => $this->uuid,
            'owner_type' => Utils::getMutationType('store:storefront'),
            'parent_uuid' => $parent instanceof Category ? $parent->uuid : null,
            'icon_file_uuid' => $iconFile instanceof File ? $iconFile->uuid : null,
            'for' => 'storefront_product',
            'name' => $name,
            'description' => $description,
            'translations' => $translations,
            'meta' => $meta,
            'icon' => $iconName,
            'icon_color' => $iconColor
        ]);
    }

    /**
     * Create a new product category if it doesn't already exists for this store record
     *
     * @param string $name
     * @param string $description
     * @param array|null $meta
     * @param array|null $translations
     * @param Category|null $parent
     * @param File|string|null $icon
     * @param string $iconColor
     * @return Category
     */
    public function createCategoryStrict(string $name, string $description = '', ?array $meta = [],  ?array $translations = [], ?Category $parent = null, $icon = null, $iconColor = '#000000'): Category
    {
        $existingCategory = Category::where(['company_uuid' => $this->company_uuid, 'owner_uuid' => $this->uuid, 'name' => $name])->first();

        if ($existingCategory) {
            return $existingCategory;
        }

        return $this->createCategory($name, $description, $meta, $translations, $parent, $icon, $iconColor);
    }

    /**
     * Creates a new product in the store.
     *
     * @param string $name
     * @param string $description
     * @param array $tags
     * @param Category|null $category
     * @param File|null $image
     * @param User|null $createdBy
     * @param string $sku
     * @param integer $price
     * @param string $status
     * @param array $options
     * @return Product
     */
    public function createProduct(string $name, string $description, array $tags = [], ?Category $category = null, ?File $image = null, ?User $createdBy = null, string $sku = '', int $price = 0, string $status = 'available', array $options = []): Product
    {
        return Product::create([
            'company_uuid' => $this->company_uuid,
            'primary_image_uuid' => $image instanceof File ? $image->uuid : null,
            'created_by_uuid' => $createdBy instanceof User ? $createdBy->uuid : null,
            'store_uuid' => $this->uuid,
            'category_uuid' => $category instanceof Category ? $category->uuid : null,
            'name' => $name,
            'description' => $description,
            'tags' => $tags,
            'sku' => $sku,
            'price' => $price,
            'sale_price' => isset($options['sale_price']) ? $options['sale_price'] : null,
            'currency' => $this->currency,
            'is_service' => isset($options['is_service']) ? $options['is_service'] : false,
            'is_bookable' => isset($options['is_bookable']) ? $options['is_bookable'] : false,
            'is_available' => isset($options['is_available']) ? $options['is_available'] : true,
            'is_on_sale' => isset($options['is_on_sale']) ? $options['is_on_sale'] : false,
            'is_recommended' => isset($options['is_recommended']) ? $options['is_recommended'] : false,
            'can_pickup' => isset($options['can_pickup']) ? $options['can_pickup'] : false,
            'status' => $status
        ]);
    }

    public function createLocation($location, string $name = null, ?User $createdBy): ?StoreLocation
    {
        $place = Place::createFromMixed($location);

        if (empty($name)) {
            $name = $this->name . ' store location';
        }

        if ($place instanceof Place) {
            return StoreLocation::create([
                'store_uuid' => $this->uuid,
                'created_by_uuid' => $createdBy instanceof User ? $createdBy->uuid : null,
                'place_uuid' => $place->uuid,
                'name' => $name
            ]);
        }

        return null;
    }
}
