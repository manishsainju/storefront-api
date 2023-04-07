<?php

namespace Fleetbase\Storefront\Http\Resources\Storefront;

use Fleetbase\Http\Resources\FleetbaseResource;

class Store extends FleetbaseResource
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
            'options' => $this->formatOptions($this->options),
            'logo_url' => $this->logo_url,
            'backdrop_url' => $this->backdrop_url,
            'rating' => $this->rating,
            'online' => $this->online,
            'is_network' => false,
            'is_store' => true,
            'media' => $this->mapMedia($this->media),
            'locations' => $this->locations->mapInto(StoreLocation::class),
            'slug' => $this->slug,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
    }

    public function mapMedia($medias = [])
    {
        return collect($medias)->map(function ($media) {
            return [
                'id' => $media->public_id,
                'filename' => $media->original_filename,
                'type' => $media->content_type,
                'caption' => $media->caption,
                'url' => $media->s3url
            ];
        });
    }

    public function formatOptions($options = [])
    {
        if (!is_array($options)) {
            return [];
        }

        $formattedOptions = [];
        $exclude = ['alerted_for_new_order'];

        foreach ($options as $key => $value) {
            if (in_array($key, $exclude)) {
                continue;
            }

            $formattedOptions[$key] = $value;
        }

        return $formattedOptions;
    }
}
