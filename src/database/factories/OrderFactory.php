<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\Item;
use App\Models\PaymentMethod;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderFactory extends Factory
{
    protected $model = Order::class;

    public function definition()
    {
        return [
            'item_id' => Item::factory(),
            'payment_method_id' => PaymentMethod::factory(),
            'order_postal_code' => $this->faker->postcode,
            'order_address' => $this->faker->address,
            'order_building' => $this->faker->word,
            'user_id' => User::factory(),
        ];
    }
}
