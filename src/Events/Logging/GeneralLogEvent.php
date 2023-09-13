<?php

declare(strict_types=1);

namespace MinVWS\AuditLogger\Events\Logging;

use Illuminate\Http\Request;
use MinVWS\AuditLogger\Contracts\LoggableUser;
use MinVWS\AuditLogger\Loggers\LogEventInterface;

abstract class GeneralLogEvent implements LogEventInterface
{
    public const AC_CREATE = 'C';
    public const AC_READ = 'R';
    public const AC_UPDATE = 'U';
    public const AC_DELETE = 'D';
    public const AC_EXECUTE = 'E';

    public const EVENT_CODE = '0000000';
    public const EVENT_KEY = 'log';

    # Fields which will be removed from the request data
    public const PRIVATE_FIELDS = [ "_token", "token", "code", "password", "secret" ];

    // Fields
    public ?LoggableUser $actor = null;
    public ?LoggableUser $target = null;
    public array $data = [];
    public array $piiData = [];
    public string $eventCode = self::EVENT_CODE;
    public string $eventKey = self::EVENT_KEY;
    public string $actionCode = self::AC_EXECUTE;
    public bool $allowedAdminView = false;
    public bool $failed = false;
    public string $source = '';
    public ?string $failedReason = null;
    public bool $logFullRequest = false;

    public function __construct()
    {
        $this->eventKey = static::EVENT_KEY;
        $this->eventCode = static::EVENT_CODE;
    }

    public function withActor(LoggableUser $actor): self
    {
        $this->actor = $actor;
        return $this;
    }

    public function withTarget(LoggableUser $target): self
    {
        $this->target = $target;
        return $this;
    }

    public function withData(array $data = []): self
    {
        $this->data = $data;
        return $this;
    }

    public function withPiiData(array $piiData = []): self
    {
        $this->piiData = $piiData;
        return $this;
    }

    public function withEventCode(string $eventCode): self
    {
        $this->eventCode = $eventCode;
        return $this;
    }

    public function asCreate(): self
    {
        $this->actionCode = self::AC_CREATE;
        return $this;
    }

    public function asUpdate(): self
    {
        $this->actionCode = self::AC_UPDATE;
        return $this;
    }

    public function asExecute(): self
    {
        $this->actionCode = self::AC_EXECUTE;
        return $this;
    }

    public function asRead(): self
    {
        $this->actionCode = self::AC_READ;
        return $this;
    }

    public function asDelete(): self
    {
        $this->actionCode = self::AC_DELETE;
        return $this;
    }

    public function withAllowedAdminView(bool $allowedAdminView): self
    {
        $this->allowedAdminView = $allowedAdminView;
        return $this;
    }

    public function withFailed(bool $failed, string $failedReason = ''): self
    {
        $this->failed = $failed;
        $this->failedReason = $failedReason;
        return $this;
    }

    public function withSource(string $source): self
    {
        $this->source = $source;
        return $this;
    }

    public function logFullRequest(bool $logFullRequest): self
    {
        $this->logFullRequest = $logFullRequest;
        return $this;
    }

    public function getLogData(): array
    {
        return [
            'user_id' => $this->actor?->getId(),
            'request' => $this->data,
            'created_at' => new \DateTimeImmutable(),
            'event_code' => $this->eventCode,
            'action_code' => $this->actionCode[0],
            'allowed_admin_view' => $this->allowedAdminView,
            'failed' => $this->failed,
            'failed_reason' => $this->failedReason,
        ];
    }

    public function getPiiLogData(): array
    {
        $data = $this->piiData;

        if ($this->logFullRequest) {
            $httpRequest = Request::capture();

            $data['http_request'] = $httpRequest->request->all();
            $data['name'] = $this->actor?->getName();
            $data['roles'] = $this->actor?->getRoles();

            # Remove private fields from the request data, if found
            foreach (self::PRIVATE_FIELDS as $field) {
                if (isset($data['http_request'][$field])) {
                    $data['http_request'][$field] = "***";
                }
            }
        }

        return [
            'request' => $data,
            'email' => $this->actor?->getEmail(),
        ];
    }

    public function getMergedPiiData(): array
    {
        return array_merge_recursive($this->getLogData(), $this->getPiiLogData());
    }

    public function getEventKey(): string
    {
        return $this->eventKey;
    }

    public function getEventCode(): string
    {
        return $this->eventCode;
    }

    public function getActor(): ?LoggableUser
    {
        return $this->actor;
    }

    public function getTargetUser(): ?LoggableUser
    {
        return $this->target;
    }
}
