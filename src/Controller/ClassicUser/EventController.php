<?php
/**
 * Created by PhpStorm.
 * User: Hp
 * Date: 28/02/2018
 * Time: 16:30
 */

namespace App\Controller\ClassicUser;
use App\Controller\CoreController;
use App\Entity\Booking;
use App\Entity\Event;
use App\Entity\Friend;
use App\Entity\Payment;
use App\Entity\User;
use App\Service\PopOutMailer;
use App\Service\Stripe;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class EventController
 * @package App\Controller\ClassicUser
 * @Route("/api")
 */
class EventController extends CoreController
{
    /**
     * @Rest\View()
     * @Rest\Post("/events/search")
     */
    public function filterEvents(Request $request)
    {
        if(!$this->isGranted('ROLE_PERSONAL'))
        {
            return new JsonResponse(['status'=>false, 'message'=>'Accès non autorisé']);
        }
        $startDate = $request->request->has('startDate')?new \DateTime($request->request->get('startDate')) : "";
        $startTime = $request->request->has('startTime')?new \DateTime($request->request->get('startTime')) : "";
        $params["criteria"] = $request->request->has('criteria')?$request->request->get('criteria'):"";
        $params["startDate"] = $startDate;
        $params["startTime"] = $startTime;
        $events = $this->getEm()->getRepository(Event::class)->searchEventByCriteria($params);
        $data['status'] = true;
        $data['events'] = $events;
        $serializer = $this->get('jms_serializer');
        $serializer->serialize($data, 'json');
        return $data;
    }

    /**
     * @Rest\View()
     * @Rest\Put("/user/classic/status")
     */
    public function activeMap(Request $request)
    {
        if(!$this->isGranted('ROLE_PERSONAL'))
        {
            return new JsonResponse(['status'=>false, 'message'=>'Accès non autorisé']);
        }
        $status = json_decode($request->request->get('status'));
        $this->getUser()->setStatus($status);
        $this->getEm()->flush();
        $data['status'] = true;
        $data['message'] = $status === true?"Map pop activé avec succès":"Map pop désactivé avec succès";
        $serializer = $this->get('jms_serializer');
        $serializer->serialize($data, 'json');
        return $data;
    }

    
    /**
     * @Rest\View()
     * @Rest\Get("/friends/active")
     */
    public function getActiveFriendsInMap(Request $request)
    {
        if(!$this->isGranted('ROLE_PERSONAL'))
        {
            return new JsonResponse(['status'=>false, 'message'=>'Accès non autorisé']);
        }
        $friends = $this->getEm()->getRepository(Friend::class)->getActiveFriends($this->getUser());
        $friendList = [];
        foreach($friends as $friend):
        {
            $friendList [] = $friend->getFriendWith();
        }endforeach;

        $data['friends'] = $friendList;
        $data['status'] = true;
        $serializer = $this->get('jms_serializer');
        $serializer->serialize($data, 'json');
        return $data;
    }

    /**
     * @Rest\View()
     * @Rest\Post("/events/book")
     */
    public function bookEvent(Request $request)
    {
        if(!$this->isGranted('ROLE_PERSONAL'))
        {
            return new JsonResponse(['status'=>false, 'message'=>'Accès non autorisé']);
        }
        $id = $request->request->get('event_id');
        $event = $this->getEm()->getRepository(Event::class)->find($id);
        if(!$event instanceof Event)
        {
            return new JsonResponse(['status'=>false,'message'=>'L\'évènement demandée n\'existe pas.']);
        }

        $booking = new Booking();
        $booking->setDate(new \DateTime());
        $booking->setEvent($event);
        $booking->setClassicUser($this->getUser());
        $this->getEm()->persist($booking);
        $this->getEm()->flush();
        $data['booking'] = $booking;
        $data['status'] = true;
        $data['message'] = 'Félicitations! vous avez réservé votre place avec succès.';
        $serializer = $this->get('jms_serializer');
        $serializer->serialize($data, 'json');
        return $data;
    }

    /**
     * @Rest\View()
     * @Rest\Get("/events/preferred")
     */
    public function getEventsByUserChoices()
    {
        if(!$this->isGranted('ROLE_PERSONAL'))
        {
            return new JsonResponse(['status'=>false, 'message'=>'Accès non autorisé']);
        }
        $serializer = $this->get('jms_serializer');
        $quizzes = $this->getUser()->getQuizzes();
        $responses = [];
        foreach($quizzes as $quiz):
        {
            foreach($quiz->getResponses() as $response):
            {
                array_push($responses, $response);
            }endforeach;
        }endforeach;
        $events = $this->getEm()->getRepository(Event::class)->getByUserResponses($responses);
        $results = [];

        foreach ($events as $event):
        {
            $bookings = $event->getBookings();
            $i = 0;
            $booked = false;
            while($i<count($bookings) && $bookings[$i]->getClassicUser() !== $this->getUser())
            {
                $i++;
            }

            if($i<count($bookings))
            {
                $booked = true;
            }
            $event = $serializer->toArray($event);
            $event['booked'] = $booked;
            $event = $serializer->deserialize(json_encode($event), Event::class, 'json');
            array_push($results, $event);
        }endforeach;

        $data['events'] = $results;
        $data['status'] = true;
        $serializer->serialize($data, 'json');
        return $data;
    }

    /**
     * @Rest\View()
     * @Rest\Post("/events/payment/checkout")
     */
    public function checkout(Request $request, Stripe $stripe, PopOutMailer $mailer)
    {
        if(!$this->isGranted('ROLE_PERSONAL'))
        {
            return new JsonResponse(['status'=>false, 'message'=>'Accès non autorisé']);
        }
        $id = $request->request->get('eventId');
        $amount = $request->request->get('amount');
        $stripeToken = $request->request->get('stripeToken');

        $event = $this->getEm()->getRepository(Event::class)->find($id);
        if(!$event instanceof Event)
        {
            return new JsonResponse(['status'=>false,'message'=>'L\'évènement demandée n\'existe pas.']);
        }
        $booking = $this->getEm()->getRepository(Booking::class)->getOneBookingByUserAndEvent($this->getUser(), $event);
        if(!$booking)
        {
            return new JsonResponse(['status'=>false,'message'=>'Vous n\'avez pas réservé votre place à cet évènement']);
        }
        if($booking->getPayment() instanceof Payment)
        {
            return new JsonResponse(['status'=>false,'message'=>'Vous avez déjà payé.']);
        }
        return $this->flushPayment($booking,$amount, $stripe, $stripeToken, $mailer);
    }

    public function flushPayment(Booking $booking,int $amount, Stripe $stripe, string $stripeToken, PopOutMailer $mailer): array
    {
        $popAmount = ($amount * (int)$this->getParameter('popout_amount_percentage'))/100;
        //$stripe->checkout($amount,$popAmount, $stripeToken);
        $amount -= $popAmount;
        $payment = new Payment();
        $payment->setPayDate(new \DateTime());
        $payment->setAmount($amount);
        $payment->setPopAmount($popAmount);
        $payment->setBooking($booking);
        $booking->setPayment($payment);
        $this->getEm()->persist($payment);
        $this->getEm()->flush();
        $data['status'] = true;
        $data['message'] = 'Paiement aboutie avec succès';
        $this->get('jms_serializer')->serialize($data, 'json');
        return $data;
    }
}