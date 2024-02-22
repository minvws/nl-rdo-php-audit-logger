<?php

declare(strict_types=1);

namespace MinVWS\AuditLogger\Events\Logging;

final class LogAccessLogEvent extends GeneralLogEvent
{
    public const EVENT_CODE = '080002';
    public const EVENT_KEY = 'log_access';
}
