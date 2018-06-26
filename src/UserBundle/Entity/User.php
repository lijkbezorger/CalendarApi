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


/**
 * Class User
 * @package UserBundle\Entity
 */
class User
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $email;

    /**
     * @var string
     */
    private $username;

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
     */
    public function setEmail($email)
    {
        $this->email = $email;
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
     */
    public function setUsername($username)
    {
        $this->username = $username;
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
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;
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
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;
    }

    public function __toString()
    {
        return $this->username;
    }


    //---?
    public function __construct()
    {
        $this->appointments = new ArrayCollection();
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