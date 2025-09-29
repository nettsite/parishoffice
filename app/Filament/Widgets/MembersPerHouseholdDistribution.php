<?php

namespace App\Filament\Widgets;

use App\Models\Household;
use Filament\Widgets\ChartWidget;

class MembersPerHouseholdDistribution extends ChartWidget
{
    protected ?string $heading = 'Members per Household Distribution';
    protected static ?int $sort = 3;

    protected function getData(): array
    {
        $households = Household::withCount('members')->get();
        $emptyHouseholds = $households->where('members_count', 0)->count();

        $memberCountGroups = [
            '1' => 0,
            '2' => 0,
            '3' => 0,
            '4' => 0,
            '5+' => 0,
        ];

        foreach ($households as $household) {
            $memberCount = $household->members_count;

            if ($memberCount == 1) {
                $memberCountGroups['1']++;
            } elseif ($memberCount == 2) {
                $memberCountGroups['2']++;
            } elseif ($memberCount == 3) {
                $memberCountGroups['3']++;
            } elseif ($memberCount == 4) {
                $memberCountGroups['4']++;
            } elseif ($memberCount >= 5) {
                $memberCountGroups['5+']++;
            }
        }

        if ($emptyHouseholds > 0) {
            $memberCountGroups['Empty'] = $emptyHouseholds;
        }

        // Filter out zero values
        $filteredData = array_filter($memberCountGroups, fn($count) => $count > 0);

        // Calculate total for percentage calculation
        $total = array_sum($filteredData);

        // Create labels with percentages
        $labelsWithPercentages = [];
        foreach ($filteredData as $memberCountGroup => $count) {
            $percentage = $total > 0 ? round(($count / $total) * 100) : 0;
            $labelsWithPercentages[] = "{$memberCountGroup} ({$percentage}%)";
        }

        return [
            'datasets' => [
                [
                    'label' => 'Households',
                    'data' => array_values($filteredData),
                    'backgroundColor' => [
                        '#10B981', // emerald-500
                        '#3B82F6', // blue-500
                        '#8B5CF6', // violet-500
                        '#F59E0B', // amber-500
                        '#EF4444', // red-500
                        '#6B7280', // gray-500 for Empty
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
            'cutout' => '50%',
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