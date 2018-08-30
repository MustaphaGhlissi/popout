<?php

namespace App\Repository;

use App\Entity\Offer;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Offer|null find($id, $lockMode = null, $lockVersion = null)
 * @method Offer|null findOneBy(array $criteria, array $orderBy = null)
 * @method Offer[]    findAll()
 * @method Offer[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OfferRepository extends ServiceEntityRepository
{
    private $queryBuilder;
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Offer::class);
        $this->queryBuilder = $this->_em->createQueryBuilder();
    }

    public function getAll()
    {
        $query = $this->queryBuilder
            ->select('o,e,pu')
            ->from($this->_entityName, 'o')
            ->join('o.event','e')
            ->join('e.proUser','pu')
            ->getQuery();
        return $query->getResult();
    }

    public function getOne($id)
    {
        $query = $this->queryBuilder
            ->select('o,e,pu')
            ->from($this->_entityName, 'o')
            ->join('o.event','e')
            ->join('e.proUser','pu')
            ->where('o.id = :id')
            ->setParameter('id', $id)
            ->getQuery();
        return $query->getOneOrNullResult();
    }
}
