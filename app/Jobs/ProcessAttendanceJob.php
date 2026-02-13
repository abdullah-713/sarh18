<?php

namespace App\Jobs;

use App\Models\AttendanceLog;
use App\Models\User;
use App\Services\GeofencingService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessAttendanceJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, SerializesModels;

    public int $timeout = 30;
    public int $tries = 3;

    public function __construct(
        protected User $user,
        protected float $latitude,
        protected float $longitude,
        protected ?string $ip = null,
        protected ?string $device = null,
    ) {}

    public function handle(GeofencingService $geofencingService): void
    {
        // 1. جلب الفرع
        $branch = $this->user->branch;
        if (! $branch) {
            Log::error('ProcessAttendanceJob: موظف بدون فرع', ['user_id' => $this->user->id]);
            return;
        }

        // 2. التحقق من الموقع
        try {
            $geofence = $geofencingService->validatePosition($branch, $this->latitude, $this->longitude);
        } catch (\Exception $e) {
            Log::warning('ProcessAttendanceJob: فشل التحقق من الموقع', [
                'user_id' => $this->user->id,
                'error'   => $e->getMessage(),
            ]);
            return;
        }

        // 3. جلب الوردية
        $shift = $this->user->currentShift();
        $shiftStart = $shift?->start_time ?? $branch->default_shift_start;
        $grace = $shift?->grace_period_minutes ?? $branch->grace_period_minutes ?? 5;

        if (! $shiftStart) {
            Log::error('ProcessAttendanceJob: لا توجد وردية للموظف', ['user_id' => $this->user->id]);
            return;
        }

        // 4. إنشاء سجل الحضور
        try {
            $log = new AttendanceLog([
                'user_id'                   => $this->user->id,
                'branch_id'                 => $branch->id,
                'attendance_date'           => now()->toDateString(),
                'check_in_at'              => now(),
                'check_in_latitude'        => $this->latitude,
                'check_in_longitude'       => $this->longitude,
                'check_in_distance_meters' => $geofence['distance_meters'],
                'check_in_within_geofence' => $geofence['within_geofence'],
                'check_in_ip'              => $this->ip,
                'check_in_device'          => $this->device,
            ]);

            // تقييم الحضور
            $log->evaluateAttendance($shiftStart, $grace);

            // اللقطة المالية
            $log->calculateFinancials();

            $log->save();

            // v4.0: إطلاق حدث تسجيل الحضور
            event(new \App\Events\AttendanceRecorded($log));
        } catch (\Exception $e) {
            Log::error('ProcessAttendanceJob: فشل في حفظ سجل الحضور', [
                'user_id' => $this->user->id,
                'error'   => $e->getMessage(),
            ]);
        }
    }
}
