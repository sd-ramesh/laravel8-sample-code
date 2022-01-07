<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = User::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->name,
            'email' => $this->faker->unique()->safeEmail,
            'phone_number' => $this->faker->e164PhoneNumber,
            'role' => 'vendor',//$this->faker->randomElement(['customer', 'vendor']),
            'username' => $this->faker->userName,
            'password' => hash('sha256', $this->faker->password),
            'cognito_access_token' => Str::random(10),
            'cognito_refresh_token' => encrypt(Str::random(10)),
        ];
    }
}
