<?php

namespace My\AppBundle\Repository;

use Doctrine\ORM\EntityRepository;

class FinalExamLogRepository extends EntityRepository
{
    public function isPassed(\My\AppBundle\Entity\User $user)
    {
        return (bool)$this->getPassedDate($user);
    }

    public function getPassedDate(\My\AppBundle\Entity\User $user)
    {
        /** @var $result \My\AppBundle\Entity\FinalExamLog */
        $result = $this->createQueryBuilder('fel')
            ->andWhere('fel.user = :user')->setParameter('user', $user)
            ->andWhere('fel.passed = :passed')->setParameter('passed', true)
            ->orderBy('fel.ended_at', 'DESC')
            ->setMaxResults(1)
            ->getQuery()->getOneOrNullResult();
        return $result ? $result->getEndedAt() : null;
    }
}
