<?php

namespace App\Filament\Resources\HolidayResource\Pages;

use App\Models\User;
use Filament\Actions;
use App\Mail\HolidayApproved;
use App\Mail\HolidayDeclined;
use Illuminate\Support\Facades\Mail;
use Illuminate\Database\Eloquent\Model;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use App\Filament\Resources\HolidayResource;

class EditHoliday extends EditRecord
{
    protected static string $resource = HolidayResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $record->update($data);
        
        if($record->type == 'approved') {
            $user = User::find($record->user_id);

            $data = [
                'name' => $user->name,
                'email' => $user->email,
                'day' => $record->day
            ];

            Mail::to($user)->send(new HolidayApproved($data));

            $recipient = $user;

            Notification::make()
                ->title('Approved holiday request')
                ->body('Your ' . $data['day'] . ' holiday is approved')
                ->sendToDatabase($recipient);

        } else if($record->type == 'declined') {
            $user = User::find($record->user_id);

            $data = [
                'name' => $user->name,
                'email' => $user->email,
                'day' => $record->day
            ];

            Mail::to($user)->send(new HolidayDeclined($data));

            $recipient = $user;

            Notification::make()
                ->title('Declined holiday request')
                ->body('Your ' . $data['day'] . ' holiday is declined')
                ->sendToDatabase($recipient);
        }
        
        return $record;
    }
}
