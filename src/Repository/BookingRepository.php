<?php

namespace App\Repository;

use App\Entity\Booking;
use App\Entity\Event;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Booking|null find($id, $lockMode = null, $lockVersion = null)
 * @method Booking|null findOneBy(array $criteria, array $orderBy = null)
 * @method Booking[]    findAll()
 * @method Booking[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BookingRepository extends ServiceEntityRepository
{
    private $queryBuilder;
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Booking::class);
        $this->queryBuilder = $this->_em->createQueryBuilder();
    }

    public function getEventMembers(Event $event)
    {
        $query = $this->queryBuilder
            ->select('count(b.id)')
            ->from($this->_entityName,'b')
            ->join('b.event','e')
            ->join('b.classicUser','cu')
            ->where('e = :event')
            ->setParameter('event',$event)
            ->getQuery();
        return $query->getSingleScalarResult();
    }

    public function getEventMembersByProUser(Event $event, User $user)
    {
        $query = $this->queryBuilder
            ->select('count(b.id)')
            ->from($this->_entityName,'b')
            ->join('b.event','e')
            ->join('b.classicUser','cu')
            ->join('e.proUser','pu')
            ->where('e = :event')
            ->andWhere('pu = :user')
            ->setParameter('event',$event)
            ->setParameter('user', $user)
            ->getQuery();
        return $query->getSingleScalarResult();
    }

    public function getByUserAndEvent(User $user, Event $event)
    {
        $query = $this->queryBuilder
            ->select('count(b.id)')
            ->from($this->_entityName,'b')
            ->join('b.event','e')
            ->join('b.classicUser','cu')
            ->where('e = :event')
            ->andWhere('cu = :user')
            ->setParameter('user',$user)
            ->setParameter('event',$event)
            ->getQuery();
        return $query->getSingleScalarResult();
    }

    /**
     * @param User $user
     * @param Event $event
     * @return mixed
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getOneBookingByUserAndEvent(User $user, Event $event)
    {
        $query = $this->queryBuilder
            ->select('b')
            ->from($this->_entityName,'b')
            ->join('b.event','e')
            ->join('b.classicUser','cu')
            ->where('e = :event')
            ->andWhere('cu = :user')
            ->setParameter('user',$user)
            ->setParameter('event',$event)
            ->getQuery();
        return $query->getOneOrNullResult();
    }

    public function getBookingSellsByEvent(Event $event)
    {
        $query = $this->queryBuilder
            ->select('b,p,cu,e,pu')
            ->from($this->_entityName,'b')
            ->join('b.payment','p')
            ->join('b.classicUser','cu')
            ->join('b.event','e')
            ->join('e.proUser','pu')
            ->where('e = :event')
            ->setParameter('event',$event)
            ->getQuery();
        return $query->getResult();
    }
}
