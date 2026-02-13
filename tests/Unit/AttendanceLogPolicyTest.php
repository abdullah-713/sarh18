<?php

namespace Tests\Unit;

use App\Models\AttendanceLog;
use App\Models\User;
use App\Policies\AttendanceLogPolicy;
use Tests\TestCase;

class AttendanceLogPolicyTest extends TestCase
{
    private AttendanceLogPolicy $policy;

    protected function setUp(): void
    {
        parent::setUp();
        $this->policy = new AttendanceLogPolicy();
    }

    /**
     * TC-ALPOL-001: المستوى 10 يرى كل السجلات
     */
    public function test_level_10_can_view_any_log(): void
    {
        $admin = new User(['security_level' => 10, 'is_super_admin' => false, 'branch_id' => 1]);
        $admin->id = 1;
        $log = new AttendanceLog(['user_id' => 999, 'branch_id' => 2]);

        $this->assertTrue($this->policy->view($admin, $log));
    }

    /**
     * TC-ALPOL-002: الموظف يرى سجلاته الشخصية
     */
    public function test_employee_can_view_own_logs(): void
    {
        $employee = new User(['security_level' => 1, 'is_super_admin' => false, 'branch_id' => 1]);
        $employee->id = 5;
        $log = new AttendanceLog(['user_id' => 5, 'branch_id' => 2]);

        $this->assertTrue($this->policy->view($employee, $log));
    }

    /**
     * TC-ALPOL-003: مستوى 6+ يرى سجلات فرعه فقط
     */
    public function test_level_6_can_view_branch_logs(): void
    {
        $manager = new User(['security_level' => 6, 'is_super_admin' => false, 'branch_id' => 1]);
        $manager->id = 10;
        $log = new AttendanceLog(['user_id' => 99, 'branch_id' => 1]);

        $this->assertTrue($this->policy->view($manager, $log));
    }

    /**
     * TC-ALPOL-004: موظف عادي لا يرى سجلات الآخرين
     */
    public function test_regular_employee_cannot_view_other_logs(): void
    {
        $employee = new User(['security_level' => 1, 'is_super_admin' => false, 'branch_id' => 1]);
        $employee->id = 5;
        $log = new AttendanceLog(['user_id' => 99, 'branch_id' => 2]);

        $this->assertFalse($this->policy->view($employee, $log));
    }
}
