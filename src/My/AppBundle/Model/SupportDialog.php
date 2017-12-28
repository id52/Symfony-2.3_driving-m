<?php

namespace My\AppBundle\Model;

/**
 * SupportDialog
 */
abstract class SupportDialog
{
    /**
     * @var integer
     */
    protected $id;

    /**
     * @var boolean
     */
    protected $answered;

    /**
     * @var \DateTime
     */
    protected $created_at;

    /**
     * @var \DateTime
     */
    protected $last_message_time;

    /**
     * @var string
     */
    protected $last_message_text;

    /**
     * @var \DateTime
     */
    protected $limit_answer_date;

    /**
     * @var boolean
     */
    protected $user_read;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    protected $messages;

    /**
     * @var \My\AppBundle\Entity\SupportCategory
     */
    protected $category;

    /**
     * @var \My\AppBundle\Entity\User
     */
    protected $last_moderator;

    /**
     * @var \My\AppBundle\Entity\User
     */
    protected $user;

    /**
     * @var \My\AppBundle\Entity\Theme
     */
    protected $theme;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->messages = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set answered
     *
     * @param boolean $answered
     * @return SupportDialog
     */
    public function setAnswered($answered)
    {
        $this->answered = $answered;

        return $this;
    }

    /**
     * Get answered
     *
     * @return boolean 
     */
    public function getAnswered()
    {
        return $this->answered;
    }

    /**
     * Set created_at
     *
     * @param \DateTime $createdAt
     * @return SupportDialog
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
     * Set last_message_time
     *
     * @param \DateTime $lastMessageTime
     * @return SupportDialog
     */
    public function setLastMessageTime($lastMessageTime)
    {
        $this->last_message_time = $lastMessageTime;

        return $this;
    }

    /**
     * Get last_message_time
     *
     * @return \DateTime 
     */
    public function getLastMessageTime()
    {
        return $this->last_message_time;
    }

    /**
     * Set last_message_text
     *
     * @param string $lastMessageText
     * @return SupportDialog
     */
    public function setLastMessageText($lastMessageText)
    {
        $this->last_message_text = $lastMessageText;

        return $this;
    }

    /**
     * Get last_message_text
     *
     * @return string 
     */
    public function getLastMessageText()
    {
        return $this->last_message_text;
    }

    /**
     * Set limit_answer_date
     *
     * @param \DateTime $limitAnswerDate
     * @return SupportDialog
     */
    public function setLimitAnswerDate($limitAnswerDate)
    {
        $this->limit_answer_date = $limitAnswerDate;

        return $this;
    }

    /**
     * Get limit_answer_date
     *
     * @return \DateTime 
     */
    public function getLimitAnswerDate()
    {
        return $this->limit_answer_date;
    }

    /**
     * Set user_read
     *
     * @param boolean $userRead
     * @return SupportDialog
     */
    public function setUserRead($userRead)
    {
        $this->user_read = $userRead;

        return $this;
    }

    /**
     * Get user_read
     *
     * @return boolean 
     */
    public function getUserRead()
    {
        return $this->user_read;
    }

    /**
     * Add messages
     *
     * @param \My\AppBundle\Entity\SupportMessage $messages
     * @return SupportDialog
     */
    public function addMessage(\My\AppBundle\Entity\SupportMessage $messages)
    {
        $this->messages[] = $messages;

        return $this;
    }

    /**
     * Remove messages
     *
     * @param \My\AppBundle\Entity\SupportMessage $messages
     */
    public function removeMessage(\My\AppBundle\Entity\SupportMessage $messages)
    {
        $this->messages->removeElement($messages);
    }

    /**
     * Get messages
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getMessages()
    {
        return $this->messages;
    }

    /**
     * Set category
     *
     * @param \My\AppBundle\Entity\SupportCategory $category
     * @return SupportDialog
     */
    public function setCategory(\My\AppBundle\Entity\SupportCategory $category = null)
    {
        $this->category = $category;

        return $this;
    }

    /**
     * Get category
     *
     * @return \My\AppBundle\Entity\SupportCategory 
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * Set last_moderator
     *
     * @param \My\AppBundle\Entity\User $lastModerator
     * @return SupportDialog
     */
    public function setLastModerator(\My\AppBundle\Entity\User $lastModerator = null)
    {
        $this->last_moderator = $lastModerator;

        return $this;
    }

    /**
     * Get last_moderator
     *
     * @return \My\AppBundle\Entity\User 
     */
    public function getLastModerator()
    {
        return $this->last_moderator;
    }

    /**
     * Set user
     *
     * @param \My\AppBundle\Entity\User $user
     * @return SupportDialog
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

    /**
     * Set theme
     *
     * @param \My\AppBundle\Entity\Theme $theme
     * @return SupportDialog
     */
    public function setTheme(\My\AppBundle\Entity\Theme $theme = null)
    {
        $this->theme = $theme;

        return $this;
    }

    /**
     * Get theme
     *
     * @return \My\AppBundle\Entity\Theme 
     */
    public function getTheme()
    {
        return $this->theme;
    }
}
