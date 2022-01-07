<?php

namespace App\Http\Requests\Vendor;

use Illuminate\Foundation\Http\FormRequest;

class AddressRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    { 
        return [
            'address' => 'string|max:255',
            'suburb' => 'string|max:255',
            'postcode' => 'integer',
            'state' => 'string|required',
            'country' => 'string|required',
            'user_id' => 'integer|required',
        ];
    }

    public function messages()
    {
        return [
            'state.required' => 'State is required',
            'user_id.required' => 'User id is required',
        ];
    }
}
