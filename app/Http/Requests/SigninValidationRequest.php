<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SigninValidationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    protected function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    protected function rules()
    {

        return [
            'email' => 'required|email|string',
            'password' => 'required|string',
        ];
    }
    protected function message()
    {
        // echo "test";
        // exit;
        return [
            'email.message' => 'This field is required and must be formatted correctly',
            'password' => 'this field is required',
        ];
    }
}
