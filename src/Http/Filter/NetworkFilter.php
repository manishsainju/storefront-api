<?php

namespace Fleetbase\Storefront\Http\Filters\Storefront;

class NetworkFilter
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

        return $query;
    }
}
