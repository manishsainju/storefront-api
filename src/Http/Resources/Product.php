<?php

namespace Fleetbase\Storefront\Http\Resources;

use Fleetbase\Http\Resources\FleetbaseResource;
use Illuminate\Support\Str;

class Product extends FleetbaseResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->public_id ?? null,
            'name' => $this->name,
            'description' => $this->description,
            'sku' => $this->sku,
            'primary_image_url' => $this->primary_image_url,
            'price' => $this->price,
            'sale_price' => $this->sale_price,
            'currency' => $this->currency,
            'is_on_sale' => $this->is_on_sale,
            'is_recommended' => $this->is_recommended,
            'is_service' => $this->is_service,
            'is_bookable' => $this->is_bookable,
            'is_available' => $this->is_available,
            'status' => $this->status,
            'slug' => $this->slug,
            'translations' => $this->translations ?? [],
            'addon_categories' => $this->mapAddonCategories($this->addonCategories),
            'variants' => $this->mapVariants($this->variants),
            'images' => $this->mapFiles($this->files),
            'videos' => $this->mapFiles($this->files, 'video'),
            'hours' => $this->mapHours($this->hours),
            'youtube_urls' => $this->youtube_urls ?? [],
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }

    public function mapHours($hours = [])
    {
        return collect($hours)->map(function ($hour) {
            return [
                'day' => $hour->day_of_week,
                'start' => $hour->start,
                'end' => $hour->end,
            ];
        });
    }

    public function mapFiles($files = [], $contentType = 'image')
    {
        return collect($files)->map(function ($file) use ($contentType) {
            if (!Str::contains($file->content_type, $contentType)) {
                return null;
            }

            return $file->s3url;
        })->filter()->values();
    }

    public function mapAddonCategories($addonCategories = [])
    {
        return collect($addonCategories)->map(function ($addonCategory) {
            return [
                'id' => $addonCategory->category->public_id ?? null,
                'name' => $addonCategory->name,
                'description' => $addonCategory->category->description ?? null,
                'addons' => $this->mapProductAddons($addonCategory->category->addons ?? [], $addonCategory->excluded_addons)
            ];
        });
    }

    public function mapProductAddons($addons = [], $excluded = [])
    {
        return collect($addons)->map(function ($addon) use ($excluded) {
            if (is_array($excluded) && in_array($addon->uuid, $excluded)) {
                return null;
            }

            return [
                'id' => $addon->public_id,
                'name' => $addon->name,
                'description' => $addon->description,
                'price' => $addon->price,
                'sale_price' => $addon->sale_price,
                'is_on_sale' => $addon->is_on_sale,
                'slug' => $addon->slug,
                'created_at' => $addon->created_at,
                'updated_at' => $addon->updated_at
            ];
        })->filter()->values();
    }

    public function mapVariants($variants = [])
    {
        return collect($variants)->map(function ($variant) {
            return [
                'id' => $variant->public_id,
                'name' => $variant->name,
                'description' => $variant->description,
                'is_multiselect' => $variant->is_multiselect,
                'is_required' => $variant->is_required,
                'slug' => $variant->slug,
                'options' => collect($variant->options)->map(function ($variantOpt) {
                    return [
                        'id' => $variantOpt->public_id,
                        'name' => $variantOpt->name,
                        'description' => $variantOpt->description,
                        'additional_cost' => $variantOpt->additional_cost,
                        'created_at' => $variantOpt->created_at,
                        'updated_at' => $variantOpt->updated_at
                    ];
                }),
                'created_at' => $variant->created_at,
                'updated_at' => $variant->updated_at
            ];
        });
    }
}
