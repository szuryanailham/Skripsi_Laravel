<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Order;
use App\Models\User;
use App\Models\Events;

class OrderFactory extends Factory
{
    protected $model = Order::class;

    public function definition()
    {
        return [
            'order_number' => 'ORD-' . $this->faker->unique()->numerify('#######'),
            'user_id' => $this->faker->numberBetween(1, 5), // ID user antara 1-10
            'event_id' => $this->faker->numberBetween(1, 5), // ID event antara 1-10
            'total_amount' => $this->faker->randomFloat(2, 50, 1000), // Jumlah total antara 50 - 1000
            'status' => $this->faker->randomElement(['pending', 'processing', 'completed', 'cancelled']),
            'paid' => $this->faker->boolean(),
            'paid_at' => $this->faker->optional()->dateTime(),
        ];
    }
}
