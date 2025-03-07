<?php

namespace App\Controller;

use App\Entity\Diet;
use App\Entity\User;
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

    #[Route('/diet/user/{id}', name: 'app_diet_user', methods: ['GET'])]
    public function getUserDiet(SerializerInterface $serializer, int $id): JsonResponse
    {
        $user = $this->entityManager->getRepository(User::class)->find($id);
        if (!$user) {
            return new JsonResponse(['error' => 'User not found'], Response::HTTP_NOT_FOUND);
        }

        $diet = $user->getDiets();
        $dietSerialized = $serializer->serialize($diet, 'json', ['groups' => 'diet']);

        return new JsonResponse($dietSerialized, Response::HTTP_OK, ['accept' => 'json'], true);
    }

}