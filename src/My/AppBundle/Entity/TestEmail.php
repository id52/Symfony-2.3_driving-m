<?php

namespace My\AppBundle\Entity;

use My\AppBundle\Model\TestEmail as TestEmailModel;

class TestEmail extends TestEmailModel
{
    public function __toString()
    {
        return $this->getEmail();
    }
}
