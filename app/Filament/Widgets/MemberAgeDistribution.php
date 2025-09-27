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

        // Calculate total for percentage calculation
        $total = array_sum($filteredData);

        // Create labels with percentages
        $labelsWithPercentages = [];
        foreach ($filteredData as $ageGroup => $count) {
            $percentage = $total > 0 ? round(($count / $total) * 100) : 0;
            $labelsWithPercentages[] = "{$ageGroup} ({$percentage}%)";
        }

        return [
            'datasets' => [
                [
                    'label' => 'Members',
                    'data' => array_values($filteredData),
                    'backgroundColor' => [
                        '#10B981', // emerald-500 for 0-5
                        '#06B6D4', // cyan-500 for 6-12
                        '#3B82F6', // blue-500 for 13-21
                        '#8B5CF6', // violet-500 for 22-35
                        '#EC4899', // pink-500 for 36-48
                        '#F59E0B', // amber-500 for 49-62
                        '#EF4444', // red-500 for 63+
                        '#6B7280', // gray-500 for Unknown
                    ],
                ],
            ],
            'labels' => $labelsWithPercentages,
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
                    'position' => 'left',
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