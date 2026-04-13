<?php

declare(strict_types=1);

namespace MinVWS\AuditLogger;

use MinVWS\AuditLogger\Loggers\LogEventInterface;
use MinVWS\AuditLogger\Loggers\LoggerInterface;

interface AuditLoggerInterface
{
    public function addLogger(LoggerInterface $logger): void;

    public function log(LogEventInterface $event): void;
}
