<?php

namespace My\AppBundle\Entity;

use My\AppBundle\Model\HowWork as HowWorkModel;

class HowWork extends HowWorkModel
{
    public function getImageId()
    {
        return $this->getImage() ? $this->getImage()->getId() : null;
    }

    public function setImageId($imageId)
    {
    }
}
