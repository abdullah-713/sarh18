<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LossAlert extends Model
{
    use HasFactory;

    protected $fillable = [
        'branch_id',
        'triggered_by_user',
        'alert_date',
        'alert_type',
        'severity',
        'threshold_value',
        'actual_value',
        'description_ar',
        'description_en',
        'context_data',
        'is_acknowledged',
        'acknowledged_by',
        'acknowledged_at',
        'resolution_notes',
    ];

    protected function casts(): array
    {
        return [
            'alert_date'      => 'date',
            'threshold_value' => 'decimal:2',
            'actual_value'    => 'decimal:2',
            'context_data'    => 'array',
            'is_acknowledged' => 'boolean',
            'acknowledged_at' => 'datetime',
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

    public function triggeredByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'triggered_by_user');
    }

    public function acknowledgedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'acknowledged_by');
    }

    /*
    |--------------------------------------------------------------------------
    | SCOPES
    |--------------------------------------------------------------------------
    */

    public function scopeUnacknowledged($query)
    {
        return $query->where('is_acknowledged', false);
    }

    public function scopeCritical($query)
    {
        return $query->where('severity', 'critical');
    }

    public function scopeForBranch($query, int $branchId)
    {
        return $query->where('branch_id', $branchId);
    }

    public function scopeRecent($query, int $days = 7)
    {
        return $query->where('alert_date', '>=', now()->subDays($days));
    }

    /*
    |--------------------------------------------------------------------------
    | METHODS
    |--------------------------------------------------------------------------
    */

    public function acknowledge(int $userId, ?string $notes = null): void
    {
        $this->update([
            'is_acknowledged' => true,
            'acknowledged_by' => $userId,
            'acknowledged_at' => now(),
            'resolution_notes' => $notes,
        ]);
    }

    public function getSeverityColor(): string
    {
        return match ($this->severity) {
            'critical' => 'danger',
            'high'     => 'warning',
            'medium'   => 'info',
            'low'      => 'gray',
            default    => 'gray',
        };
    }

    public function getSeverityLabel(): string
    {
        return match ($this->severity) {
            'critical' => 'حرج',
            'high'     => 'عالي',
            'medium'   => 'متوسط',
            'low'      => 'منخفض',
            default    => 'غير محدد',
        };
    }
}
