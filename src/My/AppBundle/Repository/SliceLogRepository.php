<?php

namespace My\AppBundle\Repository;

use Doctrine\ORM\EntityRepository;
use My\AppBundle\Entity\Slice;
use My\AppBundle\Entity\User;

class SliceLogRepository extends EntityRepository
{
    public function isPassed(Slice $slice, User $user)
    {
        $result = $this->createQueryBuilder('sl')
            ->andWhere('sl.slice = :slice')->setParameter('slice', $slice)
            ->andWhere('sl.user = :user')->setParameter('user', $user)
            ->andWhere('sl.passed = :passed')->setParameter('passed', true)
            ->getQuery()->getOneOrNullResult();
        return (bool)$result;
    }
}
