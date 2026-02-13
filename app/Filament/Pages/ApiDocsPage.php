<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Illuminate\Support\Facades\Gate;

class ApiDocsPage extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-code-bracket';

    protected static ?int $navigationSort = 99;

    protected static string $view = 'filament.pages.api-docs';

    public static function getNavigationGroup(): ?string
    {
        return 'الإعدادات';
    }

    public static function getNavigationLabel(): string
    {
        return 'توثيق API';
    }

    public function getTitle(): string
    {
        return 'توثيق نقاط API';
    }

    public static function canAccess(): bool
    {
        $user = auth()->user();

        return $user && ($user->security_level >= 7 || $user->is_super_admin);
    }

    public static function shouldRegisterNavigation(): bool
    {
        return static::canAccess();
    }
}
