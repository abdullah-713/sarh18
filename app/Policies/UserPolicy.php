<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    /**
     * هل يستطيع هذا المستخدم مشاهدة راتب الموظف المستهدف؟
     */
    public function viewSalary(User $user, User $target): bool
    {
        // المستوى 10 يرى كل شيء
        if ($user->security_level === 10 || $user->is_super_admin) {
            return true;
        }

        // المدير المباشر يرى راتب مرؤوسه
        if ($target->direct_manager_id === $user->id) {
            return true;
        }

        // مدير الفرع يرى رواتب فرعه (المستوى 7 فأعلى)
        if ($user->security_level >= 7 && $user->branch_id === $target->branch_id) {
            return true;
        }

        // مدير القسم يرى رواتب قسمه (المستوى 6 فأعلى)
        if ($user->security_level >= 6 && $user->department_id === $target->department_id) {
            return true;
        }

        return false;
    }

    /**
     * هل يستطيع هذا المستخدم تعديل راتب الموظف؟
     */
    public function updateSalary(User $user, User $target): bool
    {
        // فقط المستوى 10 أو مدير الفرع
        if ($user->security_level === 10 || $user->is_super_admin) {
            return true;
        }

        // مدير الفرع (المستوى 7+) يعدل رواتب فرعه
        if ($user->security_level >= 7 && $user->branch_id === $target->branch_id) {
            return true;
        }

        return false;
    }

    /**
     * هل يستطيع هذا المستخدم حذف الموظف؟
     */
    public function delete(User $user, User $target): bool
    {
        // فقط المستوى 10
        return $user->security_level === 10 || $user->is_super_admin;
    }
}
