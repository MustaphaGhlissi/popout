<?php
/**
 * Created by PhpStorm.
 * User: Hp
 * Date: 06/03/2018
 * Time: 09:15
 */

namespace App\EventListener;

use App\Entity\Payment;
use App\Service\FileUploader;
use App\Service\PopNotifier;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Doctrine\ORM\Event\LifecycleEventArgs;
use App\Entity\User;
use App\Entity\Event;
use App\Service\QRCodeGenerator;

class EntityListener
{
    private $qrCodeGenerator;
    private $container;
    private $uploader;
    private $imgName;
    private $path;
    private $popNotifier;
    public function __construct(QRCodeGenerator $qrCodeGenerator, ContainerInterface $container, FileUploader $uploader, PopNotifier $popNotifier)
    {
        $this->qrCodeGenerator = $qrCodeGenerator;
        $this->container = $container;
        $this->uploader = $uploader;
        $this->popNotifier = $popNotifier;
    }

    public function prePersist(LifecycleEventArgs $args){
        $entity = $args->getEntity();
        if(!$entity instanceof Event)
        {
            return;
        }
        $this->imgName = uniqid() . '.png';
        $entity->setAttachPath('uploads/events/'.$this->imgName);
    }

    public function postPersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        if($entity instanceof Event)
        {
            $this->uploader->upload($entity->getBase64(), $this->imgName);
            return;
        }
        elseif ($entity instanceof Payment)
        {
            $this->popNotifier->notify($entity->getBooking());
            return;
        }
        elseif (!$entity instanceof User || !$entity->hasRole('ROLE_PERSONAL')) {
            return;
        }
        $entityManager = $args->getEntityManager();
        $this->qrCodeGenerator->generateQRCode($entity, $this->container);
        $entityManager->flush();
    }

    public function preRemove(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        if($entity instanceof Event)
        {
            $this->path = $entity->getAttachPath();
        }
        elseif ($entity instanceof User && $entity->hasRole('ROLE_PERSONAL')) {
            $this->path = $entity->getQrCode();
        }
    }

    public function postRemove(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        if($entity instanceof Event || $entity instanceof User)
        {
            if(file_exists($this->path))
            {
                unlink($this->path);
            }
        }
    }
}