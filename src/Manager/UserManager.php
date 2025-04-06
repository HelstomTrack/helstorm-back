<?php

namespace App\Manager;

use App\Entity\User;
use App\Entity\UserMetrics;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserManager
{
    public function __construct(
        private EntityManagerInterface $em,
        private UserPasswordHasherInterface $passwordHasher,
        public UserRepository $userRepository,
    ) {}

    public function register(array $data): User
    {
        $requiredFields = ['email', 'password', 'firstname', 'lastname', 'phone', 'age', 'weight', 'height', 'goal', 'level', 'gender'];

        foreach ($requiredFields as $field) {
            if (empty($data[$field])) {
                throw new \InvalidArgumentException("Missing field: $field");
            }
        }

        if ($this->userRepository->findByEmailOrPhoneNumber($data['email'], $data['phone'])) {
            throw new \InvalidArgumentException("User already exists.");
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

        $hashedPassword = $this->passwordHasher->hashPassword($user, $data['password']);
        $user->setPassword($hashedPassword);

        $this->em->persist($user);
        $this->em->flush();

        return $user;
    }

}