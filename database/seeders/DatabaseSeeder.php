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
            'name' => 'Ahmed Hossam',
            'email' => 'Ahmed.President@c2c.com',
            'password' => Hash::make('Medo@511@'),
            'role' => 'top_management',
            'status' => 'active',
        ]);
    }
}
