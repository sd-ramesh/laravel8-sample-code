<?php

namespace Database\Factories;

use App\Models\Queue;
use App\Models\User;
use App\Models\Vendor;
use Illuminate\Database\Eloquent\Factories\Factory;

class QueueFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Queue::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'user_id'=>User::factory(),
            'vendor_id'=>Vendor::factory(),
            'status'=>$this->faker->randomElement(['ready', 'preparing', 'done']),
            'ticket_num' =>$this->faker->randomDigitNotNull
        ];
    }
}
