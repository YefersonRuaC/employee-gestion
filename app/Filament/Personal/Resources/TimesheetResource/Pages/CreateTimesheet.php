<?php

namespace App\Filament\Personal\Resources\TimesheetResource\Pages;

use Filament\Actions;
use Illuminate\Support\Facades\Auth;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Personal\Resources\TimesheetResource;

class CreateTimesheet extends CreateRecord
{
    protected static string $resource = TimesheetResource::class;
}
