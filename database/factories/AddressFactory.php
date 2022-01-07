<?php

namespace Database\Factories;

use App\Models\Address;
use App\Models\Vendor;
use Illuminate\Database\Eloquent\Factories\Factory;

class AddressFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Address::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'user_id' =>Vendor::factory()->create()->user_id,
            'address'=>$this->faker->country,
            'suburb'=>$this->faker->secondaryAddress,
            'postcode'=>$this->faker->ean8,
            'state'=>$this->faker->state,
            'country'=>$this->faker->country,
        ];
    }
}
