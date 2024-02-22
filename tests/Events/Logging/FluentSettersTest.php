<?php

declare(strict_types=1);

namespace MinVWS\AuditLogger\Tests\Events\Logging;

use MinVWS\AuditLogger\Contracts\LoggableUser;
use MinVWS\AuditLogger\Events\Logging\AdminPasswordResetLogEvent;
use MinVWS\AuditLogger\Tests\TestCase;
use Mockery;

final class FluentSettersTest extends TestCase
{
    public function testFluentSetters(): void
    {
        $actor = Mockery::mock(LoggableUser::class);
        $actor->shouldReceive('getAuditId')->andReturn('1234');

        $event = (new AdminPasswordResetLogEvent())
            ->asCreate()
            ->withActor($actor)
            ->withData(['foo' => 'bar'])
            ->withSource('source')
            ->withFailed(true, 'reason');

        $data = $event->getLogData();
        self::assertEquals('C', $data['action_code']);
        self::assertEquals('1234', $data['user_id']);
        self::assertEquals(['foo' => 'bar'], $data['request']);
        self::assertTrue($data['failed']);
        self::assertEquals('reason', $data['failed_reason']);
        self::assertEquals('090005', $event->getEventCode());
        self::assertEquals('admin_password_reset', $event->getEventKey());

        $event = $event->asDelete();
        $data = $event->getLogData();
        self::assertEquals('D', $data['action_code']);
        self::assertEquals('1234', $data['user_id']);
        self::assertEquals(['foo' => 'bar'], $data['request']);
        self::assertTrue($data['failed']);
        self::assertEquals('reason', $data['failed_reason']);
        self::assertEquals('090005', $event->getEventCode());
        self::assertEquals('admin_password_reset', $event->getEventKey());

        $event = $event->withData(['a', 'b']);
        $data = $event->getLogData();
        self::assertEquals('D', $data['action_code']);
        self::assertEquals('1234', $data['user_id']);
        self::assertEquals(['a', 'b'], $data['request']);
        self::assertTrue($data['failed']);
        self::assertEquals('reason', $data['failed_reason']);
        self::assertEquals('090005', $event->getEventCode());
        self::assertEquals('admin_password_reset', $event->getEventKey());
    }
}
