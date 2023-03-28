<?php

namespace Fleetbase\Http\Resources\Storefront;

use Fleetbase\Http\Resources\FleetbaseResource;
use Illuminate\Support\Str;

class ReviewCustomer extends FleetbaseResource
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
            'id' => Str::replaceFirst('contact', 'customer', $this->public_id),
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'photo_url' => $this->photo_url,
            'reviews_count' => $this->getReviewsCount(),
            'uploads_count' => $this->getUploadsCount(),
            'slug' => $this->slug,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }

    public function getReviewsCount()
    {
        if (session('storefront_network')) {
            return $this->storeReviews()->whereHas('subject', function ($q) {
                $q->whereHas('networks', function ($q) {
                    $q->where('network_uuid', session('storefront_network'));
                });
            })->count();

            // $productReviewsCount = $this->productReviews()->whereHas('subject', function ($q) {
            //     $q->whereHas('store', function ($q) {
            //         $q->whereHas('networks', function ($q) {
            //             $q->where('network_uuid', session('storefront_network'));
            //         });
            //     });
            // })->count();

            // return $storeReviewsCount + $productReviewsCount;
        }
    }

    public function getUploadsCount()
    {
        return $this->reviewUploads()->count();
    }
}
