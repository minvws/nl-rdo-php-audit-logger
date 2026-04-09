<?php

declare(strict_types=1);

namespace MinVWS\AuditLogger;

use MinVWS\AuditLogger\Loggers\LogEventInterface;
use MinVWS\AuditLogger\Loggers\LoggerInterface;

final class AuditLogger implements AuditLoggerInterface
{
    /**
     * @param array<LoggerInterface> $loggers
     */
    public function __construct(protected array $loggers = [])
    {
    }

    public function addLogger(LoggerInterface $logger): void
    {
        $this->loggers[] = $logger;
    }

    public function log(LogEventInterface $event): void
    {
        foreach ($this->loggers as $logger) {
            if (! $logger->canHandleEvent($event)) {
                continue;
            }

            $logger->log($event);
        }
    }
}
