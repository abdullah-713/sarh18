<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Payroll extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'branch_id',
        'period',
        'basic_salary',
        'housing_allowance',
        'transport_allowance',
        'other_allowances',
        'gross_salary',
        'delay_deductions',
        'early_leave_deductions',
        'absence_deductions',
        'other_deductions',
        'total_deductions',
        'overtime_pay',
        'bonuses',
        'total_additions',
        'net_salary',
        'total_working_days',
        'present_days',
        'absent_days',
        'late_days',
        'total_delay_minutes',
        'total_overtime_minutes',
        'status',
        'approved_by',
        'approved_at',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'basic_salary'            => 'decimal:2',
            'housing_allowance'       => 'decimal:2',
            'transport_allowance'     => 'decimal:2',
            'other_allowances'        => 'decimal:2',
            'gross_salary'            => 'decimal:2',
            'delay_deductions'        => 'decimal:2',
            'early_leave_deductions'  => 'decimal:2',
            'absence_deductions'      => 'decimal:2',
            'other_deductions'        => 'decimal:2',
            'total_deductions'        => 'decimal:2',
            'overtime_pay'            => 'decimal:2',
            'bonuses'                 => 'decimal:2',
            'total_additions'         => 'decimal:2',
            'net_salary'              => 'decimal:2',
            'approved_at'             => 'datetime',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | RELATIONSHIPS
    |--------------------------------------------------------------------------
    */

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /*
    |--------------------------------------------------------------------------
    | CALCULATION ENGINE
    |--------------------------------------------------------------------------
    */

    /**
     * Generate payroll from attendance data for a given period.
     */
    public static function generateForUser(User $user, string $period): self
    {
        $year  = (int) substr($period, 0, 4);
        $month = (int) substr($period, 5, 2);
        $startDate = \Carbon\Carbon::create($year, $month, 1)->startOfMonth();
        $endDate   = $startDate->copy()->endOfMonth();

        // Fetch attendance logs
        $logs = AttendanceLog::where('user_id', $user->id)
            ->whereBetween('attendance_date', [$startDate, $endDate])
            ->get();

        $presentDays    = $logs->where('status', 'present')->count();
        $lateDays       = $logs->where('status', 'late')->count();
        $absentDays     = $logs->where('status', 'absent')->count();
        $totalDelayMin  = (int) $logs->sum('delay_minutes');
        $totalOTMin     = (int) $logs->sum('overtime_minutes');

        $grossSalary = ($user->basic_salary ?? 0)
                     + ($user->housing_allowance ?? 0)
                     + ($user->transport_allowance ?? 0)
                     + ($user->other_allowances ?? 0);

        $costPerMinute = $user->cost_per_minute ?? 0;
        $delayDeductions     = round($totalDelayMin * $costPerMinute, 2);
        $earlyLeaveDeductions = round((float) $logs->sum('early_leave_cost'), 2);
        $absenceDeductions   = round($absentDays * ($grossSalary / max($user->working_days_per_month ?? 22, 1)), 2);
        $totalDeductions     = $delayDeductions + $earlyLeaveDeductions + $absenceDeductions;

        $overtimePay     = round($totalOTMin * $costPerMinute * 1.5, 2);
        $totalAdditions  = $overtimePay;
        $netSalary       = $grossSalary - $totalDeductions + $totalAdditions;

        return self::updateOrCreate(
            ['user_id' => $user->id, 'period' => $period],
            [
                'branch_id'              => $user->branch_id,
                'basic_salary'           => $user->basic_salary ?? 0,
                'housing_allowance'      => $user->housing_allowance ?? 0,
                'transport_allowance'    => $user->transport_allowance ?? 0,
                'other_allowances'       => $user->other_allowances ?? 0,
                'gross_salary'           => $grossSalary,
                'delay_deductions'       => $delayDeductions,
                'early_leave_deductions' => $earlyLeaveDeductions,
                'absence_deductions'     => $absenceDeductions,
                'total_deductions'       => $totalDeductions,
                'overtime_pay'           => $overtimePay,
                'bonuses'                => 0,
                'total_additions'        => $totalAdditions,
                'net_salary'             => $netSalary,
                'total_working_days'     => $presentDays + $lateDays + $absentDays,
                'present_days'           => $presentDays + $lateDays,
                'absent_days'            => $absentDays,
                'late_days'              => $lateDays,
                'total_delay_minutes'    => $totalDelayMin,
                'total_overtime_minutes' => $totalOTMin,
                'status'                 => 'draft',
            ]
        );
    }

    /*
    |--------------------------------------------------------------------------
    | SCOPES
    |--------------------------------------------------------------------------
    */

    public function scopeForPeriod($query, string $period)
    {
        return $query->where('period', $period);
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }
}
