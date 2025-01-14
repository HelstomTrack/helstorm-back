<?php

namespace App\Entity;

use App\Repository\ProgramRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProgramRepository::class)]
class Program
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'programs')]
    private ?User $user = null;

    #[ORM\Column(length: 255)]
    private ?string $goal = null;

    #[ORM\Column]
    private ?int $total_calories = null;

    #[ORM\Column]
    private ?float $total_protein = null;

    #[ORM\Column]
    private ?float $total_carbs = null;

    #[ORM\Column]
    private ?float $total_fat = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $created_at = null;

    /**
     * @var Collection<int, Meal>
     */
    #[ORM\ManyToMany(targetEntity: Meal::class, mappedBy: 'programme')]
    private Collection $meals;

    public function __construct()
    {
        $this->meals = new ArrayCollection();
    }

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

    public function getGoal(): ?string
    {
        return $this->goal;
    }

    public function setGoal(string $goal): static
    {
        $this->goal = $goal;

        return $this;
    }

    public function getTotalCalories(): ?int
    {
        return $this->total_calories;
    }

    public function setTotalCalories(int $total_calories): static
    {
        $this->total_calories = $total_calories;

        return $this;
    }

    public function getTotalProtein(): ?float
    {
        return $this->total_protein;
    }

    public function setTotalProtein(float $total_protein): static
    {
        $this->total_protein = $total_protein;

        return $this;
    }

    public function getTotalCarbs(): ?float
    {
        return $this->total_carbs;
    }

    public function setTotalCarbs(float $total_carbs): static
    {
        $this->total_carbs = $total_carbs;

        return $this;
    }

    public function getTotalFat(): ?float
    {
        return $this->total_fat;
    }

    public function setTotalFat(float $total_fat): static
    {
        $this->total_fat = $total_fat;

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
     * @return Collection<int, Meal>
     */
    public function getMeals(): Collection
    {
        return $this->meals;
    }

    public function addMeal(Meal $meal): static
    {
        if (!$this->meals->contains($meal)) {
            $this->meals->add($meal);
            $meal->addProgramme($this);
        }

        return $this;
    }

    public function removeMeal(Meal $meal): static
    {
        if ($this->meals->removeElement($meal)) {
            $meal->removeProgramme($this);
        }

        return $this;
    }
}
