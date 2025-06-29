<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

#[ORM\Entity]
#[ORM\Table(name: 'product_tag')]
class ProductTag
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 100, unique: true)]
    private string $name;

    #[ORM\ManyToMany(targetEntity: ProductEdition::class, mappedBy: 'tags')]
    private Collection $productEditions;

    public function __construct()
    {
        $this->productEditions = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return Collection|ProductEdition[]
     */
    public function getProductEditions(): Collection
    {
        return $this->productEditions;
    }

    public function addProductEdition(ProductEdition $edition): self
    {
        if (!$this->productEditions->contains($edition)) {
            $this->productEditions->add($edition);
            $edition->addTag($this);
        }
        return $this;
    }

    public function removeProductEdition(ProductEdition $edition): self
    {
        if ($this->productEditions->removeElement($edition)) {
            $edition->removeTag($this);
        }
        return $this;
    }
}