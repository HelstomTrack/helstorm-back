<?php

namespace App\Controller;

use App\Entity\User;
use App\Manager\UserManager;
use App\Repository\UserRepository;
use App\Service\ProgramGenerator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;


class UserController extends AbstractController
{

    public function __construct
    (
        public EntityManagerInterface $entityManager,
        public ProgramController $programController,
        public ProgramGenerator $programGenerator
    )
    {
    }


    #[Route('/register', name: 'app_user_post', methods: ['POST'])]
    public function register(Request $request, UserManager $userManager): Response
    {
        $data = json_decode($request->getContent(), true) ?? [];

        try {
            $user = $userManager->register($data);
        } catch (\InvalidArgumentException $e) {
            return $this->json(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }

        try {
            $program = $this->programGenerator->generateAndSave($user);
        } catch (\Throwable $e) {
            return $this->json([
                'message' => 'Registration successful, but program generation failed',
                'error'   => $e->getMessage(),
            ], Response::HTTP_CREATED);
        }

        return $this->json([
            'message'    => 'Registration successful',
            'user_id'    => $user->getId(),
            'program_id' => $program->getId(),
            'program'    => $program->getContent(), // JSON du bot
            'thread_id'  => $program->getThreadId(),
            'run_id'     => $program->getRunId(),
        ], Response::HTTP_CREATED);
    }
    /***
     * @param SerializerInterface $serializer
     * @return JsonResponse
     */
    #[OA\Response(
        response: 200,
        description: 'Successful response',
        content: new Model(type: User::class, groups: ['non_sensitive_data'])
    )]
    #[OA\Tag(name: 'User')]
    #[Route('/api/user', name: 'app_user', methods: ['GET'])]
    public function getAllUser(SerializerInterface $serializer): JsonResponse
    {
        $users = $this->entityManager->getRepository(User::class)->findAll();
        if(!$users) {
            return new JsonResponse(['error' => 'User not found'], Response::HTTP_NOT_FOUND);
        }
        $jsonData = $serializer->serialize($users, 'json', ['groups' => 'user', 'json_encode_options' => JSON_PRETTY_PRINT,
        ]);
        return new JsonResponse($jsonData, Response::HTTP_OK, ['accept' => 'json'], true);
    }

    /***
     * @param int $id
     * @param SerializerInterface $serializer
     * @return JsonResponse
     */
    #[OA\Response(
        response: 200,
        description: 'Successful response',
        content: new Model(type: User::class, groups: ['non_sensitive_data'])
    )]
    #[OA\Tag(name: 'User')]
    #[Route('/api/user/{id}', name: 'app_user_id', methods: ['GET'])]
    public function getUserById(int $id, SerializerInterface $serializer) : JsonResponse
    {
        $user = $this->entityManager->getRepository(User::class)->findOneBy(['id' => $id]);
        if(!$user) {
            return new JsonResponse(['error' => 'User not found'], Response::HTTP_NOT_FOUND);
        }
        $serializeUser = $serializer->serialize($user, 'json', ['groups' => 'user', 'json_encode_options' => JSON_PRETTY_PRINT]);

        return new JsonResponse($serializeUser, Response::HTTP_OK, ['accept' => 'json'], true);
    }

    /***
     * @param int $id
     * @return JsonResponse
     */
    #[OA\Response(
        response: 200,
        description: 'Successful response',
        content: new Model(type: User::class, groups: ['non_sensitive_data'])
    )]
    #[OA\Tag(name: 'User')]
    #[Route('/api/user/{id}', name: 'delete_user', methods: ['DELETE'])]
    public function deleteUser(int $id) : JsonResponse
    {
        $user = $this->entityManager->getRepository(User::class)->find($id);
        if($user != null) {
            $this->entityManager->remove($user);
            $this->entityManager->flush();
        }
        return new JsonResponse('user deleted', Response::HTTP_OK, ['accept' => 'json'], true);
    }

    public function refresh(): JsonResponse
    {
        return new JsonResponse(['msg' => 'JWT refresh token not found'], Response::HTTP_UNAUTHORIZED);
    }
}
