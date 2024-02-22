<?php

declare(strict_types=1);

namespace MinVWS\AuditLogger\Tests\Events\Logging;

use MinVWS\AuditLogger\Events\Logging\AdminPasswordResetLogEvent;
use MinVWS\AuditLogger\Tests\TestCase;

final class AdminPasswordResetLogEventTest extends TestCase
{
    public function testEvent(): void
    {
        $event = new AdminPasswordResetLogEvent();

        self::assertEquals(AdminPasswordResetLogEvent::EVENT_KEY, $event->getEventKey());
        self::assertEquals(AdminPasswordResetLogEvent::EVENT_CODE, $event->getEventCode());
    }
}
