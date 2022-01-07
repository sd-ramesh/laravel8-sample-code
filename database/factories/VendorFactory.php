<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Vendor;
use Illuminate\Database\Eloquent\Factories\Factory;

class VendorFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Vendor::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $company = $this->faker->company;
        return [
            'user_id'  => User::factory(),
            'business_name' => $company,
            'abn'  => $this->faker->isbn13(),
            'trading_as'  => $company,
            'logo'  => $this->faker->imageUrl,
        ];
    }
}
