<?php

namespace My\AppBundle\Model;

/**
 * SupportMessage
 */
abstract class SupportMessage
{
    /**
     * @var integer
     */
    protected $id;

    /**
     * @var string
     */
    protected $text;

    /**
     * @var \DateTime
     */
    protected $created_at;

    /**
     * @var \My\AppBundle\Entity\SupportDialog
     */
    protected $dialog;

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
     * Set text
     *
     * @param string $text
     * @return SupportMessage
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
     * Set created_at
     *
     * @param \DateTime $createdAt
     * @return SupportMessage
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
     * Set dialog
     *
     * @param \My\AppBundle\Entity\SupportDialog $dialog
     * @return SupportMessage
     */
    public function setDialog(\My\AppBundle\Entity\SupportDialog $dialog = null)
    {
        $this->dialog = $dialog;

        return $this;
    }

    /**
     * Get dialog
     *
     * @return \My\AppBundle\Entity\SupportDialog 
     */
    public function getDialog()
    {
        return $this->dialog;
    }

    /**
     * Set user
     *
     * @param \My\AppBundle\Entity\User $user
     * @return SupportMessage
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
