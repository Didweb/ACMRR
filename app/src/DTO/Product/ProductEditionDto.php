<?php
namespace App\DTO\Product;

use App\Entity\Track;
use App\Entity\Artist;
use App\Entity\ProductEdition;
use App\Entity\ProductUsedItem;

final class ProductEditionDto
{
    public function __construct(
        public readonly ?int $id,
        public readonly ?array $title,
        public readonly ?string $label,
        public readonly ?int $year,
        public readonly ?string $format,
        public readonly ?string $barcode,
        public readonly int $stockNew,
        public readonly float $priceNew,
        public readonly ?array $productUsedItems,
        public readonly ?array $artists,
        public readonly ?array $tracks
    ) {} 
    
    public static function fromEntity(ProductEdition $edition): self
    {
        $title = $edition->getTitle();
        $id = ($edition->getId() !== null) ? $edition->getId() : null;
        $barcode = ($edition->getBarcode() !== null) ? $edition->getBarcode()?->value() : null;
        $title = ($title !== null) ? ['id' => $title->getId(), 'name' => $title->getName()]: null ;
        return new self(
            id:  $id,
            title: $title,
            label: $edition->getLabel()?->getId(),
            year: $edition->getYear(),
            format: $edition->getFormat()?->getValue(),
            barcode: $barcode,
            stockNew: $edition->getStockNew(),
            priceNew: $edition->getPriceNew(),
            productUsedItems: array_map(
                fn(ProductUsedItem $item) => $item->toArray(),
                $edition->getProductUsedItems()->toArray()
            ),
            artists: array_map(
                fn(Artist $artist) => $artist->toArray(),
                $edition->getArtists()->toArray()
            ),
            tracks: array_map(
                fn(Track $track) => $track->toArray(),
                $edition->getTracks()->toArray()
            )
        );
    }
}