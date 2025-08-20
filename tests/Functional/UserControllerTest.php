<?php

namespace App\Tests\Functional;

use App\Entity\Programs;
use App\Entity\User;
use App\Service\ProgramGenerator;
use App\Manager\UserManager;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class UserControllerTest extends WebTestCase
{
    private function clearUsers(): void
    {
        $em = static::getContainer()->get('doctrine')->getManager();
        $em->createQuery('DELETE FROM App\Entity\User u')->execute();
    }

    private function payload(): array
    {
        return [
            'email' => 'john@example.com',
            'password' => 'secret',
            'firstname' => 'John',
            'lastname' => 'Doe',
            'phone' => '0600000000',
            'age' => 30,
            'weight' => 80,
            'height' => 180,
            'goal' => 'fat_loss',
            'level' => 'beginner',
            'gender' => 'male',
        ];
    }

    public function testRegisterValidationError(): void
    {
        $client = static::createClient();
        $this->clearUsers();

        $fakeUserManager = $this->createMock(UserManager::class);
        $fakeUserManager->method('register')->willThrowException(new \InvalidArgumentException('Missing field: email'));
        static::getContainer()->set(UserManager::class, $fakeUserManager);

        $client->request('POST', '/register', server: ['CONTENT_TYPE' => 'application/json'], content: json_encode([]));

        self::assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
        $json = json_decode($client->getResponse()->getContent(), true);
        self::assertSame('Missing field: email', $json['error'] ?? null);
    }

    public function testRegisterSuccessButProgramFails(): void
    {
        $client = static::createClient();
        $this->clearUsers();

        $fakeUser = (new User())->setEmail('john@example.com')->setFirstname('John')->setLastname('Doe');
        $ref = new \ReflectionClass($fakeUser);
        $prop = $ref->getProperty('id');
        $prop->setAccessible(true);
        $prop->setValue($fakeUser, 1);

        $fakeUserManager = $this->createMock(UserManager::class);
        $fakeUserManager->method('register')->willReturn($fakeUser);

        $fakeProgramGenerator = $this->createMock(ProgramGenerator::class);
        $fakeProgramGenerator->method('generateAndSave')->willThrowException(new \RuntimeException('OpenAI error: rate_limit'));

        static::getContainer()->set(UserManager::class, $fakeUserManager);
        static::getContainer()->set(ProgramGenerator::class, $fakeProgramGenerator);

        $client->request('POST', '/register', server: ['CONTENT_TYPE' => 'application/json'], content: json_encode($this->payload()));

        self::assertResponseStatusCodeSame(Response::HTTP_CREATED);
        $json = json_decode($client->getResponse()->getContent(), true);
        self::assertSame('Registration successful, but program generation failed', $json['message'] ?? null);
        self::assertSame('OpenAI error: rate_limit', $json['error'] ?? null);
    }

    public function testRegisterSuccess(): void
    {
        $client = static::createClient();
        $this->clearUsers();

        $fakeUser = (new User())->setEmail('john@example.com')->setFirstname('John')->setLastname('Doe');
        $userRef = new \ReflectionClass($fakeUser);
        $prop = $userRef->getProperty('id');
        $prop->setAccessible(true);
        $prop->setValue($fakeUser, 42);

        $fakeProgram = (new Programs())
            ->setUser($fakeUser)
            ->setCreatedAt(new \DateTimeImmutable())
            ->setContent(['plan' => 'Do stuff'])
            ->setThreadId('thr_123')
            ->setRunId('run_456');
        $progRef = new \ReflectionClass($fakeProgram);
        $prop = $progRef->getProperty('id');
        $prop->setAccessible(true);
        $prop->setValue($fakeProgram, 7);

        $fakeUserManager = $this->createMock(UserManager::class);
        $fakeUserManager->method('register')->willReturn($fakeUser);

        $fakeProgramGenerator = $this->createMock(ProgramGenerator::class);
        $fakeProgramGenerator->method('generateAndSave')->willReturn($fakeProgram);

        static::getContainer()->set(UserManager::class, $fakeUserManager);
        static::getContainer()->set(ProgramGenerator::class, $fakeProgramGenerator);

        $client->request('POST', '/register', server: ['CONTENT_TYPE' => 'application/json'], content: json_encode($this->payload()));

        self::assertResponseStatusCodeSame(Response::HTTP_CREATED);
        $json = json_decode($client->getResponse()->getContent(), true);

        self::assertSame('Registration successful', $json['message'] ?? null);
        self::assertSame(42, $json['user_id'] ?? null);
        self::assertSame(7, $json['program_id'] ?? null);
        self::assertSame(['plan' => 'Do stuff'], $json['program'] ?? null);
        self::assertSame('thr_123', $json['thread_id'] ?? null);
        self::assertSame('run_456', $json['run_id'] ?? null);
    }

    public function testGetAllUserNotFound(): void
    {
        $client = static::createClient();
        $this->clearUsers();

        $client->request('GET', '/user');

        self::assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
        $json = json_decode($client->getResponse()->getContent(), true);
        self::assertSame('User not found', $json['error'] ?? null);
    }

    public function testGetAllUserSuccess(): void
    {
        $client = static::createClient();
        $this->clearUsers();
        $em = static::getContainer()->get('doctrine')->getManager();

        $user = new User();
        $user->setEmail('jane@example.com')
            ->setFirstname('Jane')
            ->setLastname('Doe')
            ->setPhone('0610101010')
            ->setPassword('secret');
        $em->persist($user);
        $em->flush();

        $client->request('GET', '/user');

        self::assertResponseIsSuccessful();
        $json = json_decode($client->getResponse()->getContent(), true);

        $emails = array_column($json, 'email');
        self::assertContains('jane@example.com', $emails);
    }

    public function testGetUserByIdNotFound(): void
    {
        $client = static::createClient();
        $this->clearUsers();

        $client->request('GET', '/user/999999');

        self::assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
        $json = json_decode($client->getResponse()->getContent(), true);
        self::assertSame('User not found', $json['error'] ?? null);
    }

    public function testGetUserByIdSuccess(): void
    {
        $client = static::createClient();
        $this->clearUsers();
        $em = static::getContainer()->get('doctrine')->getManager();

        $user = new User();
        $user->setEmail('alice@example.com')
            ->setFirstname('Alice')
            ->setLastname('Smith')
            ->setPhone('0611111111')
            ->setPassword('secret');
        $em->persist($user);
        $em->flush();

        $client->request('GET', '/user/' . $user->getId());

        self::assertResponseIsSuccessful();
        $json = json_decode($client->getResponse()->getContent(), true);
        self::assertSame('alice@example.com', $json['email'] ?? null);
    }

    public function testDeleteUserNotFound(): void
    {
        $client = static::createClient();
        $this->clearUsers();

        $client->request('DELETE', '/user/999999');

        self::assertResponseIsSuccessful();
        self::assertSame('user deleted', $client->getResponse()->getContent());
    }

    public function testDeleteUserSuccess(): void
    {
        $client = static::createClient();
        $this->clearUsers();
        $em = static::getContainer()->get('doctrine')->getManager();

        $user = new User();
        $user->setEmail('delete@example.com')
            ->setFirstname('Del')
            ->setLastname('Ete')
            ->setPhone('0622222222')
            ->setPassword('secret');
        $em->persist($user);
        $em->flush();

        $id = $user->getId();

        $client->request('DELETE', '/user/' . $id);

        self::assertResponseIsSuccessful();
        self::assertSame('user deleted', $client->getResponse()->getContent());

        $deletedUser = $em->getRepository(User::class)->find($id);
        self::assertNull($deletedUser);
    }
}
