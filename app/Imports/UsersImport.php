<?php

namespace App\Imports;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class UsersImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        $user = User::create([
            'name'     => $row['name'],
            'email'    => $row['email'],
            'password' => Hash::make($row['password']), // Hash the password
            'role'     => $row['role'] ?? 'member',
            'status'   => $row['status'] ?? 'pending',
        ]);

        if (isset($row['committees'])) {
            $committeeNames = explode(',', $row['committees']);
            foreach ($committeeNames as $name) {
                $name = trim($name);
                if ($name) {
                    $committee = \App\Models\Committee::firstOrCreate(['name' => $name]);
                    $user->committees()->attach($committee->id);
                }
            }
        }

        // Handle Authorized Committees (for HR)
        if ($user->role === 'hr' && isset($row['authorized_committees'])) {
            $authCommitteeNames = explode(',', $row['authorized_committees']);
            foreach ($authCommitteeNames as $name) {
                $name = trim($name);
                if ($name) {
                    $committee = \App\Models\Committee::firstOrCreate(['name' => $name]);
                    // Attach to authorizedCommittees with granted_by
                    $user->authorizedCommittees()->attach($committee->id, [
                        'granted_by' => \Illuminate\Support\Facades\Auth::id() ?? 1 // Default to 1 if no auth user (e.g. seeder/console)
                    ]);
                }
            }
        }

        return $user;
    }
}
