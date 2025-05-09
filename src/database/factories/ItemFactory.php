<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;
use App\Models\Condition;

class ItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'item_name'   => $this->faker->word(),
            'price'       => $this->faker->numberBetween(100, 5000),
            'description' => $this->faker->sentence(),
            'item_image'  => 'profile_default.jpg',
            'condition_id' =>Condition::factory(),
            'user_id'     => User::factory(),
        ];
    }
}
