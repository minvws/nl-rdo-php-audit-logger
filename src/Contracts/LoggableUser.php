<?php

declare(strict_types=1);

namespace MinVWS\AuditLogger\Contracts;

/**
 * Interface LoggableUser.
 *
 * Use this interface on users that can be logged as either actor or target.
 */
interface LoggableUser
{
    public function getAuditId(): string;

    public function getName(): string;

    /**
     * @return array<array-key,string>
     */
    public function getRoles(): array;

    public function getEmail(): string;
}
