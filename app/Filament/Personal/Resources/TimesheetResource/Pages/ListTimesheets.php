<?php

namespace App\Filament\Personal\Resources\TimesheetResource\Pages;

use Carbon\Carbon;
use Filament\Actions;
use App\Models\Timesheet;
use Filament\Actions\Action;
use Illuminate\Support\Facades\Auth;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Personal\Resources\TimesheetResource;
use App\Imports\MyTimesheetImport;
use EightyNine\ExcelImport\ExcelImportAction;
use Barryvdh\DomPDF\Facade\Pdf;

class ListTimesheets extends ListRecords
{
    protected static string $resource = TimesheetResource::class;

    protected function getHeaderActions(): array
    {
        $lastTimesheet = Timesheet::where('user_id', Auth::user()->id)->orderBy('id', 'desc')->first();

        if($lastTimesheet == null) {
            return [
                Action::make('inWork')
                ->label('Starts work')
                ->color('success')
                ->requiresConfirmation()
                ->action(function (){
                    $user = Auth::user();
                    $timesheet = new Timesheet();

                    $timesheet->calendar_id = 1;
                    $timesheet->user_id = $user->id;
                    $timesheet->day_in = Carbon::now();
                    $timesheet->type = 'work';

                    $timesheet->save();

                    Notification::make()
                        ->title('You have started working')
                        ->success()
                        ->color('success') 
                        ->send();
                }),
                Actions\CreateAction::make(),
            ];
        }

        return [
            Action::make('inWork')
                ->label('Starts work')
                ->color('success')
                // ->keyBindings(['command+s', 'ctrl+s'])
                ->visible(!$lastTimesheet->day_out == null)
                ->disabled($lastTimesheet->day_out == null)
                ->requiresConfirmation()
                ->action(function (){
                    $user = Auth::user();
                    $timesheet = new Timesheet();

                    $timesheet->calendar_id = 1;
                    $timesheet->user_id = $user->id;
                    $timesheet->day_in = Carbon::now();
                    $timesheet->type = 'work';

                    $timesheet->save();

                    Notification::make()
                        ->title('You have started working')
                        ->success()
                        ->color('success') 
                        ->send();
                }),

            Action::make('inPause')
                ->label('Take a break')
                ->color('info')
                ->visible($lastTimesheet->day_out == null && $lastTimesheet->type != 'pause')
                ->disabled(!$lastTimesheet->day_out == null)
                ->requiresConfirmation()
                ->action(function () use($lastTimesheet){
                    $lastTimesheet->day_out = Carbon::now();
                    $lastTimesheet->save();
                    $timesheet = new Timesheet();

                    $timesheet->calendar_id = 1;
                    $timesheet->user_id = Auth::user()->id;
                    $timesheet->day_in = Carbon::now();
                    $timesheet->type = 'pause';

                    $timesheet->save();

                    Notification::make()
                        ->title('You have took a break')
                        ->info()
                        ->color('info') 
                        ->send();
                }),

            Action::make('stopPause')
                ->label('Stop break')
                ->color('info')
                ->visible($lastTimesheet->day_out == null && $lastTimesheet->type == 'pause')
                ->disabled(!$lastTimesheet->day_out == null)
                ->requiresConfirmation()
                ->action(function () use($lastTimesheet){
                    $lastTimesheet->day_out = Carbon::now();
                    $lastTimesheet->save();
                    $timesheet = new Timesheet();

                    $timesheet->calendar_id = 1;
                    $timesheet->user_id = Auth::user()->id;
                    $timesheet->day_in = Carbon::now();
                    $timesheet->type = 'work';

                    $timesheet->save();

                    Notification::make()
                        ->title('You have stopped the break')
                        ->info()
                        ->color('info') 
                        ->send();
                }),

            Action::make('stopWork')
                ->label('Stop work')
                ->color('success')
                ->visible($lastTimesheet->day_out == null && $lastTimesheet->type != 'pause')
                ->disabled(!$lastTimesheet->day_out == null)
                ->requiresConfirmation()
                ->action(function () use($lastTimesheet){
                    $lastTimesheet->day_out = Carbon::now();
                    $lastTimesheet->save();

                    Notification::make()
                        ->title('You have stopped to work')
                        ->success()
                        ->color('success')
                        ->send();
                }),
            Actions\CreateAction::make(),

            ExcelImportAction::make()
                ->color('primary')
                ->use(MyTimesheetImport::class),

            Action::make('createPDF')
                ->label('Create PDF')
                ->color('warning')
                ->requiresConfirmation()
                ->url(
                    fn (): string => route('pdf.example', ['user' => Auth::user()]),
                    shouldOpenInNewTab: true
                ),
        ];
    }
}
