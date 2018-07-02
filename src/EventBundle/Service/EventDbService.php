<?php
/**
 *
 *
 * @author    Yaroslav Velychko <lijkbezorger@gmail.com>
 * @copyright 2018 Yaroslav Velychko
 */

namespace EventBundle\Service;


use Doctrine\ORM\EntityManager;
use EventBundle\Entity\Appointment;
use EventBundle\Entity\Event;
use EventBundle\Entity\Reminder;

class EventDbService
{

    public function getRepositoryByType(EntityManager $em, string $type)
    {
        switch ($type) {
            case 'reminder' :
                {
                    $result = $em->getRepository(Reminder::class);
                    break;
                }
            case 'appointment' :
                {
                    $result = $em->getRepository(Appointment::class);
                    break;
                }
            default:
                {
                    $result = $em->getRepository(Event::class);
                }
        }

        return $result;
    }
}