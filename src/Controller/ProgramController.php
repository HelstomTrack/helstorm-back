<?php

namespace App\Controller;

use App\Entity\Programs;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/programs')]
class ProgramController extends AbstractController
{
    public function __construct(private EntityManagerInterface $entityManager)
    {
    }
    #[Route('', methods: ['POST'])]
    public function create(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $program = new Programs();
        $program->setThreadId($data['threadId'] ?? 'default-thread');
        $program->setRunId($data['runId'] ?? 'default-run');
        $program->setContent($data['content'] ?? []);
        $program->setCreatedAt(new \DateTimeImmutable());

        $em->persist($program);
        $em->flush();

        return $this->json($program, 201);
    }

    #[Route('', methods: ['GET'])]
    public function index(EntityManagerInterface $em): JsonResponse
    {
        $programs = $em->getRepository(Programs::class)->findAll();
        return $this->json($programs);
    }

    #[Route('/{id}', methods: ['GET'])]
    public function show(Programs $program): JsonResponse
    {
        return $this->json($program);
    }

    #[Route('/{id}', methods: ['PUT'])]
    public function update(Request $request, Programs $program, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (isset($data['threadId'])) {
            $program->setThreadId($data['threadId']);
        }
        if (isset($data['runId'])) {
            $program->setRunId($data['runId']);
        }
        if (isset($data['content'])) {
            $program->setContent($data['content']);
        }

        $em->flush();

        return $this->json($program);
    }

    #[Route('/{id}', methods: ['DELETE'])]
    public function delete(Programs $program, EntityManagerInterface $em): JsonResponse
    {
        $em->remove($program);
        $em->flush();

        return new JsonResponse(null, 204);
    }

    #[Route('/user/{id}', name: 'app_program_goals', methods: ['GET'])]
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

        return $this->json(['program' => $program->getContent()], 200);
    }
}
