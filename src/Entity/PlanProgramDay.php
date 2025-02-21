<?php

namespace App\Entity;

use App\Repository\PlanProgramDayRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PlanProgramDayRepository::class)]
class PlanProgramDay
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'planProgramDays')]
    private ?Plan $plan = null;

    #[ORM\ManyToOne(inversedBy: 'planProgramDays')]
    private ?Programs $program = null;

    #[ORM\Column(length: 255)]
    private ?string $dayofweek = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPlan(): ?Plan
    {
        return $this->plan;
    }

    public function setPlan(?Plan $plan): static
    {
        $this->plan = $plan;

        return $this;
    }

    public function getProgram(): ?Programs
    {
        return $this->program;
    }

    public function setProgram(?Programs $program): static
    {
        $this->program = $program;

        return $this;
    }

    public function getDayofweek(): ?string
    {
        return $this->dayofweek;
    }

    public function setDayofweek(string $dayofweek): static
    {
        $this->dayofweek = $dayofweek;

        return $this;
    }
}
