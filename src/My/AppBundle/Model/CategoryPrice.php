<?php

namespace My\AppBundle\Model;

/**
 * CategoryPrice
 */
abstract class CategoryPrice
{
    /**
     * @var integer
     */
    protected $price_edu;

    /**
     * @var integer
     */
    protected $price_drv;

    /**
     * @var integer
     */
    protected $price_drv_at;

    /**
     * @var boolean
     */
    protected $active;

    /**
     * @var \My\AppBundle\Entity\Category
     */
    protected $category;

    /**
     * @var \My\AppBundle\Entity\Region
     */
    protected $region;


    /**
     * Set price_edu
     *
     * @param integer $priceEdu
     * @return CategoryPrice
     */
    public function setPriceEdu($priceEdu)
    {
        $this->price_edu = $priceEdu;

        return $this;
    }

    /**
     * Get price_edu
     *
     * @return integer 
     */
    public function getPriceEdu()
    {
        return $this->price_edu;
    }

    /**
     * Set price_drv
     *
     * @param integer $priceDrv
     * @return CategoryPrice
     */
    public function setPriceDrv($priceDrv)
    {
        $this->price_drv = $priceDrv;

        return $this;
    }

    /**
     * Get price_drv
     *
     * @return integer 
     */
    public function getPriceDrv()
    {
        return $this->price_drv;
    }

    /**
     * Set price_drv_at
     *
     * @param integer $priceDrvAt
     * @return CategoryPrice
     */
    public function setPriceDrvAt($priceDrvAt)
    {
        $this->price_drv_at = $priceDrvAt;

        return $this;
    }

    /**
     * Get price_drv_at
     *
     * @return integer 
     */
    public function getPriceDrvAt()
    {
        return $this->price_drv_at;
    }

    /**
     * Set active
     *
     * @param boolean $active
     * @return CategoryPrice
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
     * Set category
     *
     * @param \My\AppBundle\Entity\Category $category
     * @return CategoryPrice
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
     * @return CategoryPrice
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
