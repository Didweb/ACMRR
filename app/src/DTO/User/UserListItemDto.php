<?php
namespace App\DTO\User;

final class UserListItemDto
{
    public function __construct(
        public readonly int $id,
        public readonly string $email,
        public readonly string $name,
        public readonly string $roles,
    ) {} 
}