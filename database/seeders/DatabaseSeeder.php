<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create Top Management User
        User::create([
            'name' => 'Top Manager',
            'email' => 'admin@c2c.com',
            'password' => Hash::make('password'),
            'role' => 'top_management',
            'status' => 'active',
        ]);

        // Create Board Member
        User::create([
            'name' => 'Board Member',
            'email' => 'board@c2c.com',
            'password' => Hash::make('password'),
            'role' => 'board',
            'status' => 'active',
        ]);

        // Create HR
        User::create([
            'name' => 'HR Manager',
            'email' => 'hr@c2c.com',
            'password' => Hash::make('password'),
            'role' => 'hr',
            'status' => 'active',
        ]);
    }
}
