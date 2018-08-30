<?php
/**
 * Created by PhpStorm.
 * User: Hp
 * Date: 01/03/2018
 * Time: 15:24
 */

namespace App\Controller\Common;


use App\Controller\CoreController;
use App\Entity\Log;
use App\Entity\User;
use App\Entity\Event;
use App\Entity\Booking;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Controller\Annotations as Rest;

/**
 * Class EventController
 * @package App\Controller\Common
 * @Route("/api")
 */
class EventController extends CoreController
{
    /**
     * @Rest\View()
     * @Rest\Post("/logs/save")
     */
    public function saveLog(Request $request)
    {
        if(!$this->getUser() instanceof User)
        {
            return new JsonResponse(['status'=>false, 'message'=>'Vous devez être authentifié']);
        }
        $user = $this->getUser();
        $log = new Log();
        $log->setUser($user);
        $log->setName($request->request->get('name'));
        $log->setDate(new \DateTime());

        $this->getEm()->persist($log);
        $this->getEm()->flush();

        $data['log'] = $log;
        $data['status'] = true;
        $data['message'] = 'Trace enregistrée avec succès';

        $serializer = $this->get('jms_serializer');
        $serializer->serialize($data, 'json');
        return $data;
    }

    /**
     * @Rest\View()
     * @Rest\Get("/events/{id}/members/count", requirements={"id"="\d+"})
     */
    public function getEventMembers(Request $request)
    {
        if(!$this->getUser() instanceof User)
        {
            return new JsonResponse(['status'=>false, 'message'=>'Vous devez être authentifié']);
        }

        $id = $request->get('id');
        $event = $this->getEm()->getRepository(Event::class)->find($id);
        if(!$event instanceof Event)
        {
            return new JsonResponse(['status'=>false,'message'=>'L\'évènement demandée n\'existe pas.']);
        }

        if($this->getUser()->hasRole('ROLE_PERSONAL'))
        {
            $eventMembers = $this->getEm()->getRepository(Booking::class)->getEventMembers($event);
        }
        else
        {
            $eventMembers = $this->getEm()->getRepository(Booking::class)->getEventMembersByProUser($event, $this->getUser());
        }

        $data['count'] = $eventMembers;
        $data['status'] = true;
        $serializer = $this->get('jms_serializer');
        $serializer->serialize($data, 'json');
        return $data;
    }
}