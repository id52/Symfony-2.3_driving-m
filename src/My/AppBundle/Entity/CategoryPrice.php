<?php

namespace My\AppBundle\Entity;

use My\AppBundle\Model\CategoryPrice as CategoryPriceModel;

class CategoryPrice extends CategoryPriceModel
{
    const DSC1 = 25;
    const DSC2 = 10;

    public function getSum($with_at = false)
    {
        return $this->getPriceEdu() + ($with_at ? $this->getPriceDrvAt() : $this->getPriceDrv());
    }

    public function getSum1($with_at)
    {
        return ceil($this->getSum($with_at) * ((100 - self::DSC1) / 100) / 100) * 100;
    }

    public function getSum2()
    {
        return ceil($this->getPriceEdu() * ((100 - self::DSC2) / 100) / 100) * 100;
    }

    public function getSum2p2($with_at, $sum2_1)
    {
        return ceil($this->getSum($with_at) * ((100 - self::DSC2) / 100) / 100) * 100 - $sum2_1;
    }

    public function getFullSum2($with_at)
    {
        return ceil($this->getSum($with_at) * ((100 - self::DSC2) / 100) / 100) * 100;
    }

    public static function getSumView($sum)
    {
        return $sum > 1000 ? (floor($sum/1000).' '.sprintf('%03d', $sum%1000)) : $sum;
    }
}
