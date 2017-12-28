<?php

namespace My\AppBundle\Entity;

use My\AppBundle\Model\Question as QuestionModel;

class Question extends QuestionModel
{
    protected $is_pdd = false;

    public function getImageId()
    {
        return $this->getImage() ? $this->getImage()->getId() : null;
    }

    public function setImageId($imageId)
    {
    }

    public function setNum($num)
    {
        if ($num) {
            $a_num = explode('.', $num);
            $a_0 = intval($a_num[0]);
            $a_0 = $a_0 > 0 ? $a_0  : 1;
            $a_1 = isset($a_num[1]) ? intval($a_num[1]) : 1;
            $a_1 = $a_1 > 0 ? $a_1  : 1;
            $this->num = sprintf('%02d.%02d', $a_0, $a_1);
        } else {
            $this->num = null;
        }
        return $this;
    }

    public function getBlockNum()
    {
        if ($this->num) {
            $num = explode('.', $this->num);
            return ceil(intval($num[1])/5);
        }
        return null;
    }

    public function getTicketNum()
    {
        if ($this->num) {
            $a_num = explode('.', $this->num);
            $a_0 = intval($a_num[0]);
            return $a_0 > 0 ? $a_0  : 1;
        } else {
            return null;
        }
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
