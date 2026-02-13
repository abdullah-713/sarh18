<?php

namespace Tests\Feature;

use App\Models\Branch;
use App\Models\User;
use App\Services\AttendanceService;
use App\Services\GeofencingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class AttendanceServiceQueueTest extends TestCase
{
    use RefreshDatabase;

    /**
     * TC-QUEUE-001: queueCheckIn يرجع حالة 'processing'
     */
    public function test_queue_check_in_returns_processing_status(): void
    {
        Queue::fake();

        $branch = Branch::create([
            'name_ar'              => 'فرع اختبار',
            'name_en'              => 'Test Branch',
            'code'                 => 'TST',
            'latitude'             => 24.7136,
            'longitude'            => 46.6753,
            'geofence_radius'      => 17,
            'default_shift_start'  => '08:00',
            'default_shift_end'    => '16:00',
            'grace_period_minutes' => 5,
            'is_active'            => true,
        ]);

        $user = User::create([
            'name_ar'               => 'موظف اختبار',
            'name_en'               => 'Test Employee',
            'email'                 => 'test.queue@sarh.test',
            'password'              => bcrypt('password'),
            'branch_id'             => $branch->id,
            'basic_salary'          => 8000.00,
            'working_days_per_month' => 22,
            'working_hours_per_day' => 8,
            'status'                => 'active',
            'hire_date'             => '2025-01-01',
        ]);

        $service = new AttendanceService(new GeofencingService());
        $result = $service->queueCheckIn($user, 24.7136, 46.6753, '127.0.0.1', 'test');

        $this->assertEquals('processing', $result['status']);
        $this->assertArrayHasKey('message', $result);

        Queue::assertPushed(\App\Jobs\ProcessAttendanceJob::class);
    }

    /**
     * TC-QUEUE-002: نقطة نهاية queue-check-in ترجع 202
     */
    public function test_queue_check_in_endpoint_returns_202(): void
    {
        Queue::fake();

        $branch = Branch::create([
            'name_ar'              => 'فرع اختبار',
            'name_en'              => 'Test Branch',
            'code'                 => 'TST2',
            'latitude'             => 24.7136,
            'longitude'            => 46.6753,
            'geofence_radius'      => 17,
            'default_shift_start'  => '08:00',
            'default_shift_end'    => '16:00',
            'grace_period_minutes' => 5,
            'is_active'            => true,
        ]);

        $user = User::create([
            'name_ar'               => 'موظف اختبار 2',
            'name_en'               => 'Test Employee 2',
            'email'                 => 'test.queue2@sarh.test',
            'password'              => bcrypt('password'),
            'branch_id'             => $branch->id,
            'basic_salary'          => 8000.00,
            'working_days_per_month' => 22,
            'working_hours_per_day' => 8,
            'status'                => 'active',
            'hire_date'             => '2025-01-01',
        ]);

        $response = $this->actingAs($user)->postJson('/attendance/queue-check-in', [
            'latitude'  => 24.7136,
            'longitude' => 46.6753,
        ]);

        $response->assertStatus(202)
            ->assertJsonStructure(['status', 'message']);
    }
}
