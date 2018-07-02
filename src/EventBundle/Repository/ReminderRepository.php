<?php
/**
 *
 *
 * @author    Yaroslav Velychko <lijkbezorger@gmail.com>
 * @copyright 2018 Yaroslav Velychko
 */

namespace EventBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use UserBundle\Entity\User;


class ReminderRepository extends EntityRepository
{
    /**
     * @return User[]
     */
    public function findAll()
    {
        return $this->createQueryBuilder('reminder')
            ->orderBy('reminder.id', 'DESC')
            ->getQuery()
            ->execute();
    }

    //Queries

    /**
     * @return QueryBuilder
     */
    public function createAlphabeticalQueryBuilder()
    {
        return $this->createQueryBuilder('user')
            ->orderBy('reminder.title', 'ASC');
    }
}