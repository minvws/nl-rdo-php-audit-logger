<?php

declare(strict_types=1);

namespace MinVWS\AuditLogger\Events\Logging;

final class OrganisationChangeLogEvent extends GeneralLogEvent
{
    public const EVENT_CODE = '090013';
    public const EVENT_KEY = 'organisation_changed';
}
