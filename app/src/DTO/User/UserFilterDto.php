<?php
namespace App\DTO\User;

final class UserFilterDto
{
    public function __construct(
        public readonly int $page = 1,
        public readonly int $limit = 10
    ) {}
}