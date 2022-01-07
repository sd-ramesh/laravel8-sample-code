<?php

namespace Database\Factories;

use App\Models\Message;
use App\Models\Vendor;
use Illuminate\Database\Eloquent\Factories\Factory;

class MessageFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Message::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'vendor_id'=>Vendor::factory(),
            'type'=>$this->faker->randomElement(['sms', 'email']),
            'subject'=>$this->faker->word,
            'content'=>$this->faker->text($maxNbChars = 50),
            'status'=>$this->faker->randomElement(['Success', 'Failed'])
        ];
    }
}
