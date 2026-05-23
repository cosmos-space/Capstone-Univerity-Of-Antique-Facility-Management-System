<?php

namespace Database\Seeders;

use App\Models\College;
use App\Models\Signatory;
use Illuminate\Database\Seeder;

class SignatorySeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            // Colleges
            ['type' => 'dean',         'name' => 'Dean A',          'unit' => 'College A'],
            ['type' => 'program_head', 'name' => 'Program Head A',  'unit' => 'College A'],
            ['type' => 'dean',         'name' => 'Dean B',          'unit' => 'College B'],
            ['type' => 'program_head', 'name' => 'Program Head B',  'unit' => 'College B'],
            ['type' => 'dean',         'name' => 'Dean C',          'unit' => 'College C'],
            ['type' => 'program_head', 'name' => 'Program Head C',  'unit' => 'College C'],

            // Organizations
            ['type' => 'org_president','name' => 'Org A President', 'unit' => 'Org A'],
            ['type' => 'org_adviser',  'name' => 'Org A Adviser',   'unit' => 'Org A'],
            ['type' => 'org_president','name' => 'Org B President', 'unit' => 'Org B'],
            ['type' => 'org_adviser',  'name' => 'Org B Adviser',   'unit' => 'Org B'],

            // GSU head
            ['type' => 'gsu_head',     'name' => 'GSU ORG HEAD',    'unit' => 'GSU'],
        ];

        foreach ($items as $item) {
            $collegeId = null;

            if (str_starts_with($item['unit'], 'College ')) {
                $collegeId = College::where('name', $item['unit'])->value('id');
            }

            Signatory::updateOrCreate(
                ['type' => $item['type'], 'name' => $item['name'], 'unit' => $item['unit']],
                [
                    'is_active' => true,
                    'college_id' => $collegeId,
                ],
            );
        }
    }
}
