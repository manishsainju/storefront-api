<?php

namespace Fleetbase\Http\Resources\Storefront;

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
        $attrs = [
            'id' => $this->public_id ?? $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'icon_url' => $this->icon_url,
            'parent' => $this->parentCategory ? $this->parentCategory->public_id : null,
            'tags' => $this->tags ?? [],
            'translations' => $this->translations ?? []
        ];

        if ($this->products) {
            $attrs = array_merge($attrs, ['products' => $this->products]);
        }

        if ($request->has('with_subcategories')) {
            $attrs = array_merge($attrs, ['subcategories' => array_map(function ($subCategory) {
                return new Category($subCategory);
            }, $this->subCategories->toArray())]);
        }

        $attrs = array_merge($attrs, [
            'slug' => $this->slug,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);

        return $attrs;
    }
}
