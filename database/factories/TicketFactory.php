<?php

namespace Database\Factories;

use App\Enums\TicketStatus;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Ticket>
 */
class TicketFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'subject' => fake()->sentence(),
            'body' => fake()->paragraph(),
            'status' => fake()->randomElement(TicketStatus::cases())->value,
            'category' => fake()->word(),
            'confidence' => fake()->randomFloat(2, 0, 1),
            'explanation' => fake()->paragraph(),
            'note' => fake()->paragraph(),
        ];
    }
}
