<?php

namespace App\Filament\Widgets;

use App\Models\Member;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;

class MemberAgeDistribution extends ChartWidget
{
    protected ?string $heading = 'Member Age Distribution';
    protected static ?int $sort = 2;

    protected function getData(): array
    {
        $members = Member::whereNotNull('date_of_birth')->get();
        $membersWithoutDob = Member::whereNull('date_of_birth')->count();

        $ageGroups = [
            '0-5' => 0,
            '6-12' => 0,
            '13-21' => 0,
            '22-35' => 0,
            '36-48' => 0,
            '49-62' => 0,
            '63+' => 0,
        ];

        foreach ($members as $member) {
            $age = Carbon::parse($member->date_of_birth)->age;

            if ($age <= 5) {
                $ageGroups['0-5']++;
            } elseif ($age <= 12) {
                $ageGroups['6-12']++;
            } elseif ($age <= 21) {
                $ageGroups['13-21']++;
            } elseif ($age <= 35) {
                $ageGroups['22-35']++;
            } elseif ($age <= 48) {
                $ageGroups['36-48']++;
            } elseif ($age <= 62) {
                $ageGroups['49-62']++;
            } else {
                $ageGroups['63+']++;
            }
        }

        if ($membersWithoutDob > 0) {
            $ageGroups['Unknown'] = $membersWithoutDob;
        }

        // Filter out zero values
        $filteredData = array_filter($ageGroups, fn($count) => $count > 0);

        return [
            'datasets' => [
                [
                    'label' => 'Members',
                    'data' => array_values($filteredData),
                    'backgroundColor' => [
                        '#10B981', // emerald-500 for Children
                        '#3B82F6', // blue-500 for Adolescents
                        '#8B5CF6', // violet-500 for Adults
                        '#F59E0B', // amber-500 for Seniors
                        '#6B7280', // gray-500 for Unknown
                    ],
                ],
            ],
            'labels' => array_keys($filteredData),
        ];
    }

    protected function getType(): string
    {
        return 'pie';
    }

    protected function getOptions(): array
    {
        return [
            'maintainAspectRatio' => false,
            'responsive' => true,
            'plugins' => [
                'legend' => [
                    'position' => 'bottom',
                ],
            ],
        ];
    }

    public function getColumnSpan(): string|array|int
    {
        return [
            'md' => 1,
            'xl' => 1,
        ];
    }

    protected function getExtraAttributes(): array
    {
        return [
            'style' => 'max-height: 200px;',
        ];
    }
}