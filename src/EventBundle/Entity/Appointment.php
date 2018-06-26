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
    /** @var User[] */
    private $invitedUsers;

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
}