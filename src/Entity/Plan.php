<?php

namespace App\Entity;

use App\Repository\PlanRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PlanRepository::class)]
class Plan
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    /**
     * @var Collection<int, User>
     */
    #[ORM\ManyToMany(targetEntity: User::class, inversedBy: 'plans')]
    private Collection $user;

    /**
     * @var Collection<int, Programs>
     */
    #[ORM\ManyToMany(targetEntity: Programs::class, inversedBy: 'plans')]
    private Collection $program;

    /**
     * @var Collection<int, PlanProgramDay>
     */
    #[ORM\OneToMany(targetEntity: PlanProgramDay::class, mappedBy: 'plan')]
    private Collection $planProgramDays;

    public function __construct()
    {
        $this->program = new ArrayCollection();
        $this->user = new ArrayCollection();
        $this->planProgramDays = new ArrayCollection();
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

    /**
     * @return Collection<int, User>
     */
    public function getUser(): Collection
    {
        return $this->user;
    }

    public function addUser(User $user): static
    {
        if (!$this->user->contains($user)) {
            $this->user->add($user);
        }

        return $this;
    }

    public function removeUser(User $user): static
    {
        $this->user->removeElement($user);

        return $this;
    }

    /**
     * @return Collection<int, Programs>
     */
    public function getProgram(): Collection
    {
        return $this->program;
    }

    public function addProgram(Programs $program): static
    {
        if (!$this->program->contains($program)) {
            $this->program->add($program);
        }

        return $this;
    }

    public function removeProgram(Programs $program): static
    {
        $this->program->removeElement($program);

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
            $planProgramDay->setPlan($this);
        }

        return $this;
    }

    public function removePlanProgramDay(PlanProgramDay $planProgramDay): static
    {
        if ($this->planProgramDays->removeElement($planProgramDay)) {
            // set the owning side to null (unless already changed)
            if ($planProgramDay->getPlan() === $this) {
                $planProgramDay->setPlan(null);
            }
        }

        return $this;
    }
}
