<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use App\Jobs\RecalculateMonthlyAttendanceJob;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('recalculate_attendance')
                ->label(__('users.recalc_action'))
                ->icon('heroicon-o-arrow-path')
                ->color('warning')
                ->requiresConfirmation()
                ->modalHeading(__('users.recalc_modal_heading'))
                ->modalDescription(__('users.recalc_modal_description'))
                ->modalSubmitActionLabel(__('users.recalc_confirm'))
                ->action(function () {
                    RecalculateMonthlyAttendanceJob::dispatch(
                        'user',
                        $this->record->id,
                        auth()->id(),
                    );

                    Notification::make()
                        ->title(__('users.recalc_dispatched'))
                        ->icon('heroicon-o-arrow-path')
                        ->success()
                        ->send();
                }),
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
