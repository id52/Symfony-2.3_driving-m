<?php

namespace My\AppBundle\Repository;

use Gedmo\Sortable\Entity\Repository\SortableRepository;
use My\AppBundle\Entity\Theme;
use My\AppBundle\Entity\User;

class ThemeRepository extends SortableRepository
{
    public function getAllGrouping()
    {
        $themes_q = $this->createQueryBuilder('t')
              ->orderBy('t.position', 'ASC')
              ->getQuery()->execute();
        $themes = array();
        foreach ($themes_q as $theme) {
            /** @var $theme \My\AppBundle\Entity\Theme */

            $themes[$theme->getSubject()->getId()][] = $theme;
        }

        return $themes;
    }

    public function isReaderExists(Theme $theme, User $user)
    {
        $qb = $this->_em->getRepository('AppBundle:Theme')->createQueryBuilder('t')
            ->leftJoin('t.readers', 'r')
            ->andWhere('t = :theme')->setParameter(':theme', $theme)
            ->andWhere('r = :user')->setParameter(':user', $user)
        ;
        return (bool)$qb->getQuery()->getArrayResult();
    }
}
