<?php

namespace My\AppBundle\Entity;

use My\AppBundle\Model\RegionPlace as RegionPlaceModel;

class RegionPlace extends RegionPlaceModel
{
    public function __toString()
    {
        return $this->getName();
    }
}
