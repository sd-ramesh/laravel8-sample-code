<?php
declare(strict_types=1);


namespace App\Http\Requests\Auth;

use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Unique;
use Illuminate\Validation\Rules\RequiredIf;
use Illuminate\Foundation\Http\FormRequest;

/**
 * @property string $username
 * @property string $password
 * @property string $name
 */
final class RegistrationRequest extends FormRequest
{
    /**
     * @return array
     */
    protected function rules(): array
    {
        return [
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'name' => ['nullable', 'string', 'max:255'],
            'phone_number' => ['nullable', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:'.config('cognito.password.minLength'), 'confirmed'],
        ];
    }

    /**
     * @return array
     */
    public function messages(): array
    {
        return [
            'email.required' => 'Email address is required',
            'password.required' => 'Email address is required'
        ];
    }

    /**
     * @return Unique
     */
    private function emailUnique(): Unique
    {
        return Rule::unique('users', 'email');
    }

    /**
     * @param bool $isVendorUserRole
     * @return RequiredIf
     */
    private function required(bool $isVendorUserRole): RequiredIf
    {
        return Rule::requiredIf($isVendorUserRole);
    }
}
