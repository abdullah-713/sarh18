<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employee_patterns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('branch_id')->nullable()->constrained()->nullOnDelete();
            $table->string('pattern_type');         // frequent_late, pre_holiday_absence, monthly_cycle, burnout_risk
            $table->decimal('frequency_score', 5, 2)->default(0);  // 0-100 probability
            $table->decimal('financial_impact', 10, 2)->default(0);
            $table->json('pattern_data')->nullable(); // Serialized analysis data
            $table->text('description_ar');
            $table->text('description_en')->nullable();
            $table->string('risk_level')->default('low'); // low, medium, high, critical
            $table->date('detected_at');
            $table->date('valid_until')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['user_id', 'pattern_type']);
            $table->index(['branch_id', 'risk_level']);
            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employee_patterns');
    }
};
