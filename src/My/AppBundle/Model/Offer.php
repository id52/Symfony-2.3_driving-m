<?php

namespace My\AppBundle\Model;

/**
 * Offer
 */
abstract class Offer
{
    /**
     * @var integer
     */
    protected $id;

    /**
     * @var string
     */
    protected $title;

    /**
     * @var string
     */
    protected $subtitle;

    /**
     * @var string
     */
    protected $text1;

    /**
     * @var string
     */
    protected $text2;

    /**
     * @var string
     */
    protected $text3;

    /**
     * @var string
     */
    protected $text4;

    /**
     * @var string
     */
    protected $_desc;

    /**
     * @var string
     */
    protected $description;

    /**
     * @var \DateTime
     */
    protected $started_at;

    /**
     * @var \DateTime
     */
    protected $ended_at;

    /**
     * @var boolean
     */
    protected $is_public;

    /**
     * @var integer
     */
    protected $position;

    /**
     * @var \DateTime
     */
    protected $updated_at;

    /**
     * @var \DateTime
     */
    protected $created_at;

    /**
     * @var \My\AppBundle\Entity\Image
     */
    protected $image;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    protected $prices;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    protected $offers;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->prices = new \Doctrine\Common\Collections\ArrayCollection();
        $this->offers = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set title
     *
     * @param string $title
     * @return Offer
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string 
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set subtitle
     *
     * @param string $subtitle
     * @return Offer
     */
    public function setSubtitle($subtitle)
    {
        $this->subtitle = $subtitle;

        return $this;
    }

    /**
     * Get subtitle
     *
     * @return string 
     */
    public function getSubtitle()
    {
        return $this->subtitle;
    }

    /**
     * Set text1
     *
     * @param string $text1
     * @return Offer
     */
    public function setText1($text1)
    {
        $this->text1 = $text1;

        return $this;
    }

    /**
     * Get text1
     *
     * @return string 
     */
    public function getText1()
    {
        return $this->text1;
    }

    /**
     * Set text2
     *
     * @param string $text2
     * @return Offer
     */
    public function setText2($text2)
    {
        $this->text2 = $text2;

        return $this;
    }

    /**
     * Get text2
     *
     * @return string 
     */
    public function getText2()
    {
        return $this->text2;
    }

    /**
     * Set text3
     *
     * @param string $text3
     * @return Offer
     */
    public function setText3($text3)
    {
        $this->text3 = $text3;

        return $this;
    }

    /**
     * Get text3
     *
     * @return string 
     */
    public function getText3()
    {
        return $this->text3;
    }

    /**
     * Set text4
     *
     * @param string $text4
     * @return Offer
     */
    public function setText4($text4)
    {
        $this->text4 = $text4;

        return $this;
    }

    /**
     * Get text4
     *
     * @return string 
     */
    public function getText4()
    {
        return $this->text4;
    }

    /**
     * Set _desc
     *
     * @param string $desc
     * @return Offer
     */
    public function setDesc($desc)
    {
        $this->_desc = $desc;

        return $this;
    }

    /**
     * Get _desc
     *
     * @return string 
     */
    public function getDesc()
    {
        return $this->_desc;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return Offer
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string 
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set started_at
     *
     * @param \DateTime $startedAt
     * @return Offer
     */
    public function setStartedAt($startedAt)
    {
        $this->started_at = $startedAt;

        return $this;
    }

    /**
     * Get started_at
     *
     * @return \DateTime 
     */
    public function getStartedAt()
    {
        return $this->started_at;
    }

    /**
     * Set ended_at
     *
     * @param \DateTime $endedAt
     * @return Offer
     */
    public function setEndedAt($endedAt)
    {
        $this->ended_at = $endedAt;

        return $this;
    }

    /**
     * Get ended_at
     *
     * @return \DateTime 
     */
    public function getEndedAt()
    {
        return $this->ended_at;
    }

    /**
     * Set is_public
     *
     * @param boolean $isPublic
     * @return Offer
     */
    public function setIsPublic($isPublic)
    {
        $this->is_public = $isPublic;

        return $this;
    }

    /**
     * Get is_public
     *
     * @return boolean 
     */
    public function getIsPublic()
    {
        return $this->is_public;
    }

    /**
     * Set position
     *
     * @param integer $position
     * @return Offer
     */
    public function setPosition($position)
    {
        $this->position = $position;

        return $this;
    }

    /**
     * Get position
     *
     * @return integer 
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * Set updated_at
     *
     * @param \DateTime $updatedAt
     * @return Offer
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updated_at = $updatedAt;

        return $this;
    }

    /**
     * Get updated_at
     *
     * @return \DateTime 
     */
    public function getUpdatedAt()
    {
        return $this->updated_at;
    }

    /**
     * Set created_at
     *
     * @param \DateTime $createdAt
     * @return Offer
     */
    public function setCreatedAt($createdAt)
    {
        $this->created_at = $createdAt;

        return $this;
    }

    /**
     * Get created_at
     *
     * @return \DateTime 
     */
    public function getCreatedAt()
    {
        return $this->created_at;
    }

    /**
     * Set image
     *
     * @param \My\AppBundle\Entity\Image $image
     * @return Offer
     */
    public function setImage(\My\AppBundle\Entity\Image $image = null)
    {
        $this->image = $image;

        return $this;
    }

    /**
     * Get image
     *
     * @return \My\AppBundle\Entity\Image 
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * Add prices
     *
     * @param \My\AppBundle\Model\OfferPrice $prices
     * @return Offer
     */
    public function addPrice(\My\AppBundle\Model\OfferPrice $prices)
    {
        $this->prices[] = $prices;

        return $this;
    }

    /**
     * Remove prices
     *
     * @param \My\AppBundle\Model\OfferPrice $prices
     */
    public function removePrice(\My\AppBundle\Model\OfferPrice $prices)
    {
        $this->prices->removeElement($prices);
    }

    /**
     * Get prices
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getPrices()
    {
        return $this->prices;
    }

    /**
     * Add offers
     *
     * @param \My\AppBundle\Entity\User $offers
     * @return Offer
     */
    public function addOffer(\My\AppBundle\Entity\User $offers)
    {
        $this->offers[] = $offers;

        return $this;
    }

    /**
     * Remove offers
     *
     * @param \My\AppBundle\Entity\User $offers
     */
    public function removeOffer(\My\AppBundle\Entity\User $offers)
    {
        $this->offers->removeElement($offers);
    }

    /**
     * Get offers
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getOffers()
    {
        return $this->offers;
    }
}
