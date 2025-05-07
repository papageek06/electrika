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
    private ?string $type = null;

    #[ORM\Column]
    private ?int $power = null;

    #[ORM\Column(length: 255)]
    private ?string $in_out = null;

    #[ORM\Column(length: 255)]
    private ?string $phase_type = null;

    /**
     * @var Collection<int, product>
     */
    #[ORM\ManyToMany(targetEntity: product::class, inversedBy: 'connectors')]
    private Collection $product;

    /**
     * @var Collection<int, SiteEvent>
     */
    #[ORM\ManyToMany(targetEntity: SiteEvent::class, inversedBy: 'connectors')]
    private Collection $site;

    public function __construct()
    {
        $this->product = new ArrayCollection();
        $this->site = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getPower(): ?int
    {
        return $this->power;
    }

    public function setPower(int $power): static
    {
        $this->power = $power;

        return $this;
    }

    public function getInOut(): ?string
    {
        return $this->in_out;
    }

    public function setInOut(string $in_out): static
    {
        $this->in_out = $in_out;

        return $this;
    }

    public function getPhaseType(): ?string
    {
        return $this->phase_type;
    }

    public function setPhaseType(string $phase_type): static
    {
        $this->phase_type = $phase_type;

        return $this;
    }

    /**
     * @return Collection<int, product>
     */
    public function getProduct(): Collection
    {
        return $this->product;
    }

    public function addProduct(product $product): static
    {
        if (!$this->product->contains($product)) {
            $this->product->add($product);
        }

        return $this;
    }

    public function removeProduct(product $product): static
    {
        $this->product->removeElement($product);

        return $this;
    }

    /**
     * @return Collection<int, SiteEvent>
     */
    public function getSite(): Collection
    {
        return $this->site;
    }

    public function addSite(SiteEvent $site): static
    {
        if (!$this->site->contains($site)) {
            $this->site->add($site);
        }

        return $this;
    }

    public function removeSite(SiteEvent $site): static
    {
        $this->site->removeElement($site);

        return $this;
    }
}
