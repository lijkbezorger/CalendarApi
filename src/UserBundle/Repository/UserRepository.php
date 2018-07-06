<?php
/**
 *
 *
 * @author    Yaroslav Velychko <lijkbezorger@gmail.com>
 * @copyright 2018 Yaroslav Velychko
 */

namespace UserBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use UserBundle\Entity\User;


class UserRepository extends EntityRepository
{
    /**
     * @return User[]
     */
    public function findAll()
    {
        return $this->createQueryBuilder('user')
            ->orderBy('user.id', 'DESC')
            ->getQuery()
            ->execute();
    }

    public function findByUsername($username)
    {
        return $this->createQueryBuilder('user')
            ->andWhere('user.username = :username')
            ->setParameter('username', $username)
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
            ->orderBy('user.username', 'ASC');
    }
}