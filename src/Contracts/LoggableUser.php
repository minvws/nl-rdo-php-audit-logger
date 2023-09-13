<?php

declare(strict_types=1);

namespace MinVWS\AuditLogger\Contracts;

/**
 * Interface LoggableUser
 *
 * Use this interface on users that can be logged as either actor or target.
 *
 * @package MinVWS\AuditLogger\Contracts
 */
interface LoggableUser
{
    public function getId(): string;
    public function getName(): string;
    /** @return string[] */
    public function getRoles(): array;
    public function getEmail(): string;
}
