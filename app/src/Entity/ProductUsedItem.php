<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\ValueObject\Product\ProductStatus;
use App\ValueObject\Product\ProductBarcode;

#[ORM\Entity]
class ProductUsedItem
{
    #[ORM\Id, ORM\GeneratedValue, ORM\Column(type:"integer")]
    private int $id;

    #[ORM\ManyToOne(targetEntity: ProductEdition::class, inversedBy: "productUsedItems")]
    #[ORM\JoinColumn(nullable: false)]
    private ?ProductEdition $edition = null;

    #[ORM\Column(type:"product_barcode", length:100, unique:true)]
    private ProductBarcode $barcode; 

    #[ORM\Column(type:"product_status", length:10)]
    private ProductStatus $conditionVinyl;

    #[ORM\Column(type:"product_status", length:10)]
    private ProductStatus $conditionFolder;

    #[ORM\Column(type:"float")]
    private float $price;   

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getEdition(): ?ProductEdition
    {
        return $this->edition;
    }

    public function setEdition(?ProductEdition $edition): self
    {
        $this->edition = $edition;

        return $this;
    }

    public function getPrice(): float
    {
        return $this->price;
    }

    public function setPrice(float $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getBarcode(): ProductBarcode
    {
        return $this->barcode;
    }

    public function setBarcode(ProductBarcode $barcode): self
    {
        $this->barcode = $barcode;

        return $this;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->getId(),
            'barcode' => $this->getBarcode()->value(),
            'conditionVinyl' => $this->getConditionVinyl()->getValue(),
            'conditionFolder' => $this->getConditionFolder()->getValue(),
            'price' => $this->getPrice(),
        ];
    }

    public function getConditionVinyl(): ProductStatus
    {
        return $this->conditionVinyl;
    }

    
    public function setConditionVinyl(ProductStatus $conditionVinyl): self
    {
        $this->conditionVinyl = $conditionVinyl;

        return $this;
    }

    public function getConditionFolder(): ProductStatus
    {
        return $this->conditionFolder;
    }


    public function setConditionFolder(ProductStatus $conditionFolder): self
    {
        $this->conditionFolder = $conditionFolder;

        return $this;
    }
}