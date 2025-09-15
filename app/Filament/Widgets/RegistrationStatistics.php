<?php

namespace App\Filament\Widgets;

use App\Models\Household;
use App\Models\Member;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class RegistrationStatistics extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        // Calculate totals
        $totalHouseholds = Household::count();
        $totalMembers = Member::count();

        // Calculate 7-day movements
        $sevenDaysAgo = Carbon::now()->subDays(7);
        $householdsLast7Days = Household::where('created_at', '>=', $sevenDaysAgo)->count();
        $membersLast7Days = Member::where('created_at', '>=', $sevenDaysAgo)->count();

        return [
            Stat::make('Total Households', $totalHouseholds)
                ->description($householdsLast7Days > 0 ? "+{$householdsLast7Days} in last 7 days" : 'No new households in last 7 days')
                ->descriptionIcon($householdsLast7Days > 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-minus')
                ->color($householdsLast7Days > 0 ? 'success' : 'gray'),

            Stat::make('Total Members', $totalMembers)
                ->description($membersLast7Days > 0 ? "+{$membersLast7Days} in last 7 days" : 'No new members in last 7 days')
                ->descriptionIcon($membersLast7Days > 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-minus')
                ->color($membersLast7Days > 0 ? 'success' : 'gray'),
        ];
    }
}
