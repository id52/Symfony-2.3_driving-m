<?php

namespace My\AppBundle\Model;

/**
 * Article
 */
abstract class Article
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
    protected $url;

    /**
     * @var string
     */
    protected $keywords;

    /**
     * @var string
     */
    protected $description;

    /**
     * @var string
     */
    protected $text;

    /**
     * @var boolean
     */
    protected $top_menu;

    /**
     * @var boolean
     */
    protected $bottom_menu;

    /**
     * @var boolean
     */
    protected $private;

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
     * @return Article
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
     * Set url
     *
     * @param string $url
     * @return Article
     */
    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * Get url
     *
     * @return string 
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Set keywords
     *
     * @param string $keywords
     * @return Article
     */
    public function setKeywords($keywords)
    {
        $this->keywords = $keywords;

        return $this;
    }

    /**
     * Get keywords
     *
     * @return string 
     */
    public function getKeywords()
    {
        return $this->keywords;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return Article
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
     * Set text
     *
     * @param string $text
     * @return Article
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
     * Set top_menu
     *
     * @param boolean $topMenu
     * @return Article
     */
    public function setTopMenu($topMenu)
    {
        $this->top_menu = $topMenu;

        return $this;
    }

    /**
     * Get top_menu
     *
     * @return boolean 
     */
    public function getTopMenu()
    {
        return $this->top_menu;
    }

    /**
     * Set bottom_menu
     *
     * @param boolean $bottomMenu
     * @return Article
     */
    public function setBottomMenu($bottomMenu)
    {
        $this->bottom_menu = $bottomMenu;

        return $this;
    }

    /**
     * Get bottom_menu
     *
     * @return boolean 
     */
    public function getBottomMenu()
    {
        return $this->bottom_menu;
    }

    /**
     * Set private
     *
     * @param boolean $private
     * @return Article
     */
    public function setPrivate($private)
    {
        $this->private = $private;

        return $this;
    }

    /**
     * Get private
     *
     * @return boolean 
     */
    public function getPrivate()
    {
        return $this->private;
    }

    /**
     * Set position
     *
     * @param integer $position
     * @return Article
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
     * @return Article
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
     * @return Article
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
}
