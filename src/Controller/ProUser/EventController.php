<?php
/**
 * Created by PhpStorm.
 * User: Hp
 * Date: 27/02/2018
 * Time: 10:18
 */

namespace App\Controller\ProUser;

use App\Controller\CoreController;
use App\Entity\Event;
use App\Entity\ScannedQRCode;
use App\Entity\User;
use App\Entity\Booking;
use FOS\RestBundle\Controller\Annotations as Rest;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class EventController
 * @package App\Controller\ProUser
 * @Route("/api")
 */
class EventController extends CoreController
{
    /**
     * @Rest\View()
     * @Rest\Post("/events")
     */
    public function postEvents(Request $request)
    {
        if (!$this->isGranted('ROLE_PROFESSIONAL')) {
            return new JsonResponse(['status' => false, 'message' => 'Accès non autorisé']);
        }
        $serializer = $this->get('jms_serializer');
        $event = new Event();
        $name = $request->request->get('name');
        $location = $request->request->get('location');
        $description = $request->request->get('description');
        $rewardPoints = $request->request->get('rewardPoints');
        $promoter = json_decode($request->request->get('promoter'));
        $startDate = $request->request->get('startDate');
        $startTime = $request->request->get('startTime');
        $endDate = $request->request->get('endDate');
        $endTime = $request->request->get('endTime');
        $keyWords = $request->request->get('keyWords');
        $latitude = (double)$request->request->get('latitude');
        $longitude = (double)$request->request->get('longitude');
        $base64Img = $request->request->get('image');
        $event->setName($name);
        $event->setLocation($location);
        $event->setDescription($description);
        $event->setKeyWords($keyWords);
        $event->setStartDate(new \DateTime($startDate));
        $event->setStartTime(new \DateTime($startTime));
        $event->setEndDate(new \DateTime($endDate));
        $event->setEndTime(new \DateTime($endTime));
        $event->setLatitude($latitude);
        $event->setLongitude($longitude);
        $event->setRewardPoint($rewardPoints);
        $event->setPromoter($promoter);
        $event->setBase64($base64Img);
        $event->setProUser($this->getUser());
        $this->getEm()->persist($event);
        $this->getEm()->flush();
        $data['status'] = true;
        $data['message'] = "Félicitations! évènement publié avec succès";
        $data['event'] = $event;
        $serializer->serialize($data, 'json');
        return $data;
    }

    /**
     * @Rest\View()
     * @Rest\Get("/events/timeline")
     */
    public function getTimeline()
    {
        if (!$this->isGranted('ROLE_PROFESSIONAL')) {
            return new JsonResponse(['status' => false, 'message' => 'Accès non autorisé']);
        }
        $serializer = $this->get('jms_serializer');
        $events = $this->getEm()->getRepository(Event::class)->getEventsByUser($this->getUser());
        $data['events'] = $events;
        $data['status'] = true;
        $serializer->serialize($data, 'json');
        return $data;
    }

    /**
     * @Rest\View()
     * @Rest\Put("/events/{id}/promote")
     */
    public function promoteEvent(Request $request)
    {
        if (!$this->isGranted('ROLE_PROFESSIONAL')) {
            return new JsonResponse(['status' => false, 'message' => 'Accès non autorisé']);
        }
        $id = $request->get('id');
        $event = $this->getEm()->getRepository(Event::class)->find($id);
        if (!$event instanceof Event) {
            return new JsonResponse(['status' => false, 'message' => 'Aucun évènement n\'est trouvé.']);
        }
        $event->setPromoter(json_decode($request->request->get('promoter')));
        $this->getEm()->flush();
        $data['status'] = true;
        $data['message'] = "Mode promoteur " . ($event->isPromoter() ? "activé avec succès" : "désactivé avec succès");
        $serializer = $this->get('jms_serializer');
        $serializer->serialize($data, 'json');
        return $data;
    }

    /**
     * @Rest\View()
     * @Rest\Get("/events/{event_id}/qrcode/{user_id}/scan")
     */
    public function scanQRCode(Request $request)
    {
        if (!$this->isGranted('ROLE_PROFESSIONAL')) {
            return new JsonResponse(['status' => false, 'message' => 'Accès non autorisé']);
        }
        $userId = $request->get('user_id');
        $eventId = $request->get('event_id');
        $user = $this->getEm()->getRepository(User::class)->find($userId);
        $event = $this->getEm()->getRepository(Event::class)->find($eventId);
        if (!$user instanceof User) {
            return new JsonResponse(['status' => false, 'message' => 'L\'utilisateur n\'existe pas.']);
        }
        if (!$event instanceof Event) {
            return new JsonResponse(['status' => false, 'message' => 'Aucun évènement n\'est trouvé.']);
        }
        $count = $this->getEm()->getRepository(Booking::class)->getByUserAndEvent($user, $event);
        if($count>0)
        {
            $scannedCode = new ScannedQRCode();
            $scannedCode->setClassicUser($user);
            $scannedCode->setEvent($event);
            $scannedCode->setDateScanQrCode(new \DateTime('now'));
            $this->getEm()->persist($scannedCode);
            $this->getEm()->flush();
            $data['status'] = true;
            $data['message'] = 'Cet utilisateur a déjà réservé sa place';
        }
        else
        {
            $data['status'] = false;
            $data['message'] = 'Cet utilisateur n\'a pas réservé sa place dans cet évènement';
        }
        $serializer = $this->get('jms_serializer');
        $serializer->serialize($data, 'json');
        return $data;
    }


    /*
     ******************************************************* CRON job **************************************************************************
    */
    /**
     * @Rest\View()
     * @Rest\Put("/events/reward/update")
     */
    public function updateRewardPoints(Request $request)
    {
        if (!$this->isGranted('ROLE_PROFESSIONAL')) {
            return new JsonResponse(['status' => false, 'message' => 'Accès non autorisé']);
        }
        $id = $request->request->get('classicUserId');
        $user = $this->getEm()->getRepository(User::class)->getOneByIdAndRole($id, 'ROLE_PERSONAL');
        if (!$user instanceof User) {
            return new JsonResponse(['status' => false, 'message' => 'L\'utilisateur n\'existe pas.']);
        }
        $user->setRewardPoint($user->getRewardPoint() + $request->request->get('rewardPoint'));
        $this->getEm()->flush();
        $data['status'] = true;
        $data['message'] = "Les points pop sont crédités sur le compte de l'utilisateur avec succès";
        $serializer = $this->get('jms_serializer');
        $serializer->serialize($data, 'json');
        return $data;
    }


    /**
     * @Rest\View()
     * @Rest\Get("/events/{id}/sells")
     */
    public function getEventSells(Request $request)
    {
        if (!$this->isGranted('ROLE_PROFESSIONAL')) {
            return new JsonResponse(['status' => false, 'message' => 'Accès non autorisé']);
        }
        $id = $request->get('id');
        $event = $this->getEm()->getRepository(Event::class)->find($id);
        if (!$event instanceof Event) {
            return new JsonResponse(['status' => false, 'message' => 'Aucun évènement n\'est trouvé.']);
        }
        $bookings = $this->getEm()->getRepository(Booking::class)->getBookingSellsByEvent($event);
        $data['status'] = true;
        $data['bookings'] = $bookings;
        $serializer = $this->get('jms_serializer');
        $serializer->serialize($data, 'json');
        return $data;
    }
}