<?php
/**
 *
 *
 * @author    Yaroslav Velychko <lijkbezorger@gmail.com>
 * @copyright 2018 Yaroslav Velychko
 */

namespace UserBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use EventBundle\Entity\Appointment;
use EventBundle\Entity\IEvent;
use FOS\UserBundle\Model\User as BaseUser;


/**
 * Class User
 * @package UserBundle\Entity
 */
class User extends BaseUser
{
    /**
     * @var integer
     */
    protected $id;

    /**
     * @var string
     */
    protected $email;

    /**
     * @var string
     */
    protected $username;

    /**
     * @var string
     */
    private $firstName;

    /**
     * @var string
     */
    private $lastName;

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
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param string $email
     * @return User
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @param string $username
     * @return User
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * @param string $firstName
     * @return User
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;

        return $this;
    }

    /**
     * @return string
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * @param string $lastName
     * @return User
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function __toString()
    {
        return $this->getUsername();
    }


    public function __construct()
    {
        parent::__construct();

        $this->events = new ArrayCollection();
        $this->appointments = new ArrayCollection();
    }

    /** @var IEvent[] */
    private $events;

    /**
     * @return IEvent[]
     */
    public function getEvents()
    {
        return $this->events;
    }

    /**
     * @param IEvent[] $events
     */
    public function setEvents($events)
    {
        $this->events = $events;
    }

    /**
     * @var Appointment[]
     */
    private $appointments;

    /**
     * @return Appointment[]
     */
    public function getAppointments()
    {
        return $this->appointments;
    }

    /**
     * @param Appointment[] $appointments
     */
    public function setAppointments($appointments)
    {
        $this->appointments = $appointments;
    }

}