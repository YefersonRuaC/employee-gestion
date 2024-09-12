<?php

namespace App\Filament\Personal\Widgets;

use App\Models\Holiday;
use App\Models\Timesheet;
use App\Models\User;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class PersonalWidgetStats extends BaseWidget
{
    protected function getStats(): array
    {
        $user = Auth::user();

        return [
            Stat::make('Pending holidays', $user instanceof User ? $this->getPendingHoliday($user) : 'N/A'),
            Stat::make('Approved holidays', $user instanceof User ? $this->getApprovedHoliday($user) : 'N/A'),
            Stat::make('Total worked', $this->getTotalWorked($user)),
            Stat::make('Total pause', $this->getTotalPause($user)),
        ];
    }

    protected function getPendingHoliday(User $user)
    {
        $totalPendingHolidays = Holiday::where('user_id', $user->id)
            ->where('type', 'pending')->get()->count();

        return $totalPendingHolidays;
    }

    protected function getApprovedHoliday(User $user)
    {
        $totalApprovedHolidays = Holiday::where('user_id', $user->id)
            ->where('type', 'approved')->get()->count();

        return $totalApprovedHolidays;
    }

    protected function getTotalWorked(User $user)
    {
        $timesheets = Timesheet::where('user_id', $user->id)
            ->where('type', 'work')->whereDate('created_at', Carbon::today())->get();

        $sumSeconds = 0;

        foreach ($timesheets as $timesheet) {
            $startTime = Carbon::parse($timesheet->day_in);
            $finishTime = Carbon::parse($timesheet->day_out);

            $totalDuration = $finishTime->diffInSeconds($startTime);
            $sumSeconds += $totalDuration;
        }
        $positiveSumSeconds = $sumSeconds * -1;
        // dd($positiveSumSeconds);
        $timeFormat = gmdate("H:i:s", $positiveSumSeconds);

        return $timeFormat;
    }

    protected function getTotalPause(User $user)
    {
        $timesheets = Timesheet::where('user_id', $user->id)
            ->where('type', 'pause')->whereDate('created_at', Carbon::today())->get();

        $sumSeconds = 0;

        foreach ($timesheets as $timesheet) {
            $startTime = Carbon::parse($timesheet->day_in);
            $finishTime = Carbon::parse($timesheet->day_out);

            $totalDuration = $finishTime->diffInSeconds($startTime);
            $sumSeconds += $totalDuration;
        }
        $positiveSumSeconds = $sumSeconds * -1;

        $timeFormat = gmdate("H:i:s", $positiveSumSeconds);

        return $timeFormat;
    }
}
