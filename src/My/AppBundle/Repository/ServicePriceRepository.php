<?php

namespace My\AppBundle\Repository;

use Doctrine\ORM\EntityRepository;
use My\AppBundle\Entity\Region;

class ServicePriceRepository extends EntityRepository
{
    /**
     * Стоимость второй оплаты
     *
     * @param Region $region
     * @return array
     */
    public function getPriceByRegion(Region $region)
    {
        return $this->createQueryBuilder('sp')
            ->andWhere('sp.active = :active')->setParameter('active', true)
            ->andWhere('sp.region = :region')->setParameter('region', $region)
            ->innerJoin('sp.service', 's')
            ->andWhere('s.type = :type')->setParameter('type', 'training')
            ->getQuery()->getResult();
    }
}
