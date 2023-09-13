<?php

declare(strict_types=1);

namespace MinVWS\AuditLogger\Tests;

use MinVWS\AuditLogger\Events\Logging\UserLoginLogEvent;
use MinVWS\AuditLogger\Loggers\LoggerInterface;
use MinVWS\AuditLogger\AuditLogger;
use Mockery;

class LogServiceTest extends Mockery\Adapter\Phpunit\MockeryTestCase
{
    public function testContructedLoggers(): void
    {
        $mockService = Mockery::mock(LoggerInterface::class);
        $mockService->shouldReceive('canHandleEvent')->once()->andReturn(false);

        $mockService2 = Mockery::mock(LoggerInterface::class);
        $mockService2->shouldReceive('canHandleEvent')->once()->andReturn(false);

        $service = new AuditLogger([$mockService, $mockService2]);
        $service->log(new UserLoginLogEvent());
    }

    public function testAddedLoggers(): void
    {
        $mockService = Mockery::mock(LoggerInterface::class);
        $mockService->shouldReceive('canHandleEvent')->once()->andReturn(false);

        $service = new AuditLogger([]);
        $service->addLogger($mockService);
        $service->log(new UserLoginLogEvent());
    }

    public function testLogging(): void
    {
        $mockService = Mockery::mock(LoggerInterface::class);
        $mockService->shouldReceive('canHandleEvent')->once()->andReturn(true);
        $mockService->shouldReceive('log')->once();

        $service = new AuditLogger([$mockService]);
        $service->log(new UserLoginLogEvent());
    }
}
