<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('analytics_snapshots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->constrained()->cascadeOnDelete();
            $table->date('snapshot_date');
            $table->string('period_type')->default('daily'); // daily, weekly, monthly

            // Attendance metrics
            $table->integer('total_employees')->default(0);
            $table->integer('present_count')->default(0);
            $table->integer('absent_count')->default(0);
            $table->integer('late_count')->default(0);
            $table->decimal('attendance_rate', 5, 2)->default(0);
            $table->integer('total_delay_minutes')->default(0);
            $table->decimal('avg_delay_minutes', 8, 2)->default(0);

            // Financial metrics
            $table->decimal('total_salary_cost', 12, 2)->default(0);
            $table->decimal('delay_losses', 10, 2)->default(0);
            $table->decimal('absence_losses', 10, 2)->default(0);
            $table->decimal('early_leave_losses', 10, 2)->default(0);
            $table->decimal('total_losses', 12, 2)->default(0);
            $table->decimal('overtime_cost', 10, 2)->default(0);

            // KPI metrics
            $table->decimal('vpm', 10, 2)->default(0);             // Value Per Minute
            $table->decimal('productivity_gap', 5, 2)->default(0); // % gap from target
            $table->decimal('loss_ratio', 5, 2)->default(0);       // total_losses / total_salary_cost
            $table->decimal('efficiency_score', 5, 2)->default(0); // 0-100
            $table->decimal('roi_discipline', 8, 2)->default(0);   // ratio

            // Heatmap data (JSON: hour => count)
            $table->json('hourly_checkin_distribution')->nullable();
            $table->json('daily_pattern_data')->nullable();

            $table->timestamps();

            $table->unique(['branch_id', 'snapshot_date', 'period_type'], 'analytics_unique');
            $table->index(['snapshot_date', 'period_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('analytics_snapshots');
    }
};
