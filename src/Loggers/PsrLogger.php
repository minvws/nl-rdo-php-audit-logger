<?php

declare(strict_types=1);

namespace MinVWS\AuditLogger\Loggers;

use MinVWS\AuditLogger\EncryptionHandler;
use MinVWS\AuditLogger\Events\Logging\GeneralLogEvent;
use Psr\Log\LoggerInterface as PsrLoggerInterface;

final class PsrLogger implements LoggerInterface
{
    protected PsrLoggerInterface $logger;
    protected bool $logPiiData;
    protected EncryptionHandler $encryptionHandler;

    public function __construct(
        EncryptionHandler $encryptionHandler,
        PsrLoggerInterface $logger,
        bool $logPiiData = false,
    ) {
        $this->encryptionHandler = $encryptionHandler;
        $this->logger = $logger;
        $this->logPiiData = $logPiiData;
    }

    public function log(LogEventInterface $event): void
    {
        $data = $this->logPiiData ? $event->getMergedPiiData() : $event->getLogData();

        $data = json_encode($data, JSON_THROW_ON_ERROR);
        if ($this->encryptionHandler->isEnabled()) {
            $data = $this->encryptionHandler->encrypt($data);
        }

        $this->logger->info('AuditLog: ' . $data);
    }

    public function canHandleEvent(LogEventInterface $event): bool
    {
        if (is_a($event, GeneralLogEvent::class)) {
            return true;
        }

        return false;
    }
}
