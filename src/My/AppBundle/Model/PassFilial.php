<?php

namespace My\AppBundle\Model;

/**
 * PassFilial
 */
abstract class PassFilial
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
    protected $address;

    /**
     * @var string
     */
    protected $station;

    /**
     * @var string
     */
    protected $work_time;

    /**
     * @var array
     */
    protected $phones;

    /**
     * @var array
     */
    protected $emails;

    /**
     * @var array
     */
    protected $coords;

    /**
     * @var string
     */
    protected $address_geo;

    /**
     * @var string
     */
    protected $map_code;

    /**
     * @var boolean
     */
    protected $active;

    /**
     * @var array
     */
    protected $groups;

    /**
     * @var \DateTime
     */
    protected $updated_at;

    /**
     * @var \DateTime
     */
    protected $created_at;

    /**
     * @var \My\AppBundle\Entity\Image
     */
    protected $image;

    /**
     * @var \My\AppBundle\Entity\Region
     */
    protected $region;


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
     * @return PassFilial
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
     * Set address
     *
     * @param string $address
     * @return PassFilial
     */
    public function setAddress($address)
    {
        $this->address = $address;

        return $this;
    }

    /**
     * Get address
     *
     * @return string 
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * Set station
     *
     * @param string $station
     * @return PassFilial
     */
    public function setStation($station)
    {
        $this->station = $station;

        return $this;
    }

    /**
     * Get station
     *
     * @return string 
     */
    public function getStation()
    {
        return $this->station;
    }

    /**
     * Set work_time
     *
     * @param string $workTime
     * @return PassFilial
     */
    public function setWorkTime($workTime)
    {
        $this->work_time = $workTime;

        return $this;
    }

    /**
     * Get work_time
     *
     * @return string 
     */
    public function getWorkTime()
    {
        return $this->work_time;
    }

    /**
     * Set phones
     *
     * @param array $phones
     * @return PassFilial
     */
    public function setPhones($phones)
    {
        $this->phones = $phones;

        return $this;
    }

    /**
     * Get phones
     *
     * @return array 
     */
    public function getPhones()
    {
        return $this->phones;
    }

    /**
     * Set emails
     *
     * @param array $emails
     * @return PassFilial
     */
    public function setEmails($emails)
    {
        $this->emails = $emails;

        return $this;
    }

    /**
     * Get emails
     *
     * @return array 
     */
    public function getEmails()
    {
        return $this->emails;
    }

    /**
     * Set coords
     *
     * @param array $coords
     * @return PassFilial
     */
    public function setCoords($coords)
    {
        $this->coords = $coords;

        return $this;
    }

    /**
     * Get coords
     *
     * @return array 
     */
    public function getCoords()
    {
        return $this->coords;
    }

    /**
     * Set address_geo
     *
     * @param string $addressGeo
     * @return PassFilial
     */
    public function setAddressGeo($addressGeo)
    {
        $this->address_geo = $addressGeo;

        return $this;
    }

    /**
     * Get address_geo
     *
     * @return string 
     */
    public function getAddressGeo()
    {
        return $this->address_geo;
    }

    /**
     * Set map_code
     *
     * @param string $mapCode
     * @return PassFilial
     */
    public function setMapCode($mapCode)
    {
        $this->map_code = $mapCode;

        return $this;
    }

    /**
     * Get map_code
     *
     * @return string 
     */
    public function getMapCode()
    {
        return $this->map_code;
    }

    /**
     * Set active
     *
     * @param boolean $active
     * @return PassFilial
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
     * Set groups
     *
     * @param array $groups
     * @return PassFilial
     */
    public function setGroups($groups)
    {
        $this->groups = $groups;

        return $this;
    }

    /**
     * Get groups
     *
     * @return array 
     */
    public function getGroups()
    {
        return $this->groups;
    }

    /**
     * Set updated_at
     *
     * @param \DateTime $updatedAt
     * @return PassFilial
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
     * Set created_at
     *
     * @param \DateTime $createdAt
     * @return PassFilial
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
     * Set image
     *
     * @param \My\AppBundle\Entity\Image $image
     * @return PassFilial
     */
    public function setImage(\My\AppBundle\Entity\Image $image = null)
    {
        $this->image = $image;

        return $this;
    }

    /**
     * Get image
     *
     * @return \My\AppBundle\Entity\Image 
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * Set region
     *
     * @param \My\AppBundle\Entity\Region $region
     * @return PassFilial
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
