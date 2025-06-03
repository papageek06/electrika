<?php

namespace App\Entity;

use App\Repository\InterventionTeamRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: InterventionTeamRepository::class)]
class InterventionTeam
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $type = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTime $stardDate = null;

    #[ORM\Column]
    private ?\DateTime $endDate = null;

    #[ORM\ManyToOne(inversedBy: 'interventionTeams')]
    private ?Event $event = null;

    /**
     * @var Collection<int, Technician>
     */
    #[ORM\ManyToMany(targetEntity: Technician::class, inversedBy: 'interventionTeams')]
    private Collection $technicians;

    public function __construct()
    {
        $this->technicians = new ArrayCollection();
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

    public function getStardDate(): ?\DateTime
    {
        return $this->stardDate;
    }

    public function setStardDate(\DateTime $stardDate): static
    {
        $this->stardDate = $stardDate;

        return $this;
    }

    public function getEndDate(): ?\DateTime
    {
        return $this->endDate;
    }

    public function setEndDate(\DateTime $endDate): static
    {
        $this->endDate = $endDate;

        return $this;
    }

    public function getEvent(): ?Event
    {
        return $this->event;
    }

    public function setEvent(?Event $event): static
    {
        $this->event = $event;

        return $this;
    }

    /**
     * @return Collection<int, Technician>
     */
    public function getTechnicians(): Collection
    {
        return $this->technicians;
    }

    public function addTechnician(Technician $technician): static
    {
        if (!$this->technicians->contains($technician)) {
            $this->technicians->add($technician);
        }

        return $this;
    }

    public function removeTechnician(Technician $technician): static
    {
        $this->technicians->removeElement($technician);

        return $this;
    }
}
