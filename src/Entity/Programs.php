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

    #[ORM\Column]
    private ?\DateTimeImmutable $created_at = null;

    /**
     * @var Collection<int, ProgramsExercises>
     */
    #[ORM\OneToMany(targetEntity: ProgramsExercises::class, mappedBy: 'program')]
    private Collection $programsExercises;

    /**
     * @var Collection<int, Plan>
     */
    #[ORM\ManyToMany(targetEntity: Plan::class, mappedBy: 'program')]
    private Collection $plans;

    /**
     * @var Collection<int, PlanProgramDay>
     */
    #[ORM\OneToMany(targetEntity: PlanProgramDay::class, mappedBy: 'program')]
    private Collection $planProgramDays;

    #[ORM\Column(type: Types::ARRAY)]
    private array $content = [];

    #[ORM\ManyToOne(inversedBy: 'programs')]
    private ?User $user = null;

    #[ORM\Column(length: 255)]
    private ?string $threadId = null;

    #[ORM\Column(length: 255)]
    private ?string $runId = null;

    public function __construct()
    {
        $this->userPrograms = new ArrayCollection();
        $this->programsExercises = new ArrayCollection();
        $this->plans = new ArrayCollection();
        $this->planProgramDays = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getPlan(): ?Plan
    {
        return $this->plan;
    }

    public function setPlan(?Plan $plan): static
    {
        $this->plan = $plan;

        return $this;
    }

    /**
     * @return Collection<int, Plan>
     */
    public function getPlans(): Collection
    {
        return $this->plans;
    }

    public function addPlan(Plan $plan): static
    {
        if (!$this->plans->contains($plan)) {
            $this->plans->add($plan);
            $plan->addProgram($this);
        }

        return $this;
    }

    public function removePlan(Plan $plan): static
    {
        if ($this->plans->removeElement($plan)) {
            $plan->removeProgram($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, PlanProgramDay>
     */
    public function getPlanProgramDays(): Collection
    {
        return $this->planProgramDays;
    }

    public function addPlanProgramDay(PlanProgramDay $planProgramDay): static
    {
        if (!$this->planProgramDays->contains($planProgramDay)) {
            $this->planProgramDays->add($planProgramDay);
            $planProgramDay->setProgram($this);
        }

        return $this;
    }

    public function removePlanProgramDay(PlanProgramDay $planProgramDay): static
    {
        if ($this->planProgramDays->removeElement($planProgramDay)) {
            // set the owning side to null (unless already changed)
            if ($planProgramDay->getProgram() === $this) {
                $planProgramDay->setProgram(null);
            }
        }

        return $this;
    }

    public function getContent(): array
    {
        return $this->content;
    }

    public function setContent(array $content): static
    {
        $this->content = $content;

        return $this;
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

    public function getThreadId(): ?string
    {
        return $this->threadId;
    }

    public function setThreadId(string $threadId): static
    {
        $this->threadId = $threadId;

        return $this;
    }

    public function getRunId(): ?string
    {
        return $this->runId;
    }

    public function setRunId(string $runId): static
    {
        $this->runId = $runId;

        return $this;
    }
}
