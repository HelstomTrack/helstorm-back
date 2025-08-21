<?php

namespace App\Tests\Service;

use App\Entity\Plan;
use App\Service\ProgramSelectorService;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use PHPUnit\Framework\TestCase;

class ProgramSelectorServiceTest extends TestCase
{
    private $entityManager;
    private $repository;
    private $service;

    protected function setUp(): void
    {
        // Mock EntityRepository (et non ObjectRepository)
        $this->repository = $this->createMock(EntityRepository::class);

        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->entityManager
            ->method('getRepository')
            ->with(Plan::class)
            ->willReturn($this->repository);

        $this->service = new ProgramSelectorService($this->entityManager);
    }

    public function testGetProgramReturnsCorrectPlan(): void
    {
        $plan = new Plan();
        $plan->setName('Full Body');

        $this->repository
            ->expects($this->once())
            ->method('findOneBy')
            ->with(['name' => 'Full Body'])
            ->willReturn($plan);

        $result = $this->service->getProgram('Bulk', 55, 160); // light + little => Full Body
        $this->assertSame($plan, $result);
    }

    public function testGetProgramReturnsNullIfNotMapped(): void
    {
        $result = $this->service->getProgram('UnknownGoal', 70, 170);
        $this->assertNull($result);
    }

    public function testCategorizeWeight(): void
    {
        $method = new \ReflectionMethod(ProgramSelectorService::class, 'categorizeWeight');
        $method->setAccessible(true);

        $this->assertSame('light', $method->invokeArgs($this->service, [60]));
        $this->assertSame('medium', $method->invokeArgs($this->service, [70]));
        $this->assertSame('heavy', $method->invokeArgs($this->service, [90]));
    }

    public function testCategorizeHeight(): void
    {
        $method = new \ReflectionMethod(ProgramSelectorService::class, 'categorizeHeight');
        $method->setAccessible(true);

        $this->assertSame('little', $method->invokeArgs($this->service, [160]));
        $this->assertSame('average', $method->invokeArgs($this->service, [175]));
        $this->assertSame('big', $method->invokeArgs($this->service, [185]));
    }

    public function testDifferentMappingCombination(): void
    {
        $plan = new Plan();
        $plan->setName('Hypertrophy');

        $this->repository
            ->expects($this->once())
            ->method('findOneBy')
            ->with(['name' => 'Hypertrophy'])
            ->willReturn($plan);

        $result = $this->service->getProgram('Bulk', 70, 170); // medium + average = Hypertrophy
        $this->assertSame($plan, $result);
    }
}
