<?php

declare(strict_types=1);

namespace MinVWS\AuditLogger;

use MinVWS\AuditLogger\Loggers\LogEventInterface;
use MinVWS\AuditLogger\Loggers\LoggerInterface;

class AuditLogger
{
    /**
     * @param array<LoggerInterface> $loggers
     */
    public function __construct(protected array $loggers = [])
    {
    }

    /**
     * Adds an extra logger adapter to the service. Does not check if the same logger is already present.
     */
    public function addLogger(LoggerInterface $logger): void
    {
        $this->loggers[] = $logger;
    }

    /**
     * Logs the given event to the connected logger adapters.
     */
    public function log(LogEventInterface $event): void
    {
        foreach ($this->loggers as $logger) {
            if (!$logger->canHandleEvent($event)) {
                continue;
            }

            $logger->log($event);
        }
    }
}
