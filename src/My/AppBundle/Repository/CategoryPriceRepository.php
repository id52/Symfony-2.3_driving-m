<?php

namespace My\AppBundle\Repository;

use Doctrine\ORM\EntityRepository;
use My\AppBundle\Entity\Category;
use My\AppBundle\Entity\Region;

class CategoryPriceRepository extends EntityRepository
{
    /**
     * Возвращает сумму для первой оплаты
     *
     * @param Region $region
     * @param Category $category
     * @return int
     */
    public function getCategoriesPricesSum(Region $region, Category $category)
    {
        $qb = $this->createQueryBuilder('cp');
        $qb
            ->andWhere('cp.active = :active')->setParameter(':active', true)
            ->andWhere('cp.region = :region')->setParameter(':region', $region)
            ->andWhere('cp.category = :category')->setParameter(':category', $category)
        ;
        $categories_prices = $qb->getQuery()->execute();
        $categories_prices_sum = 0;
        foreach ($categories_prices as $price) {
            /** @var $price \My\AppBundle\Entity\CategoryPrice */

            $categories_prices_sum += $price->getPrice();
        }
        return $categories_prices_sum;
    }
}
