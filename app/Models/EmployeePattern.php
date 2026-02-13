<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeePattern extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'branch_id',
        'pattern_type',
        'frequency_score',
        'financial_impact',
        'pattern_data',
        'description_ar',
        'description_en',
        'risk_level',
        'detected_at',
        'valid_until',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'frequency_score'  => 'decimal:2',
            'financial_impact' => 'decimal:2',
            'pattern_data'     => 'array',
            'detected_at'      => 'date',
            'valid_until'      => 'date',
            'is_active'        => 'boolean',
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

    /*
    |--------------------------------------------------------------------------
    | SCOPES
    |--------------------------------------------------------------------------
    */

    public function scopeActive($query)
    {
        return $query->where('is_active', true)
                     ->where(function ($q) {
                         $q->whereNull('valid_until')
                           ->orWhere('valid_until', '>=', now());
                     });
    }

    public function scopeHighRisk($query)
    {
        return $query->whereIn('risk_level', ['high', 'critical']);
    }

    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeByType($query, string $type)
    {
        return $query->where('pattern_type', $type);
    }

    /*
    |--------------------------------------------------------------------------
    | PATTERN TYPES
    |--------------------------------------------------------------------------
    */

    public static function patternTypes(): array
    {
        return [
            'frequent_late'       => 'تأخير متكرر',
            'pre_holiday_absence' => 'غياب ما قبل الإجازة',
            'monthly_cycle'       => 'نمط شهري',
            'burnout_risk'        => 'خطر إرهاق',
            'improving'           => 'تحسّن ملحوظ',
            'declining'           => 'تراجع مستمر',
        ];
    }

    public function getPatternLabel(): string
    {
        return self::patternTypes()[$this->pattern_type] ?? $this->pattern_type;
    }

    public function getRiskColor(): string
    {
        return match ($this->risk_level) {
            'critical' => 'danger',
            'high'     => 'warning',
            'medium'   => 'info',
            'low'      => 'success',
            default    => 'gray',
        };
    }
}
