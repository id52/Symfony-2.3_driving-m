<?php

namespace My\AppBundle\Model;

/**
 * OfferPrice
 */
abstract class OfferPrice
{
    /**
     * @var boolean
     */
    protected $at;

    /**
     * @var integer
     */
    protected $price;

    /**
     * @var \My\AppBundle\Entity\Category
     */
    protected $category;

    /**
     * @var \My\AppBundle\Entity\Region
     */
    protected $region;

    /**
     * @var \My\AppBundle\Entity\Offer
     */
    protected $offer;


    /**
     * Set at
     *
     * @param boolean $at
     * @return OfferPrice
     */
    public function setAt($at)
    {
        $this->at = $at;

        return $this;
    }

    /**
     * Get at
     *
     * @return boolean 
     */
    public function getAt()
    {
        return $this->at;
    }

    /**
     * Set price
     *
     * @param integer $price
     * @return OfferPrice
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
     * Set category
     *
     * @param \My\AppBundle\Entity\Category $category
     * @return OfferPrice
     */
    public function setCategory(\My\AppBundle\Entity\Category $category)
    {
        $this->category = $category;

        return $this;
    }

    /**
     * Get category
     *
     * @return \My\AppBundle\Entity\Category 
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * Set region
     *
     * @param \My\AppBundle\Entity\Region $region
     * @return OfferPrice
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

    /**
     * Set offer
     *
     * @param \My\AppBundle\Entity\Offer $offer
     * @return OfferPrice
     */
    public function setOffer(\My\AppBundle\Entity\Offer $offer)
    {
        $this->offer = $offer;

        return $this;
    }

    /**
     * Get offer
     *
     * @return \My\AppBundle\Entity\Offer 
     */
    public function getOffer()
    {
        return $this->offer;
    }
}
