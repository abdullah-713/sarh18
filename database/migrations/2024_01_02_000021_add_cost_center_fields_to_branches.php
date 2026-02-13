<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('branches', function (Blueprint $table) {
            if (!Schema::hasColumn('branches', 'cost_center_code')) {
                $table->string('cost_center_code')->nullable()->after('email');
            }
            if (!Schema::hasColumn('branches', 'annual_budget')) {
                $table->decimal('annual_budget', 12, 2)->default(0)->after('monthly_delay_losses');
            }
            if (!Schema::hasColumn('branches', 'target_attendance_rate')) {
                $table->decimal('target_attendance_rate', 5, 2)->default(95.00)->after('annual_budget');
            }
            if (!Schema::hasColumn('branches', 'max_acceptable_loss_percent')) {
                $table->decimal('max_acceptable_loss_percent', 5, 2)->default(5.00)->after('target_attendance_rate');
            }
            if (!Schema::hasColumn('branches', 'vpm_target')) {
                $table->decimal('vpm_target', 8, 2)->default(100.00)->after('max_acceptable_loss_percent');
            }
        });
    }

    public function down(): void
    {
        Schema::table('branches', function (Blueprint $table) {
            $table->dropColumn([
                'cost_center_code',
                'annual_budget',
                'target_attendance_rate',
                'max_acceptable_loss_percent',
                'vpm_target',
            ]);
        });
    }
};
