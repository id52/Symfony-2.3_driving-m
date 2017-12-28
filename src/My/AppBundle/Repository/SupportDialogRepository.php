<?php

namespace My\AppBundle\Repository;

use Doctrine\ORM\EntityRepository;
use My\AppBundle\Entity\User;

class SupportDialogRepository extends EntityRepository
{
    //get user dialogs with category and parent category data
    public function getUserDialogs(User $user)
    {
        $dialogs = $this->createQueryBuilder('sd')
            ->andWhere('sd.user = :user')->setParameter('user', $user)
            ->leftJoin('sd.category', 'category')
            ->leftJoin('category.parent', 'parent_category')
            ->select('sd.id id, sd.created_at createdAt, sd.last_message_time lastMessageTime, sd.user_read userRead')
            ->addSelect('category.name categoryName, parent_category.name parentCategoryName')
            ->andWhere('category.type != :type')->setParameter('type', 'teacher')
            ->addOrderBy('sd.last_message_time', 'DESC')
            ->getQuery()->getResult();
        return $dialogs;
    }

    //get user dialogs with teachers
    public function getUserDialogsWithTeachers(User $user)
    {
        $dialogs = $this->createQueryBuilder('sd')
            ->andWhere('sd.user = :user')->setParameter('user', $user)
            ->leftJoin('sd.category', 'category')
            ->leftJoin('sd.theme', 't')
            ->leftJoin('category.user', 'categoryUser')
            ->select('sd.id id, sd.created_at createdAt, sd.last_message_time lastMessageTime, sd.user_read userRead')
            ->addSelect('category.name categoryName')
            ->addSelect('categoryUser.first_name')
            ->addSelect('categoryUser.last_name')
            ->addSelect('t.title themeTitle')
            ->addOrderBy('sd.last_message_time', 'DESC')
            ->andWhere('category.type = :type')->setParameter('type', 'teacher')
            ->getQuery()->getResult();
        return $dialogs;
    }

    public function getCountOfUserUnreadCategoryDialogs(User $user)
    {
        $cnt = $this->createQueryBuilder('sd')
            ->select('COUNT(sd) cnt')
            ->innerJoin('sd.category', 'category')
            ->andWhere('category.type = :categoryType')->setParameter('categoryType', 'category')
            ->andWhere('sd.user = :user')->setParameter('user', $user)
            ->andWhere('sd.user_read = :ur')->setParameter('ur', false)
            ->getQuery()->getScalarResult();
        return current(array_map('current', $cnt)); // get first element of 2D array
    }

    public function getCountOfUserUnreadTeachersDialogs(User $user)
    {
        $cnt = $this->createQueryBuilder('sd')
            ->select('COUNT(sd) cnt')
            ->innerJoin('sd.category', 'category')
            ->andWhere('category.type = :categoryType')->setParameter('categoryType', 'teacher')
            ->andWhere('sd.user = :user')->setParameter('user', $user)
            ->andWhere('sd.user_read = :ur')->setParameter('ur', false)
            ->getQuery()->getScalarResult();
        return current(array_map('current', $cnt)); // get first element of 2D array
    }

    //get categories or just qb for Paginating purposes e.g.
    public function getModeratorAvailableDialogs(User $user, $getQb = false)
    {
        $categories = $this->getEntityManager()->getRepository('AppBundle:SupportCategory')
            ->getModeratedSupportCategoriesArray($user);
        $qb = $this->createQueryBuilder('sd')
            ->andWhere('sd.category IN (:categories)')->setParameter('categories', $categories)
            ->leftJoin('sd.user', 'u')
            ->leftJoin('sd.last_moderator', 'lm')
            ->leftJoin('sd.category', 'category')
            ->leftJoin('category.user', 'categoryUser')
            ->leftJoin('category.parent', 'parent_category')
            ->leftJoin('sd.theme', 't')
            ->select('sd, sd.id, sd.created_at createdAt, sd.answered answered, sd.last_message_text lastMessageText')
            ->addSelect('sd.last_message_time lastMessageTime, sd.limit_answer_date limitAnswerDate')
            ->addSelect('category.name categoryName')
            ->addSelect('parent_category.name parentCategoryName')
            ->addSelect('category.type categoryType')
            ->addSelect('categoryUser.first_name teacherFirstName')
            ->addSelect('categoryUser.last_name teacherLastName')
            ->addSelect('u.last_name userLastName, u.first_name userFirstName, u.patronymic userPatronomic')
            ->addSelect('lm.id lastModeratorId, lm.last_name lastModeratorLastName')
            ->addSelect('lm.first_name lastModeratorFirstName, lm.patronymic lastModeratorPatronomic')
            ->addSelect('t.title themeTitle')
        ;
        if ($getQb) {
            return $qb;
        } else {
            return $qb->getQuery()->getResult();
        }
    }

    //get outdated dialogs
    public function getOutdatedDialogs()
    {
        return $this->createQueryBuilder('sd')
            ->leftJoin('sd.category', 'category')
            ->leftJoin('category.parent', 'parent_category')
            ->andWhere('sd.answered = :ans')->setParameter('ans', false)
            ->andWhere('sd.limit_answer_date < :lad')->setParameter('lad', new \DateTime())
            ->select('sd.id, category.name categoryName, parent_category.name parentCategoryName')
            ->getQuery()->getResult();
    }
}
