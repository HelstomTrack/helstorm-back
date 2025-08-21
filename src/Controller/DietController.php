<?php

namespace App\Controller;

use App\Entity\Diet;
use App\Repository\DietRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/diets')]
class DietController extends AbstractController
{
    #[Route('', name: 'diet_index', methods: ['GET'])]
    public function index(DietRepository $dietRepository, SerializerInterface $serializer): JsonResponse
    {
        $diets = $dietRepository->findAll();
        $data = $serializer->serialize($diets, 'json', ['groups' => 'diet']);

        return new JsonResponse($data, 200, [], true);
    }

    #[Route('/{id}', name: 'diet_show', methods: ['GET'])]
    public function show(Diet $diet, SerializerInterface $serializer): JsonResponse
    {
        $data = $serializer->serialize($diet, 'json', ['groups' => 'diet']);
        return new JsonResponse($data, 200, [], true);
    }

    #[Route('', name: 'diet_create', methods: ['POST'])]
    public function create(Request $request, EntityManagerInterface $em, SerializerInterface $serializer): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $diet = new Diet();
        $diet->setName($data['name'] ?? 'No name');

        $em->persist($diet);
        $em->flush();

        $json = $serializer->serialize($diet, 'json', ['groups' => 'diet']);
        return new JsonResponse($json, 201, [], true);
    }

    #[Route('/{id}', name: 'diet_update', methods: ['PUT', 'PATCH'])]
    public function update(Request $request, Diet $diet, EntityManagerInterface $em, SerializerInterface $serializer): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (isset($data['name'])) {
            $diet->setName($data['name']);
        }

        $em->flush();

        $json = $serializer->serialize($diet, 'json', ['groups' => 'diet']);
        return new JsonResponse($json, 200, [], true);
    }

    #[Route('/{id}', name: 'diet_delete', methods: ['DELETE'])]
    public function delete(Diet $diet, EntityManagerInterface $em): JsonResponse
    {
        $em->remove($diet);
        $em->flush();

        return new JsonResponse(null, 204);
    }
}
