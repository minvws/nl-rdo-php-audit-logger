<?php

declare(strict_types=1);

namespace MinVWS\AuditLogger\Tests;

use MinVWS\AuditLogger\Events\Logging\GeneralLogEvent;

class CustomLogEvent extends GeneralLogEvent
{
    public const EVENT_CODE = '999999';
    public const EVENT_KEY = 'custom_test_event';
}
