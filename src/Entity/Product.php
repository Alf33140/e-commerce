<?php

namespace App\Entity;

use App\Repository\ProductRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProductRepository::class)]
class Product
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $descriptionproduct = null;

    #[ORM\Column]
    private ?int $Price = null;

    /**
     * @var Collection<int, Subcategorie>
     */
    #[ORM\ManyToMany(targetEntity: Subcategorie::class, inversedBy: 'products')]
    private Collection $subcategory;

    public function __construct()
    {
        $this->subcategory = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getDescriptionproduct(): ?string
    {
        return $this->descriptionproduct;
    }

    public function setDescriptionproduct(?string $descriptionproduct): static
    {
        $this->descriptionproduct = $descriptionproduct;

        return $this;
    }

    public function getPrice(): ?int
    {
        return $this->Price;
    }

    public function setPrice(int $Price): static
    {
        $this->Price = $Price;

        return $this;
    }

    /**
     * @return Collection<int, Subcategorie>
     */
    public function getSubcategory(): Collection
    {
        return $this->subcategory;
    }

    public function addSubcategory(Subcategorie $subcategory): static
    {
        if (!$this->subcategory->contains($subcategory)) {
            $this->subcategory->add($subcategory);
        }

        return $this;
    }

    public function removeSubcategory(Subcategorie $subcategory): static
    {
        $this->subcategory->removeElement($subcategory);

        return $this;
    }
}
