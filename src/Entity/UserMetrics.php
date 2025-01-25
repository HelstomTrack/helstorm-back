<?php

namespace App\Entity;

use App\Repository\UserMetricsRepository;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: UserMetricsRepository::class)]
#[ORM\HasLifecycleCallbacks]
class UserMetrics
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\OneToOne(inversedBy: 'userMetrics', cascade: ['persist', 'remove'])]
    private ?User $user = null;

    #[ORM\Column]
    #[Groups('user')]
    private ?int $age = null;

    #[ORM\Column]
    #[Groups('user')]
    private ?float $weight = null;

    #[ORM\Column]
    #[Groups('user')]
    private ?float $height = null;

    #[ORM\Column(length: 255)]
    #[Groups('user')]
    private ?string $goal = null;

    #[ORM\Column(length: 255)]
    #[Groups('user')]
    private ?string $level = null;

    #[ORM\Column(length: 255)]
    #[Groups('user')]
    private ?string $gender = null;
    #[ORM\Column]
    private ?\DateTimeImmutable $created_at = null;


    public function getId(): ?int
    {
        return $this->id;
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

    public function getAge(): ?int
    {
        return $this->age;
    }

    public function setAge(int $age): static
    {
        $this->age = $age;

        return $this;
    }

    public function getWeight(): ?float
    {
        return $this->weight;
    }

    public function setWeight(float $weight): static
    {
        $this->weight = $weight;

        return $this;
    }

    public function getHeight(): ?float
    {
        return $this->height;
    }

    public function setHeight(float $height): static
    {
        $this->height = $height;

        return $this;
    }

    public function getGoal(): ?string
    {
        return $this->goal;
    }

    public function setGoal(string $goal): static
    {
        $this->goal = $goal;

        return $this;
    }

    public function getLevel(): ?string
    {
        return $this->level;
    }

    public function setLevel(string $level): static
    {
        $this->level = $level;

        return $this;
    }

    public function getGender(): ?string
    {
        return $this->gender;
    }

    public function setGender(string $gender): static
    {
        $this->gender = $gender;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
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
}
