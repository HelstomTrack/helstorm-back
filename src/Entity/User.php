<?php

namespace App\Entity;

use App\Repository\UserRepository;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;


#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\HasLifecycleCallbacks]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups('user')]
    private ?string $email = null;

    #[ORM\Column(length: 255)]
    #[Groups('user')]
    private ?string $firstname = null;

    #[ORM\Column(length: 255)]
    #[Groups('user')]
    private ?string $lastname = null;

    #[ORM\Column(length: 255)]
    private ?string $password = null;

    #[ORM\Column(length: 255)]
    #[Assert\Regex(
        pattern: '/^\+?[0-9]{10,15}$/',
        message: 'Le numéro de téléphone doit contenir entre 10 et 15 chiffres et peut commencer par +'
    )]
    private ?string $phone = null;

    #[ORM\Column]
    #[Groups('user')]
    private array $roles = ['ROLE_USER'];

    #[ORM\Column]
    #[Groups('user')]
    private ?\DateTimeImmutable $created_at = null;

    #[ORM\OneToOne(mappedBy: 'user', cascade: ['persist', 'remove'])]
    #[Groups('user')]
    private ?UserMetrics $userMetrics = null;

    /**
     * @var Collection<int, UserPrograms>
     */
    #[ORM\OneToMany(targetEntity: UserPrograms::class, mappedBy: 'user')]
    private Collection $userPrograms;

    public function __construct()
    {
        $this->userPrograms = new ArrayCollection();
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

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): static
    {
        $this->firstname = $firstname;

        return $this;
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

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(string $phone): static
    {
        $this->phone = $phone;

        return $this;
    }

    public function getRoles(): array
    {
        $roles = $this->roles;
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;
        return $this;
    }
    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->created_at;
    }

    public function getDefaultCreatedAt(): \DateTimeInterface
    {
        return $this->created_at;
    }

    #[ORM\PrePersist]
    public function setDefaultCreatedAt(): void
    {
        if ($this->created_at === null) {
            $this->created_at = new DateTimeImmutable();
        }
    }

    public function getUserMetrics(): ?UserMetrics
    {
        return $this->userMetrics;
    }

    public function setUserMetrics(?UserMetrics $userMetrics): static
    {
        // unset the owning side of the relation if necessary
        if ($userMetrics === null && $this->userMetrics !== null) {
            $this->userMetrics->setUser(null);
        }

        // set the owning side of the relation if necessary
        if ($userMetrics !== null && $userMetrics->getUser() !== $this) {
            $userMetrics->setUser($this);
        }

        $this->userMetrics = $userMetrics;

        return $this;
    }

    public function eraseCredentials(): void
    {
        // TODO: Implement eraseCredentials() method.
    }

    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @return Collection<int, UserPrograms>
     */
    public function getUserPrograms(): Collection
    {
        return $this->userPrograms;
    }

    public function addUserProgram(UserPrograms $userProgram): static
    {
        if (!$this->userPrograms->contains($userProgram)) {
            $this->userPrograms->add($userProgram);
            $userProgram->setUser($this);
        }

        return $this;
    }

    public function removeUserProgram(UserPrograms $userProgram): static
    {
        if ($this->userPrograms->removeElement($userProgram)) {
            // set the owning side to null (unless already changed)
            if ($userProgram->getUser() === $this) {
                $userProgram->setUser(null);
            }
        }

        return $this;
    }
}
