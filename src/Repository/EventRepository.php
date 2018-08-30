<?php

namespace App\Repository;

use App\Entity\Event;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Event|null find($id, $lockMode = null, $lockVersion = null)
 * @method Event|null findOneBy(array $criteria, array $orderBy = null)
 * @method Event[]    findAll()
 * @method Event[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EventRepository extends ServiceEntityRepository
{
    private $queryBuilder;
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Event::class);
        $this->queryBuilder = $this->_em->createQueryBuilder();
    }

    public function getAllEvents()
    {
        $query = $this->queryBuilder->select('e,pu')
            ->from($this->_entityName, 'e')
            ->join('e.proUser', 'pu')
            ->getQuery();
        return $query->getResult();
    }

    public function searchEventByCriteria($data)
    {
        if(count($data)===0)
        {
            $this->getAllEvents();
        }
        else
        {
            $params['criteria'] = '%'.$data['criteria'].'%';
            if($data['startDate'] === "")
            {
                unset($data);
                $query = $this->queryBuilder->select('e,pu')
                    ->from($this->_entityName, 'e')
                    ->join('e.proUser','pu')
                    ->where('e.name like :criteria')
                    ->orWhere('e.description like :criteria')
                    ->orWhere('e.location like :criteria')
                    ->orWhere('e.keyWords like :criteria')
                    ->setParameters($params)
                    ->getQuery();
            }
            else
            {
                $params['startDate'] = new \DateTime($data['startDate']->format('Y-m-d'));
                if(empty($data['startTime']))
                {
                    unset($data);
                    $query = $this->queryBuilder->select('e,pu')
                        ->from($this->_entityName, 'e')
                        ->join('e.proUser','pu')
                        ->where('e.name like :criteria')
                        ->orWhere('e.description like :criteria')
                        ->orWhere('e.location like :criteria')
                        ->orWhere('e.keyWords like :criteria')
                        ->orWhere("e.startDate = :startDate")
                        ->setParameters($params)
                        ->getQuery();
                }
                else
                {
                    $params['startTime'] = new \DateTime($data['startTime']->format('H:i'));
                    unset($data);
                    $query = $this->queryBuilder->select('e,pu')
                        ->from($this->_entityName, 'e')
                        ->join('e.proUser','pu')
                        ->where('e.name like :criteria')
                        ->orWhere('e.description like :criteria')
                        ->orWhere('e.location like :criteria')
                        ->orWhere('e.keyWords like :criteria')
                        ->orWhere('e.startDate = :startDate')
                        ->orWhere('e.startTime = :startTime')
                        ->setParameters($params)
                        ->getQuery();
                }
            }
            return $query->getResult();
        }
    }

    public function getEventsByUser(User $user)
    {
        $query = $this->queryBuilder
            ->select('e.id,e.name,e.description, e.rewardPoint, e.promoter,e.location, e.latitude,e.longitude, e.keyWords,e.startDate, e.startTime, e.endDate, e.endTime , e.attachPath')
            ->from($this->_entityName,'e')
            ->where('e.proUser = :user')
            ->setParameter('user',$user)
            ->getQuery();
        return $query->getResult();
    }

    public function getByUserResponses(array $responses)
    {
        $query = $this->queryBuilder
            ->select('e, pu, b')
            ->from($this->_entityName,'e')
            ->join('e.proUser','pu')
            ->leftJoin('e.bookings','b')
            ->where('(e.startDate > :startDate) OR (e.startDate = :startDate AND e.startTime >= :startTime AND e.endTime <= :endTime)')
            ->andWhere($this->extractResponses($responses)[0])
            ->setParameters($this->extractResponses($responses)[1])->getQuery();
        return $query->getResult();
    }

    public function extractResponses(array $responses)
    {
        $params = [];
        $startTime = $responses[7][0];
        $endTime = $responses[8][0];
        $startTime = \DateTime::createFromFormat('H:i',$startTime);
        $endTime = \DateTime::createFromFormat('H:i',$endTime);
        $params['startTime'] = $startTime;
        $params['endTime'] = $endTime;
        $startDate = new \DateTime();
        $startDate = $startDate->format('Y-m-d');
        $params['startDate'] = $startDate;
        $andConds = "";

        if(count($responses[1])>0)
        {
            foreach($responses[1] as $key=>$value):
                {
                    $params['music'.$key] = "%".$value."%";
                    $andConds .= "e.keyWords like :music$key";
                    if($key+1 < count($responses[1]))
                    {
                        $andConds .= " OR";
                    }
                }endforeach;
        }

        $resps = [4=>'club',6=>'drink',9=>'loisir'];
        foreach ($resps as $paramKey=>$paramValue)
        {
            if($andConds !== "" && count($responses[$paramKey])>0)
            {
                $andConds .= " OR";
                foreach($responses[$paramKey] as $key=>$value):
                    {
                        $params["$paramValue".$key] = "%".$value."%";
                        $andConds .= " e.keyWords like :$paramValue$key";
                        if($key+1 < count($responses[$paramKey]))
                        {
                            $andConds .= " OR";
                        }
                    }endforeach;
            }
        }
        return [$andConds,$params];
    }
}
