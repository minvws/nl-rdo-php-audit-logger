<?php

declare(strict_types=1);

namespace MinVWS\AuditLogger\Tests\Events\Logging;

use MinVWS\AuditLogger\Contracts\LoggableUser;
use MinVWS\AuditLogger\Events\Logging\AdminPasswordResetLogEvent;
use Mockery;
use PHPUnit\Framework\TestCase;

class FluentSettersTest extends TestCase
{
    public function testFluentSetters()
    {
        $actor = Mockery::mock(LoggableUser::class);
        $actor->expects('getId')->andReturns("1234")->zeroOrMoreTimes();

        $event = (new AdminPasswordResetLogEvent())
            ->asCreate()
            ->withActor($actor)
            ->withData(['foo' => 'bar'])
            ->withSource('source')
            ->withFailed(true, 'reason')
        ;

        $data = $event->getLogData();
        $this->assertEquals('C', $data['action_code']);
        $this->assertEquals('1234', $data['user_id']);
        $this->assertEquals(['foo' => 'bar'], $data['request']);
        $this->assertTrue($data['failed']);
        $this->assertEquals('reason', $data['failed_reason']);
        $this->assertEquals('090005', $event->getEventCode());
        $this->assertEquals('admin_password_reset', $event->getEventKey());

        $event = $event->asDelete();
        $data = $event->getLogData();
        $this->assertEquals('D', $data['action_code']);
        $this->assertEquals('1234', $data['user_id']);
        $this->assertEquals(['foo' => 'bar'], $data['request']);
        $this->assertTrue($data['failed']);
        $this->assertEquals('reason', $data['failed_reason']);
        $this->assertEquals('090005', $event->getEventCode());
        $this->assertEquals('admin_password_reset', $event->getEventKey());

        $event = $event->withData(['a', 'b']);
        $data = $event->getLogData();
        $this->assertEquals('D', $data['action_code']);
        $this->assertEquals('1234', $data['user_id']);
        $this->assertEquals(['a', 'b'], $data['request']);
        $this->assertTrue($data['failed']);
        $this->assertEquals('reason', $data['failed_reason']);
        $this->assertEquals('090005', $event->getEventCode());
        $this->assertEquals('admin_password_reset', $event->getEventKey());
    }
}
