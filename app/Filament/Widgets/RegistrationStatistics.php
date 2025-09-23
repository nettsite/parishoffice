<?php

namespace App\Filament\Widgets;

use App\Models\Household;
use App\Models\Member;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class RegistrationStatistics extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        // Calculate totals
        $totalHouseholds = Household::count();
        $totalMembers = Member::count();

        // Calculate time periods
        $today = Carbon::today();
        $yesterday = Carbon::yesterday();
        $sevenDaysAgo = Carbon::now()->subDays(7);

        // Calculate household movements
        $householdsToday = Household::whereDate('created_at', $today)->count();
        $householdsYesterday = Household::whereDate('created_at', $yesterday)->count();
        $householdsLast7Days = Household::where('created_at', '>=', $sevenDaysAgo)->count();

        // Calculate member movements
        $membersToday = Member::whereDate('created_at', $today)->count();
        $membersYesterday = Member::whereDate('created_at', $yesterday)->count();
        $membersLast7Days = Member::where('created_at', '>=', $sevenDaysAgo)->count();

        // Build household description
        $householdParts = [];
        if ($householdsToday > 0) $householdParts[] = "+{$householdsToday} today";
        if ($householdsYesterday > 0) $householdParts[] = "+{$householdsYesterday} yesterday";
        if ($householdsLast7Days > 0) $householdParts[] = "+{$householdsLast7Days} in last 7 days";
        $householdDescription = !empty($householdParts) ? implode(', ', $householdParts) : 'No new households recently';

        // Build member description
        $memberParts = [];
        if ($membersToday > 0) $memberParts[] = "+{$membersToday} today";
        if ($membersYesterday > 0) $memberParts[] = "+{$membersYesterday} yesterday";
        if ($membersLast7Days > 0) $memberParts[] = "+{$membersLast7Days} in last 7 days";
        $memberDescription = !empty($memberParts) ? implode(', ', $memberParts) : 'No new members recently';

        // Determine colors and icons
        $householdHasActivity = $householdsToday > 0 || $householdsYesterday > 0 || $householdsLast7Days > 0;
        $memberHasActivity = $membersToday > 0 || $membersYesterday > 0 || $membersLast7Days > 0;

        return [
            Stat::make('Total Households', $totalHouseholds)
                ->description($householdDescription)
                ->descriptionIcon($householdHasActivity ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-minus')
                ->color($householdHasActivity ? 'success' : 'gray'),

            Stat::make('Total Members', $totalMembers)
                ->description($memberDescription)
                ->descriptionIcon($memberHasActivity ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-minus')
                ->color($memberHasActivity ? 'success' : 'gray'),
        ];
    }
}
