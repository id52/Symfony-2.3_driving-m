<?php

namespace My\PromoBundle\Entity;

use My\PromoBundle\Model\Campaign as CampaignModel;

class Campaign extends CampaignModel
{
    protected $active = true;

    public function getActive()
    {
        $active = parent::getActive();
        if ($this->getUsedTo() < new \DateTime || $this->getUsedFrom() > new \DateTime) {
            $active = false;
        }

        return $active;
    }
}
