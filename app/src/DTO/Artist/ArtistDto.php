<?php
namespace App\DTO\Artist;

use App\Entity\Artist;

class ArtistDto
{
    public function __construct(
        public readonly ?int $id,
        public readonly string $name
    ) {}

    public static function fromEntity(Artist $artist): self 
    {
        return new self(
            $artist->getId(),
            $artist->getName()
        );
    }
}