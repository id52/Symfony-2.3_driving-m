<?php

namespace My\AppBundle\Entity;

use My\AppBundle\Model\Subject as SubjectModel;

class Subject extends SubjectModel
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

    public function getVersionsIds()
    {
        $ids = array();
        $versions = $this->getVersions();
        foreach ($versions as $version) {
            $ids[] = $version->getId();
        }
        return $ids;
    }
}
