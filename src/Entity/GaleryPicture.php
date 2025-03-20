<?php

namespace App\Entity;

use App\Repository\GaleryPictureRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: GaleryPictureRepository::class)]
class GaleryPicture
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $picture = null;

    /**
     * @var Collection<int, Event>
     */
    #[ORM\ManyToMany(targetEntity: Event::class, inversedBy: 'galeryPictures')]
    private Collection $event;

    /**
     * @var Collection<int, SiteEvent>
     */
    #[ORM\ManyToMany(targetEntity: SiteEvent::class, inversedBy: 'galeryPictures')]
    private Collection $site;

    public function __construct()
    {
        $this->event = new ArrayCollection();
        $this->site = new ArrayCollection();
    }



   
    

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPicture(): ?string
    {
        return $this->picture;
    }

    public function setPicture(string $picture): static
    {
        $this->picture = $picture;

        return $this;
    }

    /**
     * @return Collection<int, Event>
     */
    public function getEvent(): Collection
    {
        return $this->event;
    }

    public function addEvent(Event $event): static
    {
        if (!$this->event->contains($event)) {
            $this->event->add($event);
        }

        return $this;
    }

    public function removeEvent(Event $event): static
    {
        $this->event->removeElement($event);

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
