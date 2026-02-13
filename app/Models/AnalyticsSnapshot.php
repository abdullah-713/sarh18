<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AnalyticsSnapshot extends Model
{
    use HasFactory;

    protected $fillable = [
        'branch_id',
        'snapshot_date',
        'period_type',
        'total_employees',
        'present_count',
        'absent_count',
        'late_count',
        'attendance_rate',
        'total_delay_minutes',
        'avg_delay_minutes',
        'total_salary_cost',
        'delay_losses',
        'absence_losses',
        'early_leave_losses',
        'total_losses',
        'overtime_cost',
        'vpm',
        'productivity_gap',
        'loss_ratio',
        'efficiency_score',
        'roi_discipline',
        'hourly_checkin_distribution',
        'daily_pattern_data',
    ];

    protected function casts(): array
    {
        return [
            'snapshot_date'               => 'date',
            'attendance_rate'             => 'decimal:2',
            'avg_delay_minutes'           => 'decimal:2',
            'total_salary_cost'           => 'decimal:2',
            'delay_losses'                => 'decimal:2',
            'absence_losses'              => 'decimal:2',
            'early_leave_losses'          => 'decimal:2',
            'total_losses'                => 'decimal:2',
            'overtime_cost'               => 'decimal:2',
            'vpm'                         => 'decimal:2',
            'productivity_gap'            => 'decimal:2',
            'loss_ratio'                  => 'decimal:2',
            'efficiency_score'            => 'decimal:2',
            'roi_discipline'              => 'decimal:2',
            'hourly_checkin_distribution' => 'array',
            'daily_pattern_data'          => 'array',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | RELATIONSHIPS
    |--------------------------------------------------------------------------
    */

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    /*
    |--------------------------------------------------------------------------
    | SCOPES
    |--------------------------------------------------------------------------
    */

    public function scopeDaily($query)
    {
        return $query->where('period_type', 'daily');
    }

    public function scopeWeekly($query)
    {
        return $query->where('period_type', 'weekly');
    }

    public function scopeMonthly($query)
    {
        return $query->where('period_type', 'monthly');
    }

    public function scopeForBranch($query, int $branchId)
    {
        return $query->where('branch_id', $branchId);
    }

    public function scopeForDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('snapshot_date', [$startDate, $endDate]);
    }

    /*
    |--------------------------------------------------------------------------
    | HELPERS
    |--------------------------------------------------------------------------
    */

    public function getLossPercentage(): float
    {
        if ($this->total_salary_cost <= 0) {
            return 0;
        }
        return round(($this->total_losses / $this->total_salary_cost) * 100, 2);
    }

    public function isAboveThreshold(float $threshold = 5.0): bool
    {
        return $this->getLossPercentage() > $threshold;
    }
}
