<?php

namespace App\ML;

use App\Models\AttendanceLog;
use App\Models\User;
use Carbon\Carbon;

/**
 * SARH v4.0 — نظام التنبؤ بمغادرة الموظفين (Churn Prediction)
 *
 * يحسب درجة خطر مغادرة الموظف بناءً على:
 * - نمط التأخر الشهري
 * - الانصراف المبكر المتكرر
 * - الغياب المتكرر
 * - انخفاض النقاط أو عدم وجود شارات
 *
 * TODO v5.0: ربط مع نموذج ML فعلي (Python/ONNX)
 */
class ChurnPredictor
{
    /**
     * حساب درجة خطر المغادرة
     *
     * @return string 'low' | 'medium' | 'high' | 'critical'
     */
    public function calculateRisk(User $user): string
    {
        $score = 0;

        // 1. تحليل التأخر (آخر 30 يوم)
        $thirtyDaysAgo = Carbon::now()->subDays(30);
        $recentLogs = AttendanceLog::where('user_id', $user->id)
            ->where('attendance_date', '>=', $thirtyDaysAgo)
            ->get();

        if ($recentLogs->isEmpty()) {
            // لا يوجد بيانات كافية
            return 'low';
        }

        $lateDays = $recentLogs->where('status', 'late')->count();
        $absentDays = $recentLogs->where('status', 'absent')->count();
        $earlyLeaveDays = $recentLogs->where('early_leave_minutes', '>', 0)->count();
        $totalDays = $recentLogs->count();

        // 2. نسبة التأخر
        if ($totalDays > 0) {
            $lateRatio = $lateDays / $totalDays;
            if ($lateRatio > 0.5) {
                $score += 30;
            } elseif ($lateRatio > 0.3) {
                $score += 20;
            } elseif ($lateRatio > 0.15) {
                $score += 10;
            }
        }

        // 3. الغياب
        if ($absentDays >= 5) {
            $score += 30;
        } elseif ($absentDays >= 3) {
            $score += 20;
        } elseif ($absentDays >= 1) {
            $score += 10;
        }

        // 4. الانصراف المبكر المتكرر
        if ($earlyLeaveDays >= 5) {
            $score += 15;
        } elseif ($earlyLeaveDays >= 3) {
            $score += 10;
        }

        // 5. قلة النقاط
        if (($user->total_points ?? 0) <= 0) {
            $score += 15;
        } elseif (($user->total_points ?? 0) < 20) {
            $score += 5;
        }

        // 6. حساب المستوى
        return match (true) {
            $score >= 70 => 'critical',
            $score >= 45 => 'high',
            $score >= 20 => 'medium',
            default      => 'low',
        };
    }

    /**
     * حساب تفاصيل المخاطر لعرضها في الداشبورد
     */
    public function getRiskDetails(User $user): array
    {
        $risk = $this->calculateRisk($user);

        return [
            'user_id'    => $user->id,
            'risk_level' => $risk,
            'label_ar'   => match ($risk) {
                'critical' => 'خطر حرج — احتمال مغادرة عالي جداً',
                'high'     => 'خطر مرتفع — يحتاج متابعة فورية',
                'medium'   => 'خطر متوسط — يحتاج مراقبة',
                'low'      => 'خطر منخفض — مستقر',
            },
        ];
    }
}
