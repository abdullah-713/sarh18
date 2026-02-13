<?php
/**
 * SARH — مسح + إعادة تهيئة + توليد بيانات
 *
 * 1. مسح جميع السجلات التشغيلية
 * 2. ضبط الفروع بعناوينها الأساسية
 * 3. جعل جميع الرواتب 3000 ريال
 * 4. توليد بيانات حضور من 1 فبراير 2026 بمستوى جيد (gauge=7)
 *
 * Usage: php reset_and_generate.php
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\AttendanceLog;
use App\Models\Branch;
use App\Models\User;
use App\Models\Shift;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\DB;

echo "╔══════════════════════════════════════════════════╗\n";
echo "║  SARH — مسح + إعادة تهيئة + توليد بيانات       ║\n";
echo "╚══════════════════════════════════════════════════╝\n\n";

// ═══════════════════════════════════════════════════════════
//  STEP 1: مسح جميع السجلات التشغيلية
// ═══════════════════════════════════════════════════════════
echo "━━━ الخطوة 1: مسح جميع السجلات ━━━\n";

DB::statement('SET FOREIGN_KEY_CHECKS=0');

$tables = [
    'attendance_logs',
    'leave_requests',
    'payrolls',
    'financial_reports',
    'loss_alerts',
    'analytics_snapshots',
    'employee_patterns',
    'points_transactions',
    'score_adjustments',
];

foreach ($tables as $table) {
    try {
        DB::table($table)->truncate();
        echo "  ✓ تم مسح: {$table}\n";
    } catch (\Exception $e) {
        echo "  ⚠ لم يتم مسح {$table}: " . $e->getMessage() . "\n";
    }
}

// Reset employee counters
User::where('is_super_admin', false)->update([
    'total_points'   => 0,
    'current_streak' => 0,
    'longest_streak' => 0,
]);
echo "  ✓ تم تصفير نقاط الموظفين\n";

// Reset branch financial counters
Branch::query()->update(['monthly_delay_losses' => 0]);
echo "  ✓ تم تصفير خسائر الفروع\n";

DB::statement('SET FOREIGN_KEY_CHECKS=1');
echo "\n";

// ═══════════════════════════════════════════════════════════
//  STEP 2: ضبط الفروع بعناوينها الأساسية
// ═══════════════════════════════════════════════════════════
echo "━━━ الخطوة 2: ضبط الفروع بعناوينها الأساسية ━━━\n";

$branchesData = [
    'SARH-HQ' => [
        'name_ar'              => 'صرح الاتقان الرئيسي',
        'name_en'              => 'SARH Al-Itqan HQ',
        'city_ar'              => 'الرياض',
        'city_en'              => 'Riyadh',
        'address_ar'           => 'صرح الاتقان — المقر الرئيسي',
        'address_en'           => 'SARH Al-Itqan — Headquarters',
        'latitude'             => 24.572368,
        'longitude'            => 46.602829,
        'geofence_radius'      => 17,
        'default_shift_start'  => '08:00',
        'default_shift_end'    => '17:00',
        'grace_period_minutes' => 15,
        'is_active'            => true,
    ],
    'SARH-CORNER' => [
        'name_ar'              => 'صرح الاتقان كورنر',
        'name_en'              => 'SARH Al-Itqan Corner',
        'city_ar'              => 'الرياض',
        'city_en'              => 'Riyadh',
        'address_ar'           => 'صرح الاتقان — كورنر',
        'address_en'           => 'SARH Al-Itqan — Corner',
        'latitude'             => 24.572439,
        'longitude'            => 46.603008,
        'geofence_radius'      => 17,
        'default_shift_start'  => '08:00',
        'default_shift_end'    => '17:00',
        'grace_period_minutes' => 15,
        'is_active'            => true,
    ],
    'SARH-2' => [
        'name_ar'              => 'صرح الاتقان 2',
        'name_en'              => 'SARH Al-Itqan 2',
        'city_ar'              => 'الرياض',
        'city_en'              => 'Riyadh',
        'address_ar'           => 'صرح الاتقان — الفرع الثاني',
        'address_en'           => 'SARH Al-Itqan — Branch 2',
        'latitude'             => 24.572262,
        'longitude'            => 46.602580,
        'geofence_radius'      => 17,
        'default_shift_start'  => '08:00',
        'default_shift_end'    => '17:00',
        'grace_period_minutes' => 15,
        'is_active'            => true,
    ],
    'FADA-1' => [
        'name_ar'              => 'فضاء المحركات 1',
        'name_en'              => 'Fada Al-Muharrikat 1',
        'city_ar'              => 'الرياض',
        'city_en'              => 'Riyadh',
        'address_ar'           => 'فضاء المحركات — الفرع الأول',
        'address_en'           => 'Fada Al-Muharrikat — Branch 1',
        'latitude'             => 24.56968126,
        'longitude'            => 46.61405911,
        'geofence_radius'      => 17,
        'default_shift_start'  => '08:00',
        'default_shift_end'    => '17:00',
        'grace_period_minutes' => 15,
        'is_active'            => true,
    ],
    'FADA-2' => [
        'name_ar'              => 'فضاء المحركات 2',
        'name_en'              => 'Fada Al-Muharrikat 2',
        'city_ar'              => 'الرياض',
        'city_en'              => 'Riyadh',
        'address_ar'           => 'فضاء المحركات — الفرع الثاني',
        'address_en'           => 'Fada Al-Muharrikat — Branch 2',
        'latitude'             => 24.566088,
        'longitude'            => 46.621759,
        'geofence_radius'      => 17,
        'default_shift_start'  => '08:00',
        'default_shift_end'    => '17:00',
        'grace_period_minutes' => 15,
        'is_active'            => true,
    ],
];

foreach ($branchesData as $code => $data) {
    $branch = Branch::where('code', $code)->first();
    if ($branch) {
        $branch->update($data);
        echo "  ✓ {$data['name_ar']} ({$code}) — تم التحديث\n";
    } else {
        $data['code'] = $code;
        Branch::create($data);
        echo "  ✓ {$data['name_ar']} ({$code}) — تم الإنشاء\n";
    }
}
echo "\n";

// ═══════════════════════════════════════════════════════════
//  STEP 3: جعل جميع الرواتب 3000 ريال (ما عدا الرئيسي)
// ═══════════════════════════════════════════════════════════
echo "━━━ الخطوة 3: تعيين الرواتب 3000 ريال ━━━\n";

$updatedSalary = User::where('is_super_admin', false)->update([
    'basic_salary'        => 3000,
    'housing_allowance'   => 750,   // 25% of 3000
    'transport_allowance' => 500,
    'other_allowances'    => 0,
]);
echo "  ✓ تم تحديث رواتب {$updatedSalary} موظف (أساسي: 3000 + سكن: 750 + نقل: 500 = 4250 ريال)\n\n";

// ═══════════════════════════════════════════════════════════
//  STEP 4: توليد بيانات حضور من 1 الشهر بمستوى جيد (gauge=7)
// ═══════════════════════════════════════════════════════════
echo "━━━ الخطوة 4: توليد بيانات الحضور ━━━\n";

$dateFrom  = Carbon::parse('2026-02-01');
$dateTo    = Carbon::now();
$gauge     = 7;  // جيد
$weekends  = [5, 6]; // Friday, Saturday
$batchSize = 300;
$batch     = [];
$totalInserted = 0;

echo "  الفترة: {$dateFrom->format('Y-m-d')} → {$dateTo->format('Y-m-d')}\n";
echo "  مستوى الانضباط: {$gauge} (جيد)\n";
echo "  أيام العطلة: الجمعة + السبت\n\n";

$branches = Branch::where('is_active', true)->get();

foreach ($branches as $branch) {
    $users = User::where('branch_id', $branch->id)
        ->where('is_super_admin', false)
        ->where('status', 'active')
        ->get();

    if ($users->isEmpty()) {
        echo "  ⚠ {$branch->name_ar}: لا يوجد موظفون نشطون\n";
        continue;
    }

    $shiftStart    = $branch->default_shift_start ?? '08:00';
    $shiftEnd      = $branch->default_shift_end ?? '17:00';
    $graceMinutes  = $branch->grace_period_minutes ?? 15;
    $branchRecords = 0;

    $period = CarbonPeriod::create($dateFrom, $dateTo);

    foreach ($period as $day) {
        // Skip weekends
        if (in_array($day->dayOfWeek, $weekends)) continue;

        foreach ($users as $user) {
            $record = generateAttendanceRecord(
                $user, $branch, $day, $shiftStart, $shiftEnd, $graceMinutes, $gauge
            );

            $batch[] = $record;
            $branchRecords++;

            if (count($batch) >= $batchSize) {
                AttendanceLog::insert($batch);
                $totalInserted += count($batch);
                $batch = [];
            }
        }
    }

    echo "  ✓ {$branch->name_ar}: {$users->count()} موظف × أيام العمل = {$branchRecords} سجل\n";
}

// Insert remaining
if (!empty($batch)) {
    AttendanceLog::insert($batch);
    $totalInserted += count($batch);
}

echo "\n";
echo "╔══════════════════════════════════════════════════╗\n";
echo "║  ✅ تم بنجاح!                                   ║\n";
echo "║  إجمالي السجلات المولّدة: {$totalInserted}                ║\n";
echo "╚══════════════════════════════════════════════════╝\n";

// ═══════════════════════════════════════════════════════════
//  FUNCTIONS
// ═══════════════════════════════════════════════════════════

function generateAttendanceRecord(
    User $user,
    Branch $branch,
    Carbon $day,
    string $shiftStart,
    string $shiftEnd,
    int $graceMinutes,
    int $gauge
): array {
    $now = now();
    $shiftStartTime = Carbon::parse($day->format('Y-m-d') . ' ' . $shiftStart);
    $shiftEndTime   = Carbon::parse($day->format('Y-m-d') . ' ' . $shiftEnd);

    $scenario = determineScenario($gauge);

    $salary        = (float) ($user->basic_salary ?? 3000);
    $workingDays   = $user->working_days_per_month ?? 22;
    $hoursPerDay   = $user->working_hours_per_day ?? 8;
    $costPerMinute = round($salary / ($workingDays * $hoursPerDay * 60), 4);

    $gpsData = generateHaversineCoordinates($branch, $gauge);

    switch ($scenario) {
        case 'absent':
            return [
                'user_id'               => $user->id,
                'branch_id'             => $branch->id,
                'attendance_date'       => $day->format('Y-m-d'),
                'check_in_at'           => null,
                'check_out_at'          => null,
                'status'                => 'absent',
                'delay_minutes'         => 0,
                'early_leave_minutes'   => 0,
                'overtime_minutes'      => 0,
                'worked_minutes'        => 0,
                'cost_per_minute'       => $costPerMinute,
                'delay_cost'            => round($costPerMinute * $hoursPerDay * 60, 2),
                'early_leave_cost'      => 0,
                'overtime_value'        => 0,
                'check_in_latitude'     => $gpsData['latitude'],
                'check_in_longitude'    => $gpsData['longitude'],
                'check_in_within_geofence' => $gpsData['within_geofence'],
                'check_in_distance_meters' => $gpsData['distance_meters'],
                'check_in_ip'           => '192.168.' . rand(1, 255) . '.' . rand(1, 255),
                'check_in_device'       => 'SARH Demo Generator',
                'notes'                 => 'سجل تجريبي — غائب',
                'is_manual_entry'       => false,
                'created_at'            => $now,
                'updated_at'            => $now,
            ];

        case 'late':
            $lateMinutes = generateLateMinutes($gauge);
            $checkIn     = $shiftStartTime->copy()->addMinutes($graceMinutes + $lateMinutes);
            $earlyLeaveChance = max(0, (10 - $gauge) * 5);
            $earlyLeaveMin    = rand(0, 100) < $earlyLeaveChance ? rand(5, 30) : 0;
            $checkOut         = $shiftEndTime->copy()->subMinutes($earlyLeaveMin);
            $workedMinutes    = max(0, (int) $checkIn->diffInMinutes($checkOut));
            $delayCost        = round($lateMinutes * $costPerMinute, 2);
            $earlyLeaveCost   = round($earlyLeaveMin * $costPerMinute, 2);

            return [
                'user_id'               => $user->id,
                'branch_id'             => $branch->id,
                'attendance_date'       => $day->format('Y-m-d'),
                'check_in_at'           => $checkIn->format('Y-m-d H:i:s'),
                'check_out_at'          => $checkOut->format('Y-m-d H:i:s'),
                'status'                => 'late',
                'delay_minutes'         => $lateMinutes,
                'early_leave_minutes'   => $earlyLeaveMin,
                'overtime_minutes'      => 0,
                'worked_minutes'        => $workedMinutes,
                'cost_per_minute'       => $costPerMinute,
                'delay_cost'            => $delayCost,
                'early_leave_cost'      => $earlyLeaveCost,
                'overtime_value'        => 0,
                'check_in_latitude'     => $gpsData['latitude'],
                'check_in_longitude'    => $gpsData['longitude'],
                'check_in_within_geofence' => $gpsData['within_geofence'],
                'check_in_distance_meters' => $gpsData['distance_meters'],
                'check_in_ip'           => '192.168.' . rand(1, 255) . '.' . rand(1, 255),
                'check_in_device'       => 'SARH Demo Generator',
                'notes'                 => "سجل تجريبي — تأخير {$lateMinutes} دقيقة",
                'is_manual_entry'       => false,
                'created_at'            => $now,
                'updated_at'            => $now,
            ];

        case 'overtime':
            $earlyArrival    = rand(0, 5);
            $checkIn         = $shiftStartTime->copy()->subMinutes($earlyArrival);
            $overtimeMinutes = rand(15, 90);
            $checkOut        = $shiftEndTime->copy()->addMinutes($overtimeMinutes);
            $workedMinutes   = max(0, (int) $checkIn->diffInMinutes($checkOut));
            $overtimeValue   = round($overtimeMinutes * $costPerMinute * 1.5, 2);

            return [
                'user_id'               => $user->id,
                'branch_id'             => $branch->id,
                'attendance_date'       => $day->format('Y-m-d'),
                'check_in_at'           => $checkIn->format('Y-m-d H:i:s'),
                'check_out_at'          => $checkOut->format('Y-m-d H:i:s'),
                'status'                => 'present',
                'delay_minutes'         => 0,
                'early_leave_minutes'   => 0,
                'overtime_minutes'      => $overtimeMinutes,
                'worked_minutes'        => $workedMinutes,
                'cost_per_minute'       => $costPerMinute,
                'delay_cost'            => 0,
                'early_leave_cost'      => 0,
                'overtime_value'        => $overtimeValue,
                'check_in_latitude'     => $gpsData['latitude'],
                'check_in_longitude'    => $gpsData['longitude'],
                'check_in_within_geofence' => $gpsData['within_geofence'],
                'check_in_distance_meters' => $gpsData['distance_meters'],
                'check_in_ip'           => '192.168.' . rand(1, 255) . '.' . rand(1, 255),
                'check_in_device'       => 'SARH Demo Generator',
                'notes'                 => "سجل تجريبي — عمل إضافي {$overtimeMinutes} دقيقة",
                'is_manual_entry'       => false,
                'created_at'            => $now,
                'updated_at'            => $now,
            ];

        case 'present':
        default:
            $variance   = rand(-3, max(1, (int) ($graceMinutes * 0.5)));
            $checkIn    = $shiftStartTime->copy()->addMinutes($variance);
            $endVariance = rand(-5, 5);
            $checkOut   = $shiftEndTime->copy()->addMinutes($endVariance);
            $workedMinutes = max(0, (int) $checkIn->diffInMinutes($checkOut));

            return [
                'user_id'               => $user->id,
                'branch_id'             => $branch->id,
                'attendance_date'       => $day->format('Y-m-d'),
                'check_in_at'           => $checkIn->format('Y-m-d H:i:s'),
                'check_out_at'          => $checkOut->format('Y-m-d H:i:s'),
                'status'                => 'present',
                'delay_minutes'         => 0,
                'early_leave_minutes'   => max(0, -$endVariance),
                'overtime_minutes'      => 0,
                'worked_minutes'        => $workedMinutes,
                'cost_per_minute'       => $costPerMinute,
                'delay_cost'            => 0,
                'early_leave_cost'      => round(max(0, -$endVariance) * $costPerMinute, 2),
                'overtime_value'        => 0,
                'check_in_latitude'     => $gpsData['latitude'],
                'check_in_longitude'    => $gpsData['longitude'],
                'check_in_within_geofence' => $gpsData['within_geofence'],
                'check_in_distance_meters' => $gpsData['distance_meters'],
                'check_in_ip'           => '192.168.' . rand(1, 255) . '.' . rand(1, 255),
                'check_in_device'       => 'SARH Demo Generator',
                'notes'                 => null,
                'is_manual_entry'       => false,
                'created_at'            => $now,
                'updated_at'            => $now,
            ];
    }
}

function determineScenario(int $gauge): string
{
    $roll = rand(1, 100);
    $absentChance   = max(0, (10 - $gauge) * 2.5);   // gauge 7 → 7.5%
    $lateChance     = max(0, (10 - $gauge) * 5);      // gauge 7 → 15%
    $overtimeChance = min(15, $gauge * 1.5);           // gauge 7 → 10.5%

    if ($roll <= $absentChance) return 'absent';
    if ($roll <= $absentChance + $lateChance) return 'late';
    if ($roll <= $absentChance + $lateChance + $overtimeChance) return 'overtime';
    return 'present';
}

function generateLateMinutes(int $gauge): int
{
    return match (true) {
        $gauge >= 9 => rand(1, 5),
        $gauge >= 7 => rand(3, 20),
        $gauge >= 5 => rand(5, 45),
        $gauge >= 3 => rand(15, 90),
        default     => rand(30, 180),
    };
}

function generateHaversineCoordinates(Branch $branch, int $gauge): array
{
    $radius = (int) $branch->geofence_radius;

    $maxDistance = match (true) {
        $gauge >= 9 => $radius * 0.3,
        $gauge >= 7 => $radius * 0.6,
        $gauge >= 5 => $radius * 0.9,
        $gauge >= 3 => $radius * 1.3,
        default     => $radius * 2.0,
    };

    $bearing  = deg2rad(rand(0, 360));
    $distance = rand(0, (int) $maxDistance);

    $earthRadius = 6371000;
    $branchLat = deg2rad((float) $branch->latitude);
    $branchLng = deg2rad((float) $branch->longitude);

    $newLat = asin(
        sin($branchLat) * cos($distance / $earthRadius)
        + cos($branchLat) * sin($distance / $earthRadius) * cos($bearing)
    );

    $newLng = $branchLng + atan2(
        sin($bearing) * sin($distance / $earthRadius) * cos($branchLat),
        cos($distance / $earthRadius) - sin($branchLat) * sin($newLat)
    );

    $lat = round(rad2deg($newLat), 7);
    $lng = round(rad2deg($newLng), 7);

    $actualDistance = $branch->distanceTo($lat, $lng);
    $withinGeofence = $actualDistance <= $radius;

    return [
        'latitude'        => $lat,
        'longitude'       => $lng,
        'distance_meters' => (int) $actualDistance,
        'within_geofence' => $withinGeofence,
    ];
}
