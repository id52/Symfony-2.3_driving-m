<?php

namespace My\AppBundle\Entity;

use My\AppBundle\Model\Region as RegionModel;

class Region extends RegionModel
{
    protected $discount_1_amount = 0;
    protected $discount_1_timer_period = 0;
    protected $discount_2_first_amount = 0;
    protected $discount_2_first_days = 0;
    protected $discount_2_second_amount = 0;
    protected $discount_2_second_days = 0;
    protected $discount_2_between_period_days = 0;

    public function __toString()
    {
        return $this->getName();
    }

    public function isDiscount2FirstEnabled()
    {
        return ($this->getDiscount2FirstAmount() > 0 && $this->getDiscount2FirstDays() > 0);
    }

    public function isDiscount2SecondEnabled()
    {
        return ($this->getDiscount2SecondAmount() > 0 && $this->getDiscount2SecondDays() > 0);
    }
}
