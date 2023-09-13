<?php

declare(strict_types=1);

namespace MinVWS\AuditLogger\Events\Logging;

class AdminPasswordResetLogEvent extends GeneralLogEvent
{
    public const EVENT_CODE = '090005';
    public const EVENT_KEY = 'admin_password_reset';
}
