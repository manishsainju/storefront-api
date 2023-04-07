<?php

namespace Fleetbase\Storefront\Http\Filters\Storefront;

use Fleetbase\Models\Category;

class ProductFilter
{
    /**
     * Apply the filters to a given Eloquent query builder and request.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  \Illuminate\Http\Request     $request
     * @param  \Fleetbase\Models\Storefront\Product  $model
     * @return void
     */
    public static function apply($query, $request, $model)
    {
        // Query only this company sessions resources
        $query->where('company_uuid', session('company'));

        // Query by category slug
        if ($request->filled('category_slug')) {
            $category = Category::where(['slug' => $request->input('category_slug'), 'for' => 'storefront_product'])->first();

            if($category) {
                $query->where('category_uuid', $category->uuid);
            }
        }

        return $query;
    }
}
