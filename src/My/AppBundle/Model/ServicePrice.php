<?php

namespace My\AppBundle\Model;

/**
 * ServicePrice
 */
abstract class ServicePrice
{
    /**
     * @var integer
     */
    protected $price;

    /**
     * @var boolean
     */
    protected $active;

    /**
     * @var \My\AppBundle\Entity\Service
     */
    protected $service;

    /**
     * @var \My\AppBundle\Entity\Region
     */
    protected $region;


    /**
     * Set price
     *
     * @param integer $price
     * @return ServicePrice
     */
    public function setPrice($price)
    {
        $this->price = $price;

        return $this;
    }

    /**
     * Get price
     *
     * @return integer 
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * Set active
     *
     * @param boolean $active
     * @return ServicePrice
     */
    public function setActive($active)
    {
        $this->active = $active;

        return $this;
    }

    /**
     * Get active
     *
     * @return boolean 
     */
    public function getActive()
    {
        return $this->active;
    }

    /**
     * Set service
     *
     * @param \My\AppBundle\Entity\Service $service
     * @return ServicePrice
     */
    public function setService(\My\AppBundle\Entity\Service $service)
    {
        $this->service = $service;

        return $this;
    }

    /**
     * Get service
     *
     * @return \My\AppBundle\Entity\Service 
     */
    public function getService()
    {
        return $this->service;
    }

    /**
     * Set region
     *
     * @param \My\AppBundle\Entity\Region $region
     * @return ServicePrice
     */
    public function setRegion(\My\AppBundle\Entity\Region $region)
    {
        $this->region = $region;

        return $this;
    }

    /**
     * Get region
     *
     * @return \My\AppBundle\Entity\Region 
     */
    public function getRegion()
    {
        return $this->region;
    }
}
