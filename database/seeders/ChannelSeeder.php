<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ChannelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $channels = ['SMS', 'E-Mail', 'Push Notification'];
        foreach ($channels as $channel) {
            \App\Models\Channel::create(['name' => $channel]);
        }
    }
}
