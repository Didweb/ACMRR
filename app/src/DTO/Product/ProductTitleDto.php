<?php
namespace App\DTO\Product;

use App\Entity\Track;
use App\Entity\ProductTitle;
use App\Entity\ProductEdition;

final class ProductTitleDto
{
    public function __construct(
        public readonly ?int $id,
        public readonly string $name,
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