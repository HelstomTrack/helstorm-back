<?php

namespace App\Tests\Manager;

use App\Entity\User;
use App\Entity\UserMetrics;
use App\Manager\UserManager;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserManagerTest extends TestCase
{
    private function validPayload(): array
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

    public function testRegisterSuccess(): void
    {
        $em = $this->createMock(EntityManagerInterface::class);
        $hasher = $this->createMock(UserPasswordHasherInterface::class);
        $repo = $this->createMock(UserRepository::class);

        $repo->method('findByEmailOrPhoneNumber')->willReturn(null);
        $hasher->method('hashPassword')->willReturn('HASHED');

        $em->expects(self::once())->method('persist')->with(self::callback(fn($u) => $u instanceof User && $u->getUserMetrics() instanceof UserMetrics));
        $em->expects(self::once())->method('flush');

        $manager = new UserManager($em, $hasher, $repo);
        $user = $manager->register($this->validPayload());

        self::assertSame('john@example.com', $user->getEmail());
        self::assertSame('HASHED', $user->getPassword());
        self::assertSame(30, $user->getUserMetrics()->getAge());
    }

    public function testRegisterMissingFieldThrows(): void
    {
        $em = $this->createMock(EntityManagerInterface::class);
        $hasher = $this->createMock(UserPasswordHasherInterface::class);
        $repo = $this->createMock(UserRepository::class);

        $manager = new UserManager($em, $hasher, $repo);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Missing field: lastname');

        $payload = $this->validPayload();
        unset($payload['lastname']);
        $manager->register($payload);
    }

    public function testRegisterExistingUserThrows(): void
    {
        $em = $this->createMock(EntityManagerInterface::class);
        $hasher = $this->createMock(UserPasswordHasherInterface::class);
        $repo = $this->createMock(UserRepository::class);

        $repo->method('findByEmailOrPhoneNumber')->willReturn(new User());

        $manager = new UserManager($em, $hasher, $repo);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('User already exists.');

        $manager->register($this->validPayload());
    }
}
