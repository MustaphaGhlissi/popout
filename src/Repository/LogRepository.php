<?php
/**
 * Created by PhpStorm.
 * User: Hp
 * Date: 01/03/2018
 * Time: 15:11
 */

namespace App\Repository;


use App\Entity\Log;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

class LogRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Log::class);
    }
}