<?php

declare(strict_types=1);

namespace MinVWS\AuditLogger\Events\Logging;

final class AccountChangeLogEvent extends GeneralLogEvent
{
    public const EVENT_CODE = '090001';
    public const EVENT_KEY = 'account_change';

    // The account change log event encompasses multiple event codes.
    // Use "withEventCode()" to set the correct event code.

    public const EVENTCODE_USERDATA = '900101';
    public const EVENTCODE_ROLES = '900102';
    public const EVENTCODE_TIMESLOT = '900103';
    public const EVENTCODE_ACTIVE = '900104';
    public const EVENTCODE_RESET = '900105';
    public const EVENTCODE_ADDRESS = '900106';

    public const EVENTCODE_KVTB_USERDATA = '900201';
    public const EVENTCODE_KVTB_ROLES = '900202';
    public const EVENTCODE_KVTB_RESET = '900203';
}
