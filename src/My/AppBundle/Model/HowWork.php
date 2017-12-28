<?php

namespace My\AppBundle\Model;

/**
 * HowWork
 */
abstract class HowWork
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
    protected $_desc;

    /**
     * @var integer
     */
    protected $position;

    /**
     * @var \My\AppBundle\Entity\Image
     */
    protected $image;


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
     * @return HowWork
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
     * Set _desc
     *
     * @param string $desc
     * @return HowWork
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
     * Set position
     *
     * @param integer $position
     * @return HowWork
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
     * Set image
     *
     * @param \My\AppBundle\Entity\Image $image
     * @return HowWork
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
}
