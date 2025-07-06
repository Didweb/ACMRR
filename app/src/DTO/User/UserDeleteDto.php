<?php
namespace App\DTO\User;

use Symfony\Component\Validator\Constraints as Assert;

class UserDeleteDto
{
    public function __construct(
        #[Assert\NotNull]
        public readonly int $userId,

        #[Assert\NotBlank]
        public readonly string $csrfToken
    ) {}
}