<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

#[ORM\Entity]
class Track
{
    #[ORM\Id, ORM\GeneratedValue, ORM\Column(type: "integer")]
    private ?int $id = null;

    #[ORM\Column(type: "string", length: 255)]
    private string $title;

    #[ORM\ManyToOne(targetEntity: ProductEdition::class, inversedBy: "tracks")]
    #[ORM\JoinColumn(nullable: false)]
    private ?ProductEdition $productEdition = null;

    #[ORM\ManyToMany(targetEntity: Artist::class, inversedBy: "tracks")]
    #[ORM\JoinTable(name: "track_artist")]
    private Collection $artists;

    #[ORM\ManyToOne(targetEntity: Riddim::class, inversedBy: "tracks")]
    #[ORM\JoinColumn(nullable: true)]
    private ?Riddim $riddim = null;

    public function __construct()
    {
        $this->artists = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;
        return $this;
    }

    public function getProductEdition(): ?ProductEdition
    {
        return $this->productEdition;
    }

    public function setProductEdition(?ProductEdition $productEdition): self
    {
        $this->productEdition = $productEdition;
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
            $artist->addTrack($this);
        }
        return $this;
    }

    public function removeArtist(Artist $artist): self
    {
        if ($this->artists->removeElement($artist)) {
            $artist->removeTrack($this);
        }
        return $this;
    }

    public function getRiddim(): ?Riddim
    {
        return $this->riddim;
    }

    public function setRiddim(?Riddim $riddim): self
    {
        $this->riddim = $riddim;
        return $this;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->getId() ?? null,
            'title' => $this->getTitle(),
            'artists' => $this->artists->map(
                            fn(Artist $artist) => $artist->toArray()
                        )->toArray(),
            'riddim' => $this->riddim?->getName(),
        ];
    }
}