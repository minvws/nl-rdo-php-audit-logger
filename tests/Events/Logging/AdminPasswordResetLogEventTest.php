<?php

declare(strict_types=1);

namespace MinVWS\AuditLogger\Tests\Events\Logging;

use MinVWS\AuditLogger\Events\Logging\AdminPasswordResetLogEvent;
use PHPUnit\Framework\TestCase;

class AdminPasswordResetLogEventTest extends TestCase
{
    public function testEvent()
    {
        $event = new AdminPasswordResetLogEvent(null, null, [], [], '', false, false, '');

        $this->assertEquals(AdminPasswordResetLogEvent::EVENT_KEY, $event->getEventKey());
        $this->assertEquals(AdminPasswordResetLogEvent::EVENT_CODE, $event->getEventCode());
    }
}
