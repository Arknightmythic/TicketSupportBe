<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Tickets>
 */
class TicketsFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title'=>ucwords(join(' ', fake()->words(2))),
            'description'=>fake()->paragraph(5),
            'status' => fake()->randomElement(['waiting', 'process', 'close'])
        ];
    }
}
