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
    public function findLastMinuteForType($type) {
        $now = new \DateTime();
        $before = $now->sub(new \DateInterval('PT1M'));

        $qb = $this->createQueryBuilder('e')
            ->andWhere('e.createdat > :before')
            ->setParameter('before', $before);

        return $qb->getQuery()->getResult();
    }
}
