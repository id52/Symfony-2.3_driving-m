<?php

namespace My\AppBundle\Repository;

use Doctrine\ORM\EntityRepository;
use My\AppBundle\Entity\Subject;
use My\AppBundle\Entity\User;

class ExamLogRepository extends EntityRepository
{
    public function isPassed(Subject $subject, User $user)
    {
        return (bool)$this->getPassedDate($subject, $user);
    }

    public function getPassedDate(Subject $subject, User $user)
    {
        /** @var $result \My\AppBundle\Entity\ExamLog */
        $result = $this->createQueryBuilder('el')
            ->andWhere('el.subject = :subject')->setParameter('subject', $subject)
            ->andWhere('el.user = :user')->setParameter('user', $user)
            ->andWhere('el.passed = :passed')->setParameter('passed', true)
            ->orderBy('el.ended_at', 'DESC')
            ->setMaxResults(1)
            ->getQuery()->getOneOrNullResult();
        return $result ? $result->getEndedAt() : null;
    }
}
