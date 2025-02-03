<?php

namespace App\Entity;

use App\Repository\ProgramsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: ProgramsRepository::class)]
class Programs
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Groups('program')]
    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $created_at = null;

    /**
     * @var Collection<int, UserPrograms>
     */
    #[ORM\OneToMany(targetEntity: UserPrograms::class, mappedBy: 'programs')]
    private Collection $userPrograms;

    /**
     * @var Collection<int, ProgramsExercises>
     */
    #[ORM\OneToMany(targetEntity: ProgramsExercises::class, mappedBy: 'program')]
    private Collection $programsExercises;


    public function __construct()
    {
        $this->userPrograms = new ArrayCollection();
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

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeImmutable $created_at): static
    {
        $this->created_at = $created_at;

        return $this;
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
            $userProgram->setPrograms($this);
        }

        return $this;
    }

    public function removeUserProgram(UserPrograms $userProgram): static
    {
        if ($this->userPrograms->removeElement($userProgram)) {
            // set the owning side to null (unless already changed)
            if ($userProgram->getPrograms() === $this) {
                $userProgram->setPrograms(null);
            }
        }

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
            $programsExercise->setProgram($this);
        }

        return $this;
    }

    public function removeProgramsExercise(ProgramsExercises $programsExercise): static
    {
        if ($this->programsExercises->removeElement($programsExercise)) {
            // set the owning side to null (unless already changed)
            if ($programsExercise->getProgram() === $this) {
                $programsExercise->setProgram(null);
            }
        }

        return $this;
    }

    public function __toString(): string
    {
        return $this->name;
    }
}
