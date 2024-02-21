<?php

declare(strict_types=1);

namespace MinVWS\AuditLogger\Events\Logging;

final class ResetCredentialsLogEvent extends GeneralLogEvent
{
    public const EVENT_CODE = '090003';
    public const EVENT_KEY = 'reset_credentials';
}
