<?php

namespace App\Filament\Widgets;

use App\Models\Branch;
use App\Services\AnalyticsService;
use Filament\Widgets\Widget;

class EfficiencyScoreCardWidget extends Widget
{
    protected static string $view = 'filament.widgets.efficiency-score-card';

    protected int | string | array $columnSpan = 'full';

    protected static ?int $sort = 4;

    public array $scores = [];

    public function mount(): void
    {
        $this->loadScores();
    }

    public function loadScores(): void
    {
        $service  = app(AnalyticsService::class);
        $branches = Branch::where('is_active', true)->get();
        $scores   = [];

        foreach ($branches as $branch) {
            $efficiency = $service->calculateEfficiencyScore(
                $branch,
                now()->startOfMonth(),
                now()
            );

            $vpm = $service->calculateVPM($branch);
            $gap = $service->calculateProductivityGap($branch, now());

            $scores[] = [
                'id'         => $branch->id,
                'name'       => $branch->name_ar,
                'efficiency' => round($efficiency, 1),
                'vpm'        => round($vpm, 4),
                'gap'        => round($gap, 1),
                'target'     => (float) ($branch->target_attendance_rate ?? 95),
                'budget'     => (float) ($branch->monthly_salary_budget ?? 0),
            ];
        }

        // Sort by efficiency descending
        usort($scores, fn ($a, $b) => $b['efficiency'] <=> $a['efficiency']);
        $this->scores = $scores;
    }

    protected function getViewData(): array
    {
        return [
            'scores' => $this->scores,
        ];
    }
}
