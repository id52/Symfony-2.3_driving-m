<?php

namespace My\AppBundle\Model;

/**
 * Notify
 */
abstract class Notify
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
     * @var boolean
     */
    protected $readed;

    /**
     * @var \DateTime
     */
    protected $sended_at;

    /**
     * @var \My\AppBundle\Entity\User
     */
    protected $user_requiring;

    /**
     * @var \My\AppBundle\Entity\User
     */
    protected $user;


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
     * @return Notify
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
     * @return Notify
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
     * Set readed
     *
     * @param boolean $readed
     * @return Notify
     */
    public function setReaded($readed)
    {
        $this->readed = $readed;

        return $this;
    }

    /**
     * Get readed
     *
     * @return boolean 
     */
    public function getReaded()
    {
        return $this->readed;
    }

    /**
     * Set sended_at
     *
     * @param \DateTime $sendedAt
     * @return Notify
     */
    public function setSendedAt($sendedAt)
    {
        $this->sended_at = $sendedAt;

        return $this;
    }

    /**
     * Get sended_at
     *
     * @return \DateTime 
     */
    public function getSendedAt()
    {
        return $this->sended_at;
    }

    /**
     * Set user_requiring
     *
     * @param \My\AppBundle\Entity\User $userRequiring
     * @return Notify
     */
    public function setUserRequiring(\My\AppBundle\Entity\User $userRequiring = null)
    {
        $this->user_requiring = $userRequiring;

        return $this;
    }

    /**
     * Get user_requiring
     *
     * @return \My\AppBundle\Entity\User 
     */
    public function getUserRequiring()
    {
        return $this->user_requiring;
    }

    /**
     * Set user
     *
     * @param \My\AppBundle\Entity\User $user
     * @return Notify
     */
    public function setUser(\My\AppBundle\Entity\User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \My\AppBundle\Entity\User 
     */
    public function getUser()
    {
        return $this->user;
    }
}
