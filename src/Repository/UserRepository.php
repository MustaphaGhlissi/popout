<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

class UserRepository extends ServiceEntityRepository
{
    private $queryBuilder;
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, User::class);
        $this->queryBuilder = $this->_em->createQueryBuilder();
    }

    public function getAllByRole($role)
    {
        $query = $this->queryBuilder->select('u')
            ->from($this->_entityName, 'u')
            ->where('u.roles like :role')
            ->setParameter('role', '%'.$role.'%')
            ->getQuery();
        return $query->getResult();
    }

    public function getOneByIdAndRole($id, $role)
    {
        $query = $this->queryBuilder->select('u')
            ->from($this->_entityName, 'u')
            ->where('u.id = :id')
            ->andWhere('u.roles like :role')
            ->setParameter('id', $id)
            ->setParameter('role', '%'.$role.'%')
            ->getQuery();
        return $query->getOneOrNullResult();
    }


    public function getActiveFriends(User $user)
    {
        $query = $this->queryBuilder
            ->select('f')
            ->from($this->_entityName, 'f')
            ->join('f.friends','friends')
            ->join('friends.classicUser','cu')
            ->where('cu = :user')
            ->andWhere('cu.status = true')
            ->setParameter('user', $user)
            ->getQuery();
        return $query->getResult();
    }
}
