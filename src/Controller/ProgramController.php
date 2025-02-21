<?php

namespace App\Controller;

use App\Entity\PlanProgramDay;
use App\Entity\Programs;
use App\Entity\User;
use App\Entity\UserMetrics;
use App\Service\ProgramSelectorService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ProgramController extends AbstractController
{
    public function __construct(
        public EntityManagerInterface $entityManager,
        public ProgramSelectorService $programSelectorService
    )
    {
    }

    #[Route('/program', name: 'app_program', methods: ['GET'])]
    public function getAllProgram(): JsonResponse
    {
        $programs = $this->entityManager->getRepository(Programs::class)->findAll();

        if (!$programs) {
            throw $this->createNotFoundException('No programs found');
        }

        $data = array_map(function ($program) {
            $exercises = $program->getProgramsExercises()->map(function ($programExercise) {
                $exercise = $programExercise->getExercise();
                return [
                    'id' => $exercise->getId(),
                    'name' => $exercise->getName(),
                    'description' => $exercise->getDescription(),
                    'rest_time' => $exercise->getRestTime(),
                    'difficulty' => $exercise->getDifficulty(),
                    'category' => $exercise->getCategory(),
                    'series' => $exercise->getSeries(),
                    'calories' => $exercise->getCalories()
                ];
            })->toArray();

            return [
                'id' => $program->getId(),
                'name' => $program->getName(),
                'exercises' => $exercises
            ];
        }, $programs);

        return new JsonResponse($data);
    }

    #[Route('/api/program/assign/{id}', name: 'app_program_goal', methods: ['POST'])]
    public function createUserWithProgram(int $id, EntityManagerInterface $entityManager): JsonResponse
    {
        $user = $entityManager->getRepository(User::class)->find($id);
        $userMetrics = $entityManager->getRepository(UserMetrics::class)->findOneBy(['user' => $user]);

        if (!$user) {
            return new JsonResponse(['error' => 'Utilisateur non trouvé'], Response::HTTP_NOT_FOUND);
        } elseif (!$userMetrics) {
            return new JsonResponse(['error' => 'Metrics utilisateur non trouvées'], Response::HTTP_NOT_FOUND);
        }

        $plan = $this->programSelectorService->getProgram(
            $userMetrics->getGoal(),
            $userMetrics->getWeight(),
            $userMetrics->getHeight()
        );
        if (!$plan) {
            return new JsonResponse(['error' => 'Aucun plan trouvé pour cet utilisateur'], Response::HTTP_NOT_FOUND);
        }

        $user->addPlan($plan);
        $entityManager->flush();

        return new JsonResponse(['message' => 'Programme assigné avec succès'], Response::HTTP_CREATED);
    }

    #[Route('/program/user/{id}', name: 'app_program_goals', methods: ['GET'])]
    public function getUserPlan(int $id) : JsonResponse
    {
        $user = $this->entityManager->getRepository(User::class)->find($id);
        if (!$user) {
            return new JsonResponse(['error' => 'Utilisateur non trouvé'], Response::HTTP_NOT_FOUND);
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
}
