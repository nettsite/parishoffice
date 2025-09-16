<?php

namespace Database\Seeders;

use App\Models\GroupType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class GroupTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $defaultTypes = [
            [
                'name' => 'Altar Servers',
                'description' => 'Youth who assist during Mass celebrations',
                'color' => '#dc2626',
            ],
            [
                'name' => 'Choir',
                'description' => 'Musical ministry for liturgical celebrations',
                'color' => '#2563eb',
            ],
            [
                'name' => 'Catechism Class',
                'description' => 'Religious education groups for children and adults',
                'color' => '#16a34a',
            ],
            [
                'name' => 'Pastoral Council',
                'description' => 'Advisory body for parish governance',
                'color' => '#7c3aed',
            ],
            [
                'name' => 'Youth Group',
                'description' => 'Social and spiritual activities for young parishioners',
                'color' => '#ea580c',
            ],
            [
                'name' => 'Bible Study',
                'description' => 'Scripture study and reflection groups',
                'color' => '#0891b2',
            ],
            [
                'name' => 'Knights of Columbus',
                'description' => 'Catholic fraternal organization',
                'color' => '#059669',
            ],
            [
                'name' => 'Ladies Auxiliary',
                'description' => 'Women\'s service and fellowship group',
                'color' => '#be185d',
            ],
        ];

        foreach ($defaultTypes as $type) {
            $groupType = GroupType::firstOrCreate(['name' => $type['name']], $type);

            // Assign default permissions based on group type
            if ($type['name'] === 'Catechism Class') {
                $groupType->givePermissionTo([
                    'member.view.basic',
                    'member.view.sacramental',
                    'member.view.certificates'
                ]);
            } else {
                $groupType->givePermissionTo(['member.view.basic']);
            }
        }
    }
}
