<?php
namespace App\Entity;

use App\Entity\ProductEdition;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

#[ORM\Entity]
class Artist
{
    #[ORM\Id, ORM\GeneratedValue, ORM\Column(type:"integer")]
    private int $id;

    #[ORM\Column(type:"string", length:255)]
    private string $name;

    #[ORM\ManyToMany(targetEntity: ProductEdition::class, mappedBy: "artists")]
    private Collection $productEditions;

    #[ORM\ManyToMany(targetEntity: Track::class, mappedBy: "artists")]
    private Collection $tracks;

    public function __construct()
    {
        $this->tracks = new ArrayCollection();
        $this->productEditions = new ArrayCollection();
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

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getProductEditions(): Collection
    {
        return $this->productEditions;
    }

    public function addProductEdition(ProductEdition $edition): self
    {
        if (!$this->productEditions->contains($edition)) {
            $this->productEditions->add($edition);
            $edition->addArtist($this); 
        }
        return $this;
    }

    public function removeProductEdition(ProductEdition $edition): self
    {
        if ($this->productEditions->contains($edition)) {
            $this->productEditions->removeElement($edition);
            $edition->removeArtist($this); 
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
            $track->addArtist($this);
        }
        return $this;
    }

    public function removeTrack(Track $track): self
    {
        if ($this->tracks->removeElement($track)) {
            $track->removeArtist($this);
        }
        return $this;
    }
}