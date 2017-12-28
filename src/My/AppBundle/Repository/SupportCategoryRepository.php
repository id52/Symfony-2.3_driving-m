<?php

namespace My\AppBundle\Repository;

use Doctrine\ORM\EntityRepository;
use My\AppBundle\Entity\User;

class SupportCategoryRepository extends EntityRepository
{
    //get array of user's moderating categories (not root categories!)
    public function getModeratedSupportCategoriesArray(User $user)
    {
        $categories = array();
        if ($user->hasRole('ROLE_ADMIN')) {
            $categories = $this->createQueryBuilder('c')
                ->select('c.id')
                ->leftJoin('c.user', 'u')
                ->andWhere('(c.type = :type AND c.parent IS NOT NULL) OR (c.type = :type2 AND c.user IS NOT NULL)')
                ->setParameter('type', 'category')
                ->setParameter('type2', 'teacher')
                ->orderBy('c.createdAt')
                ->orderBy('c.parent')
                ->getQuery()->getArrayResult();
        } elseif ($user->hasRole('ROLE_MOD_SUPPORT') || $user->hasRole('ROLE_MOD_TEACHER')) {
            $categories = $this->createQueryBuilder('c')
                ->select('c.id')
                ->leftJoin('c.moderators', 'moderator')
                ->andWhere('(c.type = :type AND c.parent IS NOT NULL) OR (c.type = :type2 AND c.user IS NOT NULL)')
                ->setParameter('type', 'category')
                ->setParameter('type2', 'teacher')
                ->orderBy('c.createdAt')
                ->orderBy('c.parent')
                ->andWhere('moderator = :moderator')->setParameter(':moderator', $user->getId())
                ->getQuery()->getArrayResult();
        }
        return array_map('current', $categories);
    }

    public function getSupportStatistics(\DateTime $from, \DateTime $to)
    {
        //list of subcategories
        $subcategoriesRaw = $this->createQueryBuilder('sc')
            //only subcategories
            ->andWhere('sc.parent IS NOT NULL')
            //get parent info
            ->leftJoin('sc.parent', 'scp')

            //order by parent then by child
            ->orderBy('scp.name')
            ->orderBy('sc.name')

            //what do we need?
            ->select('sc.id id, sc.name name, scp.name parentName')

            //get data already
            ->getQuery()->getArrayResult();

        $subcategories = array();
        foreach ($subcategoriesRaw as $sub) {
            $sub['dialogsTotalCount'] = 0;
            $sub['dialogsWithoutAnswerCount'] = 0;
            $subcategories[$sub['id']] = $sub;
        }

        //count of dialogs total
        $dialogsInfo = $this->createQueryBuilder('sc')
            //only subcategories
            ->andWhere('sc.parent IS NOT NULL')

            //get dialogs info
            ->leftJoin('sc.dialogs', 'sd')
            ->groupBy('sc')

            //what do we need?
            ->select('sc.id id, count(sd) as dialogsTotalCount')

            //add conditions
            ->andWhere('sd.created_at >= :from')->setParameter('from', $from)
            ->andWhere('sd.created_at <= :to')->setParameter('to', $to)

            //get data already
            ->getQuery()->getResult();

        foreach ($dialogsInfo as $dc) {
            $subcategories[$dc['id']]['dialogsTotalCount'] = $dc['dialogsTotalCount'];
        }

        // count of dialogs without answers
        $dialogsWithoutAnswerInfo = $this->createQueryBuilder('sc')
            //only subcategories
            ->andWhere('sc.parent IS NOT NULL')
            //get parent info
            ->leftJoin('sc.parent', 'scp')

            //get dialogs info
            ->leftJoin('sc.dialogs', 'sd')
            ->groupBy('sc')

            //only without answer
            ->andWhere('sd.answered = :sdu')->setParameter('sdu', false)

            //what do we need?
            ->select('sc.id id, count(sd) as dialogsWithoutAnswerCount')

            //add conditions
            ->andWhere('sd.created_at >= :from')->setParameter('from', $from)
            ->andWhere('sd.created_at <= :to')->setParameter('to', $to)

            //get data already
            ->getQuery()->getResult();

        foreach ($dialogsWithoutAnswerInfo as $dwac) {
            $subcategories[$dwac['id']]['dialogsWithoutAnswerCount'] = $dwac['dialogsWithoutAnswerCount'];
        }

        return $subcategories;
    }


    public function getSupportTeachersStatistics(\DateTime $from, \DateTime $to)
    {
        //list of subcategories
        $subcategoriesRaw = $this->createQueryBuilder('sc')
            //only subcategories
            ->andWhere('sc.type = :type')->setParameter('type', 'teacher')
            ->andWhere('sc.user IS NOT NULL')
            //get user info
            ->leftJoin('sc.user', 'scu')

            //order by parent then by child
            ->orderBy('scu.last_name')

            //what do we need?
            ->select('sc.id id, scu.last_name last_name, scu.first_name first_name, scu.patronymic patronymic')

            //get data already
            ->getQuery()->getArrayResult();

        $subcategories = array();
        foreach ($subcategoriesRaw as $sub) {
            $sub['dialogsTotalCount'] = 0;
            $sub['dialogsWithoutAnswerCount'] = 0;
            $subcategories[$sub['id']] = $sub;
        }

        //count of dialogs total
        $dialogsInfo = $this->createQueryBuilder('sc')
            //only subcategories
            ->andWhere('sc.type = :type')->setParameter('type', 'teacher')
            ->andWhere('sc.user IS NOT NULL')

            //get dialogs info
            ->leftJoin('sc.dialogs', 'sd')
            ->leftJoin('sc.user', 'u')
            ->groupBy('sc')

            //what do we need?
            ->select('sc.id id, count(sd) as dialogsTotalCount')

            //add conditions
            ->andWhere('sd.created_at >= :from')->setParameter('from', $from)
            ->andWhere('sd.created_at <= :to')->setParameter('to', $to)

            //get data already
            ->getQuery()->getResult();

        foreach ($dialogsInfo as $dc) {
            $subcategories[$dc['id']]['dialogsTotalCount'] = $dc['dialogsTotalCount'];
        }

        // count of dialogs without answers
        $dialogsWithoutAnswerInfo = $this->createQueryBuilder('sc')
            //only subcategories
            ->andWhere('sc.type = :type')->setParameter('type', 'teacher')
            ->andWhere('sc.user IS NOT NULL')

            //get dialogs info
            ->leftJoin('sc.dialogs', 'sd')
            ->leftJoin('sc.user', 'u')
            ->groupBy('sc')

            //only without answer
            ->andWhere('sd.answered = :sdu')->setParameter('sdu', false)

            //what do we need?
            ->select('sc.id id, count(sd) as dialogsWithoutAnswerCount')

            //add conditions
            ->andWhere('sd.created_at >= :from')->setParameter('from', $from)
            ->andWhere('sd.created_at <= :to')->setParameter('to', $to)

            //get data already
            ->getQuery()->getResult();

        foreach ($dialogsWithoutAnswerInfo as $dwac) {
            $subcategories[$dwac['id']]['dialogsWithoutAnswerCount'] = $dwac['dialogsWithoutAnswerCount'];
        }

        return $subcategories;
    }

    public function getOnlyCategories()
    {
        return $this->createQueryBuilder('sc')
            ->where('sc.type = :type')->setParameter('type', 'category')
            ->getQuery()->getResult();
    }
}
