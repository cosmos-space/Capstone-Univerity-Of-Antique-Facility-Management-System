<?php

namespace Database\Seeders;

use App\Models\College;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CollegeSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $colleges = [
            ['name' => 'College A', 'short_name' => 'A', 'description' => 'Sample College A for seeded test data.'],
            ['name' => 'College B', 'short_name' => 'B', 'description' => 'Sample College B for seeded test data.'],
            ['name' => 'College C', 'short_name' => 'C', 'description' => 'Sample College C for seeded test data.'],
        ];

        foreach ($colleges as $data) {
            College::updateOrCreate(
                ['name' => $data['name']],
                [
                    'short_name' => $data['short_name'],
                    'description' => $data['description'],
                ]
            );
        }
    }
}
