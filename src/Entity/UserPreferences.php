<?php

namespace App\Entity;

use App\Repository\UserPreferencesRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserPreferencesRepository::class)]
class UserPreferences
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    private ?User $user = null;

    #[ORM\Column(length: 255)]
    private ?string $dietary_restriction = null;

    #[ORM\Column]
    private ?int $calorie_goal = null;

    #[ORM\Column]
    private ?float $protein_goal = null;

    #[ORM\Column]
    private ?float $fat_goal = null;

    #[ORM\Column]
    private ?float $carb_goal = null;

    #[ORM\Column(length: 255)]
    private ?string $goal = null;

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

    public function getDietaryRestriction(): ?string
    {
        return $this->dietary_restriction;
    }

    public function setDietaryRestriction(string $dietary_restriction): static
    {
        $this->dietary_restriction = $dietary_restriction;

        return $this;
    }

    public function getCalorieGoal(): ?int
    {
        return $this->calorie_goal;
    }

    public function setCalorieGoal(int $calorie_goal): static
    {
        $this->calorie_goal = $calorie_goal;

        return $this;
    }

    public function getProteinGoal(): ?float
    {
        return $this->protein_goal;
    }

    public function setProteinGoal(float $protein_goal): static
    {
        $this->protein_goal = $protein_goal;

        return $this;
    }

    public function getFatGoal(): ?float
    {
        return $this->fat_goal;
    }

    public function setFatGoal(float $fat_goal): static
    {
        $this->fat_goal = $fat_goal;

        return $this;
    }

    public function getCarbGoal(): ?float
    {
        return $this->carb_goal;
    }

    public function setCarbGoal(float $carb_goal): static
    {
        $this->carb_goal = $carb_goal;

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
}
