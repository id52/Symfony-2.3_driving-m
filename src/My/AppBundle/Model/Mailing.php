<?php

namespace My\AppBundle\Model;

/**
 * Mailing
 */
abstract class Mailing
{
    /**
     * @var integer
     */
    protected $id;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $title;

    /**
     * @var string
     */
    protected $message;

    /**
     * @var \DateTime
     */
    protected $date;

    /**
     * @var array
     */
    protected $filters;

    /**
     * @var string
     */
    protected $users;

    /**
     * @var boolean
     */
    protected $forceSending;


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
     * Set name
     *
     * @param string $name
     * @return Mailing
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set title
     *
     * @param string $title
     * @return Mailing
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
     * Set message
     *
     * @param string $message
     * @return Mailing
     */
    public function setMessage($message)
    {
        $this->message = $message;

        return $this;
    }

    /**
     * Get message
     *
     * @return string 
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * Set date
     *
     * @param \DateTime $date
     * @return Mailing
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime 
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set filters
     *
     * @param array $filters
     * @return Mailing
     */
    public function setFilters($filters)
    {
        $this->filters = $filters;

        return $this;
    }

    /**
     * Get filters
     *
     * @return array 
     */
    public function getFilters()
    {
        return $this->filters;
    }

    /**
     * Set users
     *
     * @param string $users
     * @return Mailing
     */
    public function setUsers($users)
    {
        $this->users = $users;

        return $this;
    }

    /**
     * Get users
     *
     * @return string 
     */
    public function getUsers()
    {
        return $this->users;
    }

    /**
     * Set forceSending
     *
     * @param boolean $forceSending
     * @return Mailing
     */
    public function setForceSending($forceSending)
    {
        $this->forceSending = $forceSending;

        return $this;
    }

    /**
     * Get forceSending
     *
     * @return boolean 
     */
    public function getForceSending()
    {
        return $this->forceSending;
    }
}
