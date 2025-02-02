<?php

namespace App\Entity;

use App\Repository\UserProgramsRepository;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserProgramsRepository::class)]
#[ORM\HasLifecycleCallbacks]

class UserPrograms
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'userPrograms')]
    private ?User $user = null;

    #[ORM\ManyToOne(inversedBy: 'userPrograms')]
    private ?Programs $programs = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $assigned_at = null;

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

    public function getPrograms(): ?Programs
    {
        return $this->programs;
    }

    public function setPrograms(?Programs $programs): static
    {
        $this->programs = $programs;

        return $this;
    }

    public function getAssignedAt(): ?\DateTimeImmutable
    {
        return $this->assigned_at;
    }

    public function setAssignedAt(\DateTimeImmutable $assigned_at): static
    {
        $this->assigned_at = $assigned_at;

        return $this;
    }
    #[ORM\PrePersist]
    public function setDefaultAssignedAt(): void
    {
        if ($this->assigned_at === null) {
            $this->assigned_at = new DateTimeImmutable();
        }
    }

}
