<?php

namespace App\Entity;

use App\Repository\ExercisesRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: ExercisesRepository::class)]
class Exercises
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['program:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = null;

    #[ORM\Column]
    private ?int $rest_time = null;

    #[ORM\Column(length: 255)]
    private ?string $difficulty = null;

    #[ORM\Column(length: 255)]
    private ?string $category = null;

    /**
     * @var Collection<int, ProgramsExercises>
     */
    #[ORM\OneToMany(targetEntity: ProgramsExercises::class, mappedBy: 'exercise')]
    private Collection $programsExercises;

    #[ORM\Column(length: 255)]
    private ?string $series = null;

    #[ORM\Column]
    private ?int $calories = null;

    public function __construct()
    {
        $this->programsExercises = new ArrayCollection();
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getRestTime(): ?int
    {
        return $this->rest_time;
    }

    public function setRestTime(int $rest_time): static
    {
        $this->rest_time = $rest_time;

        return $this;
    }

    public function getDifficulty(): ?string
    {
        return $this->difficulty;
    }

    public function setDifficulty(string $difficulty): static
    {
        $this->difficulty = $difficulty;

        return $this;
    }

    public function getCategory(): ?string
    {
        return $this->category;
    }

    public function setCategory(string $category): static
    {
        $this->category = $category;

        return $this;
    }

    /**
     * @return Collection<int, ProgramsExercises>
     */
    public function getProgramsExercises(): Collection
    {
        return $this->programsExercises;
    }

    public function addProgramsExercise(ProgramsExercises $programsExercise): static
    {
        if (!$this->programsExercises->contains($programsExercise)) {
            $this->programsExercises->add($programsExercise);
            $programsExercise->setExercise($this);
        }

        return $this;
    }

    public function removeProgramsExercise(ProgramsExercises $programsExercise): static
    {
        if ($this->programsExercises->removeElement($programsExercise)) {
            // set the owning side to null (unless already changed)
            if ($programsExercise->getExercise() === $this) {
                $programsExercise->setExercise(null);
            }
        }

        return $this;
    }

    public function getSeries(): ?string
    {
        return $this->series;
    }

    public function setSeries(string $series): static
    {
        $this->series = $series;

        return $this;
    }

    public function getCalories(): ?int
    {
        return $this->calories;
    }

    public function setCalories(int $calories): static
    {
        $this->calories = $calories;

        return $this;
    }

    public function __toString(): string
    {
        return $this->name;
    }
}
