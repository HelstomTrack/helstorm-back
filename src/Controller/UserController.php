<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\UserMetrics;
use App\Repository\UserRepository;
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
    )
    {
    }

    /***
     * @param UserPasswordHasherInterface $passwordHasher
     * @param Request $request
     * @param UserRepository $userRepository
     * @return Response
     */
    #[OA\Response(
        response: 201,
        description: 'Successful response',
        content: new Model(type: User::class, groups: ['non_sensitive_data'])
    )]
    #[OA\Tag(name: 'User')]
    #[Route('/register', name: 'app_user_post', methods: ['POST'])]
    public function registration(UserPasswordHasherInterface $passwordHasher, Request $request, UserRepository $userRepository): Response
    {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['email'], $data['password'], $data['firstname'], $data['lastname'], $data['phone'], $data['age'], $data['weight'], $data['height'], $data['goal'], $data['level'], $data['gender'])) {
            return new JsonResponse(['error' => 'Missing required fields'], Response::HTTP_BAD_REQUEST);
        }

        if ($userRepository->findByEmailOrPhoneNumber($data['email'], $data['phone'])) {
            return new JsonResponse(['error' => 'User already exists'], Response::HTTP_BAD_REQUEST);
        }

        $userMetrics = (new UserMetrics())
            ->setAge($data['age'])
            ->setWeight($data['weight'])
            ->setHeight($data['height'])
            ->setGoal($data['goal'])
            ->setLevel($data['level'])
            ->setGender($data['gender']);

        $user = (new User())
            ->setEmail($data['email'])
            ->setFirstname($data['firstname'])
            ->setLastname($data['lastname'])
            ->setPhone($data['phone'])
            ->setUserMetrics($userMetrics);

        // hash the password (based on the security.yaml config for the $user class)
        $user->setPassword(
            $passwordHasher->hashPassword($user, $data['password'])
        );
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        if ($this->programController->createUserWithProgram($user->getId(), $this->entityManager)) {
            return new JsonResponse(['message' => 'Registration successful'], Response::HTTP_CREATED);
        } else {
            return new JsonResponse(['error' => 'No plan found for this user'], Response::HTTP_NOT_FOUND);
        }
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
    #[Route('/user', name: 'app_user', methods: ['GET'])]
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
    #[Route('/user/{id}', name: 'app_user_id', methods: ['GET'])]
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
