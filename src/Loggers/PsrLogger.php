<?php

declare(strict_types=1);

namespace MinVWS\AuditLogger\Loggers;

use MinVWS\AuditLogger\EncryptionHandler;
use MinVWS\AuditLogger\Events\Logging\GeneralLogEvent;
use Psr\Log\LoggerInterface as PsrLoggerInterface;

class PsrLogger implements LoggerInterface
{
    protected PsrLoggerInterface $logger;
    protected bool $logPiiData;
    protected ?EncryptionHandler $encryptionHandler;

    public function __construct(PsrLoggerInterface $logger, bool $logPiiData = false, EncryptionHandler $encryptionHandler = null)
    {
        $this->logger = $logger;
        $this->encryptionHandler = $encryptionHandler;
        $this->logPiiData = $logPiiData;
    }

    public function log(LogEventInterface $event): void
    {
        $data = ($this->logPiiData) ? $event->getMergedPiiData() : $event->getLogData();

        $data = json_encode($data, JSON_THROW_ON_ERROR);
        if ($this->encryptionHandler !== null) {
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
