<?php
namespace App\DTO\User;

class UserDeleteOutputDto
{
    public function __construct(
        public readonly bool $success,
        public readonly ?string $message = null
    ) {}
}