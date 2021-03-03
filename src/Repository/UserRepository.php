<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

class UserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function findByUserRole($role)
    {
        $queryBuilder = $this->createQueryBuilder('user')
            ->where('user.roles LIKE :role')
            ->setParameter('role', '%' . $role . '%');

        return $queryBuilder->getQuery()->execute();
    }
}
