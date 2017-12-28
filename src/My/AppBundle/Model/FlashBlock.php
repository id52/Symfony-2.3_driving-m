<?php

namespace My\AppBundle\Model;

/**
 * FlashBlock
 */
abstract class FlashBlock
{
    /**
     * @var integer
     */
    protected $id;

    /**
     * @var string
     */
    protected $_key;

    /**
     * @var string
     */
    protected $title;

    /**
     * @var boolean
     */
    protected $is_simple;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    protected $items;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->items = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set _key
     *
     * @param string $key
     * @return FlashBlock
     */
    public function setKey($key)
    {
        $this->_key = $key;

        return $this;
    }

    /**
     * Get _key
     *
     * @return string 
     */
    public function getKey()
    {
        return $this->_key;
    }

    /**
     * Set title
     *
     * @param string $title
     * @return FlashBlock
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
     * Set is_simple
     *
     * @param boolean $isSimple
     * @return FlashBlock
     */
    public function setIsSimple($isSimple)
    {
        $this->is_simple = $isSimple;

        return $this;
    }

    /**
     * Get is_simple
     *
     * @return boolean 
     */
    public function getIsSimple()
    {
        return $this->is_simple;
    }

    /**
     * Add items
     *
     * @param \My\AppBundle\Model\FlashBlockItem $items
     * @return FlashBlock
     */
    public function addItem(\My\AppBundle\Model\FlashBlockItem $items)
    {
        $this->items[] = $items;

        return $this;
    }

    /**
     * Remove items
     *
     * @param \My\AppBundle\Model\FlashBlockItem $items
     */
    public function removeItem(\My\AppBundle\Model\FlashBlockItem $items)
    {
        $this->items->removeElement($items);
    }

    /**
     * Get items
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getItems()
    {
        return $this->items;
    }
}
