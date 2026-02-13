<?php

namespace App\Filament\Widgets;

use App\Models\AttendanceLog;
use App\Models\Branch;
use App\Models\User;
use Filament\Widgets\Widget;

class DailyNewsTicker extends Widget
{
    protected static string $view = 'filament.widgets.daily-news-ticker';

    protected int | string | array $columnSpan = 'full';

    protected static ?int $sort = -1;

    /**
     * Per-branch first check-in (trophy) and last check-in (turtle) for today.
     * Uses check_in_at (datetime) and attendance_date (date) columns.
     */
    public function getBranchHighlights(): array
    {
        $today = now()->toDateString();
        $user  = auth()->user();
        $scoped = $user && !$user->is_super_admin && $user->security_level < 10;

        $branchQuery = Branch::where('is_active', true);
        if ($scoped) $branchQuery->where('id', $user->branch_id);
        $branches = $branchQuery->get();
        $highlights = [];

        foreach ($branches as $branch) {
            $first = AttendanceLog::where('branch_id', $branch->id)
                ->where('attendance_date', $today)
                ->whereNotNull('check_in_at')
                ->orderBy('check_in_at', 'asc')
                ->first();

            $last = AttendanceLog::where('branch_id', $branch->id)
                ->where('attendance_date', $today)
                ->whereNotNull('check_in_at')
                ->orderBy('check_in_at', 'desc')
                ->first();

            $firstUser = $first ? User::find($first->user_id) : null;
            $lastUser  = $last  ? User::find($last->user_id)  : null;

            // Only add if we have at least one check-in
            if ($first) {
                $highlights[] = [
                    'branch'     => $branch->name_ar,
                    'branch_en'  => $branch->name_en,
                    'first_name' => $firstUser?->name_ar ?? '—',
                    'first_time' => $first->check_in_at ? \Carbon\Carbon::parse($first->check_in_at)->format('H:i') : '—',
                    'last_name'  => ($lastUser && $lastUser->id !== $firstUser?->id) ? $lastUser->name_ar : null,
                    'last_time'  => ($last && $last->id !== $first->id) ? \Carbon\Carbon::parse($last->check_in_at)->format('H:i') : null,
                ];
            }
        }

        return $highlights;
    }

    /**
     * Scrolling news items.
     */
    public function getNewsItems(): array
    {
        $items = [];
        $today = now()->toDateString();
        $user  = auth()->user();
        $scoped = $user && !$user->is_super_admin && $user->security_level < 10;

        // Today attendance stats
        $todayLates = AttendanceLog::where('attendance_date', $today)
            ->where('delay_minutes', '>', 0)
            ->when($scoped, fn ($q) => $q->where('branch_id', $user->branch_id))
            ->count();

        $todayOnTime = AttendanceLog::where('attendance_date', $today)
            ->where(function ($q) {
                $q->where('delay_minutes', '<=', 0)->orWhereNull('delay_minutes');
            })
            ->whereNotNull('check_in_at')
            ->when($scoped, fn ($q) => $q->where('branch_id', $user->branch_id))
            ->count();

        if ($todayOnTime + $todayLates > 0) {
            $items[] = [
                'icon'  => "\u{1F4CA}",
                'text'  => __('competition.ticker_attendance', ['on_time' => $todayOnTime, 'late' => $todayLates]),
                'color' => 'text-blue-600',
            ];
        }

        // Total active employees
        $totalEmployees = User::where('status', 'active')
            ->when($scoped, fn ($q) => $q->where('branch_id', $user->branch_id))
            ->count();
        $items[] = [
            'icon'  => "\u{1F465}",
            'text'  => __('competition.ticker_total_employees', ['count' => $totalEmployees]),
            'color' => 'text-gray-600',
        ];

        // Top scorer
        $topScorer = User::where('status', 'active')
            ->where('total_points', '>', 0)
            ->when($scoped, fn ($q) => $q->where('branch_id', $user->branch_id))
            ->orderByDesc('total_points')
            ->first();

        if ($topScorer) {
            $items[] = [
                'icon'  => "\u{2B50}",
                'text'  => __('competition.ticker_top_scorer', ['name' => $topScorer->name_ar, 'points' => $topScorer->total_points]),
                'color' => 'text-amber-600',
            ];
        }

        return $items;
    }
}
