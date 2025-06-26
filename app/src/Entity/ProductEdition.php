<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\ValueObject\Product\ProductFormat;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

#[ORM\Entity]
class ProductEdition
{
    #[ORM\Id, ORM\GeneratedValue, ORM\Column(type:"integer")]
    private int $id;

    #[ORM\ManyToOne(targetEntity:ProductTitle::class, inversedBy:"editions")]
    #[ORM\JoinColumn(nullable:false)]
    private ProductTitle $title;

    #[ORM\Column(type:"string", length:255)]
    private string $label;

    #[ORM\Column(type:"integer", nullable:true)]
    private ?int $year;

    #[ORM\Column(type:"string", length:100)]
    private ProductFormat $format;

    #[ORM\Column(type:"string", length:100, nullable:true)]
    private ?string $barcode; 

    #[ORM\Column(type:"integer")]
    private int $stockNew = 0; 

    #[ORM\OneToMany(mappedBy:"edition", targetEntity:ProductUsedItem::class, cascade:["persist", "remove"])]
    private Collection $productUsedItem;

    public function __construct()
    {
        $this->productUsedItem = new ArrayCollection();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getTitle(): ProductTitle
    {
        return $this->title;
    }

    public function setTitle(ProductTitle $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function setLabel(string $label): self
    {
        $this->label = $label;

        return $this;
    }

    public function getYear(): int
    {
        return $this->year;
    }

    public function setYear(int $year): self
    {
        $this->year = $year;

        return $this;
    }

    public function getFormat(): ProductFormat
    {
        return $this->format;
    }

    public function setFormat(ProductFormat $format): self
    {
        $this->format = $format;

        return $this;
    }

    public function getBarcode(): string
    {
        return $this->barcode;
    }

    public function setBarcode(string $barcode): self
    {
        $this->barcode = $barcode;

        return $this;
    }

    public function getStockNew(): int
    {
        return $this->stockNew;
    }

    public function setStockNew(int $stockNew): self
    {
        $this->stockNew = $stockNew;

        return $this;
    }
}