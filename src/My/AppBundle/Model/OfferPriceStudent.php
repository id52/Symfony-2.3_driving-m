<?php

namespace My\AppBundle\Model;

/**
 * OfferPriceStudent
 */
abstract class OfferPriceStudent
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
     * @var \My\AppBundle\Entity\OfferStudent
     */
    protected $offer_student;


    /**
     * Set at
     *
     * @param boolean $at
     * @return OfferPriceStudent
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
     * @return OfferPriceStudent
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
     * @return OfferPriceStudent
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
     * @return OfferPriceStudent
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
     * Set offer_student
     *
     * @param \My\AppBundle\Entity\OfferStudent $offerStudent
     * @return OfferPriceStudent
     */
    public function setOfferStudent(\My\AppBundle\Entity\OfferStudent $offerStudent)
    {
        $this->offer_student = $offerStudent;

        return $this;
    }

    /**
     * Get offer_student
     *
     * @return \My\AppBundle\Entity\OfferStudent 
     */
    public function getOfferStudent()
    {
        return $this->offer_student;
    }
}
