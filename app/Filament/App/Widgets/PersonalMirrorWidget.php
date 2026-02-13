<?php

namespace App\Filament\App\Widgets;

use App\Services\AnalyticsService;
use Filament\Widgets\Widget;

class PersonalMirrorWidget extends Widget
{
    protected static string $view = 'filament.app.widgets.personal-mirror';

    protected int | string | array $columnSpan = 'full';

    protected static ?int $sort = -1;

    public array $mirror = [];

    public function mount(): void
    {
        $this->loadMirror();
    }

    public function loadMirror(): void
    {
        $user = auth()->user();
        if (!$user) return;

        $service = app(AnalyticsService::class);
        $this->mirror = $service->getPersonalMirror($user);
    }

    protected function getViewData(): array
    {
        return [
            'mirror' => $this->mirror,
        ];
    }
}
