<?php
namespace App\DTO\Riddim;

use Symfony\Component\Validator\Constraints as Assert;

class RiddimDto
{
      public function __construct(

        #[Assert\Positive(message: 'El ID debe ser un número positivo.')]
        public readonly ?int $id,

        #[Assert\NotBlank(message: 'El nombre no puede estar vacío')]
        #[Assert\Length(
            min: 2,
            max: 255,
            minMessage: 'El nombre debe tener al menos {{ limit }} caracteres',
            maxMessage: 'El nombre no puede tener más de {{ limit }} caracteres'
        )]
        public readonly string $name,

        #[Assert\Type('array', message: 'Las ediciones deben ser un array.')]
        public readonly array $tracks
    ) {}
}