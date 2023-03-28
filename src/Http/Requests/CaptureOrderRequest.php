<?php

namespace Fleetbase\Http\Requests\Storefront;

use Fleetbase\Http\Requests\Request;

class CaptureOrderRequest extends FleetbaseRequest
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
            'token' => ['required', 'exists:storefront.checkouts,token'],
        ];
    }
}
