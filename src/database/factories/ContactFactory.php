<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Channel;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Contact>
 */
class ContactFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'channel_id' => Channel::inRandomOrder()->first()->id,
            'name'       => $this->faker->name(),
            'photo'      => 'https://placehold.co/150x150?text=' . urlencode($this->faker->firstName()),
        ];
    }
}
