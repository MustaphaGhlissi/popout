<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PaymentRepository")
 */
class Payment
{
    /**
     * @var Booking
     * @ORM\OneToOne(targetEntity="App\Entity\Booking", inversedBy="payment")
     */
    private $booking;

    /**
     * @var int
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var \DateTime
     * @ORM\Column(type="datetime")
     */
    private $payDate;

    /**
     * @var float
     * @ORM\Column(type="float")
     */
    private $amount;

    /**
     * @var float
     * @ORM\Column(type="float")
     */
    private $popAmount;

    /**
     * @return int
     */
    public function getId(){
        return $this->id;
    }

    /**
     * @return \DateTime
     */
    public function getPayDate(): \DateTime
    {
        return $this->payDate;
    }

    /**
     * @param \DateTime $payDate
     */
    public function setPayDate(\DateTime $payDate): void
    {
        $this->payDate = $payDate;
    }

    /**
     * @return float
     */
    public function getAmount(): float
    {
        return $this->amount;
    }

    /**
     * @param float $amount
     */
    public function setAmount(float $amount): void
    {
        $this->amount = $amount;
    }

    /**
     * @return float
     */
    public function getPopAmount(): float
    {
        return $this->popAmount;
    }

    /**
     * @param float $popAmount
     */
    public function setPopAmount(float $popAmount): void
    {
        $this->popAmount = $popAmount;
    }

    /**
     * @return Booking
     */
    public function getBooking(): Booking
    {
        return $this->booking;
    }

    /**
     * @param Booking $booking
     */
    public function setBooking(Booking $booking): void
    {
        $this->booking = $booking;
    }
}
