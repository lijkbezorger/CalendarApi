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
use EventBundle\Entity\Event;
use EventBundle\Entity\IEvent;


class EventRepository extends EntityRepository
{
    const ALIAS = Event::class;

    /**
     * @return IEvent[]
     */
    public function findAll()
    {
        return $this->createQueryBuilder(static::ALIAS)
            ->orderBy(static::ALIAS . '.id', 'DESC')
            ->getQuery()
            ->execute();
    }

    /**
     * @param int $userId
     * @return IEvent[]
     */
    public function findByUserId(int $userId)
    {
        return $this->createQueryBuilder(static::ALIAS)
            ->andWhere(static::ALIAS . '.user = :userId')
            ->setParameter('userId', $userId)
            ->getQuery()
            ->execute();
    }

    /**
     * @param $parameters
     * @return IEvent[]
     */
    public function search($parameters)
    {
        $id = ($parameters['id']) ?? null;
        $userId = ($parameters['user_id']) ?? ($parameters['userId']) ?? null;
        $title = ($parameters['title']) ?? null;
        $description = ($parameters['description']) ?? null;
        $startAt = ($parameters['start_at']) ?? ($parameters['startAt']) ?? null;
        $endAt = ($parameters['end_at']) ?? ($parameters['endAt']) ?? null;

        /** @var QueryBuilder $qb */
        $qb = $this->createQueryBuilder(static::ALIAS);

        if ($id) {
            $qb->andWhere(static::ALIAS . '.id = :id')
                ->setParameter('id', $id);
        }
        if ($userId) {
            $qb->andWhere(static::ALIAS . '.user = :id')
                ->setParameter('id', $userId);
        }

        if ($title) {
            $qb->andWhere(static::ALIAS . '.title LIKE :title')
                ->setParameter('title', $title);
        }

        if ($description) {
            $qb->andWhere(static::ALIAS . '.description LIKE :description')
                ->setParameter('description', $description);
        }

        $startAt = ($startAt) ? new \DateTime($startAt) : null;
        $endAt = ($endAt) ? new \DateTime($endAt) : null;

        if ($startAt && !$endAt) {
            $endAt = new \DateTime('01.01.2050');
        }
        if (!$startAt && $endAt) {
            $startAt = new \DateTime('01.01.1900');
        }

        $qb->andWhere(
            $qb->expr()->not(
                $qb->expr()->orX(
                    $qb->expr()->lte(static::ALIAS . '.endDateTime', ':startAt'),
                    $qb->expr()->gte(static::ALIAS . '.startDateTime', ':endAt')
                )
            )
        )
            ->setParameter('startAt', $startAt)
            ->setParameter('endAt', $endAt);

        return $qb->getQuery()->execute();
    }
}