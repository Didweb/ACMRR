<?php
namespace App\DTO\Artist;

use App\Entity\Artist;
use Symfony\Component\Validator\Constraints as Assert;

class ArtistDto
{
    public function __construct(
        public readonly ?int $id,

        #[Assert\NotBlank(message: 'El nombre no puede estar vacío')]
        #[Assert\Length(
            min: 2,
            max: 255,
            minMessage: 'El nombre debe tener al menos {{ limit }} caracteres',
            maxMessage: 'El nombre no puede tener más de {{ limit }} caracteres'
        )]
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