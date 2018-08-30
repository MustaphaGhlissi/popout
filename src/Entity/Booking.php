<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\BookingRepository")
 */
class Booking
{
    /**
     * @var Event
     * @ORM\ManyToOne(targetEntity="Event", inversedBy="bookings")
     */
    private $event;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="User", inversedBy="bookings")
     */
    private $classicUser;

    /**
     * @var Payment
     * @ORM\OneToOne(targetEntity="Payment", orphanRemoval=true, mappedBy="booking")
     */
    private $payment;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var \DateTime
     * @ORM\Column(type="datetime")
     */
    private $date;



    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
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
     * @return \DateTime
     */
    public function getDate(): \DateTime
    {
        return $this->date;
    }

    /**
     * @param \DateTime $date
     */
    public function setDate(\DateTime $date): void
    {
        $this->date = $date;
    }

    /**
     * @return mixed
     */
    public function getPayment()
    {
        return $this->payment;
    }

    /**
     * @param Payment $payment
     */
    public function setPayment(Payment $payment): void
    {
        $this->payment = $payment;
    }
}
