<?php

namespace My\AppBundle\Entity;

use My\AppBundle\Model\Site as SiteModel;

class Site extends SiteModel
{
    protected $active = true;
    protected $active_auth = true;
    protected $_show = true;
    protected $show_auth = true;

    public function getImageId()
    {
        return $this->getImage() ? $this->getImage()->getId() : null;
    }

    public function setImageId($imageId)
    {
    }
}
