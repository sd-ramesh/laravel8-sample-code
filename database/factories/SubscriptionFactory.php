<?php

namespace Database\Factories;

use App\Models\Subscription;
use App\Models\Vendor;
use Illuminate\Database\Eloquent\Factories\Factory;

class SubscriptionFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Subscription::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'vendor_id'=>Vendor::factory(),
            'is_active'=>$this->faker->boolean,
            'subscription_date'=>$this->faker->date($format = 'Y-m-d'),
            'expiration_date'=>$this->faker->date($format = 'Y-m-d'),
        ];
    }
}
