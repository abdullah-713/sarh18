<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('loss_alerts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->constrained()->cascadeOnDelete();
            $table->foreignId('triggered_by_user')->nullable()->constrained('users')->nullOnDelete();
            $table->date('alert_date');
            $table->string('alert_type');          // threshold_exceeded, pattern_detected, anomaly
            $table->string('severity')->default('medium'); // low, medium, high, critical
            $table->decimal('threshold_value', 8, 2)->nullable();
            $table->decimal('actual_value', 8, 2)->nullable();
            $table->text('description_ar');
            $table->text('description_en')->nullable();
            $table->json('context_data')->nullable(); // Additional data for analysis
            $table->boolean('is_acknowledged')->default(false);
            $table->foreignId('acknowledged_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('acknowledged_at')->nullable();
            $table->text('resolution_notes')->nullable();
            $table->timestamps();

            $table->index(['branch_id', 'alert_date']);
            $table->index(['alert_type', 'severity']);
            $table->index('is_acknowledged');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('loss_alerts');
    }
};
