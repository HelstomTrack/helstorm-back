<?php

namespace App\Tests\Unit\Manager;

use App\Entity\User;
use App\Manager\UserManager;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserManagerTest extends TestCase
{
    private $em;
    private $passwordHasher;
    private $userRepository;
    private UserManager $userManager;

    protected function setUp(): void
    {
        $this->em = $this->createMock(EntityManagerInterface::class);
        $this->passwordHasher = $this->createMock(UserPasswordHasherInterface::class);
        $this->userRepository = $this->createMock(UserRepository::class);

        $this->userManager = new UserManager(
            $this->em,
            $this->passwordHasher,
            $this->userRepository
        );
    }

    public function testRegisterWithValidData(): void
    {
        $data = [
            'email' => 'test@example.com',
            'password' => 'securepass',
            'firstname' => 'John',
            'lastname' => 'Doe',
            'phone' => '1234567890',
            'age' => 30,
            'weight' => 70,
            'height' => 180,
            'goal' => 'fitness',
            'level' => 'beginner',
            'gender' => 'male',
        ];

        // Simule qu'aucun utilisateur n'existe
        $this->userRepository
            ->expects($this->once())
            ->method('findByEmailOrPhoneNumber')
            ->with($data['email'], $data['phone'])
            ->willReturn(null);

        // Simule le hash du mot de passe
        $this->passwordHasher
            ->expects($this->once())
            ->method('hashPassword')
            ->willReturn('hashed_pass');

        // Vérifie que l'EntityManager appelle persist() et flush()
        $this->em->expects($this->once())->method('persist');
        $this->em->expects($this->once())->method('flush');

        $user = $this->userManager->register($data);

        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals('test@example.com', $user->getEmail());
        $this->assertEquals('hashed_pass', $user->getPassword());
    }

    public function testRegisterWithMissingField(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Missing field: email');

        $data = [
            // email is missing
            'password' => 'securepass',
            'firstname' => 'John',
            'lastname' => 'Doe',
            'phone' => '1234567890',
            'age' => 30,
            'weight' => 70,
            'height' => 180,
            'goal' => 'fitness',
            'level' => 'beginner',
            'gender' => 'male',
        ];

        $this->userManager->register($data);
    }

    public function testRegisterWhenUserAlreadyExists(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('User already exists.');

        $data = [
            'email' => 'existing@example.com',
            'password' => 'securepass',
            'firstname' => 'Jane',
            'lastname' => 'Doe',
            'phone' => '1234567890',
            'age' => 25,
            'weight' => 60,
            'height' => 170,
            'goal' => 'lose_weight',
            'level' => 'intermediate',
            'gender' => 'female',
        ];

        $this->userRepository
            ->method('findByEmailOrPhoneNumber')
            ->willReturn(new User());

        $this->userManager->register($data);
    }
}
