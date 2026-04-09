<?php

declare(strict_types=1);

namespace MinVWS\AuditLogger;

use MinVWS\AuditLogger\Loggers\LogEventInterface;
use MinVWS\AuditLogger\Loggers\LoggerInterface;

interface AuditLoggerInterface
{
    /**
     * Adds an extra logger adapter to the service. Does not check if the same logger is already present.
     */
    public function addLogger(LoggerInterface $logger): void;

    /**
     * Logs the given event to the connected logger adapters.
     */
    public function log(LogEventInterface $event): void;
}
