<?php

namespace Fleetbase\Storefront\Http\Filter;

use Fleetbase\Http\Filter\Filter;

class StoreFilter extends Filter
{
    public function queryForInternal()
    {
        $this->builder->where('company_uuid', $this->request->session()->get('company'));
    }

    public function network(?string $network)
    {
        $this->builder->whereHas(
            'networks',
            function ($query) use ($network) {
                $query->where('network_uuid', $network);

                // Query stores without a category
                if ($this->request->filled('without_category')) {
                    $query->whereNull('category_uuid');
                }

                // Query stores by category
                if ($this->request->filled('category')) {
                    $query->where('category_uuid', $this->request->input('category'));
                }
            }
        );
    }
}
