<?php

use App\Jobs\RecalculateMonthlyAttendanceJob;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

/*
|--------------------------------------------------------------------------
| Scheduled Jobs (v4.0)
|--------------------------------------------------------------------------
| إعادة حساب التقارير المالية — أول يوم من كل شهر الساعة 2 فجراً
| تنظيف طابور المهام — أسبوعياً
|--------------------------------------------------------------------------
*/
Schedule::job(RecalculateMonthlyAttendanceJob::forMonth(now()->year, now()->month))
    ->monthlyOn(1, '02:00')
    ->withoutOverlapping()
    ->onOneServer()
    ->description('إعادة حساب التقارير المالية الشهرية');

Schedule::command('queue:flush')
    ->weekly()
    ->description('تنظيف طابور المهام الفاشلة');

