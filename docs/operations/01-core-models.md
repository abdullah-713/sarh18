# 🗃️ خريطة النماذج الكاملة — 32 موديل

> **الإصدار:** 3.4.1 | **آخر تحديث:** 2026-02-13
> **القاعدة:** كل موديل هنا موثق بـ: حقوله، علاقاته، دواله، سكوباته، وتعليمات التعديل والحذف.
> "لو ما فهمت الموديل من هالتوثيق — المشكلة فينا مو فيك."

---

## 📑 فهرس سريع

| # | الموديل | الغرض | الخطورة |
|---|---------|-------|---------|
| 1 | [User](#1-user) | الموظف — المحرك المالي والأمني | 🔴 |
| 2 | [Branch](#2-branch) | الفرع — السياج الجغرافي ومركز التكلفة | 🔴 |
| 3 | [Department](#3-department) | القسم | 🟡 |
| 4 | [Role](#4-role) | الدور الوظيفي | 🔴 |
| 5 | [Permission](#5-permission) | الصلاحية | 🔴 |
| 6 | [AttendanceLog](#6-attendancelog) | سجل الحضور — اللقطة المالية | 🔴 |
| 7 | [FinancialReport](#7-financialreport) | التقرير المالي | 🟡 |
| 8 | [WhistleblowerReport](#8-whistleblowerreport) | بلاغ المبلغين — مشفر | 🔴 |
| 9 | [Conversation](#9-conversation) | المحادثة | 🟢 |
| 10 | [Message](#10-message) | الرسالة | 🟢 |
| 11 | [Circular](#11-circular) | التعميم | 🟢 |
| 12 | [CircularAcknowledgment](#12-circularacknowledgment) | إقرار التعميم | 🟢 |
| 13 | [PerformanceAlert](#13-performancealert) | تنبيه الأداء | 🟢 |
| 14 | [Badge](#14-badge) | الشارة | 🟢 |
| 15 | [PointsTransaction](#15-pointstransaction) | حركة النقاط | 🟡 |
| 16 | [TrapInteraction](#16-trapinteraction) | تفاعل المصيدة | 🔴 |
| 17 | [Trap](#17-trap) | المصيدة النفسية | 🔴 |
| 18 | [LeaveRequest](#18-leaverequest) | طلب الإجازة | 🟡 |
| 19 | [Shift](#19-shift) | المناوبة | 🟡 |
| 20 | [AuditLog](#20-auditlog) | سجل التدقيق | 🔴 |
| 21 | [Holiday](#21-holiday) | الإجازة الرسمية | 🟢 |
| 22 | [UserShift](#22-usershift) | تعيين المناوبة — كيان مستقل v3.4 | 🟡 |
| 23 | [UserBadge](#23-userbadge) | منح الشارة — كيان مستقل v3.4 | 🟡 |
| 24 | [UserPermission](#24-userpermission) | تجاوز الصلاحية | 🔴 |
| 25 | [Payroll](#25-payroll) | مسير الرواتب | 🔴 |
| 26 | [Setting](#26-setting) | الإعدادات — Singleton | 🔴 |
| 27 | [ReportFormula](#27-reportformula) | صيغة التقرير الحسابية | 🟡 |
| 28 | [ScoreAdjustment](#28-scoreadjustment) | تعديل النقاط | 🟡 |
| 29 | [AnalyticsSnapshot](#29-analyticssnapshot) | لقطة التحليلات | 🟡 |
| 30 | [LossAlert](#30-lossalert) | تنبيه الخسائر | 🟢 |
| 31 | [EmployeePattern](#31-employeepattern) | نمط الموظف السلوكي | 🟢 |
| 32 | [AttendanceException](#32-attendanceexception) | استثناء الحضور | 🟡 |

**الخطورة:** 🔴 = حساس جداً (تعديل خاطئ يكسر النظام) | 🟡 = متوسط | 🟢 = آمن نسبياً

---

---

## 1. User

📍 **الملف:** `app/Models/User.php` (623 سطر — أكبر موديل في النظام)
📋 **الغرض:** الموظف/المدير. يحتوي المحرك المالي، نظام الصلاحيات RBAC، المستويات الأمنية، التلعيب.
🔴 **الخطورة:** عالية جداً — أي تعديل خاطئ يكسر الحضور والمالية والصلاحيات

### الوراثة والـ Traits
```
Authenticatable (Laravel) → implements FilamentUser
Traits: HasFactory, Notifiable, SoftDeletes
```

### 🔹 جدول الحقول ($fillable)

| الحقل | النوع | الغرض | مثال |
|-------|-------|-------|------|
| `employee_id` | `string` | رقم الموظف التلقائي `SARH-YY-XXXX` | `SARH-26-0001` |
| `name_ar` | `string` | الاسم بالعربي | `عبدالله أحمد` |
| `name_en` | `string` | الاسم بالإنجليزي | `Abdullah Ahmed` |
| `email` | `string` | البريد الإلكتروني (فريد) | `a@sarh.online` |
| `password` | `string` | كلمة المرور (مشفرة bcrypt) | — |
| `phone` | `string` | رقم الجوال | `0501234567` |
| `national_id` | `string` | رقم الهوية (مخفي في $hidden) | `1234567890` |
| `avatar` | `string` | مسار الصورة الشخصية | `avatars/1.jpg` |
| `gender` | `string` | الجنس | `male` / `female` |
| `date_of_birth` | `date` | تاريخ الميلاد | `1990-01-15` |
| `branch_id` | `FK→branches` | الفرع | `1` |
| `department_id` | `FK→departments` | القسم | `3` |
| `role_id` | `FK→roles` | الدور الوظيفي | `2` |
| `direct_manager_id` | `FK→users` | المدير المباشر (self-ref) | `1` |
| `job_title_ar` | `string` | المسمى الوظيفي عربي | `محاسب` |
| `job_title_en` | `string` | المسمى الوظيفي إنجليزي | `Accountant` |
| `hire_date` | `date` | تاريخ التعيين | `2024-01-01` |
| `employment_type` | `string` | نوع التوظيف | `full_time` / `part_time` |
| `status` | `string` | الحالة | `active` / `suspended` / `terminated` |
| `basic_salary` | `decimal:2` | الراتب الأساسي | `8000.00` |
| `housing_allowance` | `decimal:2` | بدل السكن | `2000.00` |
| `transport_allowance` | `decimal:2` | بدل النقل | `500.00` |
| `other_allowances` | `decimal:2` | بدلات أخرى | `300.00` |
| `working_days_per_month` | `int` | أيام العمل/شهر | `22` |
| `working_hours_per_day` | `int` | ساعات العمل/يوم | `8` |
| `total_points` | `int` | مجموع نقاط التلعيب | `150` |
| `current_streak` | `int` | سلسلة الحضور الحالية | `5` |
| `longest_streak` | `int` | أطول سلسلة حضور | `30` |
| `locale` | `string` | اللغة المفضلة | `ar` |
| `timezone` | `string` | المنطقة الزمنية | `Asia/Riyadh` |

### ⚠️ حقول محمية — لا تضيفها لـ $fillable أبداً!

| الحقل | النوع | الغرض | لماذا محمي |
|-------|-------|-------|-----------|
| `is_super_admin` | `boolean` | مدير أعلى | يتغير فقط بـ `forceFill()` عبر `promoteToSuperAdmin()` |
| `security_level` | `int(1-10)` | المستوى الأمني | يتغير فقط بـ `forceFill()` عبر `setSecurityLevel()` |
| `is_trap_target` | `boolean` | هدف مصيدة | يتغير فقط بـ `forceFill()` عبر `enableTrapMonitoring()` |
| `risk_score` | `int` | درجة الخطورة | يتغير بـ `incrementRiskScore()` — صيغة لوغاريتمية |

### 🔹 العلاقات (Relationships)

| العلاقة | النوع | الموديل المرتبط | الغرض |
|---------|-------|-----------------|-------|
| `branch()` | `BelongsTo` | `Branch` | الفرع الذي يتبع له |
| `department()` | `BelongsTo` | `Department` | القسم |
| `role()` | `BelongsTo` | `Role` | الدور الوظيفي |
| `directManager()` | `BelongsTo` | `User` | المدير المباشر |
| `subordinates()` | `HasMany` | `User` | المرؤوسون |
| `attendanceLogs()` | `HasMany` | `AttendanceLog` | سجلات الحضور |
| `financialReports()` | `HasMany` | `FinancialReport` | التقارير المالية |
| `leaveRequests()` | `HasMany` | `LeaveRequest` | طلبات الإجازة |
| `conversations()` | `BelongsToMany` | `Conversation` | المحادثات (pivot: `conversation_participants`) |
| `sentMessages()` | `HasMany` | `Message` | الرسائل المرسلة |
| `performanceAlerts()` | `HasMany` | `PerformanceAlert` | تنبيهات الأداء |
| `badges()` | `HasMany` | `UserBadge` | الشارات الممنوحة |
| `awardedBadges()` | `HasMany` | `UserBadge` | الشارات مع eager load |
| `pointsTransactions()` | `HasMany` | `PointsTransaction` | حركات النقاط |
| `trapInteractions()` | `HasMany` | `TrapInteraction` | تفاعلات المصائد |
| `userPermissions()` | `HasMany` | `UserPermission` | تجاوزات الصلاحيات |
| `directPermissions()` | `BelongsToMany` | `Permission` | الصلاحيات المباشرة |
| `attendanceExceptions()` | `HasMany` | `AttendanceException` | استثناءات الحضور |
| `scoreAdjustments()` | `HasMany` | `ScoreAdjustment` | تعديلات النقاط |
| `shifts()` | `HasMany` | `UserShift` | تعيينات المناوبات |

### 🔹 المحرك المالي (أهم جزء في الموديل)

```
total_salary = basic_salary + housing_allowance + transport_allowance + other_allowances
monthly_working_minutes = working_days_per_month × working_hours_per_day × 60
cost_per_minute = basic_salary ÷ monthly_working_minutes  (4 خانات عشرية)
total_cost_per_minute = total_salary ÷ monthly_working_minutes
daily_rate = basic_salary ÷ working_days_per_month

مثال عملي:
  basic_salary = 8000, days = 22, hours = 8
  monthly_working_minutes = 22 × 8 × 60 = 10,560
  cost_per_minute = 8000 ÷ 10,560 = 0.7576 ريال/دقيقة
  خسارة 15 دقيقة تأخير = 15 × 0.7576 = 11.36 ريال
```

### 🔹 الدوال

#### `canAccessPanel(Panel $panel): bool`
- **الغرض:** عزل هوية البوابات — `/admin` تحتاج `is_super_admin || security_level >= 4`، `/app` تحتاج `security_level < 4`
- **📥 المدخلات:** كائن Panel من Filament
- **📤 المخرجات:** `true` إذا مسموح الدخول
- **⚠️ تحذير:** لو غيرت المنطق، المدير ممكن يفقد الوصول

#### `activeShift(): ?UserShift`
- **الغرض:** يرجع تعيين المناوبة النشط (UserShift مع `is_current = true`)
- **📤 المخرجات:** كائن UserShift أو null

#### `currentShift(): ?Shift`
- **الغرض:** يرجع المناوبة نفسها (Shift object) — للتوافق مع الإصدارات القديمة
- **⚙️ المنطق:** `return $this->activeShift()?->shift`
- **📌 ملاحظة:** يرجع `Shift` مش `UserShift` — هذا مهم جداً

#### `hasPermission(string $slug): bool`
- **الغرض:** فحص الصلاحية بالأولوية التالية:
  1. `is_super_admin` → `true` دائماً
  2. `UserPermission` نوع `revoke` → `false`
  3. `UserPermission` نوع `grant` → `true`
  4. `Role->permissions` → التحقق من slug
- **📌 ملاحظة:** التجاوزات الفردية أقوى من صلاحيات الدور

#### `setSecurityLevel(int $level): self`
- **الغرض:** تعيين المستوى الأمني (1-10) عبر `forceFill()`
- **⚠️ تحذير:** المستوى 10 = وضع الله — يتجاوز كل شي

#### `incrementRiskScore(): int`
- **الغرض:** حساب وحفظ درجة الخطورة اللوغاريتمية
- **الصيغة:** `risk_score = 10 × (2^n − 1)` حيث n = عدد التفاعلات
- **المستويات:** `0-20 = low`, `21-50 = medium`, `51-80 = high`, `81+ = critical`

#### `generateEmployeeId(): string` (static)
- **الغرض:** يُولّد رقم موظف فريد `SARH-YY-XXXX`
- **⚙️ المنطق:** آخر رقم + 1، مع padding لأربع خانات
- **يُستدعى تلقائياً** في `booted()` عند إنشاء موظف جديد

### 🔹 النطاقات (Scopes)

| النطاق | الفلتر |
|--------|-------|
| `scopeActive` | `status = 'active'` |
| `scopeInBranch($id)` | `branch_id = $id` |
| `scopeInDepartment($id)` | `department_id = $id` |
| `scopeWithSecurityLevel($min)` | `security_level >= $min` |

### 🟢 لو بدنا نضيف خاصية جديدة للموظف
1. أضف عمود في migration جديد: `php artisan make:migration add_xxx_to_users_table`
2. أضف الحقل في `$fillable` (أو `$hidden` لو حساس)
3. أضف cast لو لازم في `$casts`
4. أضف الحقل في `UserResource` → `form()` و `table()`
5. شغّل `php artisan migrate`

### 🔴 لو بدنا نحذف User
**لا تحذفه!** استخدم Soft Delete (`status = 'terminated'`). لو حذفته:
- ❌ كل سجلات الحضور تفقد المرجع
- ❌ كل التقارير المالية تنكسر
- ❌ كل المحادثات تفقد المشارك
- ❌ كل الشارات والنقاط تضيع

---

---

## 2. Branch

📍 **الملف:** `app/Models/Branch.php`
📋 **الغرض:** الفرع — يحتوي السياج الجغرافي (Haversine)، مركز التكلفة، ميزانية الرواتب
🔴 **الخطورة:** عالية — تعديل الإحداثيات يأثر على كل الموظفين

### 🔹 جدول الحقول ($fillable)

| الحقل | النوع | الغرض | مثال |
|-------|-------|-------|------|
| `name_ar` | `string` | اسم الفرع عربي | `الفرع الرئيسي` |
| `name_en` | `string` | اسم الفرع إنجليزي | `Main Branch` |
| `code` | `string` | رمز الفرع | `BR-001` |
| `address_ar` | `string` | العنوان عربي | — |
| `address_en` | `string` | العنوان إنجليزي | — |
| `city_ar` | `string` | المدينة عربي | `جدة` |
| `city_en` | `string` | المدينة إنجليزي | `Jeddah` |
| `phone` | `string` | هاتف الفرع | — |
| `email` | `string` | بريد الفرع | — |
| `latitude` | `decimal:7` | خط العرض GPS | `21.4858003` |
| `longitude` | `decimal:7` | خط الطول GPS | `39.1925048` |
| `geofence_radius` | `int` | نصف قطر السياج (أمتار) | `17` |
| `default_shift_start` | `time` | بداية الوردية الافتراضية | `08:00` |
| `default_shift_end` | `time` | نهاية الوردية | `16:00` |
| `grace_period_minutes` | `int` | فترة السماح (دقائق) | `5` |
| `is_active` | `boolean` | فعال؟ | `true` |
| `monthly_salary_budget` | `decimal:2` | ميزانية الرواتب الشهرية | `250000.00` |
| `monthly_delay_losses` | `decimal:2` | خسائر التأخير الشهرية | `5200.00` |
| `cost_center_code` | `string` | رمز مركز التكلفة | `CC-001` |
| `annual_budget` | `decimal:2` | الميزانية السنوية | `3000000.00` |
| `target_attendance_rate` | `decimal:2` | نسبة الحضور المستهدفة | `95.00` |
| `max_acceptable_loss_percent` | `decimal:2` | أقصى نسبة خسائر مقبولة | `5.00` |
| `vpm_target` | `decimal:2` | هدف القيمة لكل دقيقة | `0.80` |

### 🔹 العلاقات

| العلاقة | النوع | الموديل | الغرض |
|---------|-------|---------|-------|
| `users()` | `HasMany` | `User` | موظفو الفرع |
| `departments()` | `HasMany` | `Department` | أقسام الفرع |
| `attendanceLogs()` | `HasMany` | `AttendanceLog` | سجلات حضور الفرع |
| `financialReports()` | `HasMany` | `FinancialReport` | تقارير الفرع المالية |
| `holidays()` | `HasMany` | `Holiday` | إجازات خاصة بالفرع |
| `payrolls()` | `HasMany` | `Payroll` | مسيرات رواتب الفرع |
| `analyticsSnapshots()` | `HasMany` | `AnalyticsSnapshot` | لقطات تحليلات الفرع |
| `lossAlerts()` | `HasMany` | `LossAlert` | تنبيهات خسائر الفرع |

### 🔹 الدوال

#### `distanceTo(float $lat, float $lng): float`
- **الغرض:** حساب المسافة بين الفرع ونقطة GPS باستخدام **صيغة Haversine**
- **📤 المخرجات:** المسافة بالأمتار
- **الصيغة:** `2 × R × asin(√(sin²(Δlat/2) + cos(lat1) × cos(lat2) × sin²(Δlng/2)))` حيث R = 6371000 متر

#### `isWithinGeofence(float $lat, float $lng): bool`
- **الغرض:** هل الإحداثيات داخل السياج الجغرافي؟
- **⚙️ المنطق:** `$this->distanceTo($lat, $lng) <= $this->geofence_radius`
- **📌 استخدام:** AttendanceService يستدعيها عند كل check-in

#### `recalculateSalaryBudget(): void`
- **الغرض:** إعادة حساب ميزانية الرواتب من مجموع رواتب الموظفين النشطين
- **📦 الأثر:** يحدّث `monthly_salary_budget` ويحفظ

### 🔹 لو بدنا نغير نصف قطر السياج
1. افتح `BranchResource` أو الإعدادات
2. عدّل `geofence_radius` (بالأمتار)
3. **أو** غيّر `default_geofence_radius` في `Setting::logic_settings`
4. **⚠️ تأكد:** لو صغرت النصف → موظفين ممكن يفشلون في تسجيل الحضور

---

---

## 3. Department

📍 **الملف:** `app/Models/Department.php`
📋 **الغرض:** القسم الإداري — يتبع لفرع، ويدعم الهيكلية الشجرية (parent/children)
🟡 **الخطورة:** متوسطة

### 🔹 الحقول

| الحقل | النوع | الغرض |
|-------|-------|-------|
| `name_ar` | `string` | اسم القسم عربي |
| `name_en` | `string` | اسم القسم إنجليزي |
| `code` | `string` | رمز القسم |
| `branch_id` | `FK→branches` | الفرع التابع له |
| `parent_id` | `FK→departments` | القسم الأب (هيكلية شجرية) |
| `head_id` | `FK→users` | رئيس القسم |
| `description_ar` | `string` | وصف عربي |
| `description_en` | `string` | وصف إنجليزي |
| `is_active` | `boolean` | فعال؟ |

### 🔹 العلاقات

| العلاقة | النوع | الموديل |
|---------|-------|---------|
| `branch()` | `BelongsTo` | `Branch` |
| `parent()` | `BelongsTo` | `Department` (self-ref) |
| `children()` | `HasMany` | `Department` (self-ref) |
| `head()` | `BelongsTo` | `User` |
| `users()` | `HasMany` | `User` |
| `financialReports()` | `HasMany` | `FinancialReport` |

### 🔹 لو بدنا نحذف قسم
- ❌ لازم ننقل الموظفين أولاً (`users` → قسم آخر)
- ❌ لازم نتأكد ما في أقسام فرعية (`children`)

---

---

## 4. Role

📍 **الملف:** `app/Models/Role.php`
📋 **الغرض:** الدور الوظيفي — يحتوي مجموعة صلاحيات
🔴 **الخطورة:** عالية — حذف دور يفقد الموظفين صلاحياتهم

### 🔹 الحقول

| الحقل | النوع | الغرض |
|-------|-------|-------|
| `name_ar` | `string` | اسم الدور عربي |
| `name_en` | `string` | اسم الدور إنجليزي |
| `slug` | `string` | المعرّف الفريد (`admin`, `hr`, `employee`) |
| `level` | `int` | مستوى الأولوية |
| `description_ar` | `string` | وصف عربي |
| `description_en` | `string` | وصف إنجليزي |
| `is_system` | `boolean` | دور نظامي (لا يُحذف) |

### 🔹 العلاقات

| العلاقة | النوع | الموديل |
|---------|-------|---------|
| `permissions()` | `BelongsToMany` | `Permission` (pivot: `role_permission`) |
| `users()` | `HasMany` | `User` |

### 🔹 الدوال

| الدالة | الغرض |
|--------|-------|
| `grantPermission(Permission)` | إضافة صلاحية للدور |
| `revokePermission(Permission)` | سحب صلاحية من الدور |
| `hasPermission(string $slug)` | فحص وجود صلاحية |

---

---

## 5. Permission

📍 **الملف:** `app/Models/Permission.php`
📋 **الغرض:** الصلاحية الفردية — 42+ صلاحية مقسمة بمجموعات
🔴 **الخطورة:** عالية — تعديل الصلاحيات يأثر على كل المستخدمين

### 🔹 الحقول

| الحقل | النوع | الغرض |
|-------|-------|-------|
| `name_ar` | `string` | اسم الصلاحية عربي |
| `name_en` | `string` | اسم الصلاحية إنجليزي |
| `slug` | `string` | المعرّف الفريد (مثل `attendance.view`) |
| `group` | `string` | المجموعة (مثل `attendance`, `financial`, `admin`) |
| `description_en` | `string` | وصف إنجليزي |

### 🔹 العلاقات

| العلاقة | النوع | الموديل |
|---------|-------|---------|
| `roles()` | `BelongsToMany` | `Role` |

### 🔹 النطاقات
- `scopeInGroup($group)` — فلترة حسب المجموعة

---

---

## 6. AttendanceLog

📍 **الملف:** `app/Models/AttendanceLog.php`
📋 **الغرض:** سجل الحضور اليومي — يحتوي **اللقطة المالية** (أهم مفهوم في النظام)
🔴 **الخطورة:** عالية جداً — هذا الجدول هو أساس كل التقارير المالية

### 🔹 الحقول

| الحقل | النوع | الغرض |
|-------|-------|-------|
| `user_id` | `FK→users` | الموظف |
| `branch_id` | `FK→branches` | الفرع |
| `attendance_date` | `date` | تاريخ الحضور |
| `check_in_at` | `datetime` | وقت تسجيل الدخول |
| `check_in_latitude` | `decimal` | خط عرض GPS عند الدخول |
| `check_in_longitude` | `decimal` | خط طول GPS عند الدخول |
| `check_in_distance_meters` | `decimal` | المسافة من الفرع بالأمتار |
| `check_in_within_geofence` | `boolean` | داخل السياج؟ |
| `check_in_ip` | `string` | عنوان IP |
| `check_in_device` | `string` | جهاز الدخول |
| `check_out_at` | `datetime` | وقت الانصراف |
| `check_out_latitude` | `decimal` | خط عرض GPS عند الخروج |
| `check_out_longitude` | `decimal` | خط طول GPS عند الخروج |
| `check_out_distance_meters` | `decimal` | المسافة عند الخروج |
| `check_out_within_geofence` | `boolean` | داخل السياج عند الخروج؟ |
| `status` | `string` | الحالة: `present` / `late` / `absent` / `leave` / `holiday` |
| `delay_minutes` | `int` | دقائق التأخير |
| `early_leave_minutes` | `int` | دقائق المغادرة المبكرة |
| `overtime_minutes` | `int` | دقائق العمل الإضافي |
| `worked_minutes` | `int` | إجمالي دقائق العمل |
| `cost_per_minute` | `decimal:4` | **اللقطة المالية** — تكلفة الدقيقة وقت التسجيل |
| `delay_cost` | `decimal:2` | تكلفة التأخير |
| `early_leave_cost` | `decimal:2` | تكلفة المغادرة المبكرة |
| `overtime_value` | `decimal:2` | قيمة العمل الإضافي (1.5×) |
| `notes` | `text` | ملاحظات |
| `approved_by` | `FK→users` | المعتمد |
| `approved_at` | `datetime` | وقت الاعتماد |
| `is_manual_entry` | `boolean` | إدخال يدوي؟ |

### 🔹 نمط اللقطة المالية — لماذا `cost_per_minute` موجود هنا؟

```
المشكلة: لو غيرنا راتب موظف من 8000 إلى 10000
         سجلات الحضور القديمة ستحسب بالراتب الجديد → خطأ!

الحل:    عند كل check-in نحفظ نسخة من cost_per_minute وقت التسجيل
         هكذا السجلات القديمة تبقى صحيحة حتى لو تغير الراتب

⚠️ لا تحذف هذا العمود أبداً — بدونه كل التقارير المالية تنكسر
```

### 🔹 الدوال

#### `calculateFinancials(): self`
- **الغرض:** يلتقط `cost_per_minute` ويحسب التكاليف
- **⚙️ المنطق:**
  1. `cost_per_minute = user->cost_per_minute` (snapshot!)
  2. `delay_cost = delay_minutes × cost_per_minute`
  3. `early_leave_cost = early_leave_minutes × cost_per_minute`
  4. `overtime_value = overtime_minutes × cost_per_minute × 1.5`

#### `evaluateAttendance(string $shiftStart, int $gracePeriod = 5): self`
- **الغرض:** يحسب التأخير والحالة من وقت المناوبة
- **⚙️ المنطق:**
  1. يحسب الفرق بين `check_in_at` و `$shiftStart`
  2. لو الفرق > `$gracePeriod` → `status = 'late'`، `delay_minutes = الفرق`
  3. لو الفرق <= `$gracePeriod` → `status = 'present'`

### 🔹 النطاقات

| النطاق | الفلتر |
|--------|-------|
| `scopeForDate($date)` | `attendance_date = $date` |
| `scopeLate` | `status = 'late'` |
| `scopeAbsent` | `status = 'absent'` |
| `scopeWithDelayCost` | `delay_cost > 0` |
| `scopeTotalDelayCost` | `sum('delay_cost')` |

---

---

## 7. FinancialReport

📍 **الملف:** `app/Models/FinancialReport.php`
📋 **الغرض:** التقرير المالي المجمّع — يُولّد من سجلات الحضور لفترة معينة
🟡 **الخطورة:** متوسطة

### 🔹 الحقول

| الحقل | النوع | الغرض |
|-------|-------|-------|
| `report_code` | `string` | رمز فريد `FIN-XXX-YYYYMMDDHHmmss-NNN` |
| `scope` | `string` | نطاق: `employee` / `branch` / `department` / `company` |
| `period_type` | `string` | نوع الفترة: `daily` / `weekly` / `monthly` / `custom` |
| `period_start` | `date` | بداية الفترة |
| `period_end` | `date` | نهاية الفترة |
| `user_id` | `FK` | الموظف (لو scope = employee) |
| `branch_id` | `FK` | الفرع |
| `department_id` | `FK` | القسم |
| `total_working_days` | `int` | أيام العمل |
| `total_present_days` | `int` | أيام الحضور |
| `total_late_days` | `int` | أيام التأخير |
| `total_absent_days` | `int` | أيام الغياب |
| `total_leave_days` | `int` | أيام الإجازة |
| `total_delay_minutes` | `int` | إجمالي دقائق التأخير |
| `total_salary_budget` | `decimal:2` | ميزانية الرواتب |
| `total_delay_cost` | `decimal:2` | إجمالي تكلفة التأخير |
| `net_financial_impact` | `decimal:2` | الأثر المالي الصافي |
| `loss_percentage` | `decimal:2` | نسبة الخسارة |
| `meta` | `array(JSON)` | بيانات إضافية |

### 🔹 الدوال

#### `generateForEmployee(User $user, string $start, string $end): self` (static)
- **الغرض:** يُبني تقرير مالي كامل لموظف واحد من سجلات الحضور
- **⚙️ المنطق:**
  1. يجلب كل سجلات الحضور للفترة
  2. يحسب الأيام (حضور/غياب/تأخير/إجازة)
  3. يجمع الدقائق والتكاليف
  4. يحسب نسبة الخسارة = `(delay_cost + early_cost) / salary_budget × 100`
  5. يحفظ التقرير

---

---

## 8. WhistleblowerReport

📍 **الملف:** `app/Models/WhistleblowerReport.php`
📋 **الغرض:** بلاغات المبلغين (Whistleblower) — **مشفرة** بالكامل
🔴 **الخطورة:** عالية — بيانات حساسة ومشفرة

### 🔹 الحقول

| الحقل | النوع | الغرض |
|-------|-------|-------|
| `ticket_number` | `string` | رقم التذكرة `WB-XXXXXXXX-yymmdd` |
| `encrypted_content` | `text` | المحتوى المشفر بـ `encrypt()` |
| `category` | `string` | التصنيف (`fraud`, `harassment`, `safety`, ...) |
| `severity` | `string` | الخطورة (`low`, `medium`, `high`, `critical`) |
| `status` | `string` | الحالة (`new`, `under_review`, `resolved`, ...) |
| `anonymous_token` | `string` | رمز المتابعة المجهول (SHA-256) |
| `encrypted_evidence_paths` | `text` | مسارات الأدلة المشفرة |
| `assigned_to` | `FK→users` | المحقق المعيّن |
| `investigator_notes` | `text` | ملاحظات المحقق |
| `resolved_at` | `datetime` | تاريخ الحل |
| `resolution_outcome` | `string` | نتيجة التحقيق |

### 🔹 الدوال

| الدالة | الغرض |
|--------|-------|
| `setContent(string)` | يشفر ويحفظ المحتوى بـ Laravel `encrypt()` |
| `getContent()` | يفك تشفير المحتوى بـ `decrypt()` |
| `generateTicketNumber()` | static: `WB-XXXXXXXX-yymmdd` |
| `generateAnonymousToken()` | static: SHA-256 token للمتابعة المجهولة |

### 🔐 ملاحظة أمنية
- المحتوى **لا يُقرأ** إلا بـ `getContent()`
- فقط المستوى 10 يقدر يفك التشفير عبر الواجهة
- الرمز المجهول يسمح للمبلغ بالمتابعة بدون كشف هويته

---

---

## 9. Conversation

📍 **الملف:** `app/Models/Conversation.php`
📋 **الغرض:** المحادثة بين الموظفين
🟢 **الخطورة:** منخفضة

### 🔹 الحقول
`title`, `type` (direct/group), `created_by` (FK→users), `is_archived` (boolean)

### 🔹 العلاقات
- `creator()` → `BelongsTo` User
- `participants()` → `BelongsToMany` User (pivot: `conversation_participants` مع `is_muted`, `last_read_at`)
- `messages()` → `HasMany` Message
- `latestMessage()` → `HasOne` Message (latestOfMany)

---

---

## 10. Message

📍 **الملف:** `app/Models/Message.php`
📋 **الغرض:** الرسالة داخل محادثة
🟢 **الخطورة:** منخفضة
**Traits:** SoftDeletes

### 🔹 الحقول
`conversation_id` (FK), `sender_id` (FK→users), `body`, `type` (text/image/file), `attachment_path`, `is_read`, `read_at`

### 🔹 العلاقات
- `conversation()` → `BelongsTo` Conversation
- `sender()` → `BelongsTo` User
- **Scope:** `scopeUnread` → `is_read = false`

---

---

## 11. Circular

📍 **الملف:** `app/Models/Circular.php`
📋 **الغرض:** التعميمات الإدارية — يمكن توجيهها لفرع/قسم/دور محدد
🟢 **الخطورة:** منخفضة

### 🔹 الحقول
`title_ar`, `title_en`, `body_ar`, `body_en`, `priority`, `target_scope`, `target_branch_id` (FK), `target_department_id` (FK), `target_role_id` (FK), `created_by` (FK), `requires_acknowledgment`, `published_at`, `expires_at`

### 🔹 العلاقات
- `creator()` → `BelongsTo` User
- `targetBranch()` → `BelongsTo` Branch
- `targetDepartment()` → `BelongsTo` Department
- `targetRole()` → `BelongsTo` Role
- `acknowledgments()` → `HasMany` CircularAcknowledgment

### 🔹 النطاقات
- `scopePublished` → published_at IS NOT NULL AND <= now()
- `scopeActive` → Published AND not expired

---

---

## 12. CircularAcknowledgment

📍 **الملف:** `app/Models/CircularAcknowledgment.php`
📋 **الغرض:** إقرار الموظف بقراءة التعميم
🟢 **الخطورة:** منخفضة

### 🔹 الحقول
`circular_id` (FK), `user_id` (FK), `acknowledged_at` (datetime)

### 🔹 العلاقات
- `circular()` → `BelongsTo` Circular
- `user()` → `BelongsTo` User

---

---

## 13. PerformanceAlert

📍 **الملف:** `app/Models/PerformanceAlert.php`
📋 **الغرض:** تنبيهات الأداء للموظف (تأخير متكرر، غياب مضطرد، إلخ)
🟢 **الخطورة:** منخفضة

### 🔹 الحقول
`user_id` (FK), `alert_type`, `severity` (info/warning/critical), `title_ar`, `title_en`, `message_ar`, `message_en`, `trigger_data` (JSON array), `is_read`, `read_at`, `dismissed_by` (FK)

### 🔹 العلاقات
- `user()` → `BelongsTo` User
- `dismissedByUser()` → `BelongsTo` User

### 🔹 النطاقات
- `scopeUnread`, `scopeCritical`

---

---

## 14. Badge

📍 **الملف:** `app/Models/Badge.php`
📋 **الغرض:** تعريف الشارات (الشارة نفسها — ليس المنح)
🟢 **الخطورة:** منخفضة

### 🔹 الحقول
`name_ar`, `name_en`, `slug`, `description_ar`, `description_en`, `icon`, `color`, `category`, `points_reward` (int), `criteria` (JSON), `is_active`

### 🔹 العلاقات
- `awards()` → `HasMany` UserBadge

### 🔹 النطاقات
- `scopeActive`, `scopeByCategory($cat)`

---

---

## 15. PointsTransaction

📍 **الملف:** `app/Models/PointsTransaction.php`
📋 **الغرض:** حركات النقاط — كل إضافة أو خصم نقاط مسجلة هنا
🟡 **الخطورة:** متوسطة — الرصيد التراكمي يعتمد عليها

### 🔹 الحقول
`user_id` (FK), `type` (earned/deducted), `amount` (int), `balance_after` (int), `source` (string), `sourceable_type`, `sourceable_id` (polymorphic), `description`

### 🔹 العلاقات
- `user()` → `BelongsTo` User
- `sourceable()` → `MorphTo` (يمكن أن يكون UserBadge, AttendanceLog, أو أي موديل)

### 🔹 النطاقات
- `scopeEarned`, `scopeDeducted`

### 📌 ملاحظة
- `sourceable()` = Polymorphic — أي موديل يمكنه أن يكون مصدر النقاط
- `balance_after` يسجل الرصيد بعد العملية (لا حاجة لحساب تراكمي)

---

---

## 16. TrapInteraction

📍 **الملف:** `app/Models/TrapInteraction.php`
📋 **الغرض:** تسجيل تفاعل الموظف مع مصيدة نفسية
🔴 **الخطورة:** عالية — بيانات سرية

### 🔹 الحقول
`user_id` (FK), `trap_id` (FK), `trap_type`, `trap_element`, `page_url`, `ip_address`, `user_agent`, `interaction_data` (JSON), `risk_level`, `is_reviewed`, `reviewed_by` (FK), `review_notes`, `reviewed_at`

### 🔹 العلاقات
- `user()` → `BelongsTo` User
- `trap()` → `BelongsTo` Trap
- `reviewer()` → `BelongsTo` User

### 🔹 النطاقات
- `scopeUnreviewed`, `scopeHighRisk`

---

---

## 17. Trap

📍 **الملف:** `app/Models/Trap.php`
📋 **الغرض:** تعريف المصيدة النفسية (الفخ الأمني)
🔴 **الخطورة:** عالية

### 🔹 الحقول
`name_ar`, `name_en`, `trap_code`, `description_ar`, `description_en`, `risk_weight` (int 1-10), `fake_response_type`, `is_active`

### 🔹 العلاقات
- `interactions()` → `HasMany` TrapInteraction

### 🔹 الدوال
- `deriveRiskLevel()` — يشتق مستوى الخطر من الوزن: 1-3=low, 4-6=medium, 7-8=high, 9-10=critical

### 🔹 النطاقات
- `scopeActive`, `scopeByCode($code)`

---

---

## 18. LeaveRequest

📍 **الملف:** `app/Models/LeaveRequest.php`
📋 **الغرض:** طلب إجازة موظف
🟡 **الخطورة:** متوسطة
**Traits:** SoftDeletes

### 🔹 الحقول
`user_id` (FK), `leave_type` (annual/sick/emergency/other), `start_date`, `end_date`, `total_days`, `reason`, `attachment_path`, `status` (pending/approved/rejected), `approved_by` (FK), `approved_at`, `rejection_reason`, `estimated_cost` (decimal)

### 🔹 العلاقات
- `user()` → `BelongsTo` User
- `approver()` → `BelongsTo` User

### 🔹 الدوال
- `calculateCost()` — يحسب `estimated_cost = total_days × user->daily_rate`

### 🔹 النطاقات
- `scopePending`, `scopeApproved`

---

---

## 19. Shift

📍 **الملف:** `app/Models/Shift.php`
📋 **الغرض:** تعريف المناوبة (وقت البداية/النهاية/السماح)
🟡 **الخطورة:** متوسطة — تغيير المناوبة يأثر على حساب التأخير

### 🔹 الحقول
`name_ar`, `name_en`, `start_time` (time), `end_time` (time), `grace_period_minutes` (int), `is_overnight` (boolean), `is_active` (boolean)

### 🔹 العلاقات
- `assignments()` → `HasMany` UserShift
- `currentlyAssignedUsers()` → `HasMany` UserShift (active + current فقط)

### 🔹 الدوال
- `getDurationMinutesAttribute()` — يحسب مدة المناوبة بالدقائق (يدعم الليلية)

---

---

## 20. AuditLog

📍 **الملف:** `app/Models/AuditLog.php`
📋 **الغرض:** سجل التدقيق — يسجل كل عملية تعديل/حذف/إنشاء مهمة
🔴 **الخطورة:** عالية — لا يُعدل ولا يُحذف أبداً

### 🔹 الحقول
`user_id` (FK), `action` (string), `auditable_type`, `auditable_id` (polymorphic), `old_values` (JSON), `new_values` (JSON), `ip_address`, `user_agent`, `description`

### 🔹 العلاقات
- `user()` → `BelongsTo` User
- `auditable()` → `MorphTo` (أي موديل)

### 🔹 الدوال

#### `record(string $action, ?Model $model, ?array $old, ?array $new, ?string $desc): self` (static)
- **الغرض:** helper ثابت لتسجيل سجل تدقيق
- **⚙️ المنطق:** يأخذ المستخدم الحالي `auth()->user()` والـ IP والـ User-Agent تلقائياً
- **📌 استخدام:** `AuditLog::record('update', $user, $oldValues, $newValues)`

### 🔹 النطاقات
- `scopeForModel($type, $id)` — سجلات لموديل محدد
- `scopeByAction($action)` — فلترة حسب نوع العملية

---

---

## 21. Holiday

📍 **الملف:** `app/Models/Holiday.php`
📋 **الغرض:** الإجازات الرسمية — يمكن أن تكون عامة أو خاصة بفرع
🟢 **الخطورة:** منخفضة

### 🔹 الحقول
`name_ar`, `name_en`, `date` (date), `type` (national/religious/company), `is_recurring` (boolean), `branch_id` (FK — nullable = عامة)

### 🔹 العلاقات
- `branch()` → `BelongsTo` Branch (nullable)

### 🔹 الدوال

#### `isHoliday(Carbon $date, ?int $branchId): bool` (static)
- **الغرض:** فحص هل تاريخ معين إجازة
- **⚙️ المنطق:** يفحص الإجازات العامة (branch_id = null) + إجازات الفرع المحدد
- **📌 استخدام:** `Holiday::isHoliday(today(), $user->branch_id)`

---

---

## 22. UserShift

📍 **الملف:** `app/Models/UserShift.php`
📋 **الغرض:** **كيان مستقل** (ليس Pivot!) — عقد تعيين مناوبة لموظف بتاريخ بداية ونهاية
🟡 **الخطورة:** متوسطة — تعديل خاطئ يكسر حساب التأخير

### ⭐ لماذا كيان مستقل؟ (v3.4)
```
في v3.3: العلاقة كانت BelongsToMany (pivot بسيط)
المشكلة: ما نقدر نعرف مين عيّن المناوبة، ومتى، وليش
الحل v3.4: UserShift موديل مستقل بحقول إضافية (assigned_by, reason, effective_from/to)
النتيجة: كل تعيين مسجل ومدقق
```

### 🔹 الحقول

| الحقل | النوع | الغرض |
|-------|-------|-------|
| `user_id` | `FK→users` | الموظف |
| `shift_id` | `FK→shifts` | المناوبة |
| `assigned_by` | `FK→users` | من عيّن المناوبة |
| `effective_from` | `date` | تاريخ بداية السريان |
| `effective_to` | `date` | تاريخ نهاية السريان (nullable = مفتوح) |
| `is_current` | `boolean` | التعيين الحالي؟ |
| `reason` | `string` | سبب التعيين/النقل |
| `approved_at` | `datetime` | وقت الموافقة |
| `approved_by` | `FK→users` | من وافق |

### 🔹 العلاقات
- `user()` → `BelongsTo` User
- `shift()` → `BelongsTo` Shift
- `assignedByUser()` → `BelongsTo` User
- `approvedByUser()` → `BelongsTo` User

### 🔹 الدوال

#### `isValidOn($date): bool`
- **الغرض:** هل التعيين صالح في تاريخ معين؟
- **⚙️:** `effective_from <= $date AND (effective_to IS NULL OR effective_to >= $date)`

#### `terminate(?string $reason): void`
- **الغرض:** إنهاء التعيين
- **⚙️:** `effective_to = yesterday, is_current = false`

#### `makeCurrent(): void`
- **الغرض:** تفعيل هذا التعيين وإلغاء البقية
- **⚙️:** يعمل `update(is_current: false)` لكل تعيينات المستخدم ثم `is_current = true` لهذا

### 🔹 النطاقات
- `scopeActive` — صالح اليوم
- `scopeCurrent` — `is_current = true`
- `scopeForUserInPeriod($userId, $start, $end)` — تعيينات تتقاطع مع فترة

---

---

## 23. UserBadge

📍 **الملف:** `app/Models/UserBadge.php`
📋 **الغرض:** **كيان مستقل** (ليس Pivot!) — سجل منح شارة لموظف مع المانح والسبب
🟡 **الخطورة:** متوسطة

### ⭐ لماذا كيان مستقل؟ (v3.4)
```
نفس السبب — نحتاج awarded_by, awarded_reason, النقاط
```

### 🔹 الحقول
`user_id` (FK), `badge_id` (FK), `awarded_at` (datetime), `awarded_reason` (string), `awarded_by` (FK→users)

### 🔹 العلاقات
- `user()` → `BelongsTo` User
- `badge()` → `BelongsTo` Badge
- `awardedByUser()` → `BelongsTo` User

### 🔹 الدوال

#### `award(int $userId, int $badgeId, int $awardedBy, string $reason): self` (static)
- **الغرض:** منح شارة + إضافة نقاط تلقائياً
- **⚙️ المنطق:**
  1. ينشئ UserBadge record
  2. يجلب `points_reward` من Badge
  3. ينشئ `PointsTransaction` (type: earned)
  4. يزيد `User->total_points`

### 🔹 النطاقات
- `scopeAwardedBetween($start, $end)`, `scopeForUser($userId)`

---

---

## 24. UserPermission

📍 **الملف:** `app/Models/UserPermission.php`
📋 **الغرض:** تجاوز صلاحية على مستوى الفرد (grant أو revoke)
🔴 **الخطورة:** عالية — تتجاوز صلاحيات الدور!

### 🔹 الحقول
`user_id` (FK), `permission_id` (FK), `type` (grant/revoke), `granted_by` (FK), `expires_at` (datetime — nullable), `reason`

### 🔹 العلاقات
- `user()` → `BelongsTo` User
- `permission()` → `BelongsTo` Permission
- `grantedByUser()` → `BelongsTo` User

### 🔹 الدوال
- `isActive()` — يفحص هل التجاوز لا زال صالحاً (لم تنتهي صلاحيته)

### 🔹 النطاقات
- `scopeActive` — غير منتهي
- `scopeGrants` — `type = 'grant'`
- `scopeRevocations` — `type = 'revoke'`

### 📌 ملاحظة الأولوية
```
hasPermission() في User يفحص بهالترتيب:
1. super_admin = true → true دائماً
2. UserPermission type=revoke → false (أقوى من الدور!)
3. UserPermission type=grant → true (أقوى من الدور!)
4. Role->permissions → الفحص العادي
```

---

---

## 25. Payroll

📍 **الملف:** `app/Models/Payroll.php`
📋 **الغرض:** مسير الرواتب الشهري
🔴 **الخطورة:** عالية — بيانات مالية حساسة
**Traits:** SoftDeletes

### 🔹 الحقول

| الحقل | النوع | الغرض |
|-------|-------|-------|
| `user_id` | `FK` | الموظف |
| `branch_id` | `FK` | الفرع |
| `period` | `string` | الفترة `YYYY-MM` |
| `basic_salary` | `decimal:2` | الراتب الأساسي (نسخة) |
| `housing_allowance` | `decimal:2` | بدل السكن (نسخة) |
| `transport_allowance` | `decimal:2` | بدل النقل (نسخة) |
| `other_allowances` | `decimal:2` | بدلات أخرى (نسخة) |
| `gross_salary` | `decimal:2` | إجمالي الراتب |
| `delay_deductions` | `decimal:2` | خصومات التأخير |
| `early_leave_deductions` | `decimal:2` | خصومات المغادرة المبكرة |
| `absence_deductions` | `decimal:2` | خصومات الغياب |
| `other_deductions` | `decimal:2` | خصومات أخرى |
| `total_deductions` | `decimal:2` | إجمالي الخصومات |
| `overtime_pay` | `decimal:2` | أجر العمل الإضافي |
| `bonuses` | `decimal:2` | مكافآت |
| `total_additions` | `decimal:2` | إجمالي الإضافات |
| `net_salary` | `decimal:2` | صافي الراتب |
| `total_working_days` | `int` | أيام العمل |
| `present_days` | `int` | أيام الحضور |
| `absent_days` | `int` | أيام الغياب |
| `late_days` | `int` | أيام التأخير |
| `total_delay_minutes` | `int` | دقائق التأخير |
| `total_overtime_minutes` | `int` | دقائق العمل الإضافي |
| `status` | `string` | draft/approved/paid |
| `approved_by` | `FK` | المعتمد |

### 🔹 الدوال

#### `generateForUser(User $user, string $period): self` (static)
- **الغرض:** يُولّد مسير رواتب كامل من بيانات الحضور
- **⚙️ المنطق:**
  1. يأخذ الراتب والبدلات الحالية (snapshot)
  2. يجلب سجلات الحضور للفترة
  3. يحسب الخصومات من `delay_cost + early_leave_cost`
  4. يحسب أجر العمل الإضافي
  5. `net_salary = gross - deductions + additions`
  6. يستخدم `updateOrCreate` لتجنب التكرار

### 🔹 النطاقات
- `scopeForPeriod($period)`, `scopeApproved`, `scopePaid`

---

---

## 26. Setting

📍 **الملف:** `app/Models/Setting.php`
📋 **الغرض:** إعدادات النظام — **Singleton Pattern** (سجل واحد فقط في الجدول)
🔴 **الخطورة:** عالية — تؤثر على كل النظام

### 🔹 الحقول

| الحقل | النوع | الغرض |
|-------|-------|-------|
| `app_name` | `string` | اسم التطبيق عربي |
| `app_name_en` | `string` | اسم التطبيق إنجليزي |
| `welcome_title` | `string` | عنوان الترحيب |
| `welcome_body` | `text` | نص الترحيب |
| `logo_path` | `string` | مسار الشعار |
| `favicon_path` | `string` | مسار الأيقونة |
| `pwa_name` | `string` | اسم PWA |
| `pwa_short_name` | `string` | الاسم المختصر |
| `pwa_theme_color` | `string` | لون الثيم |
| `pwa_background_color` | `string` | لون الخلفية |
| `logic_settings` | `JSON` | **إعدادات المنطق — أهم حقل** |

### 🔹 إعدادات المنطق (logic_settings)

```php
DEFAULT_LOGIC_SETTINGS = [
    'loss_multiplier'          => 2.0,   // مضاعف الخسائر
    'default_geofence_radius'  => 100,   // نصف قطر السياج الافتراضي (أمتار)
    'default_grace_period'     => 10,    // فترة السماح الافتراضية (دقائق)
    'overtime_multiplier'      => 1.5,   // مضاعف العمل الإضافي
]
```

### 🔹 الدوال

#### `instance(): static` (static — Singleton)
- **الغرض:** يرجع نسخة واحدة مع cache (1 ساعة TTL)
- **⚙️ المنطق:** `Cache::remember('settings', 3600, fn() => static::first())`
- **📌 استخدام:** `Setting::instance()->app_name`

#### `freshInstance(): static` (static)
- **الغرض:** نسخة بدون cache — للتعديل فقط

#### `getLogicSetting(string $key, mixed $default): mixed`
- **الغرض:** يرجع إعداد منطقي مع fallback

#### `booted()`
- **الغرض:** يمسح الـ cache تلقائياً عند الحفظ → التغييرات تسري فوراً

---

---

## 27. ReportFormula

📍 **الملف:** `app/Models/ReportFormula.php`
📋 **الغرض:** صيغ حسابية مخصصة — محرك صيغ ديناميكي
🟡 **الخطورة:** متوسطة

### 🔹 الحقول
`name_ar`, `name_en`, `slug`, `formula` (string), `variables` (JSON), `description_ar`, `description_en`, `is_active`, `created_by` (FK)

### 🔹 الدوال

#### `evaluate(array $values): ?float`
- **الغرض:** يقيّم الصيغة بقيم المتغيرات المعطاة
- **⚙️ المنطق:** يستبدل المتغيرات بقيمها، ينظف الصيغة (يسمح فقط بـ `0-9 +−×÷ .()`)، ثم يقيّمها
- **⚠️:** يستخدم `eval()` — لكن مع تنظيف صارم

#### `validateFormula(): bool`
- **الغرض:** التحقق من صحة الصيغة بتجربتها مع قيم 1.0

### 🔹 النطاقات
- `scopeActive`

---

---

## 28. ScoreAdjustment

📍 **الملف:** `app/Models/ScoreAdjustment.php`
📋 **الغرض:** تعديل يدوي لنقاط فرع/موظف/قسم
🟡 **الخطورة:** متوسطة

### 🔹 الحقول
`scope` (branch/user/department), `branch_id` (FK), `user_id` (FK), `department_id` (FK), `points_delta` (int), `value_delta` (decimal), `category`, `reason`, `adjusted_by` (FK)

### 🔹 العلاقات
- `branch()`, `user()`, `department()` — كلها `BelongsTo`
- `adjustedByUser()` → `BelongsTo` User

### 🔹 النطاقات
- `scopeForBranch($id)`, `scopeForUser($id)`, `scopePositive`, `scopeNegative`

---

---

## 29. AnalyticsSnapshot

📍 **الملف:** `app/Models/AnalyticsSnapshot.php`
📋 **الغرض:** لقطة تحليلات دورية (يومية/أسبوعية/شهرية) لكل فرع
🟡 **الخطورة:** متوسطة — بيانات تحليلية مهمة

### 🔹 الحقول

| الحقل | النوع | الغرض |
|-------|-------|-------|
| `branch_id` | `FK` | الفرع |
| `snapshot_date` | `date` | تاريخ اللقطة |
| `period_type` | `string` | daily/weekly/monthly |
| `total_employees` | `int` | عدد الموظفين |
| `present_count` | `int` | عدد الحاضرين |
| `absent_count` | `int` | عدد الغائبين |
| `late_count` | `int` | عدد المتأخرين |
| `attendance_rate` | `decimal:2` | نسبة الحضور |
| `total_delay_minutes` | `int` | إجمالي دقائق التأخير |
| `avg_delay_minutes` | `decimal:2` | متوسط التأخير |
| `total_salary_cost` | `decimal:2` | إجمالي تكلفة الرواتب |
| `delay_losses` | `decimal:2` | خسائر التأخير |
| `absence_losses` | `decimal:2` | خسائر الغياب |
| `early_leave_losses` | `decimal:2` | خسائر المغادرة المبكرة |
| `total_losses` | `decimal:2` | إجمالي الخسائر |
| `overtime_cost` | `decimal:2` | تكلفة العمل الإضافي |
| `vpm` | `decimal:2` | القيمة لكل دقيقة |
| `productivity_gap` | `decimal:2` | فجوة الإنتاجية |
| `loss_ratio` | `decimal:2` | نسبة الخسارة |
| `efficiency_score` | `decimal:2` | درجة الكفاءة |
| `roi_discipline` | `decimal:2` | عائد الاستثمار في الانضباط |
| `hourly_checkin_distribution` | `JSON` | توزيع ساعات الدخول |
| `daily_pattern_data` | `JSON` | بيانات النمط اليومي |

### 🔹 الدوال
- `getLossPercentage()` — `(total_losses / total_salary_cost) × 100`
- `isAboveThreshold(5.0)` — هل نسبة الخسارة فوق الحد؟

### 🔹 النطاقات
- `scopeDaily`, `scopeWeekly`, `scopeMonthly`, `scopeForBranch($id)`, `scopeForDateRange($start, $end)`

---

---

## 30. LossAlert

📍 **الملف:** `app/Models/LossAlert.php`
📋 **الغرض:** تنبيه خسارة — يُطلق عند تجاوز حد الخسائر
🟢 **الخطورة:** منخفضة

### 🔹 الحقول
`branch_id` (FK), `triggered_by_user` (FK), `alert_date`, `alert_type`, `severity` (info/warning/critical), `threshold_value`, `actual_value`, `description_ar`, `description_en`, `context_data` (JSON), `is_acknowledged`, `acknowledged_by` (FK), `acknowledged_at`, `resolution_notes`

### 🔹 العلاقات
- `branch()`, `triggeredByUser()`, `acknowledgedByUser()` — كلها `BelongsTo`

### 🔹 الدوال
- `acknowledge(int $userId, ?string $notes)` — وضع علامة "تمت الملاحظة"
- `getSeverityColor()` / `getSeverityLabel()` — ألوان وأسماء Filament

### 🔹 النطاقات
- `scopeUnacknowledged`, `scopeCritical`, `scopeForBranch($id)`, `scopeRecent($days = 7)`

---

---

## 31. EmployeePattern

📍 **الملف:** `app/Models/EmployeePattern.php`
📋 **الغرض:** أنماط سلوك الموظف — يُكتشف بواسطة AnalyticsService
🟢 **الخطورة:** منخفضة

### 🔹 الحقول
`user_id` (FK), `branch_id` (FK), `pattern_type`, `frequency_score` (decimal), `financial_impact` (decimal), `pattern_data` (JSON), `description_ar`, `description_en`, `risk_level`, `detected_at` (date), `valid_until` (date), `is_active`

### 🔹 أنواع الأنماط
```
frequent_late       → تأخير متكرر
pre_holiday_absence → غياب ما قبل الإجازة
monthly_cycle       → نمط شهري
burnout_risk        → خطر إرهاق
improving           → تحسّن ملحوظ
declining           → تراجع مستمر
```

### 🔹 العلاقات
- `user()`, `branch()` → `BelongsTo`

### 🔹 الدوال
- `patternTypes()` (static) — قاموس الأنواع بالعربي
- `getPatternLabel()` — الاسم العربي لهالنمط
- `getRiskColor()` — اللون في Filament

### 🔹 النطاقات
- `scopeActive`, `scopeHighRisk`, `scopeForUser($id)`, `scopeByType($type)`

---

---

## 32. AttendanceException

📍 **الملف:** `app/Models/AttendanceException.php`
📋 **الغرض:** استثناء حضور — ساعات مرنة، عمل عن بعد، تجاوز سياج
🟡 **الخطورة:** متوسطة — يأثر على حساب التأخير

### 🔹 الحقول

| الحقل | النوع | الغرض |
|-------|-------|-------|
| `user_id` | `FK→users` | الموظف |
| `exception_type` | `string` | نوع الاستثناء (flexible_hours, remote_work, bypass_geofence) |
| `custom_shift_start` | `time` | وقت بداية مخصص |
| `custom_shift_end` | `time` | وقت نهاية مخصص |
| `custom_grace_minutes` | `int` | فترة سماح مخصصة |
| `bypass_geofence` | `boolean` | تجاوز السياج الجغرافي |
| `bypass_late_flag` | `boolean` | تجاوز علامة التأخير |
| `start_date` | `date` | بداية الاستثناء |
| `end_date` | `date` | نهاية الاستثناء (nullable = دائم) |
| `reason` | `string` | السبب |
| `approved_by` | `FK→users` | المعتمد |
| `is_active` | `boolean` | فعال؟ |

### 🔹 العلاقات
- `user()` → `BelongsTo` User
- `approvedByUser()` → `BelongsTo` User

### 🔹 الدوال

#### `getActiveForUser(int $userId): ?self` (static)
- **الغرض:** يرجع الاستثناء النشط للموظف اليوم
- **⚙️:** فلترة بـ `activeToday` scope + `user_id`

#### `isValidToday(): bool`
- **الغرض:** هل الاستثناء يغطي اليوم؟

#### `getEffectiveShiftStart(): ?string`
- **الغرض:** يرجع وقت البداية المخصص أو null للافتراضي

#### `getEffectiveGracePeriod(): ?int`
- **الغرض:** يرجع فترة السماح المخصصة أو null للافتراضي

### 🔹 النطاقات
- `scopeActiveToday` — فعال ويغطي اليوم
- `scopeForUser($userId)`

### 📌 كيف يُستخدم في AttendanceService
```
عند check-in:
1. يفحص getActiveForUser($userId)
2. لو موجود استثناء bypass_geofence → يتجاوز فحص GPS
3. لو موجود custom_shift_start → يستخدمه بدل المناوبة الأصلية
4. لو موجود custom_grace_minutes → يستخدمه بدل الافتراضي
```

---

---

## 📊 ملخص الإحصائيات

| المقياس | العدد |
|---------|-------|
| إجمالي الموديلات | 32 |
| إجمالي العلاقات | ~120 |
| إجمالي النطاقات (Scopes) | ~80 |
| إجمالي الدوال المخصصة | ~70 |
| موديلات بـ SoftDeletes | 5 (User, Message, Circular, LeaveRequest, Payroll) |
| موديلات بـ Polymorphic | 2 (PointsTransaction, AuditLog) |
| كيانات مستقلة (v3.4) | 2 (UserShift, UserBadge) |
| موديل Singleton | 1 (Setting) |

---

> **صرح الإتقان v3.4.1** — *"كل موديل موثق. كل حقل مشروح. كل دالة مفهومة."*
