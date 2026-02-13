<?php

namespace App\Filament\Widgets;

use App\Services\AnalyticsService;
use Filament\Widgets\Widget;

class ROIMatrixWidget extends Widget
{
    protected static string $view = 'filament.widgets.roi-matrix';

    protected int | string | array $columnSpan = 'full';

    protected static ?int $sort = 5;

    public array $matrixData = [];

    public function mount(): void
    {
        $this->loadMatrix();
    }

    public function loadMatrix(): void
    {
        $service = app(AnalyticsService::class);
        $this->matrixData = $service->calculateROIMatrix(
            now()->startOfMonth(),
            now()
        );
    }

    protected function getViewData(): array
    {
        return [
            'matrix' => $this->matrixData,
        ];
    }
}
