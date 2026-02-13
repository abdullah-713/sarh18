<?php

namespace App\Listeners;

use App\Events\TrapTriggered;
use App\Models\AuditLog;
use Illuminate\Support\Facades\Log;

class LogTrapInteraction
{
    public function handle(TrapTriggered $event): void
    {
        $interaction = $event->interaction;

        try {
            AuditLog::record(
                'trap.triggered',
                $interaction->user_id,
                null,
                [
                    'trap_id'      => $interaction->trap_id,
                    'trap_type'    => $interaction->trap_type,
                    'trap_element' => $interaction->trap_element,
                    'risk_level'   => $interaction->risk_level,
                    'ip_address'   => $interaction->ip_address,
                    'page_url'     => $interaction->page_url,
                ],
                'تفاعل مع فخ أمني'
            );
        } catch (\Exception $e) {
            Log::warning('LogTrapInteraction: فشل تسجيل التفاعل', [
                'interaction_id' => $interaction->id,
                'error'          => $e->getMessage(),
            ]);
        }
    }
}
