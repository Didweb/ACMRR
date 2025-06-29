<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class ProductImage
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private string $filename;

    #[ORM\Column(length: 255)]
    private string $path;

    #[ORM\Column(type: 'datetime')]
    private \DateTimeInterface $createdAt;

    #[ORM\ManyToOne(targetEntity: ProductEdition::class, inversedBy: 'images')]
    private ?ProductEdition $productEdition = null;

    #[ORM\ManyToOne(targetEntity: ProductUsedItem::class, inversedBy: 'images')]
    private ?ProductUsedItem $productUsedItem = null;

    public function __construct(string $filename, string $path)
    {
        $this->filename = $filename;
        $this->path = $path;
        $this->createdAt = new \DateTime();
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

    public function getFilename(): string
    {
        return $this->filename;
    }

    public function setFilename(string $filename): self
    {
        $this->filename = $filename;

        return $this;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function setPath(string $path): self
    {
        $this->path = $path;

        return $this;
    }

    public function getCreatedAt(): \DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getProductEdition(): ProductEdition
    {
        return $this->productEdition;
    }

    public function setProductEdition(ProductEdition $productEdition): self
    {
        $this->productEdition = $productEdition;

        return $this;
    }

    public function getProductUsedItem()
    {
        return $this->productUsedItem;
    }

    public function setProductUsedItem(ProductUsedItem $productUsedItem)
    {
        $this->productUsedItem = $productUsedItem;

        return $this;
    }
}