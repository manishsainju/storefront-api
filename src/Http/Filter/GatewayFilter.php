<?php

namespace Fleetbase\Storefront\Http\Filter;

use Fleetbase\Http\Filter\Filter;

class GatewayFilter extends Filter
{
    public function queryForInternal()
    {
        $this->builder->where('company_uuid', $this->request->session()->get('company'));
    }
}
