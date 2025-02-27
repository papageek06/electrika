<?php

namespace App\Entity;

use App\Repository\EventRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EventRepository::class)]
class Event
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'events')]
    private ?SiteEvent $site = null;

    #[ORM\ManyToOne(inversedBy: 'events')]
    private ?Contact $contact = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $dateMontage = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $dateStartShow = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $dateEndSHOW = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $dateEnd = null;

    /**
     * @var Collection<int, EventDetail>
     */
    #[ORM\OneToMany(targetEntity: EventDetail::class, mappedBy: 'event')]
    private Collection $eventDetails;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $name = null;

    public function __construct()
    {
        $this->eventDetails = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSite(): ?SiteEvent
    {
        return $this->site;
    }

    public function setSite(?SiteEvent $site): static
    {
        $this->site = $site;

        return $this;
    }

    public function getContact(): ?Contact
    {
        return $this->contact;
    }

    public function setContact(?Contact $contact): static
    {
        $this->contact = $contact;

        return $this;
    }

    public function getDateMontage(): ?\DateTimeInterface
    {
        return $this->dateMontage;
    }

    public function setDateMontage(?\DateTimeInterface $dateMontage): static
    {
        $this->dateMontage = $dateMontage;

        return $this;
    }

    public function getDateStartShow(): ?\DateTimeInterface
    {
        return $this->dateStartShow;
    }

    public function setDateStartShow(?\DateTimeInterface $dateStartShow): static
    {
        $this->dateStartShow = $dateStartShow;

        return $this;
    }

    public function getDateEndSHOW(): ?\DateTimeInterface
    {
        return $this->dateEndSHOW;
    }

    public function setDateEndSHOW(?\DateTimeInterface $dateEndSHOW): static
    {
        $this->dateEndSHOW = $dateEndSHOW;

        return $this;
    }

    public function getDateEnd(): ?\DateTimeInterface
    {
        return $this->dateEnd;
    }

    public function setDateEnd(?\DateTimeInterface $dateEnd): static
    {
        $this->dateEnd = $dateEnd;

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
            $eventDetail->setEvent($this);
        }

        return $this;
    }

    public function removeEventDetail(EventDetail $eventDetail): static
    {
        if ($this->eventDetails->removeElement($eventDetail)) {
            // set the owning side to null (unless already changed)
            if ($eventDetail->getEvent() === $this) {
                $eventDetail->setEvent(null);
            }
        }

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): static
    {
        $this->name = $name;

        return $this;
    }
}
