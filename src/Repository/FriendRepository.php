<?php

namespace App\Repository;

use App\Entity\Friend;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Friend|null find($id, $lockMode = null, $lockVersion = null)
 * @method Friend|null findOneBy(array $criteria, array $orderBy = null)
 * @method Friend[]    findAll()
 * @method Friend[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FriendRepository extends ServiceEntityRepository
{
    private $queryBuilder;

    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Friend::class);
        $this->queryBuilder = $this->_em->createQueryBuilder();
    }

    public function getActiveFriends(User $user)
    {
        $query = $this->queryBuilder
            ->select('f, fw, cu')
            ->from($this->_entityName, 'f')
            ->join('f.friendWith','fw')
            ->join('f.classicUser','cu')
            ->where('cu = :user')
            ->andWhere('fw.status = true')
            ->setParameter('user', $user)
            ->getQuery();
        return $query->getResult();
    }
}
