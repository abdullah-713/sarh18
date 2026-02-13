<?php

namespace App\Filament\Resources\PayrollResource\Pages;

use App\Filament\Resources\PayrollResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPayrolls extends ListRecords
{
    protected static string $resource = PayrollResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            Actions\Action::make('generatePayroll')
                ->label('توليد كشوف الرواتب')
                ->icon('heroicon-o-calculator')
                ->color('warning')
                ->form([
                    \Filament\Forms\Components\TextInput::make('period')
                        ->label('الفترة')
                        ->placeholder('2025-06')
                        ->required(),
                ])
                ->action(function (array $data) {
                    $period = $data['period'];
                    $users = \App\Models\User::where('status', 'active')->get();
                    $count = 0;

                    foreach ($users as $user) {
                        try {
                            \App\Models\Payroll::generateForUser($user, $period);
                            $count++;
                        } catch (\Throwable $e) {
                            continue;
                        }
                    }

                    \Filament\Notifications\Notification::make()
                        ->title("تم توليد {$count} كشف راتب")
                        ->success()
                        ->send();
                }),
        ];
    }
}
