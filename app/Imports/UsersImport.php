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

        return $user;
    }
}
