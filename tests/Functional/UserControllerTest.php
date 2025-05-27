<?php

namespace App\Tests\Functional;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class UserControllerTest extends WebTestCase
{
    private function createAuthenticatedClient(): array
    {
        $client = static::createClient();
        $container = static::getContainer();

        $user = (new User())
            ->setEmail('authtest@example.com')
            ->setFirstname('Auth')
            ->setLastname('Test')
            ->setPhone('0600000002');

        $passwordHasher = $container->get('security.user_password_hasher');
        $user->setPassword($passwordHasher->hashPassword($user, 'password'));

        $entityManager = $container->get('doctrine')->getManager();
        $entityManager->persist($user);
        $entityManager->flush();

        $jwtManager = $container->get('lexik_jwt_authentication.jwt_manager');
        $token = $jwtManager->create($user);

        return [$client, $token, $user];
    }

    public function testRegisterSuccess(): void
    {
        $client = static::createClient();

        $payload = [
            'email' => 'testt@example.com',
            'password' => 'password123',
            'firstname' => 'John',
            'lastname' => 'Doe',
            'phone' => '0600000001',
            'age' => 30,
            'weight' => 70,
            'height' => 180,
            'goal' => 'gain muscle',
            'level' => 'intermediate',
            'gender' => 'male'
        ];

        $client->request(
            'POST',
            '/register',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($payload)
        );

        $this->assertResponseStatusCodeSame(Response::HTTP_CREATED);
        $this->assertSame(
            ['message' => 'Registration successful'],
            json_decode($client->getResponse()->getContent(), true)
        );
    }

    public function testRegisterWithMissingField(): void
    {
        $client = static::createClient();

        $client->request(
            'POST',
            '/register',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'password' => 'password123',
                'firstname' => 'John',
                'lastname' => 'Doe',
                'phone' => '0600000000',
                'age' => 30,
                'weight' => 70,
                'height' => 180,
                'goal' => 'gain muscle',
                'level' => 'intermediate',
                'gender' => 'male'
            ])
        );

        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
        $this->assertSame(
            ['error' => 'Missing field: email'],
            json_decode($client->getResponse()->getContent(), true)
        );
    }

    public function testGetAllUsers(): void
    {
        [$client, $token] = $this->createAuthenticatedClient();

        $client->request(
            'GET',
            '/api/user',
            [],
            [],
            ['HTTP_AUTHORIZATION' => "Bearer $token"]
        );

        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'application/json');
    }

    public function testGetUserByIdNotFound(): void
    {
        [$client, $token] = $this->createAuthenticatedClient();

        $client->request(
            'GET',
            '/api/user/999999',
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

    public function testDeleteUser(): void
    {
        [$client, $token] = $this->createAuthenticatedClient();
        $container = static::getContainer();

        $user = (new User())
            ->setEmail('todelete@example.com')
            ->setFirstname('ToDelete')
            ->setLastname('User')
            ->setPhone('0612345678');

        $passwordHasher = $container->get('security.user_password_hasher');
        $user->setPassword($passwordHasher->hashPassword($user, 'password'));

        $entityManager = $container->get('doctrine')->getManager();
        $entityManager->persist($user);
        $entityManager->flush();

        $client->request(
            'DELETE',
            "/api/user/{$user->getId()}",
            [],
            [],
            ['HTTP_AUTHORIZATION' => "Bearer $token"]
        );

        $this->assertResponseIsSuccessful();
        $this->assertEquals('user deleted', $client->getResponse()->getContent());
    }
}
