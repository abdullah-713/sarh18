<?php

namespace Tests\Unit;

use App\Models\User;
use App\Policies\UserPolicy;
use Tests\TestCase;

class UserPolicyTest extends TestCase
{
    private UserPolicy $policy;

    protected function setUp(): void
    {
        parent::setUp();
        $this->policy = new UserPolicy();
    }

    /**
     * TC-POL-001: المستوى 10 يرى كل الرواتب
     */
    public function test_level_10_can_view_any_salary(): void
    {
        $admin = new User(['security_level' => 10, 'is_super_admin' => false, 'branch_id' => 1]);
        $target = new User(['direct_manager_id' => 999, 'branch_id' => 2, 'department_id' => 3]);

        $this->assertTrue($this->policy->viewSalary($admin, $target));
    }

    /**
     * TC-POL-002: السوبر أدمن يرى كل الرواتب
     */
    public function test_super_admin_can_view_any_salary(): void
    {
        $admin = new User(['security_level' => 1, 'is_super_admin' => true, 'branch_id' => 1]);
        $target = new User(['direct_manager_id' => 999, 'branch_id' => 2, 'department_id' => 3]);

        $this->assertTrue($this->policy->viewSalary($admin, $target));
    }

    /**
     * TC-POL-003: المدير المباشر يرى راتب مرؤوسه
     */
    public function test_direct_manager_can_view_subordinate_salary(): void
    {
        $manager = new User(['id' => 5, 'security_level' => 3, 'is_super_admin' => false, 'branch_id' => 1]);
        $manager->id = 5;
        $target = new User(['direct_manager_id' => 5, 'branch_id' => 2, 'department_id' => 3]);

        $this->assertTrue($this->policy->viewSalary($manager, $target));
    }

    /**
     * TC-POL-004: مدير الفرع (مستوى 7+) يرى رواتب فرعه
     */
    public function test_branch_manager_can_view_branch_salaries(): void
    {
        $manager = new User(['security_level' => 7, 'is_super_admin' => false, 'branch_id' => 1]);
        $target = new User(['direct_manager_id' => 999, 'branch_id' => 1, 'department_id' => 3]);

        $this->assertTrue($this->policy->viewSalary($manager, $target));
    }

    /**
     * TC-POL-005: مدير القسم (مستوى 6+) يرى رواتب قسمه
     */
    public function test_department_manager_can_view_department_salaries(): void
    {
        $manager = new User(['security_level' => 6, 'is_super_admin' => false, 'branch_id' => 1, 'department_id' => 5]);
        $target = new User(['direct_manager_id' => 999, 'branch_id' => 2, 'department_id' => 5]);

        $this->assertTrue($this->policy->viewSalary($manager, $target));
    }

    /**
     * TC-POL-006: الموظف العادي لا يرى رواتب الآخرين
     */
    public function test_regular_employee_cannot_view_salary(): void
    {
        $employee = new User(['security_level' => 1, 'is_super_admin' => false, 'branch_id' => 1, 'department_id' => 1]);
        $employee->id = 10;
        $target = new User(['direct_manager_id' => 999, 'branch_id' => 2, 'department_id' => 3]);

        $this->assertFalse($this->policy->viewSalary($employee, $target));
    }

    /**
     * TC-POL-007: الموظف مستوى 5 لا يرى رواتب فرعه
     */
    public function test_level_5_cannot_view_branch_salaries(): void
    {
        $employee = new User(['security_level' => 5, 'is_super_admin' => false, 'branch_id' => 1]);
        $employee->id = 10;
        $target = new User(['direct_manager_id' => 999, 'branch_id' => 1, 'department_id' => 3]);

        $this->assertFalse($this->policy->viewSalary($employee, $target));
    }

    /**
     * TC-POL-008: فقط المستوى 10 يحذف الموظفين
     */
    public function test_only_level_10_can_delete(): void
    {
        $admin = new User(['security_level' => 10, 'is_super_admin' => false]);
        $target = new User([]);

        $this->assertTrue($this->policy->delete($admin, $target));

        $manager = new User(['security_level' => 7, 'is_super_admin' => false]);
        $this->assertFalse($this->policy->delete($manager, $target));
    }

    /**
     * TC-POL-009: مدير الفرع يعدل رواتب فرعه
     */
    public function test_branch_manager_can_update_branch_salaries(): void
    {
        $manager = new User(['security_level' => 7, 'is_super_admin' => false, 'branch_id' => 1]);
        $target = new User(['branch_id' => 1]);

        $this->assertTrue($this->policy->updateSalary($manager, $target));
    }

    /**
     * TC-POL-010: مدير الفرع لا يعدل رواتب فرع آخر
     */
    public function test_branch_manager_cannot_update_other_branch_salaries(): void
    {
        $manager = new User(['security_level' => 7, 'is_super_admin' => false, 'branch_id' => 1]);
        $target = new User(['branch_id' => 2]);

        $this->assertFalse($this->policy->updateSalary($manager, $target));
    }
}
