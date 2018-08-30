<?php

namespace App\Repository;

use App\Entity\Message;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Message|null find($id, $lockMode = null, $lockVersion = null)
 * @method Message|null findOneBy(array $criteria, array $orderBy = null)
 * @method Message[]    findAll()
 * @method Message[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MessageRepository extends ServiceEntityRepository
{
    private $queryBuilder;
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Message::class);
        $this->queryBuilder = $this->_em->createQueryBuilder();
    }

    /**
     * @param User $sender
     * @param $id
     * @return mixed
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findOneBySender(User $sender, $id)
    {
        $query =
            $this->queryBuilder
            ->select('m')
            ->from($this->_entityName,'m')
            ->join('m.sender','s')
            ->where('m.id = :id')
            ->andWhere('s = :sender')
            ->setParameter('id',$id)
            ->setParameter('sender',$sender)
            ->getQuery();
        return $query->getOneOrNullResult();
    }

    /**
     * @param User $receiver
     * @param $id
     * @return mixed
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findOneByReceiver(User $receiver, $id)
    {
        $query =
            $this->queryBuilder
                ->select('m')
                ->from($this->_entityName,'m')
                ->join('m.receiver','r')
                ->where('m.id = :id')
                ->andWhere('r = :receiver')
                ->setParameter('id',$id)
                ->setParameter('receiver',$receiver)
                ->getQuery();
        return $query->getOneOrNullResult();
    }
}
