<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Patch;
use App\Repository\UserRepository;
use App\State\UserPasswordHasher;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
    #[ApiResource(
        operations: [
        new Post(processor: UserPasswordHasher::class),
        new Put(processor: UserPasswordHasher::class),
        new Patch(processor: UserPasswordHasher::class),
        ],
        normalizationContext: ['groups' => ['user:read']],
        denormalizationContext: ['groups' => ['user:write']]
    )]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    #[Assert\NotBlank(message: "L'email est obligatoire")]
    #[Assert\Email(message: "L'email doit être valide")]
    #[Groups(['user:read', 'user:write'])]
    private ?string $email = null;

    /**
     * @var list<string> The user roles
     */
    #[ORM\Column]
    #[Groups(['user:read', 'user:write'])]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    #[Groups(['user:read'])]
    private ?string $password = null;

    /**
     * Plain password (not stored in database)
     */
    #[Groups(['user:write'])]
    #[Assert\NotBlank(groups: ['user:create'], message: "Le mot de passe est obligatoire")]
    #[Assert\Length(
        min: 6,
        minMessage: "Le mot de passe doit contenir au moins {{ limit }} caractères",
        groups: ['user:create', 'user:update']
    )]
    private ?string $plainPassword = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['user:read', 'user:write'])]
    private ?int $rateLimit = null;

    #[ORM\Column(length: 64, unique: true, nullable: true)]
    #[Assert\Length(
        exactly: 64,
        exactMessage: "Le hash de la clé API doit contenir exactement {{ limit }} caractères"
    )]
    private ?string $apiKeyHash = null;

    #[ORM\Column(length: 16, nullable: true)]
    #[Assert\Length(
        exactly: 16,
        exactMessage: "Le préfixe de la clé API doit contenir exactement {{ limit }} caractères"
    )]
    #[Groups(['user:read'])]
    private ?string $apiKeyPrefix = null;

    #[ORM\Column]
    #[Groups(['user:read', 'user:write'])]
    private ?bool $apiKeyEnabled = false;

    #[ORM\Column(nullable: true)]
    #[Groups(['user:read'])]
    private ?\DateTimeImmutable $apiKeyCreatedAt = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['user:read'])]
    private ?\DateTimeImmutable $apiKeyLastUsedAt = null;

    public function __construct()
    {
        $this->createdAt = new DateTimeImmutable();
        $this->apiKeyEnabled = false;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     *
     * @return list<string>
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * @param list<string> $roles
     */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        $this->plainPassword = null;
    }

    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }

    public function setPlainPassword(?string $plainPassword): static
    {
        $this->plainPassword = $plainPassword;

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

    public function getRateLimit(): ?int
    {
        return $this->rateLimit;
    }

    public function setRateLimit(?int $rateLimit): static
    {
        $this->rateLimit = $rateLimit;

        return $this;
    }

    public function getApiKeyHash(): ?string
    {
        return $this->apiKeyHash;
    }

    public function setApiKeyHash(?string $apiKeyHash): static
    {
        $this->apiKeyHash = $apiKeyHash;

        return $this;
    }

    public function getApiKeyPrefix(): ?string
    {
        return $this->apiKeyPrefix;
    }

    public function setApiKeyPrefix(?string $apiKeyPrefix): static
    {
        $this->apiKeyPrefix = $apiKeyPrefix;

        return $this;
    }

    public function isApiKeyEnabled(): ?bool
    {
        return $this->apiKeyEnabled;
    }

    public function setApiKeyEnabled(bool $apiKeyEnabled): static
    {
        $this->apiKeyEnabled = $apiKeyEnabled;

        return $this;
    }

    public function getApiKeyCreatedAt(): ?\DateTimeImmutable
    {
        return $this->apiKeyCreatedAt;
    }

    public function setApiKeyCreatedAt(?\DateTimeImmutable $apiKeyCreatedAt): static
    {
        $this->apiKeyCreatedAt = $apiKeyCreatedAt;

        return $this;
    }

    public function getApiKeyLastUsedAt(): ?\DateTimeImmutable
    {
        return $this->apiKeyLastUsedAt;
    }

    public function setApiKeyLastUsedAt(?\DateTimeImmutable $apiKeyLastUsedAt): static
    {
        $this->apiKeyLastUsedAt = $apiKeyLastUsedAt;

        return $this;
    }

    public function updateApiKeyLastUsedAt(): static
    {
        $this->apiKeyLastUsedAt = new DateTimeImmutable();

        return $this;
    }
}
