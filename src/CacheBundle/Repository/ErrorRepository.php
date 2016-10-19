<?php

namespace CacheBundle\Repository;

/**
 * ErrorRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ErrorRepository extends \Doctrine\ORM\EntityRepository
{
    public function getLastMinuteForType($type) {
        $now = new \DateTime();
        $before = $now->sub(new \DateInterval('T00:01:00'));

        $qb = $this->createQueryBuilder('e')
            ->andWhere('e.createdat > :before')
            ->setParameter('before', $before);

        return $qb->getQuery()->getResult();
    }
}