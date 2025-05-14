<?php

namespace App\Entity;

use App\Repository\ProductConnectorRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProductConnectorRepository::class)]
class ProductConnector
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $quantity = null;

    #[ORM\Column(length: 255)]
    private ?string $plugDirection = null;

    #[ORM\ManyToOne(targetEntity: Connector::class)]
    private ?Connector $connector = null;

    #[ORM\ManyToOne(inversedBy: 'productConnectors')]
    private ?Product $product = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): static
    {
        $this->quantity = $quantity;

        return $this;
    }

    public function getPlugDirection(): ?string
    {
        return $this->plugDirection;
    }

    public function setPlugDirection(string $plugDirection): static
    {
        $this->plugDirection = $plugDirection;

        return $this;
    }

    public function getConnector(): ?Connector
    {
        return $this->connector;
    }

    public function setConnector(?Connector $connector): static
    {
        $this->connector = $connector;

        return $this;
    }

    public function getProduct(): ?Product
    {
        return $this->product;
    }

    public function setProduct(?Product $product): static
    {
        $this->product = $product;

        return $this;
    }
}
