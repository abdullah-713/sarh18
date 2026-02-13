<?php

namespace App\Jobs;

use App\Models\Circular;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class SendCircularJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, SerializesModels;

    public int $timeout = 120;
    public int $tries = 2;

    public function __construct(
        protected Circular $circular,
        protected array $userIds,
    ) {}

    public function handle(): void
    {
        Log::info("SendCircularJob: إرسال تعميم #{$this->circular->id} إلى " . count($this->userIds) . ' موظف');

        $users = User::whereIn('id', $this->userIds)->get();

        foreach ($users->chunk(100) as $chunk) {
            foreach ($chunk as $user) {
                try {
                    // إنشاء إشعار داخلي (PerformanceAlert) كبديل عملي
                    \App\Models\PerformanceAlert::create([
                        'user_id'    => $user->id,
                        'alert_type' => 'circular',
                        'severity'   => $this->circular->priority === 'urgent' ? 'warning' : 'info',
                        'title_ar'   => $this->circular->title_ar,
                        'title_en'   => $this->circular->title_en ?? $this->circular->title_ar,
                        'message_ar' => mb_substr(strip_tags($this->circular->body_ar), 0, 500),
                        'message_en' => mb_substr(strip_tags($this->circular->body_en ?? $this->circular->body_ar), 0, 500),
                        'trigger_data' => [
                            'circular_id' => $this->circular->id,
                            'priority'    => $this->circular->priority,
                        ],
                    ]);
                } catch (\Exception $e) {
                    Log::warning("SendCircularJob: فشل إرسال إلى المستخدم {$user->id}", [
                        'error' => $e->getMessage(),
                    ]);
                }
            }

            // تجنب الضغط على الـ DB
            if ($users->count() > 100) {
                sleep(1);
            }
        }

        Log::info("SendCircularJob: اكتمل إرسال تعميم #{$this->circular->id}");
    }
}
