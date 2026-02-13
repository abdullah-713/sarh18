<?php

namespace Tests\Unit;

use App\Exceptions\BusinessException;
use App\Exceptions\OutOfGeofenceException;
use Tests\TestCase;

class ExceptionTest extends TestCase
{
    /**
     * TC-EXC-001: BusinessException يحمل رسالة المستخدم وكود HTTP
     */
    public function test_business_exception_carries_user_message(): void
    {
        $e = new BusinessException('الرصيد غير كافٍ', 'Insufficient balance for user 5', 422);

        $this->assertEquals('الرصيد غير كافٍ', $e->getUserMessage());
        $this->assertEquals(422, $e->getHttpCode());
        $this->assertEquals('Insufficient balance for user 5', $e->getMessage());
    }

    /**
     * TC-EXC-002: BusinessException يستخدم رسالة المستخدم كرسالة افتراضية
     */
    public function test_business_exception_defaults_log_message(): void
    {
        $e = new BusinessException('خطأ في البيانات');

        $this->assertEquals('خطأ في البيانات', $e->getMessage());
        $this->assertEquals('خطأ في البيانات', $e->getUserMessage());
        $this->assertEquals(422, $e->getHttpCode());
    }

    /**
     * TC-EXC-003: OutOfGeofenceException يحمل المسافة والنطاق
     */
    public function test_out_of_geofence_exception_carries_distance(): void
    {
        $e = new OutOfGeofenceException(45.678, 17.0);

        $this->assertEquals(45.68, $e->getDistance());
        $this->assertEquals(17.0, $e->getAllowedRadius());
    }

    /**
     * TC-EXC-004: BusinessException يقبل كود HTTP مخصص
     */
    public function test_business_exception_custom_http_code(): void
    {
        $e = new BusinessException('غير مصرح', null, 403);

        $this->assertEquals(403, $e->getHttpCode());
    }
}
