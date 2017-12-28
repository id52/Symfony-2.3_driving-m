<?php

namespace My\AppBundle\Model;

/**
 * Site
 */
abstract class Site
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
     * @var array
     */
    protected $coords;

    /**
     * @var boolean
     */
    protected $active;

    /**
     * @var boolean
     */
    protected $active_auth;

    /**
     * @var boolean
     */
    protected $_show;

    /**
     * @var boolean
     */
    protected $show_auth;

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
     * @var \My\AppBundle\Entity\Region
     */
    protected $region;


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
     * @return Site
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
     * Set coords
     *
     * @param array $coords
     * @return Site
     */
    public function setCoords($coords)
    {
        $this->coords = $coords;

        return $this;
    }

    /**
     * Get coords
     *
     * @return array 
     */
    public function getCoords()
    {
        return $this->coords;
    }

    /**
     * Set active
     *
     * @param boolean $active
     * @return Site
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
     * Set active_auth
     *
     * @param boolean $activeAuth
     * @return Site
     */
    public function setActiveAuth($activeAuth)
    {
        $this->active_auth = $activeAuth;

        return $this;
    }

    /**
     * Get active_auth
     *
     * @return boolean 
     */
    public function getActiveAuth()
    {
        return $this->active_auth;
    }

    /**
     * Set _show
     *
     * @param boolean $show
     * @return Site
     */
    public function setShow($show)
    {
        $this->_show = $show;

        return $this;
    }

    /**
     * Get _show
     *
     * @return boolean 
     */
    public function getShow()
    {
        return $this->_show;
    }

    /**
     * Set show_auth
     *
     * @param boolean $showAuth
     * @return Site
     */
    public function setShowAuth($showAuth)
    {
        $this->show_auth = $showAuth;

        return $this;
    }

    /**
     * Get show_auth
     *
     * @return boolean 
     */
    public function getShowAuth()
    {
        return $this->show_auth;
    }

    /**
     * Set updated_at
     *
     * @param \DateTime $updatedAt
     * @return Site
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
     * @return Site
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
     * @return Site
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
     * Set region
     *
     * @param \My\AppBundle\Entity\Region $region
     * @return Site
     */
    public function setRegion(\My\AppBundle\Entity\Region $region = null)
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
