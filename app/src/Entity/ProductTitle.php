<?php
namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class ProductTitle
{
   #[ORM\Id, ORM\GeneratedValue, ORM\Column(type:"integer")]
    private int $id;

    #[ORM\Column(type:"string", length:255)]
    private string $name;

    #[ORM\OneToMany(mappedBy:"title", targetEntity:ProductEdition::class)]
    private Collection $editions;

    public function __construct()
    {
        $this->editions = new ArrayCollection();
    }  

    public function getId()
    {
        return $this->id;
    }

    public function setId(int $id)
    {
        $this->id = $id;

        return $this;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName(string $name)
    {
        $this->name = $name;

        return $this;
    }

    public function getEditions(): Collection
    {
        return $this->editions;
    }

    public function addEdition(ProductEdition $edition): self
    {
        if (!$this->editions->contains($edition)) {
            $this->editions[] = $edition;
            $edition->setTitle($this); 
        }

        return $this;
    }

    public function removeEdition(ProductEdition $edition): self
    {
        if ($this->editions->removeElement($edition)) {
            if ($edition->getTitle() === $this) {
                $edition->setTitle(null); 
            }
        }

        return $this;
    }
}