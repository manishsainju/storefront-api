<?php

namespace Fleetbase\Http\Requests\Storefront;

use Fleetbase\Http\Requests\Request;

class VerifyCreateCustomerRequest extends FleetbaseRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return session('storefront_key') || request()->session()->has('api_credential');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'mode' => 'required|in:email,sms',
            'identity' => 'required'
        ];
    }
}
