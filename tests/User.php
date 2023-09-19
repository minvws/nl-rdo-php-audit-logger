<?php

declare(strict_types=1);

namespace MinVWS\AuditLogger\Tests;

use MinVWS\AuditLogger\Contracts\LoggableUser;

class User implements LoggableUser
{
    public function getAuditId(): string
    {
        return '12345';
    }

    public function getName(): string
    {
        return 'john doe';
    }

    public function getRoles(): array
    {
        return ['ROLE_USER'];
    }

    public function getEmail(): string
    {
        return 'john@example.org';
    }
}
