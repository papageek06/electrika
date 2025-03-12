<?php

namespace App\Entity;

use App\Repository\PictureLinkRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PictureLinkRepository::class)]
class PictureLink
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'pictureLinks')]
    private ?Event $eventsLink = null;

    #[ORM\ManyToOne(inversedBy: 'pictureLinks')]
    private ?SiteEvent $siteLink = null;

    #[ORM\ManyToOne(inversedBy: 'pictureLinks')]
    private ?GaleryPicture $galeryPicture = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEventsLink(): ?Event
    {
        return $this->eventsLink;
    }

    public function setEventsLink(?Event $eventsLink): static
    {
        $this->eventsLink = $eventsLink;

        return $this;
    }

    public function getSiteLink(): ?SiteEvent
    {
        return $this->siteLink;
    }

    public function setSiteLink(?SiteEvent $siteLink): static
    {
        $this->siteLink = $siteLink;

        return $this;
    }

    public function getGaleryPicture(): ?GaleryPicture
    {
        return $this->galeryPicture;
    }

    public function setGaleryPicture(?GaleryPicture $galeryPicture): static
    {
        $this->galeryPicture = $galeryPicture;

        return $this;
    }
}
