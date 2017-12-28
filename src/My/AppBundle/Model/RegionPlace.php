<?php

namespace My\AppBundle\Model;

/**
 * RegionPlace
 */
abstract class RegionPlace
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
     * @var \Doctrine\Common\Collections\Collection
     */
    protected $users;

    /**
     * @var \My\AppBundle\Entity\Region
     */
    protected $region;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->users = new \Doctrine\Common\Collections\ArrayCollection();
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
     * @return RegionPlace
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
     * Add users
     *
     * @param \My\AppBundle\Entity\User $users
     * @return RegionPlace
     */
    public function addUser(\My\AppBundle\Entity\User $users)
    {
        $this->users[] = $users;

        return $this;
    }

    /**
     * Remove users
     *
     * @param \My\AppBundle\Entity\User $users
     */
    public function removeUser(\My\AppBundle\Entity\User $users)
    {
        $this->users->removeElement($users);
    }

    /**
     * Get users
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getUsers()
    {
        return $this->users;
    }

    /**
     * Set region
     *
     * @param \My\AppBundle\Entity\Region $region
     * @return RegionPlace
     */
    public function setRegion(\My\AppBundle\Entity\Region $region = null)
    {
        $this->region = $region;

        return $this;
    }

    /**
     * Get region
     *
     * @return \My\AppBundle\Entity\Region 
     */
    public function getRegion()
    {
        return $this->region;
    }
}
