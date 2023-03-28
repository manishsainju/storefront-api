<?php

namespace Fleetbase\Http\Resources\Storefront;

use Fleetbase\Http\Resources\FleetbaseResource;

class Network extends FleetbaseResource
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
            'id' => $this->public_id,
            'name' => $this->name,
            'description' => $this->description,
            'translations' => $this->translations ?? [],
            'website' => $this->website,
            'facebook' => $this->facebook,
            'instagram' => $this->instagram,
            'twitter' => $this->twitter,
            'email' => $this->email,
            'phone' => $this->phone,
            'tags' => $this->tags ?? [],
            'currency' => $this->currency ?? 'USD',
            'options' => $this->options ?? [],
            'logo_url' => $this->logo_url,
            'backdrop_url' => $this->backdrop_url,
            'rating' => $this->rating,
            'online' => $this->online,
            'is_network' => true,
            'is_store' => false,
            'slug' => $this->slug,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
