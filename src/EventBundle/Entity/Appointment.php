<?php
/**
 *
 *
 * @author    Yaroslav Velychko <lijkbezorger@gmail.com>
 * @copyright 2018 Yaroslav Velychko
 */

namespace EventBundle\Entity;


use Doctrine\Common\Collections\ArrayCollection;
use UserBundle\Entity\User;

class Appointment extends Event
{
    /**
     * @var int
     */
    protected $id;

    /**
     * @var string
     */
    protected $title;

    /**
     * @var string
     */
    protected $description;

    /**
     * @var \DateTime
     */
    protected $startDateTime;

    /**
     * @var \DateTime
     */
    protected $endDateTime;

    /**
     * @var User
     */
    protected $user;


    /** @var User[] */
    private $invitedUsers;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @return \DateTime
     */
    public function getStartDateTime()
    {
        return $this->startDateTime;
    }

    /**
     * @param \DateTime $startDateTime
     */
    public function setStartDateTime($startDateTime)
    {
        $this->startDateTime = $startDateTime;
    }

    /**
     * @return \DateTime
     */
    public function getEndDateTime()
    {
        return $this->endDateTime;
    }

    /**
     * @param \DateTime $endDateTime
     */
    public function setEndDateTime($endDateTime)
    {
        $this->endDateTime = $endDateTime;
    }

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param User $user
     */
    public function setUser($user)
    {
        $this->user = $user;
    }

    public function __construct()
    {
        $this->invitedUsers = new ArrayCollection();
    }

    /**
     * @return User[]
     */
    public function getInvitedUsers()
    {
        return $this->invitedUsers;
    }

    /**
     * @param User[] $invitedUsers
     */
    public function setInvitedUsers($invitedUsers)
    {
        $this->invitedUsers = $invitedUsers;
    }

    /**
     * @param User $user
     */
    public function addUser(User $user)
    {
        if (!$this->invitedUsers->contains($user)) {
            $this->invitedUsers->add($user);
        }
    }

    /**
     * @param User $user
     */
    public function deleteUser(User $user)
    {
        if ($this->invitedUsers->contains($user)) {
            $this->invitedUsers->remove($user);
        }
    }
}