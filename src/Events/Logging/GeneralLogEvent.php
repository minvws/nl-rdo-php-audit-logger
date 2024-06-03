<?php

declare(strict_types=1);

namespace MinVWS\AuditLogger\Events\Logging;

use Carbon\CarbonImmutable;
use MinVWS\AuditLogger\Contracts\LoggableUser;
use MinVWS\AuditLogger\Loggers\LogEventInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * @phpstan-type LogData array{
 *   user_id: ?string,
 *   request: array<mixed>,
 *   created_at: CarbonImmutable,
 *   event_code: string,
 *   action_code: string,
 *   allowed_admin_view: bool,
 *   failed: bool,
 *   failed_reason: ?string,
 * }
 *
 * @phpstan-type PiiLogDataMin array{
 *   request: array<mixed>,
 *   email:?string
 * }
 * @phpstan-type PiiLogDataFull array{
 *   http_request: array<mixed>,
 *   name: ?string,
 *   roles: ?array<array-key,string>
 * }
 * @phpstan-type PiiLogData PiiLogDataMin|PiiLogDataFull
 */
abstract class GeneralLogEvent implements LogEventInterface
{
    public const AC_CREATE = 'C';
    public const AC_READ = 'R';
    public const AC_UPDATE = 'U';
    public const AC_DELETE = 'D';
    public const AC_EXECUTE = 'E';

    public const EVENT_CODE = '0000000';
    public const EVENT_KEY = 'log';

    // Fields which will be removed from the request data
    public const PRIVATE_FIELDS = ['_token', 'token', 'code', 'password', 'secret'];

    // Fields
    public ?LoggableUser $actor = null;
    public ?LoggableUser $target = null;

    /** @var array<mixed> */
    public array $data = [];
    /** @var array<mixed> */
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

    /**
     * @param array<mixed> $data
     */
    public function withData(array $data = []): self
    {
        $this->data = $data;

        return $this;
    }

    /**
     * @param array<mixed> $piiData
     */
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

    /**
     * @return array<string,null|string|CarbonImmutable|bool>
     * @phpstan-return LogData
     */
    public function getLogData(): array
    {
        return [
            'user_id' => $this->actor?->getAuditId(),
            'request' => $this->data,
            'created_at' => CarbonImmutable::now(),
            'event_code' => $this->eventCode,
            'action_code' => $this->actionCode[0],
            'source' => $this->source,
            'allowed_admin_view' => $this->allowedAdminView,
            'failed' => $this->failed,
            'failed_reason' => $this->failedReason,
        ];
    }

    /**
     * @return array<string,mixed>
     * @phpstan-return PiiLogData
     */
    public function getPiiLogData(): array
    {
        $data = $this->piiData;

        if ($this->logFullRequest) {
            $httpRequest = $this->captureRequest();

            $data['http_request'] = $this->scrubPiiData($httpRequest->request->all());
            $data['name'] = $this->actor?->getName();
            $data['roles'] = $this->actor?->getRoles();
        }

        return [
            'request' => $data,
            'email' => $this->actor?->getEmail(),
        ];
    }

    /**
     * Remove private fields from the request data, if found
     *
     * @param array<string,mixed> $data
     *
     * @return array<string,mixed> $data
     */
    public function scrubPiiData(array $data): array
    {
        foreach (self::PRIVATE_FIELDS as $field) {
            if (isset($data[$field])) {
                $data[$field] = '***';
            }
        }

        return $data;
    }

    /**
     * @return array<string,mixed>
     */
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

    private function captureRequest(): Request
    {
        Request::enableHttpMethodParameterOverride();

        return Request::createFromGlobals();
    }
}
