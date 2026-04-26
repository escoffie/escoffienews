<?php

namespace Database\Seeders;

use App\Enums\CategoryType;
use App\Enums\ChannelType;
use App\Models\Category;
use App\Models\Channel;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sports = Category::where('name', CategoryType::SPORTS->value)->first();
        $finance = Category::where('name', CategoryType::FINANCE->value)->first();
        $movies = Category::where('name', CategoryType::MOVIES->value)->first();

        $sms = Channel::where('name', ChannelType::SMS->value)->first();
        $email = Channel::where('name', ChannelType::EMAIL->value)->first();
        $push = Channel::where('name', ChannelType::PUSH->value)->first();

        $users = [
            [
                'name' => 'John Doe',
                'email' => 'john@example.com',
                'phone' => '1234567890',
                'categories' => [$sports->id, $finance->id],
                'channels' => [$sms->id, $email->id]
            ],
            [
                'name' => 'Jane Smith',
                'email' => 'jane@example.com',
                'phone' => '0987654321',
                'categories' => [$movies->id],
                'channels' => [$push->id]
            ],
            [
                'name' => 'Bob Wilson',
                'email' => 'bob@example.com',
                'phone' => '5555555555',
                'categories' => [$sports->id, $movies->id, $finance->id],
                'channels' => [$sms->id, $push->id, $email->id]
            ],
        ];

        foreach ($users as $userData) {
            $user = User::create([
                'name' => $userData['name'],
                'email' => $userData['email'],
                'password' => bcrypt('password'),
                'phone' => $userData['phone'],
            ]);
            
            $user->categories()->attach($userData['categories']);
            $user->channels()->attach($userData['channels']);
        }
    }
}
