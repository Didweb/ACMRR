<?php
namespace App\DTO\Product;

use Symfony\Component\Validator\Constraints as Assert;
use App\Entity\ProductTitle;
use App\Entity\ProductEdition;

final class ProductTitleDto
{
    public function __construct(
        #[Assert\Positive(message: 'El ID debe ser un número positivo.')]
        public readonly ?int $id,

        #[Assert\NotBlank(message: 'El nombre del producto es obligatorio.')]
        #[Assert\Length(
            max: 255,
            maxMessage: 'El nombre no puede superar los {{ limit }} caracteres.'
        )]
        public readonly string $name,

        #[Assert\Type('array', message: 'Las ediciones deben ser un array.')]
        #[Assert\All([
            new Assert\Type(type: 'array', message: 'Cada edición debe ser un array.')
        ])]
        public readonly ?array $productEditions
    ) {} 

    public static function fromEntity(ProductTitle $title): self
    {
        return new self(
            id: $title->getId(),
            name: $title->getName(),
            productEditions:  array_map(
                fn(ProductEdition $item) => $item->toArray(),
                $title->getEditions()->toArray()
            )
        );
    }
}