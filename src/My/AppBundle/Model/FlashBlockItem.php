<?php

namespace My\AppBundle\Model;

/**
 * FlashBlockItem
 */
abstract class FlashBlockItem
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
    protected $text;

    /**
     * @var string
     */
    protected $category;

    /**
     * @var integer
     */
    protected $position;

    /**
     * @var integer
     */
    protected $lft;

    /**
     * @var integer
     */
    protected $rgt;

    /**
     * @var integer
     */
    protected $lvl;

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
    protected $children;

    /**
     * @var \My\AppBundle\Entity\FlashBlock
     */
    protected $block;

    /**
     * @var \My\AppBundle\Model\FlashBlockItem
     */
    protected $parent;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->children = new \Doctrine\Common\Collections\ArrayCollection();
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
     * @return FlashBlockItem
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
     * @return FlashBlockItem
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
     * Set text
     *
     * @param string $text
     * @return FlashBlockItem
     */
    public function setText($text)
    {
        $this->text = $text;

        return $this;
    }

    /**
     * Get text
     *
     * @return string 
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * Set category
     *
     * @param string $category
     * @return FlashBlockItem
     */
    public function setCategory($category)
    {
        $this->category = $category;

        return $this;
    }

    /**
     * Get category
     *
     * @return string 
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * Set position
     *
     * @param integer $position
     * @return FlashBlockItem
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
     * Set lft
     *
     * @param integer $lft
     * @return FlashBlockItem
     */
    public function setLft($lft)
    {
        $this->lft = $lft;

        return $this;
    }

    /**
     * Get lft
     *
     * @return integer 
     */
    public function getLft()
    {
        return $this->lft;
    }

    /**
     * Set rgt
     *
     * @param integer $rgt
     * @return FlashBlockItem
     */
    public function setRgt($rgt)
    {
        $this->rgt = $rgt;

        return $this;
    }

    /**
     * Get rgt
     *
     * @return integer 
     */
    public function getRgt()
    {
        return $this->rgt;
    }

    /**
     * Set lvl
     *
     * @param integer $lvl
     * @return FlashBlockItem
     */
    public function setLvl($lvl)
    {
        $this->lvl = $lvl;

        return $this;
    }

    /**
     * Get lvl
     *
     * @return integer 
     */
    public function getLvl()
    {
        return $this->lvl;
    }

    /**
     * Set updated_at
     *
     * @param \DateTime $updatedAt
     * @return FlashBlockItem
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
     * @return FlashBlockItem
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
     * @return FlashBlockItem
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
     * Add children
     *
     * @param \My\AppBundle\Model\FlashBlockItem $children
     * @return FlashBlockItem
     */
    public function addChild(\My\AppBundle\Model\FlashBlockItem $children)
    {
        $this->children[] = $children;

        return $this;
    }

    /**
     * Remove children
     *
     * @param \My\AppBundle\Model\FlashBlockItem $children
     */
    public function removeChild(\My\AppBundle\Model\FlashBlockItem $children)
    {
        $this->children->removeElement($children);
    }

    /**
     * Get children
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * Set block
     *
     * @param \My\AppBundle\Entity\FlashBlock $block
     * @return FlashBlockItem
     */
    public function setBlock(\My\AppBundle\Entity\FlashBlock $block = null)
    {
        $this->block = $block;

        return $this;
    }

    /**
     * Get block
     *
     * @return \My\AppBundle\Entity\FlashBlock 
     */
    public function getBlock()
    {
        return $this->block;
    }

    /**
     * Set parent
     *
     * @param \My\AppBundle\Model\FlashBlockItem $parent
     * @return FlashBlockItem
     */
    public function setParent(\My\AppBundle\Model\FlashBlockItem $parent = null)
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * Get parent
     *
     * @return \My\AppBundle\Model\FlashBlockItem 
     */
    public function getParent()
    {
        return $this->parent;
    }
}
