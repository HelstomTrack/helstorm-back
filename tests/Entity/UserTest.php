<?php

namespace App\Tests\Entity;

use App\Entity\User;
use App\Entity\Plan;
use App\Entity\Diet;
use App\Entity\Programs;
use App\Entity\UserMetrics;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    public function testSettersAndGetters(): void
    {
        $user = new User();

        $user->setEmail('test@example.com')
            ->setFirstname('John')
            ->setLastname('Doe')
            ->setPassword('secret')
            ->setPhone('+33612345678')
            ->setRoles(['ROLE_ADMIN']);

        $this->assertSame('test@example.com', $user->getEmail());
        $this->assertSame('John', $user->getFirstname());
        $this->assertSame('Doe', $user->getLastname());
        $this->assertSame('secret', $user->getPassword());
        $this->assertSame('+33612345678', $user->getPhone());

        // ROLE_USER doit toujours être présent
        $this->assertContains('ROLE_USER', $user->getRoles());
        $this->assertContains('ROLE_ADMIN', $user->getRoles());

        // UserIdentifier = email
        $this->assertSame('test@example.com', $user->getUserIdentifier());
    }

    public function testCreatedAtDefault(): void
    {
        $user = new User();
        $this->assertNull($user->getCreatedAt());

        $user->setDefaultCreatedAt();
        $this->assertInstanceOf(\DateTimeImmutable::class, $user->getCreatedAt());
    }

    public function testUserMetricsRelation(): void
    {
        $user = new User();
        $metrics = new UserMetrics();

        $user->setUserMetrics($metrics);

        $this->assertSame($metrics, $user->getUserMetrics());
        $this->assertSame($user, $metrics->getUser());

        // Suppression
        $user->setUserMetrics(null);
        $this->assertNull($user->getUserMetrics());
        $this->assertNull($metrics->getUser());
    }

    public function testPlansRelation(): void
    {
        $user = new User();
        $plan = new Plan();

        $user->addPlan($plan);
        $this->assertTrue($user->getPlans()->contains($plan));
        $this->assertTrue($plan->getUser()->contains($user));

        $user->removePlan($plan);
        $this->assertFalse($user->getPlans()->contains($plan));
    }

    public function testDietsRelation(): void
    {
        $user = new User();
        $diet = new Diet();

        $user->addDiet($diet);
        $this->assertTrue($user->getDiets()->contains($diet));
        $this->assertTrue($diet->getUser()->contains($user));

        $user->removeDiet($diet);
        $this->assertFalse($user->getDiets()->contains($diet));
    }

    public function testProgramsRelation(): void
    {
        $user = new User();
        $program = new Programs();

        $user->addProgram($program);
        $this->assertTrue($user->getPrograms()->contains($program));
        $this->assertSame($user, $program->getUser());

        $user->removeProgram($program);
        $this->assertFalse($user->getPrograms()->contains($program));
        $this->assertNull($program->getUser());
    }

    public function testEraseCredentials(): void
    {
        $user = new User();
        $this->assertNull($user->eraseCredentials());
    }
}
