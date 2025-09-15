<?php

namespace App\Exceptions;

use Exception;

class RoleException extends Exception
{
    protected $requiredRoles;

    public function __construct(string $message, array $requiredRoles = [])
    {
        parent::__construct($message);
        $this->requiredRoles = $requiredRoles;
    }

    public function getRequiredRoles(): array
    {
        return $this->requiredRoles;
    }
}