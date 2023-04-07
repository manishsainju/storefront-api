<?php

namespace Fleetbase\Storefront\Http\Resources\Storefront;

use Fleetbase\Http\Resources\v1\Place;
use Fleetbase\Http\Resources\FleetbaseResource;

class StoreLocation extends FleetbaseResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $storeLocation = [
            'id' => $this->public_id,
            'store' => $this->store->public_id,
            'name' => $this->name,
            'place' => $this->place ? new Place($this->place) : null,
            'hours' => $this->mapHours($this->hours),
        ];

        if ($request->input('with_store') === true && $this->store) {
            $storeLocation['store'] = new Store($this->store);
        }

        $storeLocation = array_merge($storeLocation, [
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ]);

        return $storeLocation;
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
}
