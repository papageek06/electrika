<?php

namespace App\Entity;

use App\Repository\ConnectorRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ConnectorRepository::class)]
class Connector
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $power = null;

    #[ORM\Column(length: 255)]
    private ?string $type = null;

    /**
     * @var Collection<int, ProductConnector>
     */
    #[ORM\OneToMany(targetEntity: ProductConnector::class, mappedBy: 'connector')]
    private Collection $productConnectors;

    public function __construct()
    {
        $this->productConnectors = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPower(): ?string
    {
        return $this->power;
    }

    public function setPower(string $power): static
    {
        $this->power = $power;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): static
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return Collection<int, ProductConnector>
     */
    public function getProductConnectors(): Collection
    {
        return $this->productConnectors;
    }

    public function addProductConnector(ProductConnector $productConnector): static
    {
        if (!$this->productConnectors->contains($productConnector)) {
            $this->productConnectors->add($productConnector);
            $productConnector->setConnector($this);
        }

        return $this;
    }

    public function removeProductConnector(ProductConnector $productConnector): static
    {
        if ($this->productConnectors->removeElement($productConnector)) {
            // set the owning side to null (unless already changed)
            if ($productConnector->getConnector() === $this) {
                $productConnector->setConnector(null);
            }
        }

        return $this;
    }
}
