<?php

namespace App\Tests\Service;

use App\Entity\Plan;
use App\Service\ProgramSelectorService;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use PHPUnit\Framework\TestCase;

class ProgramSelectorServiceTest extends TestCase
{
    public function testGetProgramReturnsCorrectPlan()
    {
        $plan = new Plan();

        $planRepo = $this->createMock(EntityRepository::class);
        $planRepo->method('findOneBy')->willReturn($plan);

        $entityManager = $this->createMock(EntityManagerInterface::class);
        $entityManager->method('getRepository')->willReturn($planRepo);

        $service = new ProgramSelectorService($entityManager);

        // Strong -> medium weight -> average height = 'PPL'
        $result = $service->getProgram('Strong', 70, 175);

        $this->assertSame($plan, $result);
    }

    public function testGetProgramReturnsNullIfNotFound()
    {
        $entityManager = $this->createMock(EntityManagerInterface::class);
        $planRepo = $this->createMock(EntityRepository::class);
        $planRepo->method('findOneBy')->willReturn(null);


        $entityManager->method('getRepository')->willReturn($planRepo);

        $service = new ProgramSelectorService($entityManager);
        $result = $service->getProgram('UnknownGoal', 70, 170);

        $this->assertNull($result);
    }

    public function testCategorizeWeight()
    {
        $reflection = new \ReflectionClass(ProgramSelectorService::class);
        $method = $reflection->getMethod('categorizeWeight');
        $method->setAccessible(true);
        $service = new ProgramSelectorService($this->createMock(EntityManagerInterface::class));

        $this->assertEquals('light', $method->invoke($service, 55));
        $this->assertEquals('medium', $method->invoke($service, 70));
        $this->assertEquals('heavy', $method->invoke($service, 85));
    }

    public function testCategorizeHeight()
    {
        $reflection = new \ReflectionClass(ProgramSelectorService::class);
        $method = $reflection->getMethod('categorizeHeight');
        $method->setAccessible(true);
        $service = new ProgramSelectorService($this->createMock(EntityManagerInterface::class));

        $this->assertEquals('little', $method->invoke($service, 160));
        $this->assertEquals('average', $method->invoke($service, 175));
        $this->assertEquals('big', $method->invoke($service, 190));
    }
}
