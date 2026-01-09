<?php

namespace Database\Seeders;

use App\Models\AttendanceSession;
use App\Models\Committee;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 5. Run other seeders
        $this->call([
            TaskSeeder::class,
            TaskSubmissionSeeder::class,
            SessionFeedbackSeeder::class,
            NotificationSeeder::class,
        ]);
    }
}
