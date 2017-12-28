<?php

namespace My\AppBundle\Model;

/**
 * UserConfirmation
 */
abstract class UserConfirmation
{
    /**
     * @var integer
     */
    protected $id;

    /**
     * @var string
     */
    protected $hash;

    /**
     * @var string
     */
    protected $sms_code;

    /**
     * @var string
     */
    protected $phone;

    /**
     * @var \DateTime
     */
    protected $last_sent;

    /**
     * @var boolean
     */
    protected $activated;

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
     * Set hash
     *
     * @param string $hash
     * @return UserConfirmation
     */
    public function setHash($hash)
    {
        $this->hash = $hash;

        return $this;
    }

    /**
     * Get hash
     *
     * @return string 
     */
    public function getHash()
    {
        return $this->hash;
    }

    /**
     * Set sms_code
     *
     * @param string $smsCode
     * @return UserConfirmation
     */
    public function setSmsCode($smsCode)
    {
        $this->sms_code = $smsCode;

        return $this;
    }

    /**
     * Get sms_code
     *
     * @return string 
     */
    public function getSmsCode()
    {
        return $this->sms_code;
    }

    /**
     * Set phone
     *
     * @param string $phone
     * @return UserConfirmation
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * Get phone
     *
     * @return string 
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * Set last_sent
     *
     * @param \DateTime $lastSent
     * @return UserConfirmation
     */
    public function setLastSent($lastSent)
    {
        $this->last_sent = $lastSent;

        return $this;
    }

    /**
     * Get last_sent
     *
     * @return \DateTime 
     */
    public function getLastSent()
    {
        return $this->last_sent;
    }

    /**
     * Set activated
     *
     * @param boolean $activated
     * @return UserConfirmation
     */
    public function setActivated($activated)
    {
        $this->activated = $activated;

        return $this;
    }

    /**
     * Get activated
     *
     * @return boolean 
     */
    public function getActivated()
    {
        return $this->activated;
    }

    /**
     * Set user
     *
     * @param \My\AppBundle\Entity\User $user
     * @return UserConfirmation
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
