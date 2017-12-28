<?php

namespace My\PromoBundle\Model;

/**
 * Key
 */
abstract class Key
{
    /**
     * @var integer
     */
    protected $id;

    /**
     * @var string
     */
    protected $_key;

    /**
     * @var array
     */
    protected $activations_info;

    /**
     * @var array
     */
    protected $tried_enters;

    /**
     * @var array
     */
    protected $rezerv;

    /**
     * @var array
     */
    protected $active_user;

    /**
     * @var \DateTime
     */
    protected $created;

    /**
     * @var \DateTime
     */
    protected $activated;

    /**
     * @var \My\PromoBundle\Entity\Campaign
     */
    protected $campaign;


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
     * Set _key
     *
     * @param string $key
     * @return Key
     */
    public function setKey($key)
    {
        $this->_key = $key;

        return $this;
    }

    /**
     * Get _key
     *
     * @return string 
     */
    public function getKey()
    {
        return $this->_key;
    }

    /**
     * Set activations_info
     *
     * @param array $activationsInfo
     * @return Key
     */
    public function setActivationsInfo($activationsInfo)
    {
        $this->activations_info = $activationsInfo;

        return $this;
    }

    /**
     * Get activations_info
     *
     * @return array 
     */
    public function getActivationsInfo()
    {
        return $this->activations_info;
    }

    /**
     * Set tried_enters
     *
     * @param array $triedEnters
     * @return Key
     */
    public function setTriedEnters($triedEnters)
    {
        $this->tried_enters = $triedEnters;

        return $this;
    }

    /**
     * Get tried_enters
     *
     * @return array 
     */
    public function getTriedEnters()
    {
        return $this->tried_enters;
    }

    /**
     * Set rezerv
     *
     * @param array $rezerv
     * @return Key
     */
    public function setRezerv($rezerv)
    {
        $this->rezerv = $rezerv;

        return $this;
    }

    /**
     * Get rezerv
     *
     * @return array 
     */
    public function getRezerv()
    {
        return $this->rezerv;
    }

    /**
     * Set active_user
     *
     * @param array $activeUser
     * @return Key
     */
    public function setActiveUser($activeUser)
    {
        $this->active_user = $activeUser;

        return $this;
    }

    /**
     * Get active_user
     *
     * @return array 
     */
    public function getActiveUser()
    {
        return $this->active_user;
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     * @return Key
     */
    public function setCreated($created)
    {
        $this->created = $created;

        return $this;
    }

    /**
     * Get created
     *
     * @return \DateTime 
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * Set activated
     *
     * @param \DateTime $activated
     * @return Key
     */
    public function setActivated($activated)
    {
        $this->activated = $activated;

        return $this;
    }

    /**
     * Get activated
     *
     * @return \DateTime 
     */
    public function getActivated()
    {
        return $this->activated;
    }

    /**
     * Set campaign
     *
     * @param \My\PromoBundle\Entity\Campaign $campaign
     * @return Key
     */
    public function setCampaign(\My\PromoBundle\Entity\Campaign $campaign = null)
    {
        $this->campaign = $campaign;

        return $this;
    }

    /**
     * Get campaign
     *
     * @return \My\PromoBundle\Entity\Campaign 
     */
    public function getCampaign()
    {
        return $this->campaign;
    }
}
