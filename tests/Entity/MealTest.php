<?php

namespace App\Tests\Entity;

use App\Entity\Meal;
use App\Entity\Food;
use App\Entity\Diet;
use App\Entity\Day;
use PHPUnit\Framework\TestCase;

class MealTest extends TestCase
{
    public function testInitialCollectionsAreEmpty(): void
    {
        $meal = new Meal();

        $this->assertCount(0, $meal->getFood());
        $this->assertCount(0, $meal->getDiet());
        $this->assertCount(0, $meal->getDays());
    }

    public function testSettersAndGetters(): void
    {
        $meal = new Meal();

        $meal->setName('Lunch')
            ->setTotalCalories(550.5)
            ->setTotalProtein(30.2)
            ->setTotalCarbs('75.3') // ⚠️ ton setter accepte string
            ->setTotalFat(20.8);

        $this->assertSame('Lunch', $meal->getName());
        $this->assertSame(550.5, $meal->getTotalCalories());
        $this->assertSame(30.2, $meal->getTotalProtein());
        $this->assertSame(20.8, $meal->getTotalFat());
    }

    public function testFoodRelation(): void
    {
        $meal = new Meal();
        $food = new Food();

        $meal->addFood($food);
        $this->assertTrue($meal->getFood()->contains($food));

        $meal->removeFood($food);
        $this->assertFalse($meal->getFood()->contains($food));
    }

    public function testDietRelation(): void
    {
        $meal = new Meal();
        $diet = new Diet();

        $meal->addDiet($diet);
        $this->assertTrue($meal->getDiet()->contains($diet));

        $meal->removeDiet($diet);
        $this->assertFalse($meal->getDiet()->contains($diet));
    }

    public function testDayRelation(): void
    {
        $meal = new Meal();
        $day = new Day();

        $meal->addDay($day);
        $this->assertTrue($meal->getDays()->contains($day));
        $this->assertTrue($day->getMeals()->contains($meal));

        $meal->removeDay($day);
        $this->assertFalse($meal->getDays()->contains($day));
        $this->assertFalse($day->getMeals()->contains($meal));
    }
}
