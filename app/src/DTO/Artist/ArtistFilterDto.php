<?php
namespace App\DTO\Artist;

final class ArtistFilterDto
{
    public function __construct(
        public readonly int $page = 1,
        public readonly int $limit = 10
    ) {}
}