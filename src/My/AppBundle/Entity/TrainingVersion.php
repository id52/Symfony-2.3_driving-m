<?php

namespace My\AppBundle\Entity;

use My\AppBundle\Model\TrainingVersion as TrainingVersionModel;

class TrainingVersion extends TrainingVersionModel
{
    public function __toString()
    {
        return $this->getCategory()->getName().' ('.date_format($this->getStartDate(), 'j.n.Y').')';
    }
}
