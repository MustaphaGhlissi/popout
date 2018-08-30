<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\MessageRepository")
 */
class Message
{
    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="sentMessages")
     */
    private $sender;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy= "receivedMessages")
     */
    private $receiver;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(name="content", type="text")
     */
    private $content;

    /**
     * @var \DateTime
     * @ORM\Column(name="send_date_time", type="datetime")
     */
    private $sendDateTime;

    /**
     * @var bool
     * @ORM\Column(name="seen", type="boolean")
     */
    private $seen = false;

    /**
     * @var \DateTime
     * @ORM\Column(name="date_seen", type="datetime", nullable=true)
     */
    private $dateSeen;





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
    public function getSender(): User
    {
        return $this->sender;
    }

    /**
     * @param User $sender
     */
    public function setSender(User $sender): void
    {
        $this->sender = $sender;
    }

    /**
     * @return User
     */
    public function getReceiver(): User
    {
        return $this->receiver;
    }

    /**
     * @param User $receiver
     */
    public function setReceiver(User $receiver): void
    {
        $this->receiver = $receiver;
    }

    /**
     * @return string
     */
    public function getContent(): string
    {
        return $this->content;
    }

    /**
     * @param string $content
     */
    public function setContent(string $content): void
    {
        $this->content = $content;
    }

    /**
     * @return \DateTime
     */
    public function getSendDateTime(): \DateTime
    {
        return $this->sendDateTime;
    }

    /**
     * @param \DateTime $sendDateTime
     */
    public function setSendDateTime(\DateTime $sendDateTime): void
    {
        $this->sendDateTime = $sendDateTime;
    }

    /**
     * @return bool
     */
    public function isSeen(): bool
    {
        return $this->seen;
    }

    /**
     * @param bool $seen
     */
    public function setSeen(bool $seen): void
    {
        $this->seen = $seen;
    }

    /**
     * @return \DateTime
     */
    public function getDateSeen(): \DateTime
    {
        return $this->dateSeen;
    }

    /**
     * @param \DateTime $dateSeen
     */
    public function setDateSeen(\DateTime $dateSeen): void
    {
        $this->dateSeen = $dateSeen;
    }
}
