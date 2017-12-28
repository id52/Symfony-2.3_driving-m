<?php

namespace My\AppBundle\Model;

/**
 * Info
 */
abstract class Info
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
    protected $text;

    /**
     * @var \DateTime
     */
    protected $release_time;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    protected $readers;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->readers = new \Doctrine\Common\Collections\ArrayCollection();
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
     * @return Info
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
     * Set text
     *
     * @param string $text
     * @return Info
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
     * Set release_time
     *
     * @param \DateTime $releaseTime
     * @return Info
     */
    public function setReleaseTime($releaseTime)
    {
        $this->release_time = $releaseTime;

        return $this;
    }

    /**
     * Get release_time
     *
     * @return \DateTime 
     */
    public function getReleaseTime()
    {
        return $this->release_time;
    }

    /**
     * Add readers
     *
     * @param \My\AppBundle\Entity\User $readers
     * @return Info
     */
    public function addReader(\My\AppBundle\Entity\User $readers)
    {
        $this->readers[] = $readers;

        return $this;
    }

    /**
     * Remove readers
     *
     * @param \My\AppBundle\Entity\User $readers
     */
    public function removeReader(\My\AppBundle\Entity\User $readers)
    {
        $this->readers->removeElement($readers);
    }

    /**
     * Get readers
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getReaders()
    {
        return $this->readers;
    }
}
