<?php

namespace My\PaymentBundle\Repository;

use Doctrine\ORM\EntityRepository;
use My\AppBundle\Entity\User;

class LogRepository extends EntityRepository
{
    /**
     * Возвращает последний платеж пользователя
     *
     * @param User $user
     * @param string $type
     * @return mixed|null
     */
    public function findLastPayment(User $user, $type = 'robokassa')
    {
        return $this->createQueryBuilder('l')
            ->where('l.user = :user')->setParameter('user', $user)
            ->andWhere('l.s_type = :type')->setParameter('type', $type)
            ->orderBy('l.updated_at', 'DESC')
            ->setMaxResults(1)
            ->getQuery()->getOneOrNullResult();
    }
}
