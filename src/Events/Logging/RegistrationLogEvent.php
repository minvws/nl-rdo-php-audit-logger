<?php

declare(strict_types=1);

namespace MinVWS\AuditLogger\Events\Logging;

final class RegistrationLogEvent extends GeneralLogEvent
{
    public const EVENT_CODE = '080004';
    public const EVENT_KEY = 'registration';
}
