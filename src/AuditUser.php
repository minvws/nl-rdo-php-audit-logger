<?php

declare(strict_types=1);

namespace MinVWS\AuditLogger;

use MinVWS\AuditLogger\Contracts\LoggableUser;

final class AuditUser implements LoggableUser
{
    protected string $id;
    protected string $name;
    /** @var string[] */
    protected array $roles;
    protected string $email;

    /**
     * @param string[] $roles
     */
    public function __construct(string $id, string $name, array $roles, string $email)
    {
        $this->id = $id;
        $this->name = $name;
        $this->roles = $roles;
        $this->email = $email;
    }

    public function getAuditId(): string
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getRoles(): array
    {
        return $this->roles;
    }

    public function getEmail(): string
    {
        return $this->email;
    }
}
