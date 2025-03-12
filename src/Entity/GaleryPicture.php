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
     * @var Collection<int, PictureLink>
     */
    #[ORM\OneToMany(targetEntity: PictureLink::class, mappedBy: 'galeryPicture')]
    private Collection $pictureLinks;

    public function __construct()
    {
        $this->pictureLinks = new ArrayCollection();
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
     * @return Collection<int, PictureLink>
     */
    public function getPictureLinks(): Collection
    {
        return $this->pictureLinks;
    }

    public function addPictureLink(PictureLink $pictureLink): static
    {
        if (!$this->pictureLinks->contains($pictureLink)) {
            $this->pictureLinks->add($pictureLink);
            $pictureLink->setGaleryPicture($this);
        }

        return $this;
    }

    public function removePictureLink(PictureLink $pictureLink): static
    {
        if ($this->pictureLinks->removeElement($pictureLink)) {
            // set the owning side to null (unless already changed)
            if ($pictureLink->getGaleryPicture() === $this) {
                $pictureLink->setGaleryPicture(null);
            }
        }

        return $this;
    }
}
