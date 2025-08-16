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

        // Remplace UserManager pour renvoyer une InvalidArgumentException
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

        // Fake UserManager: renvoie un User avec un id
        $fakeUser = (new User())->setEmail('john@example.com')->setFirstname('John')->setLastname('Doe');
        // simulate id via reflection (si entité a un setId privé, sinon adapte)
        $ref = new \ReflectionClass($fakeUser);
        if ($ref->hasProperty('id')) {
            $prop = $ref->getProperty('id');
            $prop->setAccessible(true);
            $prop->setValue($fakeUser, 1);
        }

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

        // Fake user & program
        $fakeUser = (new User())->setEmail('john@example.com')->setFirstname('John')->setLastname('Doe');
        $userRef = new \ReflectionClass($fakeUser);
        if ($userRef->hasProperty('id')) {
            $prop = $userRef->getProperty('id');
            $prop->setAccessible(true);
            $prop->setValue($fakeUser, 42);
        }

        $fakeProgram = (new Programs())
            ->setUser($fakeUser)
            ->setCreatedAt(new \DateTimeImmutable())
            ->setContent(['plan' => 'Do stuff'])
            ->setThreadId('thr_123')
            ->setRunId('run_456');

        $progRef = new \ReflectionClass($fakeProgram);
        if ($progRef->hasProperty('id')) {
            $prop = $progRef->getProperty('id');
            $prop->setAccessible(true);
            $prop->setValue($fakeProgram, 7);
        }

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
}
