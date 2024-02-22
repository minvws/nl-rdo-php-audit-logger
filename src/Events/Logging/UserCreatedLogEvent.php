<?php

declare(strict_types=1);

namespace MinVWS\AuditLogger\Events\Logging;

final class UserCreatedLogEvent extends GeneralLogEvent
{
    public const EVENT_CODE = '090002';
    public const EVENT_KEY = 'user_created';
}
