# 🖥️ واجهة الإدارة — Filament Resources & Pages

> **الإصدار:** 3.4.1 | **آخر تحديث:** 2026-02-13
> **الغرض:** توثيق كل مورد وصفحة في لوحة المدير (`/admin`) — الحقول، الأعمدة، الفلاتر، الإجراءات.
> **المسار:** `/admin` — يتطلب `is_super_admin || security_level >= 4`

---

## 📑 فهرس

### الموارد (Resources — CRUD كامل)
| # | المورد | الأيقونة | المجموعة |
|---|--------|----------|----------|
| 1 | [UserResource](#1-userresource) | 👤 users | المستخدمون |
| 2 | [BranchResource](#2-branchresource) | 🏢 building-office-2 | الفروع |
| 3 | [AttendanceLogResource](#3-attendancelogresource) | ⏰ clock | الحضور |
| 4 | [CircularResource](#4-circularresource) | 📢 megaphone | التعميمات |
| 5 | [HolidayResource](#5-holidayresource) | 📅 calendar-days | الإجازات |
| 6 | [LeaveRequestResource](#6-leaverequestresource) | 📋 calendar | طلبات الإجازة |
| 7 | [TrapResource](#7-trapresource) | 🛡️ shield-exclamation | المصائد |
| 8 | [TrapInteractionResource](#8-trapinteractionresource) | 👁️ eye | المصائد |
| 9 | [PermissionResource](#9-permissionresource) | 🔑 key | الأمان |
| 10 | [RoleResource](#10-roleresource) | 🛡️ shield-check | الأمان |
| 11 | [PayrollResource](#11-payrollresource) | 💰 banknotes | المالية |
| 12 | [AttendanceExceptionResource](#12-attendanceexceptionresource) | 🔄 arrow-path | الحضور |
| 13 | [LossAlertResource](#13-lossalertresource) | 🔔 bell-alert | التحليلات |
| 14 | [EmployeePatternResource](#14-employeepatternresource) | 🔍 finger-print | التحليلات |
| 15 | [ReportFormulaResource](#15-reportformularesource) | 🧮 calculator | التقارير |
| 16 | [ScoreAdjustmentResource](#16-scoreadjustmentresource) | ⚖️ adjustments | التقارير |

### الصفحات المخصصة (Custom Pages)
| # | الصفحة | الغرض |
|---|--------|-------|
| 17 | [Dashboard](#17-dashboard) | لوحة التحكم الرئيسية |
| 18 | [BranchLeaderboardPage](#18-branchleaderboardpage) | ترتيب الفروع |
| 19 | [FinancialReportsPage](#19-financialreportspage) | التقارير المالية |
| 20 | [WhistleblowerVaultPage](#20-whistleblowervaultpage) | خزنة البلاغات |
| 21 | [TrapAuditPage](#21-trapauditpage) | تدقيق المصائد |
| 22 | [BroadcastPage](#22-broadcastpage) | الإرسال الجماعي |
| 23 | [GeneralSettingsPage](#23-generalsettingspage) | الإعدادات العامة |
| 24 | [DemoDataGenerator](#24-demodatagenerator) | مولّد البيانات |
| 25 | [DeploymentDataPage](#25-deploymentdatapage) | بيانات النشر |
| 26 | [AnalyticsDashboard](#26-analyticsdashboard) | الذكاء المؤسسي |

---

---

## 1. UserResource

📍 `app/Filament/Resources/UserResource.php`
📋 إدارة الموظفين — أكبر مورد في النظام

### 🔒 التحكم بالوصول
- غير المدير الأعلى يرى فقط موظفي فرعه (`branch_id` scope)

### 📝 حقول النموذج (Form)

| الحقل | النوع | التحقق | ملاحظات |
|-------|-------|--------|---------|
| `avatar` | FileUpload | required, image, 2MB max | circular cropper |
| `name_ar` | TextInput | required | — |
| `name_en` | TextInput | required | — |
| `email` | TextInput | required, unique, email | — |
| `password` | TextInput | required (create), revealable | Hash::make |
| `basic_salary` | TextInput | required, numeric ≥ 0 | prefix: ر.س |
| `housing_allowance` | TextInput | numeric, default 0 | prefix: ر.س |
| `transport_allowance` | TextInput | numeric, default 0 | prefix: ر.س |
| `other_allowances` | TextInput | numeric, default 0 | prefix: ر.س |
| `branch_id` | Select | searchable | relationship |
| `department_id` | Select | searchable | relationship |
| `role_id` | Select | searchable | relationship |
| `direct_manager_id` | Select | searchable | relationship |
| `phone` | TextInput | tel | — |
| `employee_id` | TextInput | unique | auto-generated |
| `status` | Select | — | active/suspended/terminated/on_leave |
| `employment_type` | Select | — | full_time/part_time/contract/intern |
| `working_days_per_month` | Hidden | default 22 | — |
| `working_hours_per_day` | Hidden | default 8 | — |

### 📊 أعمدة الجدول (Table)
`avatar` → `name_ar` + `employee_id` → `branch.name_ar` + `role.name_ar` → `email` + `name_en` → `basic_salary` + `total_points` + `security_level` + `status`

### 🔍 الفلاتر
- `branch_id` (Select)، `role_id` (Select)، `status` (Select)

### ⚡ الإجراءات المخصصة

| الإجراء | النوع | الوصف |
|---------|-------|-------|
| `adjust_points` | Action | تعديل نقاط فردي — يطلب النقاط + السبب → ينشئ PointsTransaction |
| `bulk_adjust_salary` | Bulk | تعديل رواتب جماعي — set/add/percent |
| `bulk_change_branch` | Bulk | نقل فرع جماعي |
| `bulk_change_status` | Bulk | تغيير حالة جماعي |

### 🟢 لو بدنا نضيف حقل جديد
1. أضف العمود في migration
2. أضف في `$fillable` في User.php
3. أضف الحقل هنا في `form()` — داخل Section المناسب
4. أضف العمود في `table()` لو لازم يظهر
5. `php artisan migrate`

---

---

## 2. BranchResource

📍 `app/Filament/Resources/BranchResource.php`
📋 إدارة الفروع — السياج الجغرافي والمناوبات

### 📝 حقول النموذج

| الحقل | النوع | ملاحظات |
|-------|-------|---------|
| `name_ar`, `name_en` | TextInput | required |
| `code` | TextInput | required, unique |
| `map_picker` | ViewField | خريطة تفاعلية لاختيار الموقع |
| `latitude`, `longitude` | TextInput | required, 7 خانات عشرية |
| `geofence_radius` | TextInput | required, 1-100,000 متر |
| `default_shift_start/end` | TimePicker | — |
| `grace_period_minutes` | TextInput | 0-120 دقيقة |
| Financial fields | TextInput | budget, losses (disabled), cost_center |

**Placeholders محسوبة:** عدد الموظفين، مجموع الرواتب، VPM، نسبة الخسائر

### ⚡ الإجراءات الجماعية
- `bulk_update_geofence` — تغيير نصف القطر لعدة فروع
- `bulk_change_shift` — تغيير المناوبة لعدة فروع

---

---

## 3. AttendanceLogResource

📍 `app/Filament/Resources/AttendanceLogResource.php`
📋 سجلات الحضور — مع تكاليف مالية

### 🔒 التحكم بالوصول
- غير المدير الأعلى يرى فقط سجلات فرعه

### 📊 أعمدة الجدول (مع Summarize)
- `delay_cost` → **Sum** (إجمالي خسائر التأخير)
- `overtime_value` → **Sum** (إجمالي قيمة العمل الإضافي)

### 🔍 الفلاتر
- `status`, `branch_id`, date range (from/until), `has_delay_cost` (Toggle)

### 📌 ملاحظة
- `cost_per_minute`, `delay_cost`, `overtime_value` — **حقول disabled** (لا تُعدّل يدوياً)
- هالحقول تُملأ تلقائياً من `calculateFinancials()`

---

---

## 4. CircularResource

📍 `app/Filament/Resources/CircularResource.php`
📋 التعميمات الإدارية — مع استهداف (فرع/قسم/دور)

### 📝 الحقول المميزة
- `target_scope` → live → يُظهر/يُخفي `target_branch_id` / `target_department_id` / `target_role_id`
- `body_ar` → RichEditor (محرر HTML)
- `requires_acknowledgment` → Toggle
- `created_by` → Hidden (auth()->id() تلقائياً)

---

---

## 5. HolidayResource

📍 `app/Filament/Resources/HolidayResource.php`
📋 الإجازات الرسمية

### 📝 الحقول
- `name_ar`, `name_en`, `date`, `type` (national/religious/company), `is_recurring`, `branch_id` (nullable = كل الفروع)

---

---

## 6. LeaveRequestResource

📍 `app/Filament/Resources/LeaveRequestResource.php`
📋 طلبات الإجازة — مع إجراءات موافقة/رفض

### 🔒 التحكم بالوصول
- غير المدير الأعلى يرى فقط طلبات فرعه

### 📛 الشارة في القائمة
- عدد الطلبات المعلقة (pending count) — لون تحذيري

### ⚡ الإجراءات المخصصة

| الإجراء | الوصف |
|---------|-------|
| `approve` | موافقة — يحتاج تأكيد، يعيّن status=approved + approved_by + approved_at |
| `reject` | رفض — يطلب سبب الرفض (required)، يعيّن status=rejected |

### 📝 أنواع الإجازات
`annual` (سنوية) / `sick` (مرضية) / `emergency` (طارئة) / `unpaid` (بدون راتب) / `maternity` / `paternity` / `hajj` / `death` / `marriage`

---

---

## 7. TrapResource

📍 `app/Filament/Resources/TrapResource.php`
📋 إدارة المصائد النفسية
🔐 **خفي** — يظهر فقط لمستوى 10+

### 📝 الحقول
- `trap_code` — المعرّف الفريد (مثل `SALARY_PEEK`)
- `risk_weight` — 1-10
- `fake_response_type` — success/error/warning

---

---

## 8. TrapInteractionResource

📍 `app/Filament/Resources/TrapInteractionResource.php`
📋 عرض تفاعلات المصائد
🔐 **خفي** — يظهر فقط لمستوى 10+

### 📝 ملاحظة
- معظم الحقول **disabled** (للعرض فقط)
- فقط `is_reviewed` و `review_notes` قابلة للتعديل

---

---

## 9. PermissionResource

📍 `app/Filament/Resources/PermissionResource.php`
📋 إدارة الصلاحيات — 42+ صلاحية
🔐 المستوى 10 فقط

### 📝 المجموعات
`attendance`, `finance`, `users`, `branches`, `reports`, `security`, `competition`, `messaging`, `system`

### 📌 `slug` قابل للنسخ — مفيد للبرمجة

---

---

## 10. RoleResource

📍 `app/Filament/Resources/RoleResource.php`
📋 إدارة الأدوار — مع تعيين الصلاحيات
🔐 المستوى 10 فقط

### 📝 الحقول المميزة
- `permissions` → **CheckboxList** مع bulkToggleable + searchable
- `is_system` → disabled لو الدور نظامي (لا يُحذف)
- `level` → 1-10 مع تسميات وصفية

---

---

## 11. PayrollResource

📍 `app/Filament/Resources/PayrollResource.php`
📋 مسيرات الرواتب

### 📝 الحقول
- الراتب والبدلات (snapshot)
- الخصومات (تأخير/غياب/مغادرة مبكرة/أخرى)
- الإضافات (overtime/bonuses)
- `net_salary` — **disabled** (محسوب)
- `gross_salary`, `total_deductions` — **disabled** (محسوبة)

### ⚡ إجراء مخصص
- `approve` — يغير الحالة إلى approved

---

---

## 12. AttendanceExceptionResource

📍 `app/Filament/Resources/AttendanceExceptionResource.php`
📋 استثناءات الحضور — ساعات مرنة، عمل عن بعد
🔐 المستوى 10 فقط

### 📝 أنواع الاستثناءات
`flexible_hours` / `remote_work` / `vip_bypass` / `medical` / `custom`

### 📝 الحقول المميزة
- `bypass_geofence` — تجاوز السياج
- `bypass_late_flag` — تجاوز علامة التأخير
- `end_date` — nullable = دائم

---

---

## 13. LossAlertResource

📍 `app/Filament/Resources/LossAlertResource.php`
📋 تنبيهات الخسائر
🔐 المستوى 10 فقط

### ⚡ إجراء مخصص
- `acknowledge` — وضع علامة "تمت الملاحظة"

---

---

## 14. EmployeePatternResource

📍 `app/Filament/Resources/EmployeePatternResource.php`
📋 أنماط سلوك الموظفين (للعرض فقط غالباً)
🔐 المستوى 10 فقط

### 📝 أنواع الأنماط
`frequent_late` / `pre_holiday_absence` / `monthly_cycle` / `burnout_risk` / `improving` / `declining`

---

---

## 15. ReportFormulaResource

📍 `app/Filament/Resources/ReportFormulaResource.php`
📋 صيغ التقارير الحسابية المخصصة
🔐 المستوى 10 فقط

### 📝 الحقول المميزة
- `formula` — حقل نصي للصيغة (مثل `(attendance × 0.6) + (on_time_rate × 0.4)`)
- `variables` — CheckboxList من المتغيرات المتاحة

### ⚡ إجراء مخصص
- `test_formula` — اختبار الصيغة على موظف محدد مع فترة

---

---

## 16. ScoreAdjustmentResource

📍 `app/Filament/Resources/ScoreAdjustmentResource.php`
📋 تعديلات النقاط اليدوية
🔐 المستوى 10 فقط

### 📝 الحقل المميز
- `scope` → **live** → يُظهر حقل branch_id أو user_id أو department_id حسب الاختيار

---

---

## 17. Dashboard

📍 `app/Filament/Pages/Dashboard.php`
📋 لوحة التحكم الرئيسية

### 🔍 فلاتر
- `period` — today/week/month/year/custom
- `start_date`, `end_date` — (يظهران فقط مع custom)

### 📌 الودجات تُحمّل من ملفات منفصلة (انظر الودجات في الأسفل)

---

---

## 18. BranchLeaderboardPage

📍 `app/Filament/Pages/BranchLeaderboardPage.php`
📋 ترتيب الفروع — المنافسة

### ⚙️ المنطق
- يرتب الفروع بأقل خسائر مالية
- يحسب: التأخير، التسجيلات المتأخرة، الخسائر، الموظفين المثاليين، النقاط
- **المستويات:** Legendary 🌟 / Diamond 💎 / Gold 🥇 / Silver 🥈 / Bronze 🥉 / Starter ⭐
- **المركز الأول:** 🏆 | **المركز الأخير:** 🐢

---

---

## 19. FinancialReportsPage

📍 `app/Filament/Pages/FinancialReportsPage.php`
📋 التقارير المالية — تحليل الأثر والتنبؤات

### 📝 نموذج الفلترة
- `scope` — company/branch/department/employee (ديناميكي)
- `period_start`, `period_end`

### ⚙️ المنطق
- يستدعي `FinancialReportingService::getDelayImpactAnalysis()`
- يستدعي `FinancialReportingService::getPredictiveMonthlyLoss()`

---

---

## 20. WhistleblowerVaultPage

📍 `app/Filament/Pages/WhistleblowerVaultPage.php`
📋 خزنة البلاغات المشفرة
🔐 **المستوى 10 فقط — خفي**

### ⚡ إجراء مخصص
- `view_decrypted` — يعرض المحتوى المفكوك في modal + يسجل الوصول في AuditLog

### ⚠️ تحذير
- كل عملية فتح **مسجلة** في سجل التدقيق — لا يمكن إخفاؤها

---

---

## 21. TrapAuditPage

📍 `app/Filament/Pages/TrapAuditPage.php`
📋 تدقيق تفاعلات المصائد
🔐 **المستوى 10 فقط — خفي**

### ⚡ إجراءات مخصصة
- `view_data` — عرض تفاصيل التفاعل في modal
- `mark_reviewed` — وضع علامة "تمت المراجعة" + AuditLog

### 📌 يسجل الوصول للصفحة نفسها في `mount()`

---

---

## 22. BroadcastPage

📍 `app/Filament/Pages/BroadcastPage.php`
📋 الإرسال الجماعي — إشعارات لمجموعات

### 📝 حقول النموذج
- `subject`, `body` (RichEditor)
- `target_scope` → all/branch/department (ديناميكي)
- `channel` → database (إشعار داخلي)

### ⚙️ المنطق
- يفلتر الموظفين حسب النطاق → يدرج إشعارات في جدول `notifications`

---

---

## 23. GeneralSettingsPage

📍 `app/Filament/Pages/GeneralSettingsPage.php`
📋 إعدادات النظام الكاملة
🔐 المستوى 10 فقط

### 📝 الأقسام
1. **هوية التطبيق:** اسم عربي/إنجليزي، شعار، أيقونة، رسالة ترحيب
2. **PWA:** اسم التطبيق، الاسم المختصر، ألوان الثيم
3. **إعدادات المنطق:**
   - `loss_multiplier` — مضاعف الخسائر (1.0-5.0)
   - `default_geofence_radius` — نصف قطر السياج (10-10,000 متر)
   - `default_grace_period` — فترة السماح (0-60 دقيقة)
   - `overtime_multiplier` — مضاعف العمل الإضافي (1.0-3.0)

---

---

## 24. DemoDataGenerator

📍 `app/Filament/Pages/DemoDataGenerator.php`
📋 مولّد بيانات حضور وهمية — للاختبار
🔐 المستوى 10 فقط — **خفي**

### 📝 الحقول
- `date_from`, `date_to` — فترة التوليد
- `branch_ids` — CheckboxList من الفروع
- `weekend_days` — أيام العطلة
- `compliance_gauge` — مقياس 1-10 (يتحكم بنسب الحضور/التأخير/الغياب)

### ⚡ الإجراءات
- `generatePreview()` — إحصائيات متوقعة
- `commitRecords()` — توليد وإدراج السجلات
- `wipeRecords()` — حذف سجلات الفترة

### ⚙️ خوارزمية التوليد
- GPS بصيغة Haversine مع تباين محكوم
- سيناريوهات: absent/late/overtime/present — بنسب حسب الـ gauge

---

---

## 25. DeploymentDataPage

📍 `app/Filament/Pages/DeploymentDataPage.php`
📋 أدوات النشر — إعادة تعيين وتهيئة
🔐 المستوى 10 فقط — **خفي**

### ⚡ الإجراءات

| الإجراء | الوصف |
|---------|-------|
| `resetAllRecords()` | تفريغ: الحضور، الإجازات، الرواتب، التقارير، التنبيهات، التحليلات، الأنماط، النقاط |
| `resetAllPasswords()` | إعادة كل كلمات مرور غير المدير إلى `123456` |
| `setLogoAsAvatar()` | تعيين الشعار كصورة لكل الموظفين |
| `applyStandardShift()` | إنشاء مناوبة 08:00-21:00 وتعيينها للجميع |
| `runFullDeploymentReset()` | تشغيل الأربعة بالتسلسل |

---

---

## 26. AnalyticsDashboard

📍 `app/Filament/Pages/AnalyticsDashboard.php`
📋 لوحة الذكاء المؤسسي
🔐 المستوى 10 فقط

### 📊 البيانات المعروضة
- **ساعة الفرص الضائعة** — `getLostOpportunityClock()`
- **آخر 10 تنبيهات** — `LossAlert::unacknowledged()->recent(7)`
- **أعلى 10 أنماط خطر** — `EmployeePattern::active()->highRisk()`

### ⚡ إجراءات
- `runAnalytics` — تشغيل التحليلات الآن (يدوياً) → `runFullAnalysis()`
- `acknowledgeAlert($id)` — Livewire method لتأكيد التنبيه

---

---

## 🔗 لو بدنا نضيف مورد جديد (Resource)

```bash
# 1. إنشاء المورد
php artisan make:filament-resource ModelName --generate

# 2. تعديل الملف المُنشأ:
#    - أضف العلاقات في form() كـ Select مع relationship
#    - أضف الأعمدة في table()
#    - أضف الفلاتر
#    - أضف الإجراءات المخصصة

# 3. لو يحتاج تحكم بالوصول:
public static function canAccess(): bool
{
    return auth()->user()?->is_super_admin 
        || auth()->user()?->security_level >= 10;
}

# 4. لو يحتاج يكون خفي:
public static function shouldRegisterNavigation(): bool
{
    return static::canAccess();
}
```

---

## 🔗 لو بدنا نضيف صفحة مخصصة (Custom Page)

```bash
# 1. إنشاء الصفحة
php artisan make:filament-page PageName

# 2. أضف View في resources/views/filament/pages/
# 3. عرّف النموذج والجدول لو لازم (HasForms, HasTable)
# 4. أضف الأيقونة والمجموعة والترتيب
```

---

> **صرح الإتقان v3.4.1** — *"كل حقل موثق. كل إجراء مشروح. كل فلتر واضح."*
