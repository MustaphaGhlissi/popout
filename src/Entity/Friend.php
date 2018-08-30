<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\FriendRepository")
 */
class Friend
{
    /**
     * @var integer
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="User", inversedBy="friends")
     */
    private $classicUser;


    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="User", inversedBy="friendsWith")
     */
    private $friendWith;

    /**
     * @return User
     */
    public function getClassicUser()
    {
        return $this->classicUser;
    }

    /**
     * @param User $classicUser
     */
    public function setClassicUser($classicUser): void
    {
        $this->classicUser = $classicUser;
    }

    /**
     * @return User
     */
    public function getFriendWith(): User
    {
        return $this->friendWith;
    }

    /**
     * @param User $friendWith
     */
    public function setFriendWith(User $friendWith): void
    {
        $this->friendWith = $friendWith;
    }


}
