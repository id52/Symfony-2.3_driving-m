<?php

namespace My\PaymentBundle\Model;

/**
 * Log
 */
abstract class Log
{
    /**
     * @var integer
     */
    protected $id;

    /**
     \* @var string
     */
    protected $s_type;

    /**
     * @var string
     */
    protected $s_id;

    /**
     * @var string
     */
    protected $int_ref;

    /**
     * @var integer
     */
    protected $sum;

    /**
     * @var boolean
     */
    protected $paid;

    /**
     * @var string
     */
    protected $comment;

    /**
     * @var \DateTime
     */
    protected $created_at;

    /**
     * @var \DateTime
     */
    protected $updated_at;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    protected $revert_logs;

    /**
     * @var \My\AppBundle\Entity\User
     */
    protected $user;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->revert_logs = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set s_type
     *
     \* @param string $sType
     * @return Log
     */
    public function setSType($sType)
    {
        $this->s_type = $sType;

        return $this;
    }

    /**
     * Get s_type
     *
     \* @return string 
     */
    public function getSType()
    {
        return $this->s_type;
    }

    /**
     * Set s_id
     *
     * @param string $sId
     * @return Log
     */
    public function setSId($sId)
    {
        $this->s_id = $sId;

        return $this;
    }

    /**
     * Get s_id
     *
     * @return string 
     */
    public function getSId()
    {
        return $this->s_id;
    }

    /**
     * Set int_ref
     *
     * @param string $intRef
     * @return Log
     */
    public function setIntRef($intRef)
    {
        $this->int_ref = $intRef;

        return $this;
    }

    /**
     * Get int_ref
     *
     * @return string 
     */
    public function getIntRef()
    {
        return $this->int_ref;
    }

    /**
     * Set sum
     *
     * @param integer $sum
     * @return Log
     */
    public function setSum($sum)
    {
        $this->sum = $sum;

        return $this;
    }

    /**
     * Get sum
     *
     * @return integer 
     */
    public function getSum()
    {
        return $this->sum;
    }

    /**
     * Set paid
     *
     * @param boolean $paid
     * @return Log
     */
    public function setPaid($paid)
    {
        $this->paid = $paid;

        return $this;
    }

    /**
     * Get paid
     *
     * @return boolean 
     */
    public function getPaid()
    {
        return $this->paid;
    }

    /**
     * Set comment
     *
     * @param string $comment
     * @return Log
     */
    public function setComment($comment)
    {
        $this->comment = $comment;

        return $this;
    }

    /**
     * Get comment
     *
     * @return string 
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * Set created_at
     *
     * @param \DateTime $createdAt
     * @return Log
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
     * Set updated_at
     *
     * @param \DateTime $updatedAt
     * @return Log
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
     * Add revert_logs
     *
     * @param \My\PaymentBundle\Entity\RevertLog $revertLogs
     * @return Log
     */
    public function addRevertLog(\My\PaymentBundle\Entity\RevertLog $revertLogs)
    {
        $this->revert_logs[] = $revertLogs;

        return $this;
    }

    /**
     * Remove revert_logs
     *
     * @param \My\PaymentBundle\Entity\RevertLog $revertLogs
     */
    public function removeRevertLog(\My\PaymentBundle\Entity\RevertLog $revertLogs)
    {
        $this->revert_logs->removeElement($revertLogs);
    }

    /**
     * Get revert_logs
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getRevertLogs()
    {
        return $this->revert_logs;
    }

    /**
     * Set user
     *
     * @param \My\AppBundle\Entity\User $user
     * @return Log
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
