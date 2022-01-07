<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class VendorValidationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules():array
    {
        return [
            'business_name' => 'string|required',
            'abn' =>  'required',
            'traiding_as'  => 'string|nullable',
            'logo'  => 'string|nullable',
        ];
    }

    public function messages()
    {
        return [
            'business_name.required' => 'This field is required',
            'abn.required' => 'This field is required',
        ];
    }
}
