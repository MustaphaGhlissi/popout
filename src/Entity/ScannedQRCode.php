<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ScannedQRCodeRepository")
 */
class ScannedQRCode
{
    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="scannedQRCodes")
     */
    private $classicUser;

    /**
     * @var Event
     * @ORM\ManyToOne(targetEntity="App\Entity\Event", inversedBy="scannedQRCodes")
     */
    private $event;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var \DateTime
     * @ORM\Column(name="date_scan_qr_code", type="datetime", nullable=true)
     */
    private $dateScanQrCode;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return \DateTime
     */
    public function getDateScanQrCode(): \DateTime
    {
        return $this->dateScanQrCode;
    }

    /**
     * @param \DateTime $dateScanQrCode
     */
    public function setDateScanQrCode(\DateTime $dateScanQrCode): void
    {
        $this->dateScanQrCode = $dateScanQrCode;
    }

    /**
     * @return User
     */
    public function getClassicUser(): User
    {
        return $this->classicUser;
    }

    /**
     * @param User $classicUser
     */
    public function setClassicUser(User $classicUser): void
    {
        $this->classicUser = $classicUser;
    }

    /**
     * @return Event
     */
    public function getEvent(): Event
    {
        return $this->event;
    }

    /**
     * @param Event $event
     */
    public function setEvent(Event $event): void
    {
        $this->event = $event;
    }
}
