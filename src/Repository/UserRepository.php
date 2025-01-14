<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<User>
 */
class UserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function findByEmailOrPhoneNumber(string $email, string $phoneNumber): ?User
    {
        return $this->createQueryBuilder('u')
            ->where('u.email = :email OR u.phoneNumber = :phoneNumber')
            ->setParameter('email', $email)
            ->setParameter('phoneNumber', $phoneNumber)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
