<?php

namespace App\Entity;

use App\Repository\ProductRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProductRepository::class)]
class Product
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 150)]
    private ?string $name = null;

    #[ORM\Column(length: 150)]
    private ?string $category = null;

    #[ORM\Column]
    private ?int $stockInitial = null;

    #[ORM\Column]
    private ?int $stock = null;

    #[ORM\Column(nullable: true)]
    private ?int $hs = null;

    #[ORM\Column(nullable: true)]
    private ?int $lost = null;

    /**
     * @var Collection<int, EventDetail>
     */
    #[ORM\OneToMany(targetEntity: EventDetail::class, mappedBy: 'product')]
    private Collection $eventDetails;

    public function __construct()
    {
        $this->eventDetails = new ArrayCollection();
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

    public function getCategory(): ?string
    {
        return $this->category;
    }

    public function setCategory(string $category): static
    {
        $this->category = $category;

        return $this;
    }

    public function getStockInitial(): ?int
    {
        return $this->stockInitial;
    }

    public function setStockInitial(int $stockInitial): static
    {
        $this->stockInitial = $stockInitial;

        return $this;
    }

    public function getStock(): ?int
    {
        return $this->stock;
    }

    public function setStock(int $stock): static
    {
        $this->stock = $stock;

        return $this;
    }

    public function getHs(): ?int
    {
        return $this->hs;
    }

    public function setHs(?int $hs): static
    {
        $this->hs = $hs;

        return $this;
    }

    public function getLost(): ?int
    {
        return $this->lost;
    }

    public function setLost(?int $lost): static
    {
        $this->lost = $lost;

        return $this;
    }

    /**
     * @return Collection<int, EventDetail>
     */
    public function getEventDetails(): Collection
    {
        return $this->eventDetails;
    }

    public function addEventDetail(EventDetail $eventDetail): static
    {
        if (!$this->eventDetails->contains($eventDetail)) {
            $this->eventDetails->add($eventDetail);
            $eventDetail->setProduct($this);
        }

        return $this;
    }

    public function removeEventDetail(EventDetail $eventDetail): static
    {
        if ($this->eventDetails->removeElement($eventDetail)) {
            // set the owning side to null (unless already changed)
            if ($eventDetail->getProduct() === $this) {
                $eventDetail->setProduct(null);
            }
        }

        return $this;
    }
}
