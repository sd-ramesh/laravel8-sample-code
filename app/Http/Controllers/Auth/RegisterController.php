<?php
declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Models\User;
use App\Objects\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Services\Cognito\Auth\RegistersUsers;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return User
     */
    protected function create(array $data): User
    {
        return User::create([
            // 'username' => $data['username'],
            'email' => $data['email'],
            'role' => $data['role'] ?? UserRole::CUSTOMER(),
            'name' => $data['name'] ?? '',
            'phone_number' => $data['phone_number'] ?? '',
        ]);
    }
}
