<?php

namespace My\PaymentBundle\Model;

/**
 * RevertLog
 */
abstract class RevertLog
{
    /**
     * @var integer
     */
    protected $id;

    /**
     * @var boolean
     */
    protected $paid;

    /**
     * @var array
     */
    protected $info;

    /**
     * @var \DateTime
     */
    protected $created_at;

    /**
     * @var \DateTime
     */
    protected $updated_at;

    /**
     * @var \My\PaymentBundle\Entity\Log
     */
    protected $payment_log;

    /**
     * @var \My\AppBundle\Entity\User
     */
    protected $moderator;


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
     * Set paid
     *
     * @param boolean $paid
     * @return RevertLog
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
     * Set info
     *
     * @param array $info
     * @return RevertLog
     */
    public function setInfo($info)
    {
        $this->info = $info;

        return $this;
    }

    /**
     * Get info
     *
     * @return array 
     */
    public function getInfo()
    {
        return $this->info;
    }

    /**
     * Set created_at
     *
     * @param \DateTime $createdAt
     * @return RevertLog
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
     * @return RevertLog
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
     * Set payment_log
     *
     * @param \My\PaymentBundle\Entity\Log $paymentLog
     * @return RevertLog
     */
    public function setPaymentLog(\My\PaymentBundle\Entity\Log $paymentLog = null)
    {
        $this->payment_log = $paymentLog;

        return $this;
    }

    /**
     * Get payment_log
     *
     * @return \My\PaymentBundle\Entity\Log 
     */
    public function getPaymentLog()
    {
        return $this->payment_log;
    }

    /**
     * Set moderator
     *
     * @param \My\AppBundle\Entity\User $moderator
     * @return RevertLog
     */
    public function setModerator(\My\AppBundle\Entity\User $moderator = null)
    {
        $this->moderator = $moderator;

        return $this;
    }

    /**
     * Get moderator
     *
     * @return \My\AppBundle\Entity\User 
     */
    public function getModerator()
    {
        return $this->moderator;
    }
}
