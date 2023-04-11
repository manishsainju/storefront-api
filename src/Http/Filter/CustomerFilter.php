<?php

namespace Fleetbase\Storefront\Http\Filter;

use Fleetbase\Http\Filter\Filter;

class CustomerFilter extends Filter
{
    public function queryForInternal()
    {
        $this->builder->where('company_uuid', $this->request->session()->get('company'));
    }

    public function storefront($storefront)
    {
        $this->builder->whereHas(
            'customerOrders',
            function ($query) use ($storefront) {
                $query->where('meta->storefront_id', $storefront);
            }
        );
    }
}
