<?php

namespace App\Filament\Widgets;

use App\Models\AnalyticsSnapshot;
use App\Models\Branch;
use App\Services\AnalyticsService;
use Filament\Widgets\Widget;

class AttendanceHeatmapWidget extends Widget
{
    protected static string $view = 'filament.widgets.attendance-heatmap';

    protected int | string | array $columnSpan = 'full';

    protected static ?int $sort = 3;

    public ?int $selectedBranch = null;
    public array $heatmapData = [];
    public array $branches = [];

    public function mount(): void
    {
        $this->branches = Branch::where('is_active', true)
            ->pluck('name_ar', 'id')
            ->toArray();

        $this->selectedBranch = array_key_first($this->branches);
        $this->loadHeatmap();
    }

    public function updatedSelectedBranch(): void
    {
        $this->loadHeatmap();
    }

    public function loadHeatmap(): void
    {
        if (!$this->selectedBranch) return;

        $branch = Branch::find($this->selectedBranch);
        if (!$branch) return;

        $service = app(AnalyticsService::class);
        $this->heatmapData = $service->generateHeatmapData(
            $branch,
            now()->subDays(30),
            now()
        );
    }

    protected function getViewData(): array
    {
        return [
            'heatmap'  => $this->heatmapData,
            'branches' => $this->branches,
        ];
    }
}
