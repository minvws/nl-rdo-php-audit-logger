<?php

declare(strict_types=1);

namespace MinVWS\AuditLogger\Events\Logging;

final class FileUploadLogEvent extends GeneralLogEvent
{
    public const EVENT_CODE = '080001';
    public const EVENT_KEY = 'file_upload';
}
