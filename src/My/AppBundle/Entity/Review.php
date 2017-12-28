<?php

namespace My\AppBundle\Entity;

use My\AppBundle\Model\Review as ReviewModel;

class Review extends ReviewModel
{
    public function getAge()
    {
        if (!$this->birthday) {
            return null;
        }

        return $this->birthday->diff(new \DateTime())->y;
    }

    public function getPhotoWebPath()
    {
        return $this->getPhoto() ? $this->getPhoto()->getWebPath() : null;
    }
}
