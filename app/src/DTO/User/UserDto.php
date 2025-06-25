<?php
namespace App\DTO\User;

use App\Entity\User;

final class UserDto
{
    public function __construct(
        public readonly ?int $id,
        public readonly string $email,
        public readonly string $name,
        public readonly array $roles,
        public readonly ?string $password = null,
    ) {}

    public static function fromEntity(User $user): self 
    {
        return new self(
            null,
            $user->getEmail(),
            $user->getName(),
            $user->getRoles(),
            $user->getPassword()
        );
    }
}