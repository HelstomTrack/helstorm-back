<?php

namespace App\Entity;

use App\Repository\ProgramsExercisesRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: ProgramsExercisesRepository::class)]
class ProgramsExercises
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'programsExercises')]
    private ?Programs $program = null;

    #[ORM\ManyToOne(inversedBy: 'programsExercises')]
    private ?Exercises $exercise = null;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getExercise(): ?Exercises
    {
        return $this->exercise;
    }

    public function setExercise(?Exercises $exercise): static
    {
        $this->exercise = $exercise;

        return $this;
    }

    public function __toString(): string
    {
        return $this->id;
    }
}
