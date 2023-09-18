<?php

declare(strict_types=1);

namespace MinVWS\AuditLogger\Loggers;

use MinVWS\AuditLogger\EncryptionHandler;
use MinVWS\AuditLogger\Events\Logging\GeneralLogEvent;

class FileLogger implements LoggerInterface
{
    protected string $auditLogFilePath;
    protected bool $logPiiData;
    protected ?EncryptionHandler $encryptionHandler;

    public function __construct(EncryptionHandler $encryptionHandler, string $auditLogFilePath, bool $logPiiData = false)
    {
        $this->encryptionHandler = $encryptionHandler;
        $this->auditLogFilePath = $auditLogFilePath;
        $this->logPiiData = $logPiiData;
    }

    public function log(LogEventInterface $event): void
    {
        $data = ($this->logPiiData) ? $event->getMergedPiiData() : $event->getLogData();
        $data = json_encode($data, JSON_THROW_ON_ERROR);
        if ($this->encryptionHandler->isEnabled()) {
            $data = $this->encryptionHandler->encrypt($data);
            $data = json_encode(['encrypted' => $data], JSON_THROW_ON_ERROR);
        }

        file_put_contents($this->auditLogFilePath, $data . "\n", FILE_APPEND);
    }

    public function canHandleEvent(LogEventInterface $event): bool
    {
        if (is_a($event, GeneralLogEvent::class)) {
            return true;
        }

        return false;
    }
}
