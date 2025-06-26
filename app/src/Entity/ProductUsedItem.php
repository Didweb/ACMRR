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

    #[ORM\ManyToOne(targetEntity:ProductEdition::class, inversedBy:"usedItems")]
    #[ORM\JoinColumn(nullable:false)]
    private ProductEdition $edition;

    #[ORM\Column(type:"string", length:100, unique:true)]
    private ProductBarcode $barcode; 

    #[ORM\Column(type:"string", length:10)]
    private ProductStatus $condition;

    #[ORM\Column(type:"decimal", precision:10, scale:2)]
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

    public function getEdition(): ProductEdition
    {
        return $this->edition;
    }

    public function setEdition(ProductEdition $edition): self
    {
        $this->edition = $edition;

        return $this;
    }

    public function getCondition(): ProductStatus
    {
        return $this->condition;
    }

    public function setCondition(ProductStatus $condition): self
    {
        $this->condition = $condition;

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

    public function setBarcode(string $barcode): self
    {
        $this->barcode = $barcode;

        return $this;
    }
}