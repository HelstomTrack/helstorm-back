<?php

namespace App\Controller;

use App\Entity\Diet;
use App\Entity\Programs;
use App\Entity\User;
use App\Entity\UserMetrics;
use App\Service\ProgramSelectorService;
use Doctrine\ORM\EntityManagerInterface;
use Nelmio\ApiDocBundle\Attribute\Model;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use OpenApi\Attributes as OA;

class ProgramController extends AbstractController
{
    public function __construct(
        public EntityManagerInterface $entityManager,
        public ProgramSelectorService $programSelectorService
    )
    {
    }

    /**
     * @param int $id
     * @param EntityManagerInterface $entityManager
     * @return JsonResponse
     */
    #[OA\Response(
        response: 201,
        description: 'Successful response',
        content: new Model(type: Programs::class, groups: ['program'])
    )]
    #[OA\Tag(name: 'Program')]
    #[Route('/api/program/assign/{id}', name: 'app_program_goal', methods: ['POST'])]
    public function createUserWithProgram(int $id, EntityManagerInterface $entityManager): JsonResponse
    {
        $user = $entityManager->getRepository(User::class)->find($id);
        $userMetrics = $entityManager->getRepository(UserMetrics::class)->findOneBy(['user' => $user]);

        if (!$user) {
            return new JsonResponse(['error' => 'User not found'], Response::HTTP_NOT_FOUND);
        } elseif (!$userMetrics) {
            return new JsonResponse(['error' => 'Metrics user not found'], Response::HTTP_NOT_FOUND);
        }

        $plan = $this->programSelectorService->getProgram(
            $userMetrics->getGoal(),
            $userMetrics->getWeight(),
            $userMetrics->getHeight()
        );

        if (!$plan) {
            return new JsonResponse(['error' => 'None plan found for this user'], Response::HTTP_NOT_FOUND);
        }

        $diet = new Diet();
        if ($userMetrics->getGoal() == 'Bulk') {

            $user->addDiet($diet->setName('Bulk'));
        } elseif ($userMetrics->getGoal() == 'Cut') {
            $user->addDiet($diet->setName('Cut'));
        } else {
            return new JsonResponse(['error' => 'Invalid plan name'], Response::HTTP_BAD_REQUEST);
        }
        $user->addPlan($plan);
        $entityManager->persist($diet);
       ;
        $entityManager->flush();

        return new JsonResponse(['message' => 'Plan assigned with success'], Response::HTTP_CREATED);
    }

    #[OA\Response(
        response: 200,
        description: 'Successful response',
        content: new Model(type: Programs::class, groups: ['program'])
    )]
    #[OA\Tag(name: 'Program')]
    #[Route('/api/program/user/{id}', name: 'app_program_goalss', methods: ['GET'])]
    public function getUserPlan(int $id) : JsonResponse
    {
        $user = $this->entityManager->getRepository(User::class)->find($id);
        if (!$user) {
            return new JsonResponse(['error' => 'User not found'], Response::HTTP_NOT_FOUND);
        }

        $data = array_map(fn($plan) => [
            'id' => $plan->getId(),
            'name' => $plan->getName(),

            'programs' => $plan->getProgram()->map(
                fn($program) => [
                    'id' => $program->getId(),
                    'name' => $program->getName(),
                    'exercises' => $program->getProgramsExercises()->map(
                        fn($programExercise) => [
                            'id' => $programExercise->getExercise()->getId(),
                            'name' => $programExercise->getExercise()->getName(),
                            'description' => $programExercise->getExercise()->getDescription(),
                            'rest_time' => $programExercise->getExercise()->getRestTime(),
                            'difficulty' => $programExercise->getExercise()->getDifficulty()
                        ]
                    )->toArray(),
                    'day' => implode(', ', $program->getPlanProgramDays()->map(
                        fn($planProgramDay) => $planProgramDay->getDayOfWeek()
                    )->toArray()),
                ]
            )->toArray(),
        ], $user->getPlans()->toArray());

        return new JsonResponse($data);
    }

    #[Route('/program/user/{id}', name: 'app_program_goals', methods: ['GET'])]
    public function getProgramUser(int $id): JsonResponse
    {
        $user = $this->entityManager->getRepository(User::class)->find($id);
        if (!$user) {
            return $this->json(['error' => 'User not found'], 404);
        }

        $program = $this->entityManager->getRepository(Programs::class)->findOneBy(['user' => $user]);
        if (!$program) {
            return $this->json(['error' => 'Program not found'], 404);
        }

        return $this->json([$program->getContent()], 200);
    }
}
