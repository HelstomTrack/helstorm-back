<?php

namespace App\Controller;

use App\Entity\Programs;
use App\Entity\User;
use App\Entity\UserMetrics;
use App\Entity\UserPrograms;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;

class ProgramController extends AbstractController
{
    public function __construct(
        public EntityManagerInterface $entityManager
    )
    {
    }

    #[Route('/program/{id}', name: 'app_program_id', methods: ['GET'])]
    public function getProgramId(int $id): JsonResponse
    {
        $program = $this->entityManager->getRepository(Programs::class)->find($id);

        if (!$program) {
            throw $this->createNotFoundException('Program not found');
        }

        $exercises = array_map(fn($programExercise) => [
            'id' => $programExercise->getExercise()->getId(),
            'name' => $programExercise->getExercise()->getName(),
            'description' => $programExercise->getExercise()->getDescription(),
            'rest_time' => $programExercise->getExercise()->getRestTime(),
            'difficulty' => $programExercise->getExercise()->getDifficulty()
        ], $program->getProgramsExercises()->toArray());

        return new JsonResponse([
            'program' => [
                'id' => $program->getId(),
                'name' => $program->getName(),
                'exercises' => $exercises,
            ],
        ]);
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
                    'description' => $exercise->getDescription()
                ];
            })->toArray();

            return [
                'id' => $program->getId(),
                'name' => $program->getName(),
                'exercises' => $exercises,
            ];
        }, $programs);

        return new JsonResponse($data);
    }


    #[Route('/program/assign/{id}', name: 'app_program_goal', methods: ['POST'])]
    public function createUserWithProgram(
        int $id,
        EntityManagerInterface $entityManager,
    ): JsonResponse {

        $user = $entityManager->getRepository(User::class)->find($id);

        if (!$user) {
            return new JsonResponse(['error' => 'Utilisateur non trouvé'], 404);
        }

        $userMetrics = $entityManager->getRepository(UserMetrics::class)->findOneBy(['user' => $user]);

        if (!$userMetrics) {
            return new JsonResponse(['error' => 'Metrics utilisateur non trouvées'], 404);
        }

        $userProgram = new UserPrograms();
        switch ($userMetrics->getGoal()) {
            case 'strong':
                $program = $entityManager->getRepository(Programs::class)->findOneBy(['name' => 'PPL']);
                break;
            case 'seche':
                $program = $entityManager->getRepository(Programs::class)->findOneBy(['name' => 'Hybride']);
                break;
            default:
                return new JsonResponse(['error' => 'Objectif non pris en charge'], 400);
        }

        if (!$program) {
            return new JsonResponse(['error' => 'Programme non trouvé'], 404);
        }

        $userProgram->setPrograms($program);
        $userProgram->setUser($user);

        $entityManager->persist($userProgram);
        $entityManager->flush();
        return new JsonResponse(['message' => 'Programme assigné avec succès'], 201);
    }

}
