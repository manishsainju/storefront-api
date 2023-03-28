<?php

namespace Fleetbase\Http\Filters\Storefront;

class StoreFilter
{
    /**
     * Apply the filters to a given Eloquent query builder and request.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  \Illuminate\Http\Request     $request
     * @param  \Fleetbase\Models\Driver  $model
     * @return void
     */
    public static function apply($query, $request, $model)
    {
        // Query only this company sessions resources
        $query->where('company_uuid', session('company'));

        // Query by network
        if ($request->filled('network')) {
            $query->whereHas('networks', function ($q) use ($request) {
                $q->where('network_uuid', $request->input('network'));

                // Query stores without a category
                if ($request->filled('without_category')) {
                    $q->whereNull('category_uuid');
                }

                // Query stores by category
                if ($request->filled('category')) {
                    $q->where('category_uuid', $request->input('category'));
                }
            });
        }

        return $query;
    }
}
