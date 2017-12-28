<?php

namespace My\AppBundle\Entity;

use My\AppBundle\Model\Category as CategoryModel;

class Category extends CategoryModel
{
    protected $with_at = false;

    public function __toString()
    {
        return $this->getName();
    }

    public function getImageId()
    {
        return $this->getImage() ? $this->getImage()->getId() : null;
    }

    public function setImageId($imageId)
    {
    }

    public function getPriceByRegion(\My\AppBundle\Entity\Region $region)
    {
        $result = null;

        $prices = $this->getCategoriesPrices();
        foreach ($prices as $price) { /** @var $price \My\AppBundle\Entity\CategoryPrice */
            if ($price->getRegion()->getId() == $region->getId()) {
                $result = $price;
            }
        }

        return $result;
    }
}
