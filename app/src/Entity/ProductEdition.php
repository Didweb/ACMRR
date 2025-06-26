<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\ValueObject\Product\ProductFormat;
use App\ValueObject\Product\ProductBarcode;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

#[ORM\Entity]
class ProductEdition
{
    #[ORM\Id, ORM\GeneratedValue, ORM\Column(type:"integer")]
    private int $id;

    #[ORM\ManyToOne(targetEntity: ProductTitle::class, inversedBy: "editions")]
    #[ORM\JoinColumn(nullable: false)]
    private ?ProductTitle $title = null;

    #[ORM\ManyToOne(targetEntity: RecordLabel::class)]
    #[ORM\JoinColumn(nullable: false)]
    private RecordLabel $label;

    #[ORM\Column(type:"integer", nullable:true)]
    private ?int $year;

    #[ORM\Column(type:"product_format", length:100)]
    private ProductFormat $format;

    #[ORM\Column(type:"product_barcode", length:100, nullable:true)]
    private ProductBarcode $barcode; 

    #[ORM\Column(type:"integer")]
    private int $stockNew = 0; 

    #[ORM\OneToMany(mappedBy:"edition", targetEntity:ProductUsedItem::class, cascade:["persist", "remove"], orphanRemoval: true)]
    private Collection $productUsedItems;

    #[ORM\ManyToMany(targetEntity: Artist::class, inversedBy: "productEditions")]
    #[ORM\JoinTable(name: "product_edition_artist")]
    private Collection $artists;

    #[ORM\OneToMany(mappedBy: "productEdition", targetEntity: Track::class, cascade: ["persist", "remove"])]
    private Collection $tracks;

    public function __construct()
    {
        $this->tracks = new ArrayCollection();
        $this->artists = new ArrayCollection();
        $this->productUsedItems = new ArrayCollection();
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

    public function getTitle(): ?ProductTitle
    {
        return $this->title;
    }

    public function setTitle(?ProductTitle $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getLabel(): RecordLabel
    {
        return $this->label;
    }

    public function setLabel(RecordLabel $label): self
    {
        $this->label = $label;
        return $this;
    }

    public function getYear(): ?int
    {
        return $this->year;
    }

    public function setYear(?int $year): self
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

    public function getBarcode(): ProductBarcode
    {
        return $this->barcode;
    }

    public function setBarcode(ProductBarcode $barcode): self
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

    public function getProductUsedItems(): Collection
    {
        return $this->productUsedItems;
    }

    public function addProductUsedItem(ProductUsedItem $item): self
    {
        if (!$this->productUsedItems->contains($item)) {
            $this->productUsedItems[] = $item;
            $item->setEdition($this); 
        }

        return $this;
    }

    public function removeProductUsedItem(ProductUsedItem $item): self
    {
        if ($this->productUsedItems->removeElement($item)) {
            if ($item->getEdition() === $this) {
                $item->setEdition(null);
            }
        }

        return $this;
    }

    public function getArtists(): Collection
    {
        return $this->artists;
    }

    public function addArtist(Artist $artist): self
    {
        if (!$this->artists->contains($artist)) {
            $this->artists->add($artist);
            $artist->getProductEditions()->add($this);
        }
        return $this;
    }

    public function removeArtist(Artist $artist): self
    {
        if ($this->artists->contains($artist)) {
            $this->artists->removeElement($artist);
            $artist->getProductEditions()->removeElement($this);
        }
        return $this;
    }

    public function getTracks(): Collection
    {
        return $this->tracks;
    }

    public function addTrack(Track $track): self
    {
        if (!$this->tracks->contains($track)) {
            $this->tracks->add($track);
            $track->setProductEdition($this);
        }
        return $this;
    }

    public function removeTrack(Track $track): self
    {
        if ($this->tracks->removeElement($track)) {
            if ($track->getProductEdition() === $this) {
                $track->setProductEdition(null);
            }
        }
        return $this;
    }

}