<?php

namespace My\AppBundle\Entity;

use My\AppBundle\Model\FlashBlockItem as FlashBlockItemModel;

class FlashBlockItem extends FlashBlockItemModel
{
    public function __toString()
    {
        return $this->getTitle();
    }

    public function getImageId()
    {
        return $this->getImage() ? $this->getImage()->getId() : null;
    }

    public function setImageId($imageId)
    {
    }
}
