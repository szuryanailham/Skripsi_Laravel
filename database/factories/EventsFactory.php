<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Events>
 */
class EventsFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence,
            'slug' => $this->faker->slug,
            'description' => $this->faker->paragraph,
            'price' => $this->faker->randomFloat(2, 10, 100),
            'location' => $this->faker->address,
            'date' => $this->faker->date,
            'time' => $this->faker->time,
        ];
    }
}