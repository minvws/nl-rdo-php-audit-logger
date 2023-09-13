<?php

declare(strict_types=1);

namespace MinVWS\AuditLogger\Events\Logging;

class AmpUploadEvent extends GeneralLogEvent
{
    public const EVENT_CODE = '090006';
    public const EVENT_KEY = 'amp_upload';
}
