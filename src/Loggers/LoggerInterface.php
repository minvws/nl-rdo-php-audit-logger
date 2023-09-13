<?php

declare(strict_types=1);

namespace MinVWS\AuditLogger\Loggers;

interface LoggerInterface
{
    public function log(LogEventInterface $event): void;

    public function canHandleEvent(LogEventInterface $event): bool;
}
