<?php

namespace App\Entity;

use App\Repository\TechnicianRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TechnicianRepository::class)]
class Technician
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $status = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTime $hireDate = null;

    #[ORM\Column(type: Types::ARRAY, nullable: true)]
    private ?array $specialities = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    private ?User $user = null;

    /**
     * @var Collection<int, Absence>
     */
    #[ORM\OneToMany(targetEntity: Absence::class, mappedBy: 'technicians', orphanRemoval: true)]
    private Collection $absences;

    /**
     * @var Collection<int, InterventionTeam>
     */
    #[ORM\ManyToMany(targetEntity: InterventionTeam::class, mappedBy: 'technicians')]
    private Collection $interventionTeams;

    public function __construct()
    {
        $this->absences = new ArrayCollection();
        $this->interventionTeams = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getHireDate(): ?\DateTime
    {
        return $this->hireDate;
    }

    public function setHireDate(?\DateTime $hireDate): static
    {
        $this->hireDate = $hireDate;

        return $this;
    }

    public function getSpecialities(): ?array
    {
        return $this->specialities;
    }

    public function setSpecialities(?array $specialities): static
    {
        $this->specialities = $specialities;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return Collection<int, Absence>
     */
    public function getAbsences(): Collection
    {
        return $this->absences;
    }

    public function addAbsence(Absence $absence): static
    {
        if (!$this->absences->contains($absence)) {
            $this->absences->add($absence);
            $absence->setTechnicians($this);
        }

        return $this;
    }

    public function removeAbsence(Absence $absence): static
    {
        if ($this->absences->removeElement($absence)) {
            // set the owning side to null (unless already changed)
            if ($absence->getTechnicians() === $this) {
                $absence->setTechnicians(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, InterventionTeam>
     */
    public function getInterventionTeams(): Collection
    {
        return $this->interventionTeams;
    }

    public function addInterventionTeam(InterventionTeam $interventionTeam): static
    {
        if (!$this->interventionTeams->contains($interventionTeam)) {
            $this->interventionTeams->add($interventionTeam);
            $interventionTeam->addTechnician($this);
        }

        return $this;
    }

    public function removeInterventionTeam(InterventionTeam $interventionTeam): static
    {
        if ($this->interventionTeams->removeElement($interventionTeam)) {
            $interventionTeam->removeTechnician($this);
        }

        return $this;
    }
}
