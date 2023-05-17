<?php

namespace Fleetbase\Storefront\Http\Resources;

use Fleetbase\Http\Resources\FleetbaseResource;

class Category extends FleetbaseResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->public_id ?? $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'icon_url' => $this->icon_url,
            'parent' => $this->whenLoaded(
                'parentCategory',
                function ($parentCategory) {
                    return $parentCategory->public_id;
                }
            ),
            'tags' => $this->tags ?? [],
            'translations' => $this->translations ?? [],
            'products' => Product::collection($this->whenLoaded('products')),
            'subcategories' => $this->when(
                $request->has('with_subcategories'),
                array_map(
                    function ($subCategory) {
                        return new Category($subCategory);
                    },
                    $this->subCategories->toArray()
                )
            ),
            'slug' => $this->slug,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
