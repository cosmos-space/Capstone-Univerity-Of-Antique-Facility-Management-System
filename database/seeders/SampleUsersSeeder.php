<?php

namespace Database\Seeders;

use App\Models\College;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SampleUsersSeeder extends Seeder
{
    /**
     * Seed the application's sample users.
     */
    public function run(): void
    {
        $password = Hash::make('password123');

        // Admins A, B, C
        foreach (['A', 'B', 'C'] as $letter) {
            User::updateOrCreate(
                ['email' => "admin{$letter}@example.com"],
                [
                    'name' => "Admin {$letter}",
                    'password' => $password,
                    'role' => 'admin',
                    'college_name' => null,
                    'college_id' => null,
                    'organization_name' => null,
                ]
            );
        }

        // College staff A, B, C
        foreach (['A', 'B', 'C'] as $letter) {
            $collegeId = College::where('name', "College {$letter}")->value('id');

            User::updateOrCreate(
                ['email' => "college{$letter}@example.com"],
                [
                    'name' => "College {$letter} Staff",
                    'password' => $password,
                    'role' => 'college_staff',
                    'college_name' => "College {$letter}",
                    'college_id' => $collegeId,
                    'organization_name' => null,
                ]
            );
        }

        // Org staff A, B, C
        foreach (['A', 'B', 'C'] as $letter) {
            User::updateOrCreate(
                ['email' => "org{$letter}@example.com"],
                [
                    'name' => "Org {$letter} Staff",
                    'password' => $password,
                    'role' => 'org_staff',
                    'college_name' => null,
                    'college_id' => null,
                    'organization_name' => "Org {$letter}",
                ]
            );
        }
    }
}
