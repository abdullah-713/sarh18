<?php

namespace App\Filament\Resources\BranchResource\Pages;

use App\Filament\Resources\BranchResource;
use App\Jobs\RecalculateMonthlyAttendanceJob;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditBranch extends EditRecord
{
    protected static string $resource = BranchResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('recalculate_attendance')
                ->label(__('branches.recalc_action'))
                ->icon('heroicon-o-arrow-path')
                ->color('warning')
                ->requiresConfirmation()
                ->modalHeading(__('branches.recalc_modal_heading'))
                ->modalDescription(__('branches.recalc_modal_description'))
                ->modalSubmitActionLabel(__('branches.recalc_confirm'))
                ->action(function () {
                    RecalculateMonthlyAttendanceJob::dispatch(
                        'branch',
                        $this->record->id,
                        auth()->id(),
                    );

                    Notification::make()
                        ->title(__('branches.recalc_dispatched'))
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
