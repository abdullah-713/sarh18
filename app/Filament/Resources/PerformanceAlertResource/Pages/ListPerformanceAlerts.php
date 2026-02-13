<?php

namespace App\Filament\Resources\PerformanceAlertResource\Pages;

use App\Filament\Resources\PerformanceAlertResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPerformanceAlerts extends ListRecords
{
    protected static string $resource = PerformanceAlertResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
