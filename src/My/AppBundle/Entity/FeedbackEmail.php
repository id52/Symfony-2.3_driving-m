<?php

namespace My\AppBundle\Entity;

use My\AppBundle\Model\FeedbackEmail as FeedbackEmailModel;

class FeedbackEmail extends FeedbackEmailModel
{
    public function __toString()
    {
        return $this->getName();
    }
}
