<?php

namespace Fleetbase\Http\Requests\Storefront;

use Fleetbase\Http\Requests\Request;
use Fleetbase\Rules\ExistsInAny;
use Illuminate\Validation\Rule;


class CreateCustomerRequest extends FleetbaseRequest
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
            'code' => 'required|exists:verification_codes,code',
            'name' => 'required',
            'email' => [
                'email', 'nullable', Rule::unique('contacts')->where(function ($query) {
                    return $query->whereNull('deleted_at');
                })
            ],
            'phone' => [
                'nullable', Rule::unique('contacts')->where(function ($query) {
                    return $query->whereNull('deleted_at');
                })
            ]
        ];
    }
}
