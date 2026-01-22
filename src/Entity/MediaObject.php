<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Delete;
use App\Repository\MediaObjectRepository;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Serializer\Annotation\Groups;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

#[ORM\Entity(repositoryClass: MediaObjectRepository::class)]
#[ApiResource(
    normalizationContext: ['groups' => ['media_object:read']],
    denormalizationContext: ['groups' => ['media_object:write']],
    operations: [
        new Get(),
        new GetCollection(),
        new Post(
            controller: \App\Controller\MediaObjectController::class,
            deserialize: false,
            validationContext: ['groups' => ['Default', 'media_object_create']]
        ),
        new Delete()
    ]
)]
#[Vich\Uploadable]
class MediaObject
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['media_object:read'])]
    private ?int $id = null;

    #[Vich\UploadableField(mapping: 'media_object', fileNameProperty: 'filePath')]
    #[Groups(['media_object:write'])]
    private ?File $file = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['media_object:read'])]
    private ?string $filePath = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['media_object:read', 'media_object:write'])]
    private ?string $mimeType = null;

    #[ORM\ManyToOne(targetEntity: Actor::class, inversedBy: 'mediaObjects')]
    #[ORM\JoinColumn(nullable: true)]
    #[Groups(['media_object:read', 'media_object:write'])]
    private ?Actor $actor = null;

    #[ORM\ManyToOne(targetEntity: Movie::class, inversedBy: 'mediaObjects')]
    #[ORM\JoinColumn(nullable: true)]
    #[Groups(['media_object:read', 'media_object:write'])]
    private ?Movie $movie = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    public function __construct()
    {
        $this->createdAt = new DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFile(): ?File
    {
        return $this->file;
    }

    public function setFile(?File $file): static
    {
        $this->file = $file;

        if ($file) {
            $this->mimeType = $file->getMimeType();
        }

        return $this;
    }

    public function getFilePath(): ?string
    {
        return $this->filePath;
    }

    public function setFilePath(?string $filePath): static
    {
        $this->filePath = $filePath;

        return $this;
    }

    public function getMimeType(): ?string
    {
        return $this->mimeType;
    }

    public function setMimeType(?string $mimeType): static
    {
        $this->mimeType = $mimeType;

        return $this;
    }

    public function getActor(): ?Actor
    {
        return $this->actor;
    }

    public function setActor(?Actor $actor): static
    {
        $this->actor = $actor;

        return $this;
    }

    public function getMovie(): ?Movie
    {
        return $this->movie;
    }

    public function setMovie(?Movie $movie): static
    {
        $this->movie = $movie;

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
}
