<?php

namespace App\Events;

use App\Models\UserBadge;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class BadgeAwarded
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public UserBadge $userBadge,
    ) {}
}
