<?php

namespace Fleetbase\Http\Requests\Storefront;

use Fleetbase\Http\Requests\Request;
// use Fleetbase\Rules\Storefront\CartExists;
use Fleetbase\Rules\Storefront\CustomerExists;
use Fleetbase\Rules\Storefront\GatewayExists;

class InitializeCheckoutRequest extends FleetbaseRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return session('storefront_key');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'gateway' => ['required', new GatewayExists],
            'customer' => ['required', new CustomerExists],
            'cart' => ['required', 'exists:storefront.carts,public_id'],
            'serviceQuote' => ['required', 'exists:service_quotes,public_id'],
            'cash' => ['sometimes', 'boolean'],
            'pickup' => ['sometimes', 'boolean'],
        ];
    }
}
