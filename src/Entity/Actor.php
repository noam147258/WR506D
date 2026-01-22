<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Delete;
use App\Repository\ActorRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ActorRepository::class)]
#[ApiResource]
#[Get(security: "is_granted('ROLE_USER')")]
#[GetCollection(security: "is_granted('ROLE_USER')")]
#[Post(security: "is_granted('ROLE_ADMIN')")]
#[Put(security: "is_granted('ROLE_ADMIN')")]
#[Patch(security: "is_granted('ROLE_ADMIN')")]
#[Delete(security: "is_granted('ROLE_ADMIN')")]
class Actor
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Le nom de famille est obligatoire")]
    #[Assert\Length(
        min: 2,
        max: 255,
        minMessage: "Le nom de famille doit contenir au moins {{ limit }} caractères",
        maxMessage: "Le nom de famille ne peut pas dépasser {{ limit }} caractères"
    )]
    private ?string $lastname = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\Length(
        max: 255,
        maxMessage: "Le prénom ne peut pas dépasser {{ limit }} caractères"
    )]
    private ?string $firstname = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    #[Assert\Type(\DateTimeInterface::class)]
    #[Assert\LessThan('today', message: "La date de naissance doit être antérieure à aujourd'hui")]
    private ?\DateTime $dob = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    #[Assert\Type(\DateTimeInterface::class)]
    private ?\DateTime $dod = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $bio = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\Url(message: "La photo doit être une URL valide")]
    #[Assert\Length(
        max: 255,
        maxMessage: "L'URL de la photo ne peut pas dépasser {{ limit }} caractères"
    )]
    private ?string $photo = null;

    /**
     * @var Collection<int, Movie>
     */
    #[ORM\ManyToMany(targetEntity: Movie::class, inversedBy: 'actors')]
    private Collection $movies;

    /**
     * @var Collection<int, MediaObject>
     */
    #[ORM\OneToMany(targetEntity: MediaObject::class, mappedBy: 'actor')]
    private Collection $mediaObjects;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    public function __construct()
    {
        $this->movies = new ArrayCollection();
        $this->mediaObjects = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): static
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(?string $firstname): static
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getDob(): ?\DateTime
    {
        return $this->dob;
    }

    public function setDob(?\DateTime $dob): static
    {
        $this->dob = $dob;

        return $this;
    }

    public function getDod(): ?\DateTime
    {
        return $this->dod;
    }

    public function setDod(?\DateTime $dod): static
    {
        $this->dod = $dod;

        return $this;
    }

    public function getBio(): ?string
    {
        return $this->bio;
    }

    public function setBio(?string $bio): static
    {
        $this->bio = $bio;

        return $this;
    }

    public function getPhoto(): ?string
    {
        return $this->photo;
    }

    public function setPhoto(?string $photo): static
    {
        $this->photo = $photo;

        return $this;
    }

    /**
     * @return Collection<int, Movie>
     */
    public function getMovies(): Collection
    {
        return $this->movies;
    }

    public function addMovie(Movie $movie): static
    {
        if (!$this->movies->contains($movie)) {
            $this->movies->add($movie);
        }

        return $this;
    }

    public function removeMovie(Movie $movie): static
    {
        $this->movies->removeElement($movie);

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * @return Collection<int, MediaObject>
     */
    public function getMediaObjects(): Collection
    {
        return $this->mediaObjects;
    }

    public function addMediaObject(MediaObject $mediaObject): static
    {
        if (!$this->mediaObjects->contains($mediaObject)) {
            $this->mediaObjects->add($mediaObject);
            $mediaObject->setActor($this);
        }

        return $this;
    }

    public function removeMediaObject(MediaObject $mediaObject): static
    {
        if ($this->mediaObjects->removeElement($mediaObject)) {
            if ($mediaObject->getActor() === $this) {
                $mediaObject->setActor(null);
            }
        }

        return $this;
    }
}
