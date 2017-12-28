<?php

namespace My\PromoBundle\Model;

/**
 * Campaign
 */
abstract class Campaign
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
    protected $type;

    /**
     * @var string
     */
    protected $payment_type;

    /**
     * @var \DateTime
     */
    protected $used_from;

    /**
     * @var \DateTime
     */
    protected $used_to;

    /**
     * @var boolean
     */
    protected $active;

    /**
     * @var integer
     */
    protected $discount;

    /**
     * @var integer
     */
    protected $max_activations;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    protected $keys;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->keys = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set name
     *
     * @param string $name
     * @return Campaign
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
     * Set type
     *
     * @param string $type
     * @return Campaign
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return string 
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set payment_type
     *
     * @param string $paymentType
     * @return Campaign
     */
    public function setPaymentType($paymentType)
    {
        $this->payment_type = $paymentType;

        return $this;
    }

    /**
     * Get payment_type
     *
     * @return string 
     */
    public function getPaymentType()
    {
        return $this->payment_type;
    }

    /**
     * Set used_from
     *
     * @param \DateTime $usedFrom
     * @return Campaign
     */
    public function setUsedFrom($usedFrom)
    {
        $this->used_from = $usedFrom;

        return $this;
    }

    /**
     * Get used_from
     *
     * @return \DateTime 
     */
    public function getUsedFrom()
    {
        return $this->used_from;
    }

    /**
     * Set used_to
     *
     * @param \DateTime $usedTo
     * @return Campaign
     */
    public function setUsedTo($usedTo)
    {
        $this->used_to = $usedTo;

        return $this;
    }

    /**
     * Get used_to
     *
     * @return \DateTime 
     */
    public function getUsedTo()
    {
        return $this->used_to;
    }

    /**
     * Set active
     *
     * @param boolean $active
     * @return Campaign
     */
    public function setActive($active)
    {
        $this->active = $active;

        return $this;
    }

    /**
     * Get active
     *
     * @return boolean 
     */
    public function getActive()
    {
        return $this->active;
    }

    /**
     * Set discount
     *
     * @param integer $discount
     * @return Campaign
     */
    public function setDiscount($discount)
    {
        $this->discount = $discount;

        return $this;
    }

    /**
     * Get discount
     *
     * @return integer 
     */
    public function getDiscount()
    {
        return $this->discount;
    }

    /**
     * Set max_activations
     *
     * @param integer $maxActivations
     * @return Campaign
     */
    public function setMaxActivations($maxActivations)
    {
        $this->max_activations = $maxActivations;

        return $this;
    }

    /**
     * Get max_activations
     *
     * @return integer 
     */
    public function getMaxActivations()
    {
        return $this->max_activations;
    }

    /**
     * Add keys
     *
     * @param \My\PromoBundle\Entity\Key $keys
     * @return Campaign
     */
    public function addKey(\My\PromoBundle\Entity\Key $keys)
    {
        $this->keys[] = $keys;

        return $this;
    }

    /**
     * Remove keys
     *
     * @param \My\PromoBundle\Entity\Key $keys
     */
    public function removeKey(\My\PromoBundle\Entity\Key $keys)
    {
        $this->keys->removeElement($keys);
    }

    /**
     * Get keys
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getKeys()
    {
        return $this->keys;
    }
}
