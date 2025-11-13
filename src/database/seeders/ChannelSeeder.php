<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Channel;

class ChannelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $channels = ['whatsapp', 'telegram', 'messenger'];

        foreach ($channels as $name) {
            Channel::firstOrCreate([
                'name' => $name
            ]);
        }
    }
}
