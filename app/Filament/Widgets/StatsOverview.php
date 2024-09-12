<?php

namespace App\Filament\Widgets;

use App\Models\Holiday;
use App\Models\Timesheet;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $total_employees = User::all()->count();
        $total_holidays = Holiday::where('type','pending')->count();
        $total_timesheets = Timesheet::all()->count();
        
        return [
            Stat::make('Employees', $total_employees),
            Stat::make('Pending holidays', $total_holidays),
            Stat::make('Timesheets', $total_timesheets),
        ];
    }
}
