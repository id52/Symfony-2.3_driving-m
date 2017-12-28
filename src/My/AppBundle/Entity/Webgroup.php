<?php

namespace My\AppBundle\Entity;

use My\AppBundle\Model\Webgroup as WebgroupModel;

class Webgroup extends WebgroupModel
{
    public function __toString()
    {
        return $this->getName();
    }
}
