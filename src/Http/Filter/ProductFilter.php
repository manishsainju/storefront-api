<?php

namespace Fleetbase\Storefront\Http\Filter;

use Fleetbase\Http\Filter\Filter;

class ProductFilter extends Filter
{
    public function queryForInternal()
    {
        $this->builder->where('company_uuid', $this->request->session()->get('company'));
    }

    public function categorySlug(?string $categorySlug)
    {
        $this->builder->whereHas(
            'category',
            function ($query) use ($categorySlug) {
                $query->where(
                    [
                        'slug' => $categorySlug,
                        'for' => 'storefront_product'
                    ]
                );
            }
        );
    }
}
