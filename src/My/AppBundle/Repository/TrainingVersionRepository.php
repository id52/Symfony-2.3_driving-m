<?php

namespace My\AppBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Validator\Constraints\DateTime;

class TrainingVersionRepository extends EntityRepository
{
    public function getVersionByUser(\My\AppBundle\Entity\User $user)
    {
        return $this->createQueryBuilder('v')
             ->andWhere('v.category = :category')->setParameter(':category', $user->getCategory())
             ->andWhere('v.start_date <= :start_date')
             ->setParameter(':start_date', date_format($user->getCreatedAt(), 'Y-m-d'))
             ->addOrderBy('v.start_date', 'DESC')
             ->setMaxResults(1)->getQuery()->getOneOrNullResult();
    }

    public function getVersionByCategory(\My\AppBundle\Entity\Category $category)
    {
        return $this->createQueryBuilder('v')
            ->andWhere('v.category = :category')->setParameter(':category', $category)
            ->andWhere('v.start_date <= :start_date')
            ->setParameter(':start_date', new \DateTime())
            ->addOrderBy('v.start_date', 'DESC')
            ->setMaxResults(1)->getQuery()->getOneOrNullResult();
    }
}
