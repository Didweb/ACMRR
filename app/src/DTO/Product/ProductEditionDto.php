<?php
namespace App\DTO\Product;

use App\Entity\Track;
use App\Entity\Artist;
use App\Entity\ProductEdition;
use App\Entity\ProductUsedItem;
use App\ValueObject\Product\ProductFormat;
use Symfony\Component\Validator\Constraints as Assert;

final class ProductEditionDto
{
    public function __construct(
        #[Assert\Positive(message: 'El ID debe ser un número positivo.')]
        public readonly ?int $id,


        #[Assert\NotNull(message: 'El título es obligatorio.')]
        #[Assert\Type('array')]
        public readonly ?array $title,

        #[Assert\NotBlank(message: 'El sello (label) es obligatorio.')]
        #[Assert\Type('string')]
        #[Assert\Length(
            max: 255,
            maxMessage: 'El sello no puede tener más de {{ limit }} caracteres.'
        )]
        public readonly ?string $label,

        #[Assert\NotNull(message: 'El año es obligatorio.')]
        #[Assert\Range(
            min: 1900,
            max: 2100,
            notInRangeMessage: 'El año debe estar entre {{ min }} y {{ max }}.'
        )]
        public readonly ?int $year,

        #[Assert\NotBlank(message: 'El formato es obligatorio.')]
        #[Assert\Choice(
            callback: [ProductFormat::class, 'choicesStr'],
            message: 'El formato "{{ value }}" no es válido.'
        )]
        public readonly ?string $format,

        #[Assert\Length(
            max: 255,
            maxMessage: 'El código de barras no puede tener más de {{ limit }} caracteres.'
        )]
        public readonly ?string $barcode,

        #[Assert\PositiveOrZero(message: 'El stock debe ser cero o mayor.')]
        public readonly int $stockNew,

        #[Assert\PositiveOrZero(message: 'El precio debe ser cero o mayor.')]
        public readonly float $priceNew,

        #[Assert\Type('array')]
        public readonly ?array $productUsedItems,

        #[Assert\Type('array')]
        public readonly ?array $artists,

        #[Assert\Type('array')]
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