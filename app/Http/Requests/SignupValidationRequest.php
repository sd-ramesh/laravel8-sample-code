<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SignupValidationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    protected function authorize()
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    protected function rules()
    {
        return [
            'name' => 'string|required',
            'password' => 'string|comfirmed|required',
            'phone_number' => 'required'
        ];
    }
}
