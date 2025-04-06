<?php

namespace App\Controller;

use App\Entity\Day;
use App\Entity\Diet;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;

class DietController extends AbstractController
{
    public function __construct(
        public EntityManagerInterface $entityManager
    )
    {
    }

    #[Route('/diet', name: 'app_diet', methods: ['GET'])]
    public function getDietAll(SerializerInterface $serializer): JsonResponse
    {
        $diet = $this->entityManager->getRepository(Diet::class)->findAll();
        $dietSerialized = $serializer->serialize($diet, 'json', ['groups' => 'diet']);

        return new JsonResponse($dietSerialized, Response::HTTP_OK, ['accept' => 'json'], true);
    }

    #[Route('/day', name: 'app_day_id', methods: ['GET'])]
    public function getDay(SerializerInterface $serializer): JsonResponse
    {
        $day = $this->entityManager->getRepository(Day::class)->findAll();
        $serializedDay = $serializer->serialize($day, 'json', ['groups' => 'day']);
        if (!$day) {
            return new JsonResponse(['error' => 'Day not found'], Response::HTTP_NOT_FOUND);
        }
        return new JsonResponse($serializedDay, Response::HTTP_OK, ['accept' => 'json'], true);
    }

    #[Route('/dayy/{userId}', methods: ['GET'])]
    public function getDietsByUser(
        int $userId,
        UserRepository $userRepository
    ): JsonResponse {
        $user = $userRepository->find($userId);

        if (!$user) {
            throw $this->createNotFoundException('Utilisateur non trouvé.');
        }

        $diets = $user->getDiets();

        $response = [];

        foreach ($diets as $diet) {
            $daysStructure = [];

            foreach ($diet->getDays() as $day) {
                $daysStructure[$day->getName()] = [];

                foreach ($day->getMeals() as $meal) {
                    $daysStructure[$day->getName()][] = [
                        'name' => $meal->getName(),
                        'total_calories' => $meal->getTotalCalories(),
                        'food' => array_map(
                            fn($food) => ['name' => $food->getName()],
                            $meal->getFood()->toArray()
                        ),
                    ];
                }
            }
            $response[] = [
                'name' => $diet->getName(),
                'days' => $daysStructure,
            ];
        }

        return $this->json($response);
    }
}