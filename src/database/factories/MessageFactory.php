<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Contact;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Message>
 */
class MessageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'contact_id' => Contact::inRandomOrder()->first()->id,
            'message'    => $this->faker->paragraph(),
            'origin'     => $this->faker->randomElement(['received', 'sent']),
            'is_read'    => $this->faker->boolean(),
         ];
    }
}
