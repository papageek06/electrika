<?php

namespace App\Entity;

use App\Repository\SiteEventRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SiteEventRepository::class)]
class SiteEvent
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    private ?string $name = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $address = null;

    #[ORM\Column(length: 150)]
    private ?string $city = null;

    #[ORM\Column(length: 15, nullable: true)]
    private ?string $postalCode = null;

    #[ORM\Column(length: 255)]
    private ?string $description = null;

    /**
     * @var Collection<int, Event>
     */
    #[ORM\OneToMany(targetEntity: Event::class, mappedBy: 'site')]
    private Collection $events;

    /**
     * @var Collection<int, GaleryPicture>
     */
    #[ORM\ManyToMany(targetEntity: GaleryPicture::class, mappedBy: 'site')]
    private Collection $galeryPictures;

    /**
     * @var Collection<int, Contact>
     */
    #[ORM\ManyToMany(targetEntity: Contact::class, inversedBy: 'siteEvents')]
    private Collection $contact;

    /**
     * @var Collection<int, Connector>
     */
    #[ORM\ManyToMany(targetEntity: Connector::class, mappedBy: 'site')]
    private Collection $connectors;

    

   

    public function __construct()
    {
        $this->events = new ArrayCollection();
        $this->galeryPictures = new ArrayCollection();
        $this->contact = new ArrayCollection();
        $this->connectors = new ArrayCollection();
        

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

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(?string $address): static
    {
        $this->address = $address;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(string $city): static
    {
        $this->city = $city;

        return $this;
    }

    public function getPostalCode(): ?string
    {
        return $this->postalCode;
    }

    public function setPostalCode(?string $postalCode): static
    {
        $this->postalCode = $postalCode;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return Collection<int, Event>
     */
    public function getEvents(): Collection
    {
        return $this->events;
    }

    public function addEvent(Event $event): static
    {
        if (!$this->events->contains($event)) {
            $this->events->add($event);
            $event->setSite($this);
        }

        return $this;
    }

    public function removeEvent(Event $event): static
    {
        if ($this->events->removeElement($event)) {
            // set the owning side to null (unless already changed)
            if ($event->getSite() === $this) {
                $event->setSite(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, GaleryPicture>
     */
    public function getGaleryPictures(): Collection
    {
        return $this->galeryPictures;
    }

    public function addGaleryPicture(GaleryPicture $galeryPicture): static
    {
        if (!$this->galeryPictures->contains($galeryPicture)) {
            $this->galeryPictures->add($galeryPicture);
            $galeryPicture->addSite($this);
        }

        return $this;
    }

    public function removeGaleryPicture(GaleryPicture $galeryPicture): static
    {
        if ($this->galeryPictures->removeElement($galeryPicture)) {
            $galeryPicture->removeSite($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, Contact>
     */
    public function getContact(): Collection
    {
        return $this->contact;
    }

    public function addContact(Contact $contact): static
    {
        if (!$this->contact->contains($contact)) {
            $this->contact->add($contact);
        }

        return $this;
    }

    public function removeContact(Contact $contact): static
    {
        $this->contact->removeElement($contact);

        return $this;
    }

    /**
     * @return Collection<int, Connector>
     */
    public function getConnectors(): Collection
    {
        return $this->connectors;
    }

    public function addConnector(Connector $connector): static
    {
        if (!$this->connectors->contains($connector)) {
            $this->connectors->add($connector);
            $connector->addSite($this);
        }

        return $this;
    }

    public function removeConnector(Connector $connector): static
    {
        if ($this->connectors->removeElement($connector)) {
            $connector->removeSite($this);
        }

        return $this;
    }

   

   
}
