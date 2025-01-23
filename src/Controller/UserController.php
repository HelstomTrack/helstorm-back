<?php

namespace App\Controller;

use App\Entity\User;
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
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/api/user/register', name: 'app_user_post', methods: ['POST'])]
    public function registration(UserPasswordHasherInterface $passwordHasher, Request $request, UserRepository $userRepository): Response
    {
        $data = json_decode($request->getContent(), true);

        $user = new User();
        $user->setName($data['name']);
        $user->setEmail($data['email']);
        $user->setGender($data['gender']);
        $user->setAge($data['age']);
        $user->setWeight($data['weight']);
        $user->setHeight($data['height']);
        $user->setPhoneNumber($data['phoneNumber']);

        $existingUser = $userRepository->findByEmailOrPhoneNumber($data['email'], $data['phoneNumber']);

        if($existingUser){
            return new JsonResponse(['User exist in database'], Response::HTTP_BAD_REQUEST);
        }
        // hash the password (based on the security.yaml config for the $user class)
        $hashedPassword = $passwordHasher->hashPassword(
            $user,
            $data['password']
        );
        $user->setPassword($hashedPassword);
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return new JsonResponse(['message' => 'Registration successful'], 201);

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
}
