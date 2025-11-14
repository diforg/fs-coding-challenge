<?php

namespace Database\Factories;

use App\Models\Channel;
use Illuminate\Database\Eloquent\Factories\Factory;

class ChannelFactory extends Factory
{
    protected $model = Channel::class;

    public function definition()
    {
        return [
            'name' => $this->faker->word,
            'identifier' => $this->faker->unique()->slug,
            'is_active' => true,
        ];
    }
}