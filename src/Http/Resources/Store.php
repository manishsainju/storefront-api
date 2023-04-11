<?php

namespace Fleetbase\Storefront\Http\Resources;

use Fleetbase\Http\Resources\FleetbaseResource;
use Fleetbase\Support\Http;
use Illuminate\Support\Arr;

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
        $withMedia = $request->boolean('with_media');
        $withLocations = $request->boolean('with_locations');
        $store = [
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
            'slug' => $this->slug,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];

        // modify for internal requests
        if (Http::isInternalRequest()) {
            $store['id'] = $this->id;

            $store = Arr::insertAfterKey(
                $store,
                [
                    'uuid' => $this->uuid,
                    'public_id' => $this->public_id
                ],
                'id'
            );
        }

        if ($withLocations) {
            $store = Arr::insertAfterKey($store, $this->locations->mapInto(StoreLocation::class), 'is_store');
        }

        if ($withMedia) {
            $store = Arr::insertAfterKey($store, $this->mapMedia($this->media), 'is_store');
        }

        return $store;
    }

    /**
     * Map the given collection of media objects to an array of formatted media data.
     *
     * @param \Illuminate\Database\Eloquent\Collection $medias The collection of media objects to map.
     * @return array The array of formatted media data.
     */
    public function mapMedia(\Illuminate\Database\Eloquent\Collection $medias): array
    {
        return array_map(
            function ($media) {
                return [
                    'id' => data_get($media, 'public_id'),
                    'filename' => data_get($media, 'original_filename'),
                    'type' => data_get($media, 'content_type'),
                    'caption' => data_get($media, 'caption'),
                    'url' => data_get($media, 'url')
                ];
            },
            $medias->toArray()
        );
    }

    /**
     * Format the given options array by removing excluded keys.
     *
     * @param mixed $options The options array to format.
     * @return array The formatted options array.
     */
    public function formatOptions($options = []): array
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
