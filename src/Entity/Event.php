<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

use JMS\Serializer\Annotation as JMS;


/**
 * Secured resource.
 *
 * @ORM\Entity(repositoryClass="App\Repository\EventRepository")
 */
class Event
{
    /**
     * @var User
     * @JMS\Exclude()
     * @ORM\ManyToOne(targetEntity="User", inversedBy="events", fetch="EAGER")
     */
    private $proUser;

    /**
     * @var Booking
     * @JMS\Exclude()
     * @ORM\OneToMany(targetEntity="Booking", orphanRemoval=true, mappedBy="event")
     */
    private $bookings;

    /**
     * @var ScannedQRCode
     * @JMS\Exclude
     * @ORM\OneToMany(targetEntity="App\Entity\ScannedQRCode", mappedBy="event")
     */
    private $scannedQRCodes;


    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(type="string", length=64)
     */
    private $name;

    /**
     * @var string
     * @ORM\Column(type="string", length=255)
     */
    private $location;

    /**
     * @var \DateTime
     * @ORM\Column(type="date")
     */
    private $startDate;

    /**
     * @var \DateTime
     * @ORM\Column(type="time")
     */
    private $startTime;

    /**
     * @var \DateTime
     * @ORM\Column(type="date")
     */
    private $endDate;

    /**
     * @var \DateTime
     * @ORM\Column(type="time")
     */
    private $endTime;

    /**
     * @var string
     * @ORM\Column(type="text")
     */
    private $description;

    /**
     * @var string
     * @ORM\Column(type="text")
     */
    private $keyWords;

    /**
     * @var double
     * @ORM\Column(type="decimal", precision=12, scale=9)
     */
    private $latitude;

    /**
     * @var double
     * @ORM\Column(type="decimal", precision=12, scale=9)
     */
    private $longitude;

    /**
     * @var integer
     * @ORM\Column(type="integer", nullable=true)
     */
    private $rewardPoint;

    /**
     * @var boolean
     * @ORM\Column(type="boolean")
     */
    private $promoter;

    /**
     * @var string
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $attachPath;

    /**
     * @var string
     */
    private $base64;

    /**
     * @var boolean
     * @ORM\Column(name="booked", type="boolean", nullable=true)
     */
    private $booked;

    /**
     * Event constructor.
     */
    public function __construct()
    {
        $this->bookings = new ArrayCollection();
        $this->scannedQRCodes = new ArrayCollection();
    }


    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return User
     */
    public function getProUser(): User
    {
        return $this->proUser;
    }

    /**
     * @param User $proUser
     */
    public function setProUser(User $proUser): void
    {
        $this->proUser = $proUser;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getLocation(): string 
    {
        return $this->location;
    }

    /**
     * @param string $location
     */
    public function setLocation(string $location): void
    {
        $this->location = $location;
    }

    /**
     * @return \DateTime
     */
    public function getStartDate(): \DateTime
    {
        return $this->startDate;
    }

    /**
     * @param \DateTime $startDate
     */
    public function setStartDate(\DateTime $startDate): void
    {
        $this->startDate = $startDate;
    }

    /**
     * @return \DateTime
     */
    public function getStartTime(): \DateTime
    {
        return $this->startTime;
    }

    /**
     * @param \DateTime $startTime
     */
    public function setStartTime(\DateTime $startTime): void
    {
        $this->startTime = $startTime;
    }

    /**
     * @return \DateTime
     */
    public function getEndDate(): \DateTime
    {
        return $this->endDate;
    }

    /**
     * @param \DateTime $endDate
     */
    public function setEndDate(\DateTime $endDate): void
    {
        $this->endDate = $endDate;
    }

    /**
     * @return \DateTime
     */
    public function getEndTime(): \DateTime
    {
        return $this->endTime;
    }

    /**
     * @param \DateTime $endTime
     */
    public function setEndTime(\DateTime $endTime): void
    {
        $this->endTime = $endTime;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    /**
     * @return string
     */
    public function getKeyWords(): string
    {
        return $this->keyWords;
    }

    /**
     * @param string $keyWords
     */
    public function setKeyWords(string $keyWords): void
    {
        $this->keyWords = $keyWords;
    }

    /**
     * @return float
     */
    public function getLatitude(): float
    {
        return $this->latitude;
    }

    /**
     * @param float $latitude
     */
    public function setLatitude(float $latitude): void
    {
        $this->latitude = $latitude;
    }

    /**
     * @return float
     */
    public function getLongitude(): float
    {
        return $this->longitude;
    }

    /**
     * @param float $longitude
     */
    public function setLongitude(float $longitude): void
    {
        $this->longitude = $longitude;
    }

    /**
     * @return int
     */
    public function getRewardPoint(): int
    {
        return $this->rewardPoint;
    }

    /**
     * @param int $rewardPoint
     */
    public function setRewardPoint(int $rewardPoint): void
    {
        $this->rewardPoint = $rewardPoint;
    }

    /**
     * @return bool
     */
    public function isPromoter(): bool
    {
        return $this->promoter;
    }

    /**
     * @param bool $promoter
     */
    public function setPromoter(bool $promoter): void
    {
        $this->promoter = $promoter;
    }

    /**
     * @return mixed
     */
    public function getBookings()
    {
        return $this->bookings;
    }

    /**
     * @param Booking $bookings
     */
    public function setBookings(Booking $bookings): void
    {
        $this->bookings = $bookings;
    }

    /**
     * @return string
     */
    public function getAttachPath(): string
    {
        return $this->attachPath;
    }

    /**
     * @param string $attachPath
     */
    public function setAttachPath(string $attachPath): void
    {
        $this->attachPath = $attachPath;
    }

    /**
     * @return string
     */
    public function getBase64(): string
    {
        return $this->base64;
    }

    /**
     * @param string $base64
     */
    public function setBase64(string $base64): void
    {
        $this->base64 = $base64;
    }

    /**
     * @return ScannedQRCode
     */
    public function getScannedQRCodes(): ScannedQRCode
    {
        return $this->scannedQRCodes;
    }

    /**
     * @param ScannedQRCode $scannedQRCodes
     */
    public function setScannedQRCodes(ScannedQRCode $scannedQRCodes): void
    {
        $this->scannedQRCodes = $scannedQRCodes;
    }

    /**
     * @return bool
     */
    public function isBooked(): bool
    {
        return $this->booked;
    }

    /**
     * @param bool $booked
     */
    public function setBooked(bool $booked): void
    {
        $this->booked = $booked;
    }
}
