<?php

namespace App\Tests\Functional;

use App\Entity\User;
use App\Entity\UserMetrics;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class ProgramControllerTest extends WebTestCase
{
    private function createAuthenticatedClientAndUser(): array
    {
        $client = static::createClient();
        $container = static::getContainer();

        $email = 'programtest_' . uniqid() . '@example.com';
        $phoneNumber = uniqid();
        $user = (new User())
            ->setEmail($email)
            ->setFirstname('Program')
            ->setLastname('Test')
            ->setPhone($phoneNumber);


        $passwordHasher = $container->get('security.user_password_hasher');
        $user->setPassword($passwordHasher->hashPassword($user, 'password'));

        $metrics = (new UserMetrics())
            ->setUser($user)
            ->setGoal('gain muscle')
            ->setWeight(70)
            ->setHeight(180)
            ->setAge(25)
            ->setGender('male')
            ->setLevel('beginner');

        $em = $container->get('doctrine')->getManager();
        $em->persist($user);
        $em->persist($metrics);
        $em->flush();

        $jwtManager = $container->get('lexik_jwt_authentication.jwt_manager');
        $token = $jwtManager->create($user);

        return [$client, $token, $user];
    }

    public function testAssignProgramSuccess(): void
    {
        [$client, $token, $user] = $this->createAuthenticatedClientAndUser();

        // Appel au controller d'assignation
        $client->request(
            'POST',
            "/api/program/assign/{$user->getId()}",
            [],
            [],
            ['HTTP_AUTHORIZATION' => "Bearer $token"]
        );

        $this->assertResponseStatusCodeSame(Response::HTTP_CREATED);
        $this->assertSame(
            ['message' => 'Plan assigned with success'],
            json_decode($client->getResponse()->getContent(), true)
        );
    }

    public function testAssignProgramUserNotFound(): void
    {
        [$client, $token] = $this->createAuthenticatedClientAndUser();

        $client->request(
            'POST',
            "/api/program/assign/9999999",
            [],
            [],
            ['HTTP_AUTHORIZATION' => "Bearer $token"]
        );

        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
        $this->assertSame(
            ['error' => 'User not found'],
            json_decode($client->getResponse()->getContent(), true)
        );
    }

    public function testGetUserPlanUserNotFound(): void
    {
        [$client, $token] = $this->createAuthenticatedClientAndUser();

        $client->request(
            'GET',
            "/api/program/user/999999",
            [],
            [],
            ['HTTP_AUTHORIZATION' => "Bearer $token"]
        );

        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
        $this->assertSame(
            ['error' => 'User not found'],
            json_decode($client->getResponse()->getContent(), true)
        );
    }

    public function testGetUserPlanSuccess(): void
    {
        [$client, $token, $user] = $this->createAuthenticatedClientAndUser();

        // Appel pour assigner un programme d'abord
        $client->request(
            'POST',
            "/api/program/assign/{$user->getId()}",
            [],
            [],
            ['HTTP_AUTHORIZATION' => "Bearer $token"]
        );

        $this->assertResponseStatusCodeSame(Response::HTTP_CREATED);

        // Appel pour récupérer le programme de l'utilisateur
        $client->request(
            'GET',
            "/api/program/user/{$user->getId()}",
            [],
            [],
            ['HTTP_AUTHORIZATION' => "Bearer $token"]
        );

        $this->assertResponseIsSuccessful();
        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertIsArray($data);
        $this->assertNotEmpty($data);
    }
}
