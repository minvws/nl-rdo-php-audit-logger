<?php

declare(strict_types=1);

namespace MinVWS\AuditLogger\Tests;

use MinVWS\AuditLogger\Contracts\LoggableUser;

final class StubUser implements LoggableUser
{
    /**
     * @param array<array-key,string> $roles
     */
    public function __construct(
        public string $id = '12345',
        public string $name = 'john doe',
        public array $roles = ['ROLE_USER'],
        public string $email = 'john@example.org',
    ) {
    }

    public function getAuditId(): string
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return array<array-key,string>
     */
    public function getRoles(): array
    {
        return $this->roles;
    }

    public function getEmail(): string
    {
        return $this->email;
    }
}
