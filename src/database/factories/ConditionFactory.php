<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ConditionFactory extends Factory
{
    public function definition()
    {
        return [
            'condition_name' => $this->faker->randomElement(['良好', '目立った傷や汚れなし', 'やや傷や汚れあり', '状態が悪い']),
        ];
    }
}
