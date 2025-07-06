<?php
namespace App\DTO\Product;

use Symfony\Component\Validator\Constraints as Assert;

final class ProductTagDto
{
    public function __construct(

        #[Assert\Positive(message: 'El ID debe ser un número positivo.')]
        #[Assert\Type('integer')]
        public readonly ?int $id,

        #[Assert\NotBlank(message: 'El nombre de la etiqueta es obligatorio.')]
        #[Assert\Length(min: 3, max: 50)]
        public readonly string $name
    ) {} 

}