<?php

namespace App\Controller\Common;

use App\Controller\CoreController;
use App\Entity\Message;
use App\Entity\User;
use App\Service\PusherNotifier;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class ChatController
 * @package App\Controller\Common
 * @Route("/api")
 */
class ChatController extends CoreController
{
    /**
     * @return mixed
     * @Rest\View()
     * @Rest\Get("/messages/sent")
     */
    public function getSentMessages()
    {
        if (!$this->isGranted('ROLE_PERSONAL') && !$this->isGranted('ROLE_PROFESSIONAL'))
        {
            return new JsonResponse(array('status'=>false, 'message'=>'Vous n\'êtes pas autorisé pour effectuer cette opération.'));
        }
        $messages = $this->getEm()->getRepository(Message::class)->findBySender($this->getUser());
        $data['status'] = true;
        $data['messages'] = $messages;
        $this->get('jms_serializer')->serialize($data, 'json');
        return $data;
    }

    /**
     * @return mixed
     * @Rest\View()
     * @Rest\Get("/messages/inbox")
     */
    public function getInboxMessages()
    {
        if (!$this->isGranted('ROLE_PERSONAL') && !$this->isGranted('ROLE_PROFESSIONAL'))
        {
            return new JsonResponse(array('status'=>false, 'message'=>'Vous n\'êtes pas autorisé pour effectuer cette opération.'));
        }
        $messages = $this->getEm()->getRepository(Message::class)->findByReceiver($this->getUser());
        $data['status'] = true;
        $data['messages'] = $messages;

        $this->get('jms_serializer')->serialize($data, 'json');
        return $data;
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @Rest\View()
     * @Rest\Get("/messages/{id}/sent")
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getSentMessage(Request $request)
    {
        if (!$this->isGranted('ROLE_PERSONAL') && !$this->isGranted('ROLE_PROFESSIONAL'))
        {
            return new JsonResponse(array('status'=>false, 'message'=>'Vous n\'êtes pas autorisé pour effectuer cette opération.'));
        }
        $id = $request->get('id');
        $message = $this->getEm()->getRepository(Message::class)->findOneBySender($this->getUser(),$id);
        if(!$message instanceof Message)
        {
            return new JsonResponse(array('status'=>false, 'message'=>'Message introuvable'),Response::HTTP_NOT_FOUND);
        }
        $data['status'] = true;
        $data['message'] = $message;
        $this->get('jms_serializer')->serialize($data, 'json');
        return $data;
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @Rest\View()
     * @Rest\Get("/messages/{id}/inbox")
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getInboxMessage(Request $request)
    {
        if (!$this->isGranted('ROLE_PERSONAL') && !$this->isGranted('ROLE_PROFESSIONAL'))
        {
            return new JsonResponse(array('status'=>false, 'message'=>'Vous n\'êtes pas autorisé pour effectuer cette opération.'));
        }
        $id = $request->get('id');
        $message = $this->getEm()->getRepository(Message::class)->findOneByReceiver($this->getUser(),$id);
        if(!$message instanceof Message)
        {
            return new JsonResponse(array('status'=>false, 'message'=>'Message introuvable'),Response::HTTP_NOT_FOUND);
        }
        $data['status'] = true;
        $data['message'] = $message;
        $this->get('jms_serializer')->serialize($data, 'json');
        return $data;
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @Rest\View()
     * @Rest\Post("/messages")
     */
    public function sentMessage(Request $request, PusherNotifier $pusherNotifier)
    {
        $content = $request->request->get('content');
        $receiverId = $request->request->get('receiverId');
        $receiver = $this->getEm()->getRepository(User::class)->find($receiverId);
        if(!$receiver instanceof User)
        {
            return new JsonResponse(array('status'=>false, 'message'=>'Le destinataire est introuvable'),Response::HTTP_NOT_FOUND);
        }

        /*
         * Send message notification throw pusher
         */
        $channelName = "popout";
        $eventName = "message";
        $data['message'] = $content;
        $pusherNotifier->notify($channelName, $eventName, $data);

        $message = new Message();
        $message->setContent($content);
        $message->setSendDateTime(new \DateTime('now'));
        $message->setSender($this->getUser());
        $message->setReceiver($receiver);
        $this->getEm()->persist($message);
        $this->getEm()->flush();
        $data['status'] = true;
        $data['message'] = "Message envoyé";
        $this->get('jms_serializer')->serialize($data,'json');
        return $data;
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @Rest\View()
     * @Rest\Delete("/messages/{id}/sent")
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function deleteSentMessage(Request $request)
    {
        if (!$this->isGranted('ROLE_PERSONAL') && !$this->isGranted('ROLE_PROFESSIONAL'))
        {
            return new JsonResponse(array('status'=>false, 'message'=>'Vous n\'êtes pas autorisé pour effectuer cette opération.'));
        }
        $id = $request->get('id');
        $message = $this->getEm()->getRepository(Message::class)->findOneBySender($this->getUser(),$id);
        if(!$message instanceof Message)
        {
            return new JsonResponse(array('status'=>false, 'message'=>'Message introuvable'),Response::HTTP_NOT_FOUND);
        }
        $this->getEm()->remove($message);
        $this->getEm()->flush();
        $data['status'] = true;
        $data['message'] = 'Message supprimé avec succès.';
        $this->get('jms_serializer')->serialize($data, 'json');
        return $data;
    }


    /**
     * @param Request $request
     * @return JsonResponse
     * @Rest\View()
     * @Rest\Delete("/messages/{id}/inbox")
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function deleteInboxMessage(Request $request)
    {
        if (!$this->isGranted('ROLE_PERSONAL') && !$this->isGranted('ROLE_PROFESSIONAL'))
        {
            return new JsonResponse(array('status'=>false, 'message'=>'Vous n\'êtes pas autorisé pour effectuer cette opération.'));
        }
        $id = $request->get('id');
        $message = $this->getEm()->getRepository(Message::class)->findOneByReceiver($this->getUser(),$id);
        if(!$message instanceof Message)
        {
            return new JsonResponse(array('status'=>false, 'message'=>'Message introuvable'),Response::HTTP_NOT_FOUND);
        }
        $this->getEm()->remove($message);
        $this->getEm()->flush();
        $data['status'] = true;
        $data['message'] = 'Message supprimé avec succès.';
        $this->get('jms_serializer')->serialize($data, 'json');
        return $data;
    }


    /**
     * @param Request $request
     * @return JsonResponse
     * @Rest\View()
     * @Rest\Patch("/messages/{id}/inbox/status")
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function readInboxMessage(Request $request)
    {
        if (!$this->isGranted('ROLE_PERSONAL') && !$this->isGranted('ROLE_PROFESSIONAL'))
        {
            return new JsonResponse(array('status'=>false, 'message'=>'Vous n\'êtes pas autorisé pour effectuer cette opération.'));
        }
        $id = $request->get('id');
        $message = $this->getEm()->getRepository(Message::class)->findOneByReceiver($this->getUser(),$id);

        if(!$message instanceof Message)
        {
            return new JsonResponse(array('status'=>false, 'message'=>'Message introuvable'),Response::HTTP_NOT_FOUND);
        }

        $message->setSeen(1);
        $message->setDateSeen(new \DateTime('now'));
        $this->getEm()->flush();
        $data['status'] = true;
        $data['message'] = 'Status de message modifié avec succès.';
        $this->get('jms_serializer')->serialize($data, 'json');
        return $data;
    }
}
