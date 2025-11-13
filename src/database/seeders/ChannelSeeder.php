<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Channel;

class ChannelSeeder extends Seeder
{
    public function run()
    {
        $channels = [
            [
                'name' => 'WhatsApp',
                'identifier' => 'whatsapp',
                'is_active' => true
            ],
            [
                'name' => 'Telegram',
                'identifier' => 'telegram', 
                'is_active' => false
            ],
            [
                'name' => 'Messenger',
                'identifier' => 'messenger',
                'is_active' => false
            ]
        ];

        foreach ($channels as $channel) {
            Channel::firstOrCreate(
                ['identifier' => $channel['identifier']],
                $channel
            );
        }
    }
}