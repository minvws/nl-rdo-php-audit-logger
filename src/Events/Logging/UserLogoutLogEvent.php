<?php

declare(strict_types=1);

namespace MinVWS\AuditLogger\Events\Logging;

class UserLogoutLogEvent extends GeneralLogEvent
{
    public const EVENT_CODE = '092222';
    public const EVENT_KEY = 'user_logout';
}
