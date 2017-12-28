<?php

namespace My\AppBundle\Model;

/**
 * Service
 */
abstract class Service
{
    /**
     * @var integer
     */
    protected $id;

    /**
     * @var string
     */
    protected $name;

    /**
     \* @var string
     */
    protected $type;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    protected $regions_prices;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->regions_prices = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return Service
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set type
     *
     \* @param string $type
     * @return Service
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     \* @return string 
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Add regions_prices
     *
     * @param \My\AppBundle\Model\ServicePrice $regionsPrices
     * @return Service
     */
    public function addRegionsPrice(\My\AppBundle\Model\ServicePrice $regionsPrices)
    {
        $this->regions_prices[] = $regionsPrices;

        return $this;
    }

    /**
     * Remove regions_prices
     *
     * @param \My\AppBundle\Model\ServicePrice $regionsPrices
     */
    public function removeRegionsPrice(\My\AppBundle\Model\ServicePrice $regionsPrices)
    {
        $this->regions_prices->removeElement($regionsPrices);
    }

    /**
     * Get regions_prices
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getRegionsPrices()
    {
        return $this->regions_prices;
    }
}
