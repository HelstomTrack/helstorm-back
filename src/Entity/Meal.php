<?php

namespace App\Entity;

use App\Repository\MealRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: MealRepository::class)]
class Meal
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['diet'])]
    private ?string $name = null;

    #[ORM\Column]
    #[Groups(['diet'])]
    private ?float $total_calories = null;

    #[ORM\Column]
    private ?float $total_protein = null;

    #[ORM\Column(length: 255)]
    private ?float $total_carbs = null;

    #[ORM\Column]
    private ?float $total_fat = null;

    /**
     * @var Collection<int, Food>
     */
    #[ORM\ManyToMany(targetEntity: Food::class, inversedBy: 'meals')]
    #[Groups(['diet'])]
    private Collection $food;

    /**
     * @var Collection<int, Diet>
     */
    #[ORM\ManyToMany(targetEntity: Diet::class, inversedBy: 'meals')]
    private Collection $diet;

    /**
     * @var Collection<int, Day>
     */
    #[ORM\ManyToMany(targetEntity: Day::class, mappedBy: 'meals')]
    private Collection $days;

    public function __construct()
    {
        $this->food = new ArrayCollection();
        $this->diet = new ArrayCollection();
        $this->days = new ArrayCollection();
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

    public function getTotalCalories(): ?float
    {
        return $this->total_calories;
    }

    public function setTotalCalories(float $total_calories): static
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

    public function setTotalCarbs(string $total_carbs): static
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

    /**
     * @return Collection<int, Food>
     */
    public function getFood(): Collection
    {
        return $this->food;
    }

    public function addFood(Food $food): static
    {
        if (!$this->food->contains($food)) {
            $this->food->add($food);
        }

        return $this;
    }

    public function removeFood(Food $food): static
    {
        $this->food->removeElement($food);

        return $this;
    }

    /**
     * @return Collection<int, Diet>
     */
    public function getDiet(): Collection
    {
        return $this->diet;
    }

    public function addDiet(Diet $diet): static
    {
        if (!$this->diet->contains($diet)) {
            $this->diet->add($diet);
        }

        return $this;
    }

    public function removeDiet(Diet $diet): static
    {
        $this->diet->removeElement($diet);

        return $this;
    }

    /**
     * @return Collection<int, Day>
     */
    public function getDays(): Collection
    {
        return $this->days;
    }

    public function addDay(Day $day): static
    {
        if (!$this->days->contains($day)) {
            $this->days->add($day);
            $day->addMeal($this);
        }

        return $this;
    }

    public function removeDay(Day $day): static
    {
        if ($this->days->removeElement($day)) {
            $day->removeMeal($this);
        }

        return $this;
    }
}
