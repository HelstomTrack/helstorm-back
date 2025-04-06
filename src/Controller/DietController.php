<?php

namespace App\Controller;

use App\Entity\Diet;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class DietController extends AbstractController
{
    public function __construct(
        public EntityManagerInterface $entityManager,
        public SerializerInterface $serializer
    )
    {
    }

    /**
     * @param int $userId
     * @param UserRepository $userRepository
     * @return JsonResponse
     */
    #[Route('/api/diet-day/{userId}', methods: ['GET'])]
    public function getDietsByUser(int $userId, UserRepository $userRepository): JsonResponse
    {
        $user = $userRepository->find($userId);

        if (!$user) {
            throw $this->createNotFoundException('Utilisateur non trouvé.');
        }

        $response = array_map(fn($diet) => $this->formatDiet($diet), $user->getDiets()->toArray());

        return $this->json($response);
    }

    private function formatDiet(Diet $diet): array
    {
        $daysStructure = [];

        foreach ($diet->getDays() as $day) {
            $daysStructure[$day->getName()] = array_map(fn($meal) => $this->formatMeal($meal), $day->getMeals()->toArray());
        }

        return [
            'name' => $diet->getName(),
            'days' => $daysStructure,
        ];
    }

    private function formatMeal($meal): array
    {
        return [
            'name' => $meal->getName(),
            'total_calories' => $meal->getTotalCalories(),
            'food' => array_map(
                fn($food) => ['name' => $food->getName()],
                $meal->getFood()->toArray()
            ),
        ];
    }
}