<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Str;

class NotificationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();

        foreach ($users as $user) {
            // Generate 3-5 notifications for each user
            $count = rand(3, 5);
            for ($i = 0; $i < $count; $i++) {
                $user->notifications()->create([
                    'id' => Str::uuid()->toString(),
                    'type' => 'App\Notifications\GeneralNotification',
                    'data' => [
                        'message' => 'This is a test notification #' . ($i + 1),
                        'action_url' => '#',
                    ],
                    'read_at' => rand(0, 1) ? now() : null,
                ]);
            }
        }
    }
}
