<?php

namespace App\Repository;

use App\Entity\ScannedQRCode;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method ScannedQRCode|null find($id, $lockMode = null, $lockVersion = null)
 * @method ScannedQRCode|null findOneBy(array $criteria, array $orderBy = null)
 * @method ScannedQRCode[]    findAll()
 * @method ScannedQRCode[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ScannedQRCodeRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, ScannedQRCode::class);
    }

    /*
    public function findBySomething($value)
    {
        return $this->createQueryBuilder('s')
            ->where('s.something = :value')->setParameter('value', $value)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */
}
