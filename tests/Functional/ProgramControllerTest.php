<?php

namespace App\Tests\Controller;

use App\Controller\ProgramController;
use App\Entity\Plan;
use App\Entity\Programs;
use App\Entity\User;
use App\Entity\UserMetrics;
use App\Service\ProgramSelectorService;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\JsonResponse;

class ProgramControllerTest extends TestCase
{

    public function testCreateUserWithProgramSuccess()
    {
        $user = new User();
        $userMetrics = new UserMetrics();
        $userMetrics->setUser($user)->setGoal('Bulk')->setWeight(70)->setHeight(175);
        $plan = $this->createMock(Plan::class);

        // Mock ProgramSelectorService
        $programSelectorService = $this->createMock(ProgramSelectorService::class);
        $programSelectorService->method('getProgram')->willReturn($plan);

        // Mock EntityManager & Repositories
        $userRepo = $this->createMock(EntityRepository::class);
        $userRepo->method('find')->willReturn($user);

        $metricsRepo = $this->createMock(EntityRepository::class);
        $metricsRepo->method('findOneBy')->willReturn($userMetrics);

        $entityManager = $this->createMock(EntityManagerInterface::class);
        $entityManager->method('getRepository')->willReturnMap([
            [User::class, $userRepo],
            [UserMetrics::class, $metricsRepo],
        ]);

        $controller = new ProgramController($entityManager, $programSelectorService);

        $response = $controller->createUserWithProgram(1, $entityManager);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(201, $response->getStatusCode());
    }

    public function testCreateUserWithProgramUserNotFound()
    {
        $userRepo = $this->createMock(EntityRepository::class);
        $userRepo->method('find')->willReturn(null); // simulate user not found

        $metricsRepo = $this->createMock(EntityRepository::class);
        // Not needed, but must be present to satisfy getRepository()

        $entityManager = $this->createMock(EntityManagerInterface::class);
        $entityManager->method('getRepository')->willReturnCallback(function ($class) use ($userRepo, $metricsRepo) {
            return match ($class) {
                User::class => $userRepo,
                UserMetrics::class => $metricsRepo,
                default => throw new \InvalidArgumentException("Unexpected repository class: " . $class),
            };
        });

        $programSelectorService = $this->createMock(ProgramSelectorService::class);

        $controller = new ProgramController($entityManager, $programSelectorService);

        $response = $controller->createUserWithProgram(1, $entityManager);

        $this->assertEquals(404, $response->getStatusCode());
        $this->assertJsonStringEqualsJsonString(
            json_encode(['error' => 'User not found']),
            $response->getContent()
        );
    }


    public function testCreateUserWithProgramMetricsNotFound()
    {
        $user = new User();

        $entityManager = $this->createMock(EntityManagerInterface::class);
        $userRepo = $this->createMock(EntityRepository::class);
        $userRepo->method('find')->willReturn($user);

        $metricsRepo = $this->createMock(EntityRepository::class);
        $metricsRepo->method('findOneBy')->willReturn(null);

        $entityManager->method('getRepository')->willReturnMap([
            [User::class, $userRepo],
            [UserMetrics::class, $metricsRepo],
        ]);


        $programSelectorService = $this->createMock(ProgramSelectorService::class);

        $controller = new ProgramController($entityManager, $programSelectorService);

        $response = $controller->createUserWithProgram(1, $entityManager);

        $this->assertEquals(404, $response->getStatusCode());
        $this->assertJsonStringEqualsJsonString(
            json_encode(['error' => 'Metrics user not found']),
            $response->getContent()
        );
    }
}

